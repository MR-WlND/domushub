@extends('layouts.resident.master')

@section('title', 'Trung tâm Hướng dẫn & FAQs – DomusHub')

@push('styles')
    <style>
        .guide-page {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: #1e293b;
        }

        /* --- HERO SECTION --- */
        .guide-hero {
            padding: 60px 20px 40px;
            text-align: center;
            background-color: #ffffff;
        }
        .guide-hero h1 {
            font-size: 36px;
            font-weight: 700;
            color: #0b1c4a;
            margin-bottom: 30px;
        }
        .guide-search-wrapper {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }
        .guide-search-input {
            width: 100%;
            padding: 16px 24px 16px 50px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            font-size: 16px;
            outline: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            transition: all 0.2s;
        }
        .guide-search-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 4px 20px rgba(59,130,246,0.1);
        }
        .guide-search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 18px;
        }
        .guide-suggestions {
            margin-top: 16px;
            font-size: 13px;
            color: #64748b;
        }
        .guide-suggestions a {
            color: #64748b;
            text-decoration: underline;
            margin-left: 4px;
        }
        .guide-suggestions a:hover {
            color: #3b82f6;
        }

        /* --- CATEGORIES SECTION --- */
        .guide-categories {
            padding: 40px 20px 60px;
            max-width: 1100px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .guide-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .guide-section-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }
        .guide-view-all {
            font-size: 14px;
            font-weight: 600;
            color: #1e3a8a;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .guide-view-all:hover {
            text-decoration: underline;
        }
        
        .cat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        .cat-card {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .cat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            border-color: #e2e8f0;
        }
        .cat-icon-box {
            width: 40px;
            height: 40px;
            background: #eff6ff;
            color: #1e3a8a;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .cat-title {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
            line-height: 1.4;
        }
        .cat-desc {
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .cat-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .cat-links li {
            margin-bottom: 8px;
            position: relative;
            padding-left: 12px;
        }
        .cat-links li::before {
            content: "•";
            position: absolute;
            left: 0;
            color: #3b82f6;
            font-weight: bold;
        }
        .cat-links a {
            font-size: 13px;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }
        .cat-links a:hover {
            text-decoration: underline;
        }

        /* --- FAQS SECTION --- */
        .guide-faqs-wrapper {
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            padding: 60px 20px;
        }
        .guide-faqs {
            max-width: 700px;
            margin: 0 auto;
            text-align: center;
        }
        .guide-faqs h2 {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 30px;
        }
        .faq-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            text-align: left;
            margin-bottom: 30px;
        }
        .faq-item {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            overflow: hidden;
            transition: border-color 0.2s;
        }
        .faq-item:hover {
            border-color: #cbd5e1;
        }
        .faq-item-box {
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }
        .faq-item-title {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }
        .faq-item-icon {
            color: #64748b;
            font-size: 14px;
            transition: transform 0.3s ease;
        }
        .faq-item-content {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            background: #fcfcfc;
        }
        .faq-item.active .faq-item-content {
            max-height: 500px;
            padding: 0 20px 16px 20px;
        }
        .faq-item.active .faq-item-icon {
            transform: rotate(180deg);
        }
        .faq-item-content p {
            font-size: 14px;
            color: #475569;
            line-height: 1.6;
            margin: 0;
        }
        .btn-view-all-faqs {
            background: #60a5fa; /* Light blue button */
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-view-all-faqs:hover {
            background: #3b82f6;
        }

        /* --- SUPPORT SECTION --- */
        .guide-support-wrapper {
            padding: 0 20px 80px;
            max-width: 900px;
            margin: 0 auto;
        }
        .guide-support-box {
            background-color: #001e71; /* Dark blue */
            border-radius: 20px;
            padding: 50px 40px;
            text-align: center;
            color: #fff;
        }
        .guide-support-box h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #fff;
        }
        .guide-support-box p {
            font-size: 14px;
            color: #cbd5e1;
            margin-bottom: 40px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.5;
        }
        
        .support-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .support-card {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 24px 16px;
            text-align: center;
            transition: background 0.2s;
        }
        .support-card:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        .support-card i {
            font-size: 20px;
            color: #fff;
            margin-bottom: 12px;
            display: block;
        }
        .support-card-label {
            font-size: 12px;
            font-weight: 500;
            color: #cbd5e1;
            margin-bottom: 4px;
        }
        .support-card-value {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
        }

        @media (max-width: 992px) {
            .cat-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 768px) {
            .support-grid {
                grid-template-columns: 1fr;
            }
            .guide-hero h1 {
                font-size: 28px;
            }
        }
        @media (max-width: 576px) {
            .cat-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
<div class="guide-page">

    <!-- HERO SECTION -->
    <div class="guide-hero">
        <h1>Chúng tôi có thể giúp gì cho bạn?</h1>
        <div class="guide-search-wrapper">
            <div style="position: relative;">
                <i class="fa-solid fa-magnifying-glass guide-search-icon"></i>
                <input type="text" class="guide-search-input" placeholder="Tìm kiếm bài viết hướng dẫn, câu hỏi...">
            </div>
            <div class="guide-suggestions">
                Gợi ý: 
                <a href="#">Thanh toán phí</a>, 
                <a href="{{ route('resident.tickets.create') }}">Báo hỏng</a>, 
                <a href="{{ route('resident.vehicles.index') }}">Quy định gửi xe</a>
            </div>
        </div>
    </div>

    <!-- CATEGORIES SECTION -->
    <div class="guide-categories">
        <div class="guide-section-header">
            <h2>Danh mục hướng dẫn</h2>
            <a href="#" class="guide-view-all">Xem tất cả danh mục <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        
        <div class="cat-grid">
            <!-- Card 1 -->
            <div class="cat-card">
                <div class="cat-icon-box"><i class="fa-regular fa-clipboard"></i></div>
                <h3 class="cat-title">Quản lý căn hộ</h3>
                <p class="cat-desc">Thông tin hóa đơn, phí dịch vụ và theo dõi bảng tin nội bộ.</p>
                <ul class="cat-links">
                    <li><a href="{{ route('resident.invoices.index') }}">Xem & thanh toán hóa đơn</a></li>
                    <li><a href="{{ route('resident.posts.index') }}">Cách theo dõi bảng tin</a></li>
                </ul>
            </div>
            
            <!-- Card 2 -->
            <div class="cat-card">
                <div class="cat-icon-box"><i class="fa-solid fa-wrench"></i></div>
                <h3 class="cat-title">Kỹ thuật & Sửa chữa</h3>
                <p class="cat-desc">Hướng dẫn tự xử lý sự cố cơ bản trước khi gọi kỹ thuật viên.</p>
                <ul class="cat-links">
                    <li><a href="{{ route('resident.tips.water-valve') }}">Cách khóa van nước rò rỉ</a></li>
                    <li><a href="{{ route('resident.tips.circuit-breaker') }}">Xử lý khi nhảy Aptomat</a></li>
                </ul>
            </div>
            
            <!-- Card 3 -->
            <div class="cat-card">
                <div class="cat-icon-box"><i class="fa-solid fa-person-swimming"></i></div>
                <h3 class="cat-title">Tiện ích chung cư</h3>
                <p class="cat-desc">Khám phá và đặt trước các khu vực chung như BBQ, Gym, Hồ bơi.</p>
                <ul class="cat-links">
                    <li><a href="{{ route('resident.facilities.index') }}">Hướng dẫn đặt tiện ích</a></li>
                    <li><a href="{{ route('resident.facilities.index') }}">Xem lịch sử đặt chỗ</a></li>
                </ul>
            </div>
            
            <!-- Card 4 -->
            <div class="cat-card">
                <div class="cat-icon-box"><i class="fa-solid fa-car"></i></div>
                <h3 class="cat-title">An ninh & Gửi xe</h3>
                <p class="cat-desc">Thủ tục quản lý phương tiện cá nhân và đăng ký khách ghé thăm.</p>
                <ul class="cat-links">
                    <li><a href="{{ route('resident.vehicles.index') }}">Quy trình đăng ký thẻ xe</a></li>
                    <li><a href="{{ route('resident.visitors.create') }}">Cách khai báo khách thăm</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- FAQS SECTION -->
    <div class="guide-faqs-wrapper">
        <div class="guide-faqs">
            <h2>Câu hỏi thường gặp (FAQs)</h2>
            
            <div class="faq-list">
                <div class="faq-item">
                    <div class="faq-item-box" onclick="this.parentElement.classList.toggle('active')">
                        <span class="faq-item-title">Làm thế nào để đóng phí quản lý qua ứng dụng?</span>
                        <i class="fa-solid fa-chevron-down faq-item-icon"></i>
                    </div>
                    <div class="faq-item-content">
                        <p>Bạn có thể truy cập mục <strong>Hóa đơn</strong> trên ứng dụng. Tại đây sẽ hiển thị các khoản phí cần thanh toán. Bạn chỉ cần chọn hóa đơn và thực hiện thanh toán qua VNPay để hoàn tất.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-item-box" onclick="this.parentElement.classList.toggle('active')">
                        <span class="faq-item-title">Quy trình đăng ký thẻ gửi xe mới như thế nào?</span>
                        <i class="fa-solid fa-chevron-down faq-item-icon"></i>
                    </div>
                    <div class="faq-item-content">
                        <p>Vui lòng vào phần <strong>Phương tiện</strong> > Chọn <strong>Thêm phương tiện mới</strong>. Điền đầy đủ thông tin (biển số, hình ảnh xe, giấy tờ) và gửi yêu cầu. Ban quản lý sẽ duyệt và liên hệ cấp thẻ qua ứng dụng.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-item-box" onclick="this.parentElement.classList.toggle('active')">
                        <span class="faq-item-title">Làm sao để báo cáo sự cố kỹ thuật trong căn hộ?</span>
                        <i class="fa-solid fa-chevron-down faq-item-icon"></i>
                    </div>
                    <div class="faq-item-content">
                        <p>Hãy vào mục <strong>Phản ánh</strong> và chọn <strong>Gửi phản ánh mới</strong>. Lựa chọn danh mục (Kỹ thuật/Điện/Nước), mô tả chi tiết sự cố và đính kèm hình ảnh minh họa để Kỹ thuật viên hỗ trợ bạn nhanh nhất.</p>
                    </div>
                </div>
            </div>
            
            <a href="#" class="btn-view-all-faqs">Xem tất cả câu hỏi</a>
        </div>
    </div>

    <!-- SUPPORT SECTION -->
    <div class="guide-support-wrapper">
        <div class="guide-support-box">
            <h2>Vẫn cần sự trợ giúp từ chúng tôi?</h2>
            <p>Đội ngũ hỗ trợ ResiCare luôn sẵn sàng lắng nghe và giải đáp mọi thắc mắc của cư dân 24/7.</p>
            
            <div class="support-grid">
                <div class="support-card">
                    <i class="fa-solid fa-phone"></i>
                    <div class="support-card-label">Gọi cho chúng tôi</div>
                    <div class="support-card-value">1900 8888</div>
                </div>
                
                <div class="support-card">
                    <i class="fa-regular fa-comment-dots"></i>
                    <div class="support-card-label">Chat trực tuyến</div>
                    <div class="support-card-value">Phản hồi ngay</div>
                </div>
                
                <div class="support-card">
                    <i class="fa-regular fa-envelope"></i>
                    <div class="support-card-label">Gửi Email</div>
                    <div class="support-card-value">support@resi.vn</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
