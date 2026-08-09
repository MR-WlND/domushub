@extends('layouts.admin.master')

@section('page_title', 'Tạo Đăng ký Tạm trú / Tạm vắng')

@section('content')
<div class="px-4 py-6 sm:px-6 lg:px-8 max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Thêm mới Đăng ký</h1>
        <p class="mt-1 text-sm text-gray-500">
            Hệ thống sẽ tự động chuyển trạng thái đơn này thành "Đã duyệt" và cập nhật thông tin cư dân.
        </p>
    </div>

    @if($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-4">
            <ul class="list-disc pl-5 text-sm text-red-800">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('admin.temporary-registrations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Cư dân <span class="text-red-500">*</span></label>
                        <select name="user_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">-- Chọn cư dân --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->phone }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Căn hộ <span class="text-red-500">*</span></label>
                        <select name="apartment_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">-- Chọn căn hộ --</option>
                            @foreach($apartments as $apartment)
                                <option value="{{ $apartment->id }}" {{ old('apartment_id') == $apartment->id ? 'selected' : '' }}>
                                    {{ $apartment->apartment_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-6">
                        <label class="block text-sm font-medium text-gray-700">Loại đăng ký <span class="text-red-500">*</span></label>
                        <div class="mt-2 flex space-x-4">
                            <div class="flex items-center">
                                <input type="radio" name="type" value="residence" required {{ old('type', 'residence') == 'residence' ? 'checked' : '' }} class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <label class="ml-2 block text-sm text-gray-900">Tạm trú</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="type" value="absence" required {{ old('type') == 'absence' ? 'checked' : '' }} class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <label class="ml-2 block text-sm text-gray-900">Tạm vắng</label>
                            </div>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Từ ngày <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" required value="{{ old('start_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Đến ngày</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Bỏ trống nếu chưa xác định ngày kết thúc</p>
                    </div>

                    <div class="sm:col-span-6">
                        <label class="block text-sm font-medium text-gray-700">Lý do</label>
                        <textarea name="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('reason') }}</textarea>
                    </div>

                    <div class="sm:col-span-6">
                        <label class="block text-sm font-medium text-gray-700">Giấy tờ đính kèm (CCCD, Hợp đồng...)</label>
                        <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-1 text-xs text-gray-500">Định dạng: JPG, PNG, PDF. Tối đa 2MB.</p>
                    </div>

                </div>

                <div class="mt-6 flex justify-end gap-x-3">
                    <a href="{{ route('admin.temporary-registrations.index') }}" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Hủy</a>
                    <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">Lưu thông tin</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
