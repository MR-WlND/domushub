@extends('layouts.resident.master')

@section('title', 'Kiểm tra aptomat khi mất điện – DomusHub')

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
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 16px;
            border-radius: 0 8px 8px 0;
            margin: 24px 0;
        }
        .warning-box h4 {
            color: #b45309;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .warning-box p {
            margin: 0;
            color: #92400e;
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
        <h1 class="tip-title">Hướng dẫn kiểm tra Aptomat (CB) khi mất điện</h1>
        <div class="tip-meta">
            <span><i class="fa-solid fa-clock"></i> Cập nhật: {{ now()->format('d/m/Y') }}</span>
            <span><i class="fa-solid fa-user-shield"></i> Bởi Ban Quản Lý</span>
        </div>

        <img src="https://images.unsplash.com/photo-1621905252507-b35492cc74b4?auto=format&fit=crop&q=80&w=800" alt="Circuit breaker panel" class="tip-hero-img">

        <div class="tip-body">
            <p>Mất điện cục bộ trong căn hộ (một phòng mất điện hoặc mất toàn bộ) thường xảy ra do quá tải hoặc ngắn mạch khiến Aptomat (CB - cầu dao tự động) bị nhảy để bảo vệ hệ thống. Dưới đây là cách bạn có thể tự kiểm tra và xử lý an toàn.</p>

            <h3>Bước 1: Kiểm tra tình trạng mất điện</h3>
            <p>Hãy xem hành lang hoặc các nhà hàng xóm có điện không. Nếu toàn bộ tòa nhà hoặc khu vực đều mất điện, sự cố thuộc về lưới điện quốc gia hoặc sự cố chung, BQL sẽ phát thông báo và bạn không cần phải làm gì thêm. Nếu chỉ nhà bạn mất điện, hãy tiếp tục Bước 2.</p>

            <h3>Bước 2: Tìm hộp điện tổng (Tủ điện âm tường)</h3>
            <p>Hộp điện thường được đặt gần cửa ra vào chính của căn hộ, cao ngang tầm mắt. Mở nắp nhựa bảo vệ ra, bạn sẽ thấy một hàng các công tắc Aptomat.</p>

            <h3>Bước 3: Phát hiện Aptomat bị nhảy</h3>
            <ul>
                <li>Aptomat đóng (ON) có công tắc nằm ở vị trí <strong>trên cùng</strong>.</li>
                <li>Aptomat ngắt (OFF) có công tắc nằm ở vị trí <strong>dưới cùng</strong>.</li>
                <li>Aptomat bị nhảy do sự cố (Tripped) thường nằm ở <strong>vị trí lơ lửng ở giữa</strong> (không ON cũng không OFF).</li>
            </ul>

            <div class="warning-box">
                <h4><i class="fa-solid fa-bolt"></i> Cẩn thận trước khi bật lại</h4>
                <p>Trước khi gạt lại Aptomat, hãy <strong>rút phích cắm</strong> của các thiết bị có công suất lớn (bàn ủi, lò vi sóng, bình siêu tốc...) vừa sử dụng vì rất có thể chúng là nguyên nhân gây quá tải hoặc chập cháy.</p>
            </div>

            <h3>Bước 4: Cách bật lại Aptomat bị nhảy</h3>
            <ol>
                <li>Gạt công tắc của Aptomat đang lơ lửng <strong>hoàn toàn xuống vị trí OFF (Tắt)</strong>. Bạn phải nghe thấy tiếng "tách" dứt khoát.</li>
                <li>Đợi khoảng 3-5 giây.</li>
                <li>Đẩy mạnh công tắc <strong>lên vị trí ON (Bật)</strong>.</li>
            </ol>

            <h3>Nếu Aptomat vẫn tiếp tục nhảy?</h3>
            <p>Nếu bạn vừa bật lên mà nó lại nhảy tiếng "tạch" và ngắt điện ngay lập tức, điều này báo hiệu đang có <strong>sự cố chập điện nghiêm trọng</strong>. Tuyệt đối <strong>KHÔNG</strong> cố gắng bật lại lần nữa. Hãy để nguyên trạng thái OFF và Gửi yêu cầu phản ánh để kỹ thuật viên lên kiểm tra bằng thiết bị chuyên dụng.</p>
        </div>
    </div>
</div>
@endsection
