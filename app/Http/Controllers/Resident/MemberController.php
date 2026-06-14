<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\ApartmentInvite;
use App\Models\ApartmentMember;
use App\Models\Resident;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MemberController extends Controller
{
    private function getOwnerApartmentIds(User $user): array
    {
        return $user->residents()
            ->where('relationship', 'owner')
            ->whereNull('deleted_at')
            ->pluck('apartment_id')
            ->toArray();
    }

    private function getAssociatedApartmentIds(User $user): array
    {
        return $user->residents()
            ->whereNull('deleted_at')
            ->pluck('apartment_id')
            ->toArray();
    }

    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();

        $ownerApartmentIds = $this->getOwnerApartmentIds($user);
        $isOwner = !empty($ownerApartmentIds);

        // Xác định tab hoạt động
        $allowedTabs = ['registered', 'declared'];
        if ($isOwner) {
            $allowedTabs[] = 'invitations';
        }
        $activeTab = $request->query('tab', 'registered');
        if (!in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'registered';
        }

        // Lấy danh sách căn hộ phù hợp với quyền hạn
        $apartmentIds = $isOwner ? $ownerApartmentIds : $this->getAssociatedApartmentIds($user);
        $apartments = Apartment::with(['floor.block'])
            ->whereIn('id', $apartmentIds)
            ->orderBy('apartment_number')
            ->get();

        // Lấy danh sách tòa nhà (blocks) chứa các căn hộ của user để lọc form
        $blocks = $apartments
            ->pluck('floor.block')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        // Lấy danh sách cư dân liên kết tài khoản (không bao gồm user hiện tại)
        $registeredMembers = Resident::with(['user', 'apartment.floor.block'])
            ->whereIn('apartment_id', $apartmentIds)
            ->where('user_id', '!=', $user->id)
            ->whereNull('deleted_at')
            ->orderBy('created_at')
            ->get();

        // Lấy thông tin resident của user hiện tại để hiển thị đúng vai trò
        $selfResident = Resident::with(['apartment.floor.block'])
            ->where('user_id', $user->id)
            ->whereIn('apartment_id', $apartmentIds)
            ->whereNull('deleted_at')
            ->orderBy('created_at')
            ->first();

        // Lấy danh sách nhân khẩu gia đình khai báo
        $declaredMembers = ApartmentMember::with(['apartment.floor.block'])
            ->whereIn('apartment_id', $apartmentIds)
            ->orderByDesc('created_at')
            ->get();

        // Lấy danh sách mã mời (chỉ dành cho chủ hộ)
        $invitations = null;
        if ($isOwner) {
            $invitations = ApartmentInvite::with(['apartment.floor.block'])
                ->whereIn('apartment_id', $ownerApartmentIds)
                ->orderByDesc('created_at')
                ->paginate(10)
                ->withQueryString();
        }

        // Thống kê nhanh
        $totalMembersCount = $registeredMembers->count() + $declaredMembers->count() + 1;
        $pendingDeclarationsCount = $declaredMembers->where('status', 'pending')->count();
        $activeInvitesCount = $isOwner
            ? ApartmentInvite::whereIn('apartment_id', $ownerApartmentIds)->where('status', 'active')->count()
            : 0;

        return view('resident.members.index', compact(
            'user',
            'isOwner',
            'activeTab',
            'apartments',
            'blocks',
            'registeredMembers',
            'selfResident',
            'declaredMembers',
            'invitations',
            'totalMembersCount',
            'pendingDeclarationsCount',
            'activeInvitesCount'
        ));
    }

    public function storeDeclared(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $ownerApartmentIds = $this->getOwnerApartmentIds($user);

        if (empty($ownerApartmentIds)) {
            abort(403, 'Chỉ chủ hộ mới có quyền thêm nhân khẩu.');
        }

        $validated = $request->validate([
            'apartment_id' => 'required|in:' . implode(',', $ownerApartmentIds),
            'name' => 'required|string|max:150',
            'birth_year' => 'nullable|digits:4',
            'relationship' => 'nullable|string|max:50',
        ], [
            'apartment_id.required' => 'Vui lòng chọn căn hộ.',
            'apartment_id.in' => 'Căn hộ không hợp lệ.',
            'name.required' => 'Vui lòng nhập họ tên thành viên.',
            'name.max' => 'Họ tên không được vượt quá 150 ký tự.',
            'birth_year.digits' => 'Năm sinh phải đủ 4 chữ số.',
            'relationship.max' => 'Quan hệ không được vượt quá 50 ký tự.',
        ]);

        ApartmentMember::create([
            'apartment_id' => $validated['apartment_id'],
            'name' => $validated['name'],
            'birth_year' => $validated['birth_year'] ?? null,
            'relationship' => $validated['relationship'] ?? null,
            'status' => 'verified',
        ]);

        return redirect()->route('resident.members.index', ['tab' => 'declared'])
            ->with('success', 'Đã thêm nhân khẩu vào hồ sơ thành công.');
    }

    public function destroyDeclared(ApartmentMember $member): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $ownerApartmentIds = $this->getOwnerApartmentIds($user);

        if (!in_array($member->apartment_id, $ownerApartmentIds, true)) {
            abort(403, 'Bạn không có quyền quản lý nhân khẩu căn hộ này.');
        }

        $member->delete();

        return redirect()->route('resident.members.index', ['tab' => 'declared'])
            ->with('success', 'Đã xoá nhân khẩu khai báo thành công.');
    }

    public function storeInvite(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $ownerApartmentIds = $this->getOwnerApartmentIds($user);

        if (empty($ownerApartmentIds)) {
            abort(403, 'Chỉ chủ hộ mới có quyền tạo mã mời.');
        }

        $validated = $request->validate([
            'apartment_id'           => 'required|in:' . implode(',', $ownerApartmentIds),
            'intended_relationship'  => 'required|in:family_member,tenant',
            'note'                   => 'nullable|string|max:200',
            'expired_at'             => 'nullable|date|after:now',
        ], [
            'apartment_id.required'           => 'Vui lòng chọn căn hộ.',
            'apartment_id.in'                 => 'Căn hộ không hợp lệ.',
            'intended_relationship.required'  => 'Vui lòng chọn vai trò dự kiến.',
            'intended_relationship.in'        => 'Vai trò không hợp lệ.',
            'note.max'                        => 'Ghi chú không được vượt quá 200 ký tự.',
            'expired_at.date'                 => 'Ngày hết hạn không đúng định dạng.',
            'expired_at.after'                => 'Ngày hết hạn phải ở tương lai.',
        ]);

        $apartment = Apartment::with('floor')->findOrFail($validated['apartment_id']);
        $code = 'MEM-' . strtoupper(Str::random(6));

        ApartmentInvite::create([
            'block_id'               => $apartment->floor->block_id,
            'apartment_id'           => $apartment->id,
            'created_by'             => $user->id,
            'invite_code'            => $code,
            'intended_relationship'  => $validated['intended_relationship'],
            'note'                   => $validated['note'] ?? null,
            'status'                 => 'active',
            'max_uses'               => 1,
            'uses_count'             => 0,
            'expired_at'             => $validated['expired_at'] ?? null,
        ]);

        return redirect()->route('resident.members.index', ['tab' => 'invitations'])
            ->with('new_invite_code', $code)
            ->with('success', 'Tạo mã mời thành công! Hãy sao chép và gửi mã bên dưới cho người thân.');
    }

    public function destroyInvite($id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $ownerApartmentIds = $this->getOwnerApartmentIds($user);

        $invite = ApartmentInvite::whereIn('apartment_id', $ownerApartmentIds)->findOrFail($id);
        
        $invite->update([
            'status' => 'expired',
        ]);

        return redirect()->route('resident.members.index', ['tab' => 'invitations'])
            ->with('success', 'Đã vô hiệu hóa mã mời thành công.');
    }

    public function destroyRegistered($id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $ownerApartmentIds = $this->getOwnerApartmentIds($user);

        // Đảm bảo resident nằm trong căn hộ thuộc sở hữu của chủ hộ này, và không tự gỡ chính mình
        $resident = Resident::whereIn('apartment_id', $ownerApartmentIds)
            ->where('user_id', '!=', $user->id)
            ->findOrFail($id);

        $resident->delete(); // SoftDeletes → Kích hoạt Boot Event tự cập nhật trạng thái căn hộ

        return redirect()->route('resident.members.index', ['tab' => 'registered'])
            ->with('success', 'Đã gỡ cư dân khỏi hộ gia đình thành công.');
    }
}
