@extends('admin.layout')

@section('title', 'Danh sách Đơn giá dịch vụ')

@section('breadcrumb')
    <span class="sep">›</span>
    <span class="current">Đơn giá dịch vụ</span>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Đơn giá dịch vụ</h1>
        <p class="page-subtitle">Quản lý các đơn giá dịch vụ trong hệ thống.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-error">⚠️ {{ session('error') }}</div>
@endif

<div class="table-card" style="margin-bottom:16px;">
    <div style="padding:16px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <form action="{{ route('admin.service-prices.store') }}" method="POST" style="display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end;">
            @csrf
            <div style="display:flex;flex-direction:column;gap:6px;">
                <label>Tên dịch vụ</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-input" required>
                @error('name')<span class="text-danger">{{ $message }}</span>@enderror
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                <label>Loại</label>
                <select name="type" class="form-input" required>
                    <option value="">Chọn loại</option>
                    <option value="electricity" {{ old('type')=='electricity' ? 'selected' : '' }}>Điện</option>
                    <option value="water" {{ old('type')=='water' ? 'selected' : '' }}>Nước</option>
                    <option value="parking" {{ old('type')=='parking' ? 'selected' : '' }}>Gửi xe</option>
                    <option value="management_fee" {{ old('type')=='management_fee' ? 'selected' : '' }}>Phí quản lý</option>
                    <option value="internet" {{ old('type')=='internet' ? 'selected' : '' }}>Internet</option>
                    <option value="service" {{ old('type')=='service' ? 'selected' : '' }}>Dịch vụ</option>
                    <option value="other" {{ old('type')=='other' ? 'selected' : '' }}>Khác</option>
                </select>
                @error('type')<span class="text-danger">{{ $message }}</span>@enderror
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                <label>Đơn giá</label>
                <input type="number" name="unit_price" value="{{ old('unit_price') }}" class="form-input" min="0" step="0.01" required>
                @error('unit_price')<span class="text-danger">{{ $message }}</span>@enderror
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;flex:1;min-width:220px;">
                <label>Mô tả</label>
                <input type="text" name="description" value="{{ old('description') }}" class="form-input">
                @error('description')<span class="text-danger">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Thêm đơn giá</button>
        </form>
    </div>
</div>

<div class="table-card">
    <table class="data-table" style="width:100%;border-collapse:collapse;">
        <thead>
            <tr>
                <th>Tên dịch vụ</th>
                <th>Loại</th>
                <th>Đơn giá</th>
                <th>Trạng thái</th>
                <th>Mô tả</th>
            </tr>
        </thead>
        <tbody>
            @forelse($servicePrices as $price)
                <tr>
                    <td>{{ $price->name }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $price->type)) }}</td>
                    <td>{{ number_format($price->unit_price) }} đ</td>
                    <td>{{ ucfirst($price->status) }}</td>
                    <td>{{ $price->description ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:#6b7280;padding:20px;">Chưa có đơn giá dịch vụ nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
