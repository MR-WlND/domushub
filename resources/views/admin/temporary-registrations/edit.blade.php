@extends('layouts.admin.master')

@section('page_title', 'Chi tiết / Chỉnh sửa Đăng ký Tạm trú / Tạm vắng')

@section('content')
<div class="px-4 py-6 sm:px-6 lg:px-8 max-w-4xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Chi tiết Đăng ký</h1>
            <p class="mt-1 text-sm text-gray-500">
                Xem và xử lý đăng ký tạm trú / tạm vắng.
            </p>
        </div>
        <a href="{{ route('admin.temporary-registrations.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">← Quay lại danh sách</a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-md bg-red-50 p-4">
            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-4">
            <ul class="list-disc pl-5 text-sm text-red-800">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Thông tin đăng ký</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Trạng thái hiện tại: 
                @if($temporaryRegistration->status == 'pending')
                    <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">Chờ duyệt</span>
                @elseif($temporaryRegistration->status == 'approved')
                    <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Đã duyệt</span>
                @else
                    <span class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800">Từ chối</span>
                @endif
            </p>
        </div>
        
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('admin.temporary-registrations.update', $temporaryRegistration->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Cư dân <span class="text-red-500">*</span></label>
                        <select name="user_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ (old('user_id', $temporaryRegistration->user_id) == $user->id) ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->phone }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Căn hộ <span class="text-red-500">*</span></label>
                        <select name="apartment_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @foreach($apartments as $apartment)
                                <option value="{{ $apartment->id }}" {{ (old('apartment_id', $temporaryRegistration->apartment_id) == $apartment->id) ? 'selected' : '' }}>
                                    {{ $apartment->apartment_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-6">
                        <label class="block text-sm font-medium text-gray-700">Loại đăng ký <span class="text-red-500">*</span></label>
                        <div class="mt-2 flex space-x-4">
                            <div class="flex items-center">
                                <input type="radio" name="type" value="residence" required {{ old('type', $temporaryRegistration->type) == 'residence' ? 'checked' : '' }} class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <label class="ml-2 block text-sm text-gray-900">Tạm trú</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="type" value="absence" required {{ old('type', $temporaryRegistration->type) == 'absence' ? 'checked' : '' }} class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <label class="ml-2 block text-sm text-gray-900">Tạm vắng</label>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Từ ngày <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" required value="{{ old('start_date', $temporaryRegistration->start_date ? $temporaryRegistration->start_date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Đến ngày</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $temporaryRegistration->end_date ? $temporaryRegistration->end_date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div class="sm:col-span-6">
                        <label class="block text-sm font-medium text-gray-700">Lý do</label>
                        <textarea name="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('reason', $temporaryRegistration->reason) }}</textarea>
                    </div>

                    <div class="sm:col-span-6">
                        <label class="block text-sm font-medium text-gray-700">Giấy tờ đính kèm</label>
                        @if($temporaryRegistration->attachment_path)
                            <div class="mt-2 mb-2">
                                <a href="{{ Storage::url($temporaryRegistration->attachment_path) }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    [Xem giấy tờ hiện tại]
                                </a>
                            </div>
                        @endif
                        <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-1 text-xs text-gray-500">Tải lên file mới sẽ ghi đè file cũ (nếu có).</p>
                    </div>

                    @if($temporaryRegistration->status == 'rejected' && $temporaryRegistration->rejection_reason)
                    <div class="sm:col-span-6">
                        <div class="bg-red-50 border-l-4 border-red-400 p-4">
                            <div class="flex">
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">
                                        <strong>Lý do từ chối:</strong> {{ $temporaryRegistration->rejection_reason }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="mt-6 flex justify-end gap-x-3">
                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">Cập nhật thông tin</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Phần duyệt đơn (chỉ hiển thị nếu trạng thái là pending) --}}
    @if($temporaryRegistration->status == 'pending')
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Duyệt Đăng ký</h3>
            <p class="mt-1 text-sm text-gray-500">Ban quản lý xác nhận hoặc từ chối đơn này.</p>
        </div>
        <div class="px-4 py-5 sm:p-6 flex flex-col sm:flex-row gap-4">
            <form action="{{ route('admin.temporary-registrations.approve', $temporaryRegistration->id) }}" method="POST">
                @csrf
                <button type="submit" onclick="return confirm('Xác nhận duyệt đăng ký này?');" class="inline-flex items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none">
                    Duyệt Đơn
                </button>
            </form>

            <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="inline-flex items-center justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none">
                Từ chối Đơn
            </button>
        </div>
    </div>

    <!-- Modal Từ chối -->
    <div id="rejectModal" class="hidden fixed inset-0 z-10 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('rejectModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <form action="{{ route('admin.temporary-registrations.reject', $temporaryRegistration->id) }}" method="POST">
                    @csrf
                    <div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Từ chối Đơn Đăng Ký</h3>
                            <div class="mt-2">
                                <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Lý do từ chối <span class="text-red-500">*</span></label>
                                <textarea id="rejection_reason" name="rejection_reason" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Từ chối đơn</button>
                        <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Hủy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
