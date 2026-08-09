@extends('layouts.admin.master')

@section('page_title', 'Quản lý Tạm trú / Tạm vắng')

@section('content')
<div class="px-4 py-6 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quản lý Tạm trú / Tạm vắng</h1>
            <p class="mt-2 text-sm text-gray-700">Danh sách các yêu cầu đăng ký tạm trú, tạm vắng của cư dân.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.temporary-registrations.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Tạo đăng ký mới
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mt-4 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="mt-4 rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Stats --}}
    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-4">
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <dt class="truncate text-sm font-medium text-gray-500">Tổng số</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['total'] }}</dd>
            </div>
        </div>
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <dt class="truncate text-sm font-medium text-gray-500">Chờ duyệt</dt>
                <dd class="mt-1 text-3xl font-semibold text-yellow-600">{{ $stats['pending'] }}</dd>
            </div>
        </div>
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <dt class="truncate text-sm font-medium text-gray-500">Đã duyệt</dt>
                <dd class="mt-1 text-3xl font-semibold text-green-600">{{ $stats['approved'] }}</dd>
            </div>
        </div>
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div class="p-5">
                <dt class="truncate text-sm font-medium text-gray-500">Từ chối</dt>
                <dd class="mt-1 text-3xl font-semibold text-red-600">{{ $stats['rejected'] }}</dd>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mt-6 rounded-lg bg-white p-4 shadow">
        <form method="GET" action="{{ route('admin.temporary-registrations.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700">Loại</label>
                <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Tất cả</option>
                    <option value="residence" {{ request('type') == 'residence' ? 'selected' : '' }}>Tạm trú</option>
                    <option value="absence" {{ request('type') == 'absence' ? 'selected' : '' }}>Tạm vắng</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Trạng thái</label>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Từ chối</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên, Email, SĐT..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div>
                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">Lọc</button>
                @if(request('type') || request('status') || request('search'))
                    <a href="{{ route('admin.temporary-registrations.index') }}" class="ml-2 text-sm text-red-600 hover:text-red-800">Xóa lọc</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Cư dân</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Căn hộ</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Loại</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Thời gian</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Trạng thái</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Người duyệt</th>
                                <th class="relative px-3 py-3.5"><span class="sr-only">Hành động</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($registrations as $reg)
                                <tr>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                        <div class="font-medium">{{ $reg->user->name ?? 'N/A' }}</div>
                                        <div class="text-gray-500">{{ $reg->user->phone ?? '' }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $reg->apartment->apartment_number ?? 'N/A' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        @if($reg->type == 'residence')
                                            <span class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800">Tạm trú</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-orange-100 px-2 text-xs font-semibold leading-5 text-orange-800">Tạm vắng</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $reg->start_date->format('d/m/Y') }} 
                                        @if($reg->end_date)
                                            - {{ $reg->end_date->format('d/m/Y') }}
                                        @else
                                            - (Chưa xác định)
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        @if($reg->status == 'pending')
                                            <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">Chờ duyệt</span>
                                        @elseif($reg->status == 'approved')
                                            <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Đã duyệt</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800">Từ chối</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $reg->approver->name ?? 'N/A' }}
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <a href="{{ route('admin.temporary-registrations.edit', $reg->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Chi tiết / Sửa</a>
                                        
                                        <form action="{{ route('admin.temporary-registrations.destroy', $reg->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa bản ghi này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-4 text-center text-sm text-gray-500">Không có dữ liệu</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4">
        {{ $registrations->links() }}
    </div>
</div>
@endsection
