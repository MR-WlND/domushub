@extends('layouts.resident.master')

@section('title', 'Cách khóa van nước tạm thời – DomusHub')

@push('styles')
    <style>
        .tip-page-wrapper {
            max-width: 800px;
            margin: 0 auto;
            padding: 24px;
            font-family: 'Inter', sans-serif;
        }
        .btn-back-header {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 10px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #475569;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            margin-bottom: 24px;
        }
        .btn-back-header:hover {
            background: #f8fafc;
            color: #1e3a8a;
            border-color: #94a3b8;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }
        .tip-content-card {
            background: #fff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .tip-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 16px;
        }
        .tip-meta {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 24px;
            display: flex;
            gap: 16px;
            align-items: center;
        }
        .tip-meta i {
            color: #3b82f6;
        }
        .tip-hero-img {
            width: 100%;
            border-radius: 12px;
            margin-bottom: 24px;
            object-fit: cover;
            height: 300px;
        }
        .tip-body h3 {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-top: 24px;
            margin-bottom: 12px;
        }
        .tip-body p {
            font-size: 15px;
            color: #334155;
            line-height: 1.6;
            margin-bottom: 16px;
        }
        .tip-body ul, .tip-body ol {
            padding-left: 20px;
            margin-bottom: 20px;
        }
        .tip-body li {
            font-size: 15px;
            color: #334155;
            line-height: 1.6;
            margin-bottom: 8px;
        }
        .warning-box {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 16px;
            border-radius: 0 8px 8px 0;
            margin: 24px 0;
        }
        .warning-box h4 {
            color: #b91c1c;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .warning-box p {
            margin: 0;
            color: #7f1d1d;
            font-size: 14px;
        }
        @media (max-width: 640px) {
            .tip-page-wrapper {
                padding: 16px;
            }
            .tip-content-card {
                padding: 20px;
            }
            .tip-title {
                font-size: 20px;
            }
        }
    </style>
@endpush

@section('content')
<div class="tip-page-wrapper">
    <a href="{{ route('resident.tickets.create') }}" class="btn-back-header">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Quay lại</span>
    </a>

    <div class="tip-content-card">
        <h1 class="tip-title">Cách khóa van nước tạm thời khi xảy ra rò rỉ</h1>
        <div class="tip-meta">
            <span><i class="fa-solid fa-clock"></i> Cập nhật: {{ now()->format('d/m/Y') }}</span>
            <span><i class="fa-solid fa-user-shield"></i> Bởi Ban Quản Lý</span>
        </div>

        <img src="https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&q=80&w=800" alt="Water valve" class="tip-hero-img">

        <div class="tip-body">
            <p>Rò rỉ nước là một trong những sự cố phổ biến và có thể gây thiệt hại nghiêm trọng đến đồ đạc cũng như kết cấu căn hộ nếu không được xử lý kịp thời. Trước khi kỹ thuật viên có mặt, bạn cần nhanh chóng cắt nguồn nước để giảm thiểu rủi ro.</p>

            <h3>Bước 1: Xác định nguồn rò rỉ</h3>
            <p>Kiểm tra xem vị trí rò rỉ xuất phát từ đâu. Nếu là từ một thiết bị cụ thể như bồn cầu, bồn rửa mặt, hay máy giặt, bạn chỉ cần khóa van nước cục bộ của thiết bị đó.</p>

            <h3>Bước 2: Khóa van cục bộ</h3>
            <ul>
                <li><strong>Bồn cầu:</strong> Van khóa thường nằm trên tường, ngay phía dưới hoặc bên cạnh két nước bồn cầu. Xoay van theo chiều kim đồng hồ cho đến khi chặt.</li>
                <li><strong>Bồn rửa mặt/Chậu rửa bát:</strong> Van khóa thường nằm trong tủ dưới bồn rửa. Thường sẽ có 2 van (nước nóng và nước lạnh). Hãy khóa cả hai van lại.</li>
            </ul>

            <h3>Bước 3: Khóa van nước tổng của căn hộ</h3>
            <p>Nếu bạn không tìm thấy van cục bộ hoặc rò rỉ ở vị trí đường ống ngầm, hãy khóa van nước tổng. Van nước tổng của căn hộ thường được đặt ở hộp kỹ thuật ngoài hành lang (gần cửa ra vào) hoặc trong phòng tắm, khu vực lô gia.</p>
            <ol>
                <li>Mở cửa hộp kỹ thuật (nếu ở ngoài hành lang).</li>
                <li>Tìm đồng hồ nước có ghi số phòng của bạn.</li>
                <li>Gạt hoặc xoay van màu đỏ/xanh nằm ngay cạnh đồng hồ sang vị trí vuông góc với đường ống.</li>
            </ol>

            <div class="warning-box">
                <h4><i class="fa-solid fa-triangle-exclamation"></i> Lưu ý an toàn quan trọng</h4>
                <p>Nếu khu vực rò rỉ nước ở gần các ổ cắm điện hoặc thiết bị điện tử, hãy <strong>NGẮT CẦU DAO ĐIỆN (APTOMAT)</strong> của khu vực đó hoặc cầu dao tổng ngay lập tức trước khi chạm vào nước để tránh nguy cơ điện giật.</p>
            </div>

            <h3>Bước 4: Gọi kỹ thuật viên hỗ trợ</h3>
            <p>Sau khi đã khóa được nguồn nước và đảm bảo an toàn, hãy gửi phản ánh trên hệ thống hoặc gọi Hotline Khẩn cấp để đội ngũ kỹ thuật của tòa nhà lên hỗ trợ khắc phục triệt để.</p>
        </div>
    </div>
</div>
@endsection
