@extends('layouts.resident.master')

@section('title', 'Liên hệ hỗ trợ')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/pages/resident/contact.css'])
@endpush

@section('content')
    <section class="resident-contact-page">
        <!-- KHẨN CẤP SECTION -->
        <div class="contact-group">
            <h2 class="contact-group__title contact-group__title--danger">
                <span class="contact-group__title-icon"><i class="fa-solid fa-circle-exclamation"></i></span>
                Khẩn cấp
            </h2>
            <div class="contact-grid">
                <!-- Card 1: Ban Quản lý -->
                <div class="contact-card contact-card--emergency contact-card--highlight">
                    <span class="contact-card__badge-247">24/7</span>
                    <div class="contact-card__body">
                        <span class="contact-card__icon contact-card__icon--danger"><i class="fa-solid fa-hotel"></i></span>
                        <div class="contact-card__info">
                            <h3 class="contact-card__name">Ban Quản lý</h3>
                            <p class="contact-card__desc">Hỗ trợ khẩn cấp mọi lúc</p>
                        </div>
                    </div>
                    <a href="tel:19001234" class="contact-btn contact-btn--red">
                        <i class="fa-solid fa-phone"></i> 1900 1234
                    </a>
                </div>

                <!-- Card 2: An ninh / Bảo vệ -->
                <div class="contact-card contact-card--emergency">
                    <div class="contact-card__body">
                        <span class="contact-card__icon contact-card__icon--primary"><i class="fa-solid fa-user-shield"></i></span>
                        <div class="contact-card__info">
                            <h3 class="contact-card__name">An ninh / Bảo vệ</h3>
                            <p class="contact-card__desc">Giám sát và an toàn</p>
                        </div>
                    </div>
                    <a href="tel:0287654321" class="contact-btn contact-btn--navy">
                        <i class="fa-solid fa-phone"></i> 028 7654 321
                    </a>
                </div>

                <!-- Card 3: Phòng Kỹ thuật -->
                <div class="contact-card contact-card--emergency">
                    <div class="contact-card__body">
                        <span class="contact-card__icon contact-card__icon--primary"><i class="fa-solid fa-wrench"></i></span>
                        <div class="contact-card__info">
                            <h3 class="contact-card__name">Phòng Kỹ thuật</h3>
                            <p class="contact-card__desc">Sự cố hạ tầng, thiết bị</p>
                        </div>
                    </div>
                    <a href="tel:0289988776" class="contact-btn contact-btn--navy">
                        <i class="fa-solid fa-phone"></i> 028 9988 776
                    </a>
                </div>

                <!-- Card 4: Cứu hỏa/Công an -->
                <div class="contact-card contact-card--emergency">
                    <div class="contact-card__body">
                        <span class="contact-card__icon contact-card__icon--danger"><i class="fa-solid fa-shield-halved"></i></span>
                        <div class="contact-card__info">
                            <h3 class="contact-card__name">Cứu hỏa/Công an</h3>
                            <p class="contact-card__desc">Đường dây nóng quốc gia</p>
                        </div>
                    </div>
                    <div class="contact-card__split-btns">
                        <a href="tel:114" class="contact-btn-split contact-btn-split--red">
                            <span>PCCC (114)</span>
                            <i class="fa-solid fa-phone"></i>
                        </a>
                        <a href="tel:115" class="contact-btn-split contact-btn-split--blue">
                            <span>Cấp cứu (115)</span>
                            <i class="fa-solid fa-phone"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- DỊCH VỤ TÒA NHÀ SECTION -->
        <div class="contact-group">
            <h2 class="contact-group__title contact-group__title--primary">
                <span class="contact-group__title-icon"><i class="fa-solid fa-building"></i></span>
                Dịch vụ tòa nhà
            </h2>
            <div class="contact-grid">
                <!-- Card 1: Ban Quản trị -->
                <div class="contact-card">
                    <div class="contact-card__body">
                        <span class="contact-card__icon contact-card__icon--outline"><i class="fa-solid fa-users"></i></span>
                        <div class="contact-card__info">
                            <h3 class="contact-card__name">Ban Quản trị</h3>
                            <p class="contact-card__desc">Góp ý, kiến nghị chung</p>
                        </div>
                    </div>
                    <a href="tel:028112233" class="contact-btn contact-btn--outline">
                        <i class="fa-solid fa-phone"></i> 028 1122 33
                    </a>
                </div>

                <!-- Card 2: Lễ tân Sảnh -->
                <div class="contact-card">
                    <div class="contact-card__body">
                        <span class="contact-card__icon contact-card__icon--outline"><i class="fa-solid fa-bell"></i></span>
                        <div class="contact-card__info">
                            <h3 class="contact-card__name">Lễ tân Sảnh</h3>
                            <p class="contact-card__desc">Nhận bưu phẩm, khách thăm</p>
                        </div>
                    </div>
                    <a href="tel:028445566" class="contact-btn contact-btn--outline">
                        <i class="fa-solid fa-phone"></i> 028 4455 66
                    </a>
                </div>

                <!-- Card 3: Dịch vụ Vệ sinh -->
                <div class="contact-card">
                    <div class="contact-card__body">
                        <span class="contact-card__icon contact-card__icon--outline"><i class="fa-solid fa-broom"></i></span>
                        <div class="contact-card__info">
                            <h3 class="contact-card__name">Dịch vụ Vệ sinh</h3>
                            <p class="contact-card__desc">Vệ sinh chung & căn hộ</p>
                        </div>
                    </div>
                    <a href="tel:028778899" class="contact-btn contact-btn--outline">
                        <i class="fa-solid fa-phone"></i> 028 7788 99
                    </a>
                </div>

                <!-- Card 4: Thang máy/Điện -->
                <div class="contact-card">
                    <div class="contact-card__body">
                        <span class="contact-card__icon contact-card__icon--outline"><i class="fa-solid fa-bolt"></i></span>
                        <div class="contact-card__info">
                            <h3 class="contact-card__name">Thang máy/Điện</h3>
                            <p class="contact-card__desc">Bảo trì hệ thống kỹ thuật</p>
                        </div>
                    </div>
                    <a href="tel:028001122" class="contact-btn contact-btn--outline">
                        <i class="fa-solid fa-phone"></i> 028 0011 22
                    </a>
                </div>
            </div>
        </div>

        <!-- TIỆN ÍCH KHU VỰC SECTION -->
        @php
            $currentHour = now()->timezone('Asia/Ho_Chi_Minh')->hour;
            $isOpen = ($currentHour >= 7 && $currentHour < 22);
        @endphp
        <div class="contact-group">
            <div class="contact-group__header-row">
                <h2 class="contact-group__title contact-group__title--success">
                    <span class="contact-group__title-icon"><i class="fa-solid fa-store"></i></span>
                    Tiện ích khu vực
                </h2>
                <span class="status-indicator-badge {{ $isOpen ? 'status-indicator-badge--open' : 'status-indicator-badge--closed' }}">
                    {{ $isOpen ? 'Đang hoạt động' : 'Tạm đóng cửa' }}
                </span>
            </div>
            <div class="contact-grid">
                <!-- Card 1: Siêu thị ResiMart -->
                <div class="contact-card">
                    <div class="contact-card__body">
                        <span class="contact-card__icon contact-card__icon--green"><i class="fa-solid fa-basket-shopping"></i></span>
                        <div class="contact-card__info contact-card__info--status">
                            <div class="contact-card__title-row">
                                <h3 class="contact-card__name">Siêu thị ResiMart</h3>
                                <span class="status-badge {{ $isOpen ? 'status-badge--open' : 'status-badge--closed' }}">
                                    {{ $isOpen ? 'MỞ CỬA' : 'ĐÓNG CỬA' }}
                                </span>
                            </div>
                            <p class="contact-card__desc">Thực phẩm tươi sống & thiết yếu</p>
                            <span class="contact-card__time"><i class="fa-regular fa-clock"></i> 07:00 - 22:00</span>
                        </div>
                    </div>
                    <div class="contact-phone-display">
                        <a href="tel:0901234567" class="contact-phone-link">
                            <i class="fa-solid fa-phone"></i> 090 123 4567
                        </a>
                    </div>
                </div>

                <!-- Card 2: Hiệu thuốc PharmCare -->
                <div class="contact-card">
                    <div class="contact-card__body">
                        <span class="contact-card__icon contact-card__icon--green"><i class="fa-solid fa-prescription-bottle-medical"></i></span>
                        <div class="contact-card__info contact-card__info--status">
                            <div class="contact-card__title-row">
                                <h3 class="contact-card__name">Hiệu thuốc PharmCare</h3>
                                <span class="status-badge {{ $isOpen ? 'status-badge--open' : 'status-badge--closed' }}">
                                    {{ $isOpen ? 'MỞ CỬA' : 'ĐÓNG CỬA' }}
                                </span>
                            </div>
                            <p class="contact-card__desc">Dược phẩm & vật tư y tế</p>
                            <span class="contact-card__time"><i class="fa-regular fa-clock"></i> 07:00 - 22:00</span>
                        </div>
                    </div>
                    <div class="contact-phone-display">
                        <a href="tel:0902345678" class="contact-phone-link">
                            <i class="fa-solid fa-phone"></i> 090 234 5678
                        </a>
                    </div>
                </div>

                <!-- Card 3: Giặt ủi CleanPro -->
                <div class="contact-card">
                    <div class="contact-card__body">
                        <span class="contact-card__icon contact-card__icon--green"><i class="fa-solid fa-soap"></i></span>
                        <div class="contact-card__info contact-card__info--status">
                            <div class="contact-card__title-row">
                                <h3 class="contact-card__name">Giặt ủi CleanPro</h3>
                                <span class="status-badge {{ $isOpen ? 'status-badge--open' : 'status-badge--closed' }}">
                                    {{ $isOpen ? 'MỞ CỬA' : 'ĐÓNG CỬA' }}
                                </span>
                            </div>
                            <p class="contact-card__desc">Giặt sấy lấy ngay trong ngày</p>
                            <span class="contact-card__time"><i class="fa-regular fa-clock"></i> 07:00 - 22:00</span>
                        </div>
                    </div>
                    <div class="contact-phone-display">
                        <a href="tel:0903456789" class="contact-phone-link">
                            <i class="fa-solid fa-phone"></i> 090 345 6789
                        </a>
                    </div>
                </div>

                <!-- Card 4: Trường Mầm non -->
                <div class="contact-card">
                    <div class="contact-card__body">
                        <span class="contact-card__icon contact-card__icon--green"><i class="fa-solid fa-school"></i></span>
                        <div class="contact-card__info contact-card__info--status">
                            <div class="contact-card__title-row">
                                <h3 class="contact-card__name">Trường Mầm non</h3>
                                <span class="status-badge {{ $isOpen ? 'status-badge--open' : 'status-badge--closed' }}">
                                    {{ $isOpen ? 'MỞ CỬA' : 'ĐÓNG CỬA' }}
                                </span>
                            </div>
                            <p class="contact-card__desc">Tiêu chuẩn quốc tế ResiKids</p>
                            <span class="contact-card__time"><i class="fa-regular fa-clock"></i> 07:00 - 22:00</span>
                        </div>
                    </div>
                    <div class="contact-phone-display">
                        <a href="tel:0904567890" class="contact-phone-link">
                            <i class="fa-solid fa-phone"></i> 090 456 7890
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
