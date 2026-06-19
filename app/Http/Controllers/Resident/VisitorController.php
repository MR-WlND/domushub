<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VisitorController extends Controller
{
    /**
     * Danh sách QR khách đã đăng ký
     */
    public function index()
    {
        $user = Auth::user();

        if (empty($user->apartment_id)) {
            return view('resident.visitors.index', ['visitors' => collect()]);
        }

        $visitors = Visitor::where('apartment_id', $user->apartment_id)
            ->orderByDesc('created_at')
            ->get();

        // Đánh dấu hết hạn tự động
        $visitors->each(function ($v) {
            if ($v->status === 'pending' && $v->expired_at->isPast()) {
                $v->update(['status' => 'expired']);
            }
        });

        return view('resident.visitors.index', compact('visitors'));
    }

    /**
     * Form tạo QR mời khách
     */
    public function create()
    {
        $user = Auth::user();

        if (empty($user->apartment_id)) {
            return redirect()->route('resident.visitors.index')
                ->withErrors(['apartment' => 'Tài khoản chưa được gắn căn hộ.']);
        }

        return view('resident.visitors.create');
    }

    /**
     * Lưu khách + sinh QR image
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (empty($user->apartment_id)) {
            return back()->withErrors(['apartment' => 'Tài khoản chưa được gắn căn hộ.']);
        }

        $validated = $request->validate([
            'guest_name'  => ['required', 'string', 'max:100'],
            'guest_phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\+\-\s]+$/'],
            'expired_at'  => ['required', 'date', 'after:now'],
            'note'        => ['nullable', 'string', 'max:500'],
        ], [
            'guest_name.required'  => 'Vui lòng nhập tên khách.',
            'guest_name.max'       => 'Tên khách không quá 100 ký tự.',
            'guest_phone.regex'    => 'Số điện thoại không hợp lệ.',
            'expired_at.required'  => 'Vui lòng chọn thời gian hợp lệ.',
            'expired_at.after'     => 'Thời gian hợp lệ phải ở tương lai.',
        ]);

        $token = Visitor::generateToken();

        // Tạo bản ghi trước
        $visitor = Visitor::create([
            'apartment_id'  => $user->apartment_id,
            'registered_by' => $user->id,
            'guest_name'    => $validated['guest_name'],
            'guest_phone'   => $validated['guest_phone'] ?? null,
            'qr_token'      => $token,
            'expired_at'    => $validated['expired_at'],
            'note'          => $validated['note'] ?? null,
            'status'        => 'pending',
        ]);

        // Sinh QR image
        $this->generateQrImage($visitor);

        return redirect()->route('resident.visitors.show', $visitor->id)
            ->with('success', 'Đã tạo QR mời khách thành công. Chia sẻ QR cho khách để vào tòa nhà.');
    }

    /**
     * Hiển thị chi tiết + QR lớn để chia sẻ
     */
    public function show($id)
    {
        $user    = Auth::user();
        $visitor = Visitor::where('id', $id)
            ->where('apartment_id', $user->apartment_id)
            ->firstOrFail();

        // Auto-expire
        if ($visitor->status === 'pending' && $visitor->expired_at->isPast()) {
            $visitor->update(['status' => 'expired']);
            $visitor->refresh();
        }

        return view('resident.visitors.show', compact('visitor'));
    }

    /**
     * Hủy QR khách
     */
    public function destroy($id)
    {
        $user    = Auth::user();
        $visitor = Visitor::where('id', $id)
            ->where('apartment_id', $user->apartment_id)
            ->firstOrFail();

        if ($visitor->isCheckedIn()) {
            return back()->withErrors(['visitor' => 'Không thể hủy QR khi khách đang ở trong tòa nhà.']);
        }

        $visitor->update(['status' => 'cancelled']);

        return redirect()->route('resident.visitors.index')
            ->with('success', 'Đã hủy QR mời khách.');
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function generateQrImage(Visitor $visitor): void
    {
        try {
            if (!class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                return;
            }

            $dir = storage_path('app/public/qr/visitors');
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $filePath = $dir . '/' . $visitor->qr_token . '.svg';

            \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(300)
                ->errorCorrection('H')
                ->generate($visitor->qr_token, $filePath);
        } catch (\Throwable $e) {
            // Silent fail — QR sẽ được hiển thị qua JS fallback
            \Log::warning('QR generation failed: ' . $e->getMessage());
        }
    }
}
