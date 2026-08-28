@extends('layouts.resident.master')

@section('title', 'Liên hệ hỗ trợ')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/pages/resident/contact.css'])
    <style>
        .desktop-view { display: block; }
        .mobile-view { display: none; }

        @media (max-width: 768px) {
            .desktop-view { display: none !important; }
            .mobile-view { display: block !important; min-height: 100vh; background: #f8fafc; padding-bottom: 80px; }

            .mob-list { padding: 16px; display: flex; flex-direction: column; gap: 16px; }
            .mob-card {
                display: block;
                background: #fff;
                border: 1px solid #f1f5f9;
                border-radius: 16px;
                padding: 16px;
                text-decoration: none;
                color: inherit;
                box-shadow: 0 4px 15px rgba(0,0,0,0.03);
                transition: transform 0.2s;
            }
            .mob-card-top { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px; }
            .mob-icon-box {
                width: 44px;
                height: 44px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                flex-shrink: 0;
            }
            .mob-icon-box.danger { background: #fef2f2; color: #ef4444; }
            .mob-icon-box.primary { background: #eff6ff; color: #1e40af; }
            .mob-icon-box.outline { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
            .mob-icon-box.success { background: #ecfdf5; color: #059669; }
            
            .mob-card-info { flex: 1; min-width: 0; }
            .mob-card-title {
                font-size: 15px;
                font-weight: 700;
                color: #0f172a;
                margin-bottom: 4px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .mob-card-meta { font-size: 12px; color: #64748b; }
            
            .mob-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                width: 100%;
                padding: 12px;
                border-radius: 10px;
                font-size: 14px;
                font-weight: 600;
                text-decoration: none;
                text-align: center;
                transition: background 0.2s;
            }
            .mob-btn.red { background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; }
            .mob-btn.navy { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
            .mob-btn.outline { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
            .mob-btn.blue-text { background: transparent; color: #2563eb; padding: 12px 0 0 0; border-top: 1px dashed #e2e8f0; border-radius: 0; }
            
            .mob-split-btns { display: flex; flex-direction: column; gap: 8px; width: 100%; }
            .mob-split-btn { display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none; border: 1px solid #e2e8f0; }
            .mob-split-btn.red { background: #fef2f2; color: #ef4444; border-color: #fecaca; }
            .mob-split-btn.blue { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }

            .mob-section-title {
                color: #1e293b;
                font-size: 16px;
                font-weight: 700;
                margin: 20px 0 8px;
                display: flex;
                align-items: center;
                gap: 8px;
            }
        }
    </style>
@endpush

@section('content')
<div class="desktop-view">
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
</div> <!-- End desktop-view -->

<!-- Mobile View -->
<div class="mobile-view">
    <div class="mobile-page-header" style="padding: 16px 16px 0;">
        <h1 class="mobile-page-title" style="margin: 0; font-size: 20px; font-weight: 700; color: #1e293b;">Liên hệ hỗ trợ</h1>
    </div>
    <div class="mob-list">
        <!-- Khẩn cấp -->
        <h3 class="mob-section-title" style="margin-top: 4px;">
            <i class="fa-solid fa-circle-exclamation" style="color: #ef4444;"></i> Khẩn cấp
        </h3>
        
        <div class="mob-card">
            <div class="mob-card-top">
                <div class="mob-icon-box danger"><i class="fa-solid fa-hotel"></i></div>
                <div class="mob-card-info">
                    <div class="mob-card-title">Ban Quản lý</div>
                    <div class="mob-card-meta">Hỗ trợ khẩn cấp mọi lúc</div>
                </div>
            </div>
            <a href="tel:19001234" class="mob-btn red"><i class="fa-solid fa-phone"></i> 1900 1234</a>
        </div>

        <div class="mob-card">
            <div class="mob-card-top">
                <div class="mob-icon-box primary"><i class="fa-solid fa-user-shield"></i></div>
                <div class="mob-card-info">
                    <div class="mob-card-title">An ninh / Bảo vệ</div>
                    <div class="mob-card-meta">Giám sát và an toàn</div>
                </div>
            </div>
            <a href="tel:0287654321" class="mob-btn navy"><i class="fa-solid fa-phone"></i> 028 7654 321</a>
        </div>
        
        <div class="mob-card">
            <div class="mob-card-top">
                <div class="mob-icon-box primary"><i class="fa-solid fa-wrench"></i></div>
                <div class="mob-card-info">
                    <div class="mob-card-title">Phòng Kỹ thuật</div>
                    <div class="mob-card-meta">Sự cố hạ tầng, thiết bị</div>
                </div>
            </div>
            <a href="tel:0289988776" class="mob-btn navy"><i class="fa-solid fa-phone"></i> 028 9988 776</a>
        </div>

        <div class="mob-card">
            <div class="mob-card-top">
                <div class="mob-icon-box danger"><i class="fa-solid fa-shield-halved"></i></div>
                <div class="mob-card-info">
                    <div class="mob-card-title">Cứu hỏa/Công an</div>
                    <div class="mob-card-meta">Đường dây nóng quốc gia</div>
                </div>
            </div>
            <div class="mob-split-btns">
                <a href="tel:114" class="mob-split-btn red">
                    <span>PCCC (114)</span><i class="fa-solid fa-phone"></i>
                </a>
                <a href="tel:115" class="mob-split-btn blue">
                    <span>Cấp cứu (115)</span><i class="fa-solid fa-phone"></i>
                </a>
            </div>
        </div>

        <!-- Dịch vụ tòa nhà -->
        <h3 class="mob-section-title">
            <i class="fa-solid fa-building" style="color: #3b82f6;"></i> Dịch vụ tòa nhà
        </h3>

        <div class="mob-card">
            <div class="mob-card-top">
                <div class="mob-icon-box outline"><i class="fa-solid fa-users"></i></div>
                <div class="mob-card-info">
                    <div class="mob-card-title">Ban Quản trị</div>
                    <div class="mob-card-meta">Góp ý, kiến nghị chung</div>
                </div>
            </div>
            <a href="tel:028112233" class="mob-btn outline"><i class="fa-solid fa-phone"></i> 028 1122 33</a>
        </div>

        <div class="mob-card">
            <div class="mob-card-top">
                <div class="mob-icon-box outline"><i class="fa-solid fa-bell"></i></div>
                <div class="mob-card-info">
                    <div class="mob-card-title">Lễ tân Sảnh</div>
                    <div class="mob-card-meta">Nhận bưu phẩm, khách thăm</div>
                </div>
            </div>
            <a href="tel:028445566" class="mob-btn outline"><i class="fa-solid fa-phone"></i> 028 4455 66</a>
        </div>
        
        <div class="mob-card">
            <div class="mob-card-top">
                <div class="mob-icon-box outline"><i class="fa-solid fa-broom"></i></div>
                <div class="mob-card-info">
                    <div class="mob-card-title">Dịch vụ Vệ sinh</div>
                    <div class="mob-card-meta">Vệ sinh chung & căn hộ</div>
                </div>
            </div>
            <a href="tel:028778899" class="mob-btn outline"><i class="fa-solid fa-phone"></i> 028 7788 99</a>
        </div>

        <div class="mob-card">
            <div class="mob-card-top">
                <div class="mob-icon-box outline"><i class="fa-solid fa-bolt"></i></div>
                <div class="mob-card-info">
                    <div class="mob-card-title">Thang máy/Điện</div>
                    <div class="mob-card-meta">Bảo trì hệ thống kỹ thuật</div>
                </div>
            </div>
            <a href="tel:028001122" class="mob-btn outline"><i class="fa-solid fa-phone"></i> 028 0011 22</a>
        </div>

        <!-- Tiện ích khu vực -->
        @php
            $currentHour = now()->timezone('Asia/Ho_Chi_Minh')->hour;
            $isOpen = ($currentHour >= 7 && $currentHour < 22);
            $statusColor = $isOpen ? '#10b981' : '#94a3b8';
            $statusText = $isOpen ? 'MỞ CỬA' : 'ĐÓNG CỬA';
        @endphp
        <h3 class="mob-section-title" style="justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-store" style="color: #10b981;"></i> Tiện ích khu vực</div>
            <span style="font-size: 11px; background: {{ $isOpen ? '#dcfce7' : '#f1f5f9' }}; color: {{ $isOpen ? '#15803d' : '#475569' }}; padding: 4px 10px; border-radius: 12px; font-weight: 600;">{{ $isOpen ? 'Đang hoạt động' : 'Tạm đóng cửa' }}</span>
        </h3>

        <div class="mob-card">
            <div class="mob-card-top">
                <div class="mob-icon-box success"><i class="fa-solid fa-basket-shopping"></i></div>
                <div class="mob-card-info">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="mob-card-title">Siêu thị ResiMart</div>
                        <span style="font-size: 9px; font-weight: 700; color: #fff; background: {{ $statusColor }}; padding: 2px 6px; border-radius: 4px;">{{ $statusText }}</span>
                    </div>
                    <div class="mob-card-meta">Thực phẩm tươi sống & thiết yếu</div>
                    <div class="mob-card-meta" style="margin-top: 2px;"><i class="fa-regular fa-clock"></i> 07:00 - 22:00</div>
                </div>
            </div>
            <a href="tel:0901234567" class="mob-btn blue-text"><i class="fa-solid fa-phone"></i> 090 123 4567</a>
        </div>

        <div class="mob-card">
            <div class="mob-card-top">
                <div class="mob-icon-box success"><i class="fa-solid fa-prescription-bottle-medical"></i></div>
                <div class="mob-card-info">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="mob-card-title">Hiệu thuốc PharmCare</div>
                        <span style="font-size: 9px; font-weight: 700; color: #fff; background: {{ $statusColor }}; padding: 2px 6px; border-radius: 4px;">{{ $statusText }}</span>
                    </div>
                    <div class="mob-card-meta">Dược phẩm & vật tư y tế</div>
                    <div class="mob-card-meta" style="margin-top: 2px;"><i class="fa-regular fa-clock"></i> 07:00 - 22:00</div>
                </div>
            </div>
            <a href="tel:0902345678" class="mob-btn blue-text"><i class="fa-solid fa-phone"></i> 090 234 5678</a>
        </div>

        <div class="mob-card">
            <div class="mob-card-top">
                <div class="mob-icon-box success"><i class="fa-solid fa-soap"></i></div>
                <div class="mob-card-info">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="mob-card-title">Giặt ủi CleanPro</div>
                        <span style="font-size: 9px; font-weight: 700; color: #fff; background: {{ $statusColor }}; padding: 2px 6px; border-radius: 4px;">{{ $statusText }}</span>
                    </div>
                    <div class="mob-card-meta">Giặt sấy lấy ngay trong ngày</div>
                    <div class="mob-card-meta" style="margin-top: 2px;"><i class="fa-regular fa-clock"></i> 07:00 - 22:00</div>
                </div>
            </div>
            <a href="tel:0903456789" class="mob-btn blue-text"><i class="fa-solid fa-phone"></i> 090 345 6789</a>
        </div>
        
        <div class="mob-card">
            <div class="mob-card-top">
                <div class="mob-icon-box success"><i class="fa-solid fa-school"></i></div>
                <div class="mob-card-info">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="mob-card-title">Trường Mầm non</div>
                        <span style="font-size: 9px; font-weight: 700; color: #fff; background: {{ $statusColor }}; padding: 2px 6px; border-radius: 4px;">{{ $statusText }}</span>
                    </div>
                    <div class="mob-card-meta">Tiêu chuẩn quốc tế ResiKids</div>
                    <div class="mob-card-meta" style="margin-top: 2px;"><i class="fa-regular fa-clock"></i> 07:00 - 22:00</div>
                </div>
            </div>
            <a href="tel:0904567890" class="mob-btn blue-text"><i class="fa-solid fa-phone"></i> 090 456 7890</a>
        </div>
    </div>

</div>
@endsection
