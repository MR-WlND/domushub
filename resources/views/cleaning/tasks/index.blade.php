<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DomusHub – Công việc hàng ngày</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',system-ui,sans-serif;background:#F4F7FE;color:#1B2559;min-height:100vh;display:flex;}

        /* ===== SIDEBAR ===== */
        .sidebar{width:240px;background:white;border-right:1px solid #E9EDF7;display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:50;padding:28px 0;}
        .sidebar-brand{padding:0 24px;margin-bottom:32px;display:flex;align-items:center;gap:12px;}
        .sidebar-brand-icon{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#3652D9,#6B8AFF);display:flex;align-items:center;justify-content:center;color:white;font-size:15px;}
        .sidebar-brand-text h2{font-size:16px;font-weight:800;color:#3652D9;letter-spacing:-.3px;line-height:1.2;}
        .sidebar-brand-text p{font-size:10.5px;color:#A3AED0;font-weight:500;}
        .sidebar-nav{flex:1;display:flex;flex-direction:column;gap:2px;padding:0 12px;}
        .sidebar-nav a{display:flex;align-items:center;gap:12px;padding:11px 16px;border-radius:10px;font-size:13.5px;font-weight:500;color:#707EAE;text-decoration:none;transition:.2s;}
        .sidebar-nav a:hover{background:#F4F7FE;color:#3652D9;}
        .sidebar-nav a.active{background:#F4F7FE;color:#3652D9;font-weight:700;}
        .sidebar-nav a.active i{color:#3652D9;}
        .sidebar-nav a i{width:18px;text-align:center;font-size:15px;color:#A3AED0;}
        .sidebar-footer{padding:16px 24px;border-top:1px solid #E9EDF7;margin-top:auto;}
        .sidebar-footer a{display:flex;align-items:center;gap:10px;font-size:13px;color:#707EAE;text-decoration:none;font-weight:500;}
        .sidebar-footer a:hover{color:#3652D9;}

        /* ===== MAIN ===== */
        .main{margin-left:240px;flex:1;min-height:100vh;display:flex;flex-direction:column;}

        /* ===== TOP BAR ===== */
        .topbar{background:white;padding:16px 32px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #E9EDF7;position:sticky;top:0;z-index:40;}
        .topbar-search{display:flex;align-items:center;gap:10px;background:#F4F7FE;border-radius:10px;padding:10px 16px;width:360px;}
        .topbar-search i{color:#A3AED0;font-size:14px;}
        .topbar-search input{border:none;background:none;outline:none;font-size:13px;color:#1B2559;width:100%;font-family:inherit;}
        .topbar-search input::placeholder{color:#A3AED0;}
        .topbar-right{display:flex;align-items:center;gap:20px;}
        .topbar-icons{display:flex;align-items:center;gap:14px;}
        .topbar-icons button{background:none;border:none;cursor:pointer;width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;color:#A3AED0;font-size:16px;transition:.2s;}
        .topbar-icons button:hover{background:#F4F7FE;color:#3652D9;}
        .topbar-user{display:flex;align-items:center;gap:10px;}
        .topbar-user-info{text-align:right;}
        .topbar-user-info .name{font-size:13px;font-weight:700;color:#1B2559;}
        .topbar-user-info .role{font-size:11px;color:#A3AED0;font-weight:500;}
        .topbar-avatar{width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#3652D9,#6B8AFF);display:flex;align-items:center;justify-content:center;color:white;font-size:14px;font-weight:700;}

        /* ===== CONTENT ===== */
        .content{padding:28px 32px;flex:1;}

        /* ===== PAGE HEADER ===== */
        .page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;}
        .page-header h1{font-size:24px;font-weight:800;color:#1B2559;letter-spacing:-.3px;}
        .page-header p{font-size:13px;color:#A3AED0;margin-top:4px;}
        .header-actions{display:flex;gap:10px;}
        .btn-filter{
            display:inline-flex;align-items:center;gap:6px;
            background:white;border:1px solid #E9EDF7;border-radius:10px;
            padding:10px 16px;font-size:12.5px;font-weight:600;color:#707EAE;
            cursor:pointer;transition:.2s;font-family:inherit;
        }
        .btn-filter:hover{border-color:#3652D9;color:#3652D9;}
        .btn-filter.active{background:#3652D9;color:white;border-color:#3652D9;}

        /* ===== TABS ===== */
        .tabs{display:flex;gap:4px;margin-bottom:20px;background:white;border-radius:12px;padding:4px;box-shadow:0 2px 8px rgba(54,82,217,.04);width:fit-content;}
        .tab{
            padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;
            color:#707EAE;cursor:pointer;transition:.2s;display:flex;align-items:center;gap:6px;
        }
        .tab:hover{color:#3652D9;}
        .tab.active{background:#3652D9;color:white;}
        .tab .badge{
            background:rgba(255,255,255,.2);padding:2px 7px;border-radius:10px;
            font-size:10.5px;font-weight:700;
        }
        .tab.active .badge{background:rgba(255,255,255,.25);}
        .tab:not(.active) .badge{background:#F4F7FE;color:#3652D9;}

        /* ===== TASK LIST ===== */
        .task-list{display:flex;flex-direction:column;gap:12px;}
        .task-item{
            background:white;border-radius:14px;padding:20px 24px;
            box-shadow:0 2px 8px rgba(54,82,217,.04);
            display:grid;grid-template-columns:auto 1fr auto;gap:16px;align-items:center;
            transition:.2s;cursor:pointer;border:1.5px solid transparent;
        }
        .task-item:hover{border-color:#D8E0F0;transform:translateY(-1px);box-shadow:0 4px 16px rgba(54,82,217,.08);}

        .task-item__indicator{width:4px;height:48px;border-radius:4px;}
        .task-item__indicator--high{background:#EE5D50;}
        .task-item__indicator--medium{background:#FFB547;}
        .task-item__indicator--low{background:#05CD99;}

        .task-item__body{display:flex;flex-direction:column;gap:6px;}
        .task-item__title{font-size:14.5px;font-weight:700;color:#1B2559;}
        .task-item__desc{font-size:12.5px;color:#A3AED0;}
        .task-item__meta{display:flex;align-items:center;gap:16px;margin-top:4px;}
        .task-meta-tag{
            display:inline-flex;align-items:center;gap:5px;
            font-size:11.5px;color:#707EAE;
        }
        .task-meta-tag i{font-size:11px;color:#A3AED0;}

        .task-item__right{display:flex;flex-direction:column;align-items:flex-end;gap:8px;}
        .task-time{font-size:12px;font-weight:600;color:#1B2559;}
        .task-time span{color:#A3AED0;font-weight:400;}
        .priority-badge{
            display:inline-flex;align-items:center;gap:4px;
            padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700;
        }
        .priority-badge--high{background:#FFF0F0;color:#EE5D50;}
        .priority-badge--medium{background:#FFF4E5;color:#FF9B05;}
        .priority-badge--low{background:#E6F9F0;color:#05CD99;}
        .priority-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor;}
        .status-badge{
            display:inline-flex;align-items:center;gap:5px;
            padding:5px 10px;border-radius:6px;font-size:11px;font-weight:600;
        }
        .status-badge--progress{background:#EEF2FF;color:#3652D9;}
        .status-badge--pending{background:#F4F7FE;color:#A3AED0;}
        .status-badge--done{background:#E6F9F0;color:#05CD99;}
        .status-badge i{font-size:9px;}

        /* ===== SUMMARY BAR ===== */
        .summary-bar{
            display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;
        }
        .summary-item{
            background:white;border-radius:12px;padding:16px 18px;
            box-shadow:0 2px 8px rgba(54,82,217,.04);
            display:flex;align-items:center;gap:12px;
        }
        .summary-icon{
            width:40px;height:40px;border-radius:10px;
            display:flex;align-items:center;justify-content:center;font-size:16px;
        }
        .summary-icon--blue{background:#EEF2FF;color:#3652D9;}
        .summary-icon--orange{background:#FFF4E5;color:#FF9B05;}
        .summary-icon--green{background:#E6F9F0;color:#05CD99;}
        .summary-icon--red{background:#FFF0F0;color:#EE5D50;}
        .summary-info .summary-value{font-size:20px;font-weight:800;color:#1B2559;}
        .summary-info .summary-label{font-size:11px;color:#A3AED0;font-weight:500;}

        /* ===== RESPONSIVE ===== */
        @media(max-width:1024px){.summary-bar{grid-template-columns:repeat(2,1fr);}}
        @media(max-width:768px){
            .sidebar{display:none;}.main{margin-left:0;}.content{padding:20px 16px;}
            .topbar-search{width:180px;}.summary-bar{grid-template-columns:1fr 1fr;}
            .task-item{grid-template-columns:auto 1fr;}.task-item__right{display:none;}
            .page-header{flex-direction:column;align-items:flex-start;gap:12px;}
        }
    </style>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon"><i class="fa-solid fa-building"></i></div>
        <div class="sidebar-brand-text">
            <h2>Domus Hub</h2>
            <p>Luxury Management</p>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('cleaning.dashboard') }}"><i class="fa-solid fa-grid-2"></i> Bảng điều khiển</a>
        <a href="{{ route('cleaning.tasks') }}" class="active"><i class="fa-solid fa-clipboard-list"></i> Công việc hàng ngày</a>
        <a href="{{ route('cleaning.report') }}"><i class="fa-solid fa-triangle-exclamation"></i> Báo cáo sự cố</a>
        <a href="#"><i class="fa-solid fa-spray-can-sparkles"></i> Thiết bị</a>
        <a href="#"><i class="fa-solid fa-route"></i> Lịch trình</a>
        <a href="#"><i class="fa-solid fa-gear"></i> Cài đặt</a>
    </nav>
    <div class="sidebar-footer">
        <a href="#"><i class="fa-solid fa-circle-question"></i> Hỗ trợ</a>
    </div>
</aside>

<!-- ===== MAIN ===== -->
<div class="main">

    <!-- TOP BAR -->
    <header class="topbar">
        <div class="topbar-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Tìm kiếm công việc, khu vực...">
        </div>
        <div class="topbar-right">
            <div class="topbar-icons">
                <button title="Thông báo"><i class="fa-solid fa-bell"></i></button>
                <button title="Cài đặt"><i class="fa-solid fa-gear"></i></button>
            </div>
            <div class="topbar-user">
                <div class="topbar-user-info">
                    <div class="name">{{ auth()->user()->name }}</div>
                    <div class="role">NHÂN VIÊN VỆ SINH</div>
                </div>
                <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <div class="content">

        <!-- PAGE HEADER -->
        <div class="page-header">
            <div>
                <h1>Công việc hàng ngày</h1>
                <p>Danh sách tất cả công việc được giao hôm nay — {{ now()->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
            <div class="header-actions">
                <button class="btn-filter"><i class="fa-solid fa-filter"></i> Lọc</button>
                <button class="btn-filter"><i class="fa-solid fa-arrow-down-wide-short"></i> Sắp xếp</button>
            </div>
        </div>

        <!-- SUMMARY -->
        <div class="summary-bar">
            <div class="summary-item">
                <div class="summary-icon summary-icon--blue"><i class="fa-solid fa-list-check"></i></div>
                <div class="summary-info">
                    <div class="summary-value">07</div>
                    <div class="summary-label">Tổng công việc</div>
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-icon summary-icon--orange"><i class="fa-solid fa-spinner"></i></div>
                <div class="summary-info">
                    <div class="summary-value">02</div>
                    <div class="summary-label">Đang thực hiện</div>
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-icon summary-icon--green"><i class="fa-solid fa-circle-check"></i></div>
                <div class="summary-info">
                    <div class="summary-value">03</div>
                    <div class="summary-label">Hoàn thành</div>
                </div>
            </div>
            <div class="summary-item">
                <div class="summary-icon summary-icon--red"><i class="fa-solid fa-clock"></i></div>
                <div class="summary-info">
                    <div class="summary-value">02</div>
                    <div class="summary-label">Chờ xử lý</div>
                </div>
            </div>
        </div>

        <!-- TABS -->
        <div class="tabs">
            <div class="tab active" data-filter="all">Tất cả <span class="badge">7</span></div>
            <div class="tab" data-filter="progress">Đang làm <span class="badge">2</span></div>
            <div class="tab" data-filter="pending">Chờ xử lý <span class="badge">2</span></div>
            <div class="tab" data-filter="done">Hoàn thành <span class="badge">3</span></div>
        </div>

        <!-- TASK LIST -->
        <div class="task-list">

            <div class="task-item" data-status="progress" onclick="window.location='{{ route('cleaning.tasks.show', 1) }}'">
                <div class="task-item__indicator task-item__indicator--high"></div>
                <div class="task-item__body">
                    <div class="task-item__title">Vệ sinh sâu sảnh chính</div>
                    <div class="task-item__desc">Làm sạch sàn, cửa kính mặt tiền và khử trùng bề mặt tiếp xúc</div>
                    <div class="task-item__meta">
                        <span class="task-meta-tag"><i class="fa-solid fa-location-dot"></i> Tầng trệt</span>
                        <span class="task-meta-tag"><i class="fa-solid fa-clock"></i> 09:00 - 11:00</span>
                        <span class="task-meta-tag"><i class="fa-solid fa-check-double"></i> 2/5 bước</span>
                    </div>
                </div>
                <div class="task-item__right">
                    <span class="priority-badge priority-badge--high">Cao</span>
                    <span class="status-badge status-badge--progress"><i class="fa-solid fa-rotate"></i> Đang làm</span>
                </div>
            </div>

            <div class="task-item" data-status="progress" onclick="window.location='{{ route('cleaning.tasks.show', 2) }}'">
                <div class="task-item__indicator task-item__indicator--medium"></div>
                <div class="task-item__body">
                    <div class="task-item__title">Lau kính cánh đông</div>
                    <div class="task-item__desc">Vệ sinh bề mặt kính bên ngoài tầng 4</div>
                    <div class="task-item__meta">
                        <span class="task-meta-tag"><i class="fa-solid fa-location-dot"></i> Tầng 4</span>
                        <span class="task-meta-tag"><i class="fa-solid fa-clock"></i> 10:30 - 12:30</span>
                        <span class="task-meta-tag"><i class="fa-solid fa-check-double"></i> 1/3 bước</span>
                    </div>
                </div>
                <div class="task-item__right">
                    <span class="priority-badge priority-badge--medium">Trung bình</span>
                    <span class="status-badge status-badge--progress"><i class="fa-solid fa-rotate"></i> Đang làm</span>
                </div>
            </div>

            <div class="task-item" data-status="pending" onclick="window.location='{{ route('cleaning.tasks.show', 3) }}'">
                <div class="task-item__indicator task-item__indicator--low"></div>
                <div class="task-item__body">
                    <div class="task-item__title">Kiểm tra hệ thống đèn</div>
                    <div class="task-item__desc">Hành lang khu vực C, kiểm tra bóng đèn hỏng</div>
                    <div class="task-item__meta">
                        <span class="task-meta-tag"><i class="fa-solid fa-location-dot"></i> Tầng 2</span>
                        <span class="task-meta-tag"><i class="fa-solid fa-clock"></i> 13:00 - 15:00</span>
                        <span class="task-meta-tag"><i class="fa-solid fa-check-double"></i> 0/4 bước</span>
                    </div>
                </div>
                <div class="task-item__right">
                    <span class="priority-badge priority-badge--low">Thấp</span>
                    <span class="status-badge status-badge--pending"><i class="fa-solid fa-clock"></i> Chờ xử lý</span>
                </div>
            </div>

            <div class="task-item" data-status="pending" onclick="window.location='{{ route('cleaning.tasks.show', 4) }}'">
                <div class="task-item__indicator task-item__indicator--medium"></div>
                <div class="task-item__body">
                    <div class="task-item__title">Thu gom rác văn phòng</div>
                    <div class="task-item__desc">Khu vực văn phòng điều hành tầng 5</div>
                    <div class="task-item__meta">
                        <span class="task-meta-tag"><i class="fa-solid fa-location-dot"></i> Tầng 5</span>
                        <span class="task-meta-tag"><i class="fa-solid fa-clock"></i> 14:00 - 15:00</span>
                        <span class="task-meta-tag"><i class="fa-solid fa-check-double"></i> 0/3 bước</span>
                    </div>
                </div>
                <div class="task-item__right">
                    <span class="priority-badge priority-badge--medium">Trung bình</span>
                    <span class="status-badge status-badge--pending"><i class="fa-solid fa-clock"></i> Chờ xử lý</span>
                </div>
            </div>

            <div class="task-item" data-status="done" onclick="window.location='{{ route('cleaning.tasks.show', 5) }}'">
                <div class="task-item__indicator task-item__indicator--high"></div>
                <div class="task-item__body">
                    <div class="task-item__title">Vệ sinh thang máy A1, A2</div>
                    <div class="task-item__desc">Lau sàn, gương và nút bấm thang máy block A</div>
                    <div class="task-item__meta">
                        <span class="task-meta-tag"><i class="fa-solid fa-location-dot"></i> Block A</span>
                        <span class="task-meta-tag"><i class="fa-solid fa-clock"></i> 08:00 - 09:00</span>
                        <span class="task-meta-tag"><i class="fa-solid fa-check-double"></i> 4/4 bước</span>
                    </div>
                </div>
                <div class="task-item__right">
                    <span class="priority-badge priority-badge--high">Cao</span>
                    <span class="status-badge status-badge--done"><i class="fa-solid fa-check"></i> Hoàn thành</span>
                </div>
            </div>

            <div class="task-item" data-status="done" onclick="window.location='{{ route('cleaning.tasks.show', 6) }}'">
                <div class="task-item__indicator task-item__indicator--low"></div>
                <div class="task-item__body">
                    <div class="task-item__title">Tưới cây cảnh hành lang</div>
                    <div class="task-item__desc">Tưới nước và kiểm tra cây xanh khu vực hành lang tầng 1-3</div>
                    <div class="task-item__meta">
                        <span class="task-meta-tag"><i class="fa-solid fa-location-dot"></i> Tầng 1-3</span>
                        <span class="task-meta-tag"><i class="fa-solid fa-clock"></i> 06:30 - 07:30</span>
                        <span class="task-meta-tag"><i class="fa-solid fa-check-double"></i> 2/2 bước</span>
                    </div>
                </div>
                <div class="task-item__right">
                    <span class="priority-badge priority-badge--low">Thấp</span>
                    <span class="status-badge status-badge--done"><i class="fa-solid fa-check"></i> Hoàn thành</span>
                </div>
            </div>

            <div class="task-item" data-status="done" onclick="window.location='{{ route('cleaning.tasks.show', 7) }}'">
                <div class="task-item__indicator task-item__indicator--medium"></div>
                <div class="task-item__body">
                    <div class="task-item__title">Dọn dẹp khu vực hồ bơi</div>
                    <div class="task-item__desc">Thu dọn ghế, lau sàn ướt và kiểm tra thùng rác</div>
                    <div class="task-item__meta">
                        <span class="task-meta-tag"><i class="fa-solid fa-location-dot"></i> Tầng thượng</span>
                        <span class="task-meta-tag"><i class="fa-solid fa-clock"></i> 14:00 - 15:30</span>
                        <span class="task-meta-tag"><i class="fa-solid fa-check-double"></i> 3/3 bước</span>
                    </div>
                </div>
                <div class="task-item__right">
                    <span class="priority-badge priority-badge--medium">Trung bình</span>
                    <span class="status-badge status-badge--done"><i class="fa-solid fa-check"></i> Hoàn thành</span>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
// Tab filtering
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', function() {
        // Switch active tab
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');

        const filter = this.dataset.filter;
        const items = document.querySelectorAll('.task-item');

        items.forEach(item => {
            if (filter === 'all' || item.dataset.status === filter) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>

</body>
</html>
