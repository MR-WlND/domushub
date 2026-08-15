<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\TemporaryRegistration;
use App\Models\User;
use App\Notifications\NewTemporaryRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TemporaryRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Show registrations that belong to the user OR the user's apartment
        $query = TemporaryRegistration::with(['user', 'apartment', 'approver'])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('apartment_id', $user->apartment_id);
            })
            ->orderBy('created_at', 'desc');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $registrations = $query->paginate(20)->withQueryString();

        return view('resident.temporary-registrations.index', compact('registrations'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user->apartment_id) {
            return redirect()->back()->with('error', 'Bạn chưa được liên kết với căn hộ nào.');
        }

        $extendRegistration = null;
        if ($request->has('extend_id')) {
            $extendRegistration = TemporaryRegistration::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('apartment_id', $user->apartment_id);
            })->find($request->extend_id);
        }

        return view('resident.temporary-registrations.create', compact('user', 'extendRegistration'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'guest_name' => 'nullable|required_if:type,residence|string|max:255',
            'guest_phone' => 'nullable|required_if:type,residence|regex:/^(0)[0-9]{9}$/',
            'guest_cccd' => 'nullable|required_if:type,residence|digits:12',
            'guest_email' => 'nullable|email|max:255',
            'guest_dob' => 'nullable|date',
            'guest_gender' => 'nullable|in:male,female,other',
            'guest_hometown' => 'nullable|string|max:255',
            'relationship' => 'nullable|string|max:255',
            'type' => 'required|in:residence,absence',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'attachments' => 'nullable|required_if:type,residence|array',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['attachments']);
            
            $data['apartment_id'] = $user->apartment_id;
            
            $data['user_id'] = $user->id;
            
            $data['status'] = 'pending';
            
            $attachmentPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $attachmentPaths[] = $file->store('temporary_registrations', 'public');
                }
            }
            $data['attachments'] = $attachmentPaths;

            $registration = TemporaryRegistration::create($data);

            // Notify admins
            $admins = User::whereIn('role', ['admin', 'manager'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewTemporaryRegistrationNotification($registration));
            }

            DB::commit();
            return redirect()->route('resident.temporary-registrations.index')
                ->with('success', 'Tạo đơn đăng ký thành công. Đang chờ Ban quản lý phê duyệt.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        $temporaryRegistration = TemporaryRegistration::with(['user', 'apartment', 'approver'])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('apartment_id', $user->apartment_id);
            })
            ->findOrFail($id);

        return view('resident.temporary-registrations.show', compact('temporaryRegistration'));
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $temporaryRegistration = TemporaryRegistration::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('apartment_id', $user->apartment_id);
            })
            ->findOrFail($id);

        if ($temporaryRegistration->status !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ có thể xóa đơn đang ở trạng thái chờ duyệt.');
        }

        if ($temporaryRegistration->attachment_path) {
            Storage::disk('public')->delete($temporaryRegistration->attachment_path);
        }
        
        if ($temporaryRegistration->attachments) {
            foreach ($temporaryRegistration->attachments as $path) {
                Storage::disk('public')->delete($path);
            }
        }
        
        $temporaryRegistration->delete();

        return redirect()->route('resident.temporary-registrations.index')
            ->with('success', 'Đã xóa đơn đăng ký.');
    }

    public function endEarly($id)
    {
        $user = Auth::user();
        $temporaryRegistration = TemporaryRegistration::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('apartment_id', $user->apartment_id);
            })
            ->findOrFail($id);

        if ($temporaryRegistration->status !== 'approved') {
            return redirect()->back()->with('error', 'Chỉ có thể kết thúc sớm các đăng ký đã được duyệt.');
        }

        DB::beginTransaction();
        try {
            $temporaryRegistration->update([
                'end_date' => now(),
            ]);

            $resident = \App\Models\Resident::where('user_id', $temporaryRegistration->user_id)
                ->where('apartment_id', $temporaryRegistration->apartment_id)
                ->first();

            if ($resident) {
                if ($temporaryRegistration->type === 'residence' && $resident->temporary_status === 'temporary') {
                    $resident->temporary_status = null;
                    $resident->save();
                } elseif ($temporaryRegistration->type === 'absence' && $resident->temporary_status === 'absent') {
                    $resident->temporary_status = null;
                    $resident->save();
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Đã báo cáo kết thúc sớm thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
