<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DomusHub – Trang chủ Vệ sinh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',system-ui,sans-serif;background:#F4F7FE;color:#1B2559;min-height:100vh;display:flex;}

        /* ===== SIDEBAR ===== */
        .sidebar{
            width:240px;
            background:white;
            border-right:1px solid #E9EDF7;
            display:flex;
            flex-direction:column;
            position:fixed;
            top:0;left:0;bottom:0;
            z-index:50;
            padding:28px 0;
        }
        .sidebar-brand{padding:0 24px;margin-bottom:32px;}
        .sidebar-brand h2{font-size:18px;font-weight:800;color:#082B7A;letter-spacing:-.3px;}
        .sidebar-brand p{font-size:11px;color:#A3AED0;font-weight:500;margin-top:2px;}
        .sidebar-nav{flex:1;display:flex;flex-direction:column;gap:2px;padding:0 12px;}
        .sidebar-nav a{
            display:flex;align-items:center;gap:12px;
            padding:11px 16px;border-radius:10px;
            font-size:13.5px;font-weight:500;color:#707EAE;
            text-decoration:none;transition:.2s;
        }
        .sidebar-nav a:hover{background:#F4F7FE;color:#3652D9;}
        .sidebar-nav a.active{background:#F4F7FE;color:#3652D9;font-weight:700;}
        .sidebar-nav a.active i{color:#3652D9;}
        .sidebar-nav a i{width:18px;text-align:center;font-size:15px;color:#A3AED0;}
        .sidebar-footer{
            padding:16px 24px;border-top:1px solid #E9EDF7;margin-top:auto;
        }
        .sidebar-footer a{
            display:flex;align-items:center;gap:10px;
            font-size:13px;color:#707EAE;text-decoration:none;font-weight:500;transition:.2s;
        }
        .sidebar-footer a:hover{color:#dc2626;}

        /* ===== MAIN ===== */
        .main{margin-left:240px;flex:1;min-height:100vh;display:flex;flex-direction:column;}

        /* ===== TOP BAR ===== */
        .topbar{
            background:white;
            padding:16px 32px;
            display:flex;align-items:center;justify-content:space-between;
            border-bottom:1px solid #E9EDF7;
            position:sticky;top:0;z-index:40;
        }
        .topbar-search{
            display:flex;align-items:center;gap:10px;
            background:#F4F7FE;border-radius:10px;padding:10px 16px;width:360px;
        }
        .topbar-search i{color:#A3AED0;font-size:14px;}
        .topbar-search input{border:none;background:none;outline:none;font-size:13px;color:#1B2559;width:100%;font-family:inherit;}
        .topbar-search input::placeholder{color:#A3AED0;}
        .topbar-right{display:flex;align-items:center;gap:20px;}
        .topbar-icons{display:flex;align-items:center;gap:14px;}
        .topbar-icons button{
            background:none;border:none;cursor:pointer;
            width:36px;height:36px;border-radius:10px;
            display:flex;align-items:center;justify-content:center;
            color:#A3AED0;font-size:16px;transition:.2s;
        }
        .topbar-icons button:hover{background:#F4F7FE;color:#3652D9;}
        .topbar-user{display:flex;align-items:center;gap:10px;}
        .topbar-user-info{text-align:right;}
        .topbar-user-info .name{font-size:13px;font-weight:700;color:#1B2559;}
        .topbar-user-info .role{font-size:11px;color:#A3AED0;font-weight:500;}
        .topbar-avatar{
            width:38px;height:38px;border-radius:50%;
            background:linear-gradient(135deg,#3652D9,#6B8AFF);
            display:flex;align-items:center;justify-content:center;
            color:white;font-size:14px;font-weight:700;
        }

        /* ===== CONTENT ===== */
        .content{padding:28px 32px;flex:1;}

        /* ===== GREETING ===== */
        .greeting{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;}
        .greeting h1{font-size:24px;font-weight:800;color:#1B2559;letter-spacing:-.3px;}
        .greeting p{font-size:14px;color:#707EAE;margin-top:4px;}
        .btn-report{
            display:inline-flex;align-items:center;gap:8px;
            background:#3652D9;color:white;
            padding:12px 20px;border-radius:10px;border:none;
            font-size:13px;font-weight:600;cursor:pointer;
            transition:.2s;text-decoration:none;
        }
        .btn-report:hover{background:#2a43b8;transform:translateY(-1px);box-shadow:0 6px 16px rgba(54,82,217,.3);}

        /* ===== KPI CARDS ===== */
        .kpi-row{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:28px;}
        .kpi-card{
            background:white;border-radius:14px;padding:22px 20px;
            box-shadow:0 2px 12px rgba(54,82,217,.04);
            position:relative;overflow:hidden;
        }
        .kpi-card::after{
            content:"";position:absolute;top:0;left:0;right:0;height:3px;
            background:linear-gradient(90deg,#3652D9,#6B8AFF);opacity:0;transition:.2s;
        }
        .kpi-card:hover::after{opacity:1;}
        .kpi-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;}
        .kpi-label{font-size:11px;font-weight:700;color:#A3AED0;text-transform:uppercase;letter-spacing:.5px;}
        .kpi-icon{
            width:36px;height:36px;border-radius:10px;
            display:flex;align-items:center;justify-content:center;
            font-size:15px;
        }
        .kpi-icon--blue{background:#EEF2FF;color:#3652D9;}
        .kpi-icon--orange{background:#FFF4E5;color:#FF9B05;}
        .kpi-icon--green{background:#E6F9F0;color:#05CD99;}
        .kpi-icon--red{background:#FFF0F0;color:#EE5D50;}
        .kpi-value{font-size:36px;font-weight:800;color:#1B2559;line-height:1;margin-bottom:6px;}
        .kpi-sub{font-size:11.5px;color:#A3AED0;display:flex;align-items:center;gap:4px;}
        .kpi-sub i{font-size:10px;}

        /* ===== TABLE CARD ===== */
        .table-card{
            background:white;border-radius:14px;
            box-shadow:0 2px 12px rgba(54,82,217,.04);
            overflow:hidden;
        }
        .table-card__header{
            display:flex;align-items:center;justify-content:space-between;
            padding:22px 24px 16px;
        }
        .table-card__title{font-size:18px;font-weight:700;color:#1B2559;}
        .table-card__actions{display:flex;gap:8px;}
        .table-card__actions button{
            display:inline-flex;align-items:center;gap:6px;
            background:white;border:1px solid #E9EDF7;border-radius:8px;
            padding:8px 14px;font-size:12px;font-weight:600;color:#707EAE;
            cursor:pointer;transition:.2s;font-family:inherit;
        }
        .table-card__actions button:hover{border-color:#3652D9;color:#3652D9;}
        .task-table{width:100%;border-collapse:collapse;}
        .task-table th{
            text-align:left;padding:12px 24px;
            font-size:11px;font-weight:700;color:#A3AED0;
            text-transform:uppercase;letter-spacing:.5px;
            border-bottom:1px solid #E9EDF7;background:#FAFCFE;
        }
        .task-table td{
            padding:16px 24px;border-bottom:1px solid #F4F7FE;
            font-size:13px;color:#1B2559;vertical-align:middle;
        }
        .task-table tr:last-child td{border-bottom:none;}
        .task-table tr:hover td{background:#FAFCFE;}
        .task-name{font-weight:700;color:#1B2559;margin-bottom:2px;}
        .task-desc{font-size:12px;color:#A3AED0;font-weight:400;}
        .task-indicator{
            width:4px;height:40px;border-radius:4px;
            display:inline-block;margin-right:12px;vertical-align:middle;
        }
        .task-indicator--high{background:#EE5D50;}
        .task-indicator--medium{background:#FFB547;}
        .task-indicator--low{background:#05CD99;}
        .task-area{display:flex;align-items:center;gap:6px;font-size:12.5px;color:#707EAE;}
        .task-area i{font-size:11px;color:#A3AED0;}
        .priority-badge{
            display:inline-flex;align-items:center;gap:4px;
            padding:4px 10px;border-radius:6px;font-size:11px;font-weight:700;
        }
        .priority-badge--high{background:#FFF0F0;color:#EE5D50;}
        .priority-badge--medium{background:#FFF4E5;color:#FF9B05;}
        .priority-badge--low{background:#E6F9F0;color:#05CD99;}
        .priority-badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor;}
        .status-badge{
            display:inline-flex;align-items:center;gap:6px;
            padding:6px 12px;border-radius:8px;font-size:11.5px;font-weight:600;
        }
        .status-badge--progress{background:#EEF2FF;color:#3652D9;}
        .status-badge--pending{background:#F4F7FE;color:#A3AED0;}
        .status-badge--done{background:#E6F9F0;color:#05CD99;}
        .status-badge i{font-size:11px;}
        .check-done{
            width:22px;height:22px;border-radius:50%;
            background:#05CD99;display:inline-flex;
            align-items:center;justify-content:center;
            color:white;font-size:10px;margin-left:8px;
        }

        /* ===== PAGINATION ===== */
        .table-footer{
            display:flex;align-items:center;justify-content:space-between;
            padding:14px 24px;border-top:1px solid #E9EDF7;
        }
        .table-footer__info{font-size:12px;color:#A3AED0;}
        .table-footer__nav{display:flex;gap:6px;}
        .table-footer__nav button{
            width:32px;height:32px;border-radius:8px;border:1px solid #E9EDF7;
            background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;
            font-size:13px;color:#707EAE;transition:.15s;
        }
        .table-footer__nav button:hover{border-color:#3652D9;color:#3652D9;}

        /* ===== RESPONSIVE ===== */
        @media(max-width:1024px){
            .kpi-row{grid-template-columns:repeat(2,1fr);}
        }
        @media(max-width:768px){
            .sidebar{display:none;}
            .main{margin-left:0;}
            .kpi-row{grid-template-columns:1fr;}
            .topbar-search{width:200px;}
            .greeting{flex-direction:column;gap:12px;}
            .content{padding:20px 16px;}
            .task-table th:nth-child(4),.task-table td:nth-child(4),
            .task-table th:nth-child(5),.task-table td:nth-child(5){display:none;}
        }
    </style>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <h2>Chung cư Số</h2>
        <p>Nhân viên vệ sinh</p>
    </div>
    <nav class="sidebar-nav">
        <a href="#" class="active"><i class="fa-solid fa-grid-2"></i> Bảng điều khiển</a>
        <a href="{{ route('cleaning.tasks') }}"><i class="fa-solid fa-clipboard-list"></i> Công việc hàng ngày</a>
        <a href="{{ route('cleaning.report') }}"><i class="fa-solid fa-triangle-exclamation"></i> Báo cáo sự cố</a>
    </nav>
    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:10px;font-size:13px;color:#707EAE;font-weight:500;font-family:inherit;">
                <i class="fa-solid fa-circle-question" style="font-size:15px;"></i> Hỗ trợ
            </button>
        </form>
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
            <div class="topbar-user">
                <div class="topbar-user-info">
                    <div class="name">{{ auth()->user()->name }}</div>
                    <div class="role">NHÂN VIÊN VỆ SINH</div>
                </div>
                <div class="topbar-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <div class="content">

        <!-- GREETING -->
        <div class="greeting">
            <div>
                <h1>Chào buổi sáng, {{ explode(' ', auth()->user()->name)[0] }}</h1>
                <p>Hôm nay bạn có <strong>12</strong> công việc cần xử lý. Hãy bắt đầu thôi!</p>
            </div>
            <a href="{{ route('cleaning.report') }}" class="btn-report">
                <i class="fa-solid fa-plus"></i> Báo cáo sự cố mới
            </a>
        </div>

        <!-- KPI ROW -->
        <div class="kpi-row">
            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-label">Tổng công việc</span>
                    <div class="kpi-icon kpi-icon--blue"><i class="fa-solid fa-clipboard-list"></i></div>
                </div>
                <div class="kpi-value">12</div>
                <div class="kpi-sub"><i class="fa-solid fa-calendar"></i> Hôm nay, {{ now()->format('d') }} Tháng {{ now()->format('m') }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-label">Đang thực hiện</span>
                    <div class="kpi-icon kpi-icon--orange"><i class="fa-solid fa-spinner"></i></div>
                </div>
                <div class="kpi-value">02</div>
                <div class="kpi-sub"><i class="fa-solid fa-circle" style="color:#FF9B05;font-size:7px;"></i> Đang xử lý</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-label">Đã hoàn thành</span>
                    <div class="kpi-icon kpi-icon--green"><i class="fa-solid fa-circle-check"></i></div>
                </div>
                <div class="kpi-value">08</div>
                <div class="kpi-sub"><i class="fa-solid fa-arrow-up" style="color:#05CD99;"></i> +2 so với hôm qua</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-header">
                    <span class="kpi-label">Chờ xử lý</span>
                    <div class="kpi-icon kpi-icon--red"><i class="fa-solid fa-clock"></i></div>
                </div>
                <div class="kpi-value">02</div>
                <div class="kpi-sub"><i class="fa-solid fa-exclamation-circle" style="color:#EE5D50;font-size:10px;"></i> Cần ưu tiên</div>
            </div>
        </div>

        <!-- TASK TABLE -->
        <div class="table-card">
            <div class="table-card__header">
                <h2 class="table-card__title">Công việc được giao</h2>
                <div class="table-card__actions">
                    <button><i class="fa-solid fa-filter"></i> Lọc</button>
                    <button><i class="fa-solid fa-arrow-down-wide-short"></i> Sắp xếp</button>
                </div>
            </div>
            <table class="task-table">
                <thead>
                    <tr>
                        <th>Mô tả công việc</th>
                        <th>Khu vực</th>
                        <th>Ưu tiên</th>
                        <th>Bắt đầu</th>
                        <th>Thời hạn</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;">
                                <span class="task-indicator task-indicator--high"></span>
                                <div>
                                    <div class="task-name">Vệ sinh sảnh chính</div>
                                    <div class="task-desc">Làm sạch sàn và cửa kính mặt tiền</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="task-area"><i class="fa-solid fa-location-dot"></i> Tầng trệt</span></td>
                        <td><span class="priority-badge priority-badge--high">Cao</span></td>
                        <td>08:00</td>
                        <td>10:00 (Hôm nay)</td>
                        <td><span class="status-badge status-badge--progress"><i class="fa-solid fa-rotate"></i> Đang thực hiện</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;">
                                <span class="task-indicator task-indicator--medium"></span>
                                <div>
                                    <div class="task-name">Lau kính cánh đông</div>
                                    <div class="task-desc">Vệ sinh bề mặt kính bên ngoài</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="task-area"><i class="fa-solid fa-location-dot"></i> Tầng 4</span></td>
                        <td><span class="priority-badge priority-badge--medium">Trung bình</span></td>
                        <td>10:30</td>
                        <td>12:30 (Hôm nay)</td>
                        <td><span class="status-badge status-badge--pending"><i class="fa-solid fa-clock"></i> Chờ xử lý</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;">
                                <span class="task-indicator task-indicator--low"></span>
                                <div>
                                    <div class="task-name">Kiểm tra hệ thống đèn</div>
                                    <div class="task-desc">Hành lang khu vực C</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="task-area"><i class="fa-solid fa-location-dot"></i> Tầng 2</span></td>
                        <td><span class="priority-badge priority-badge--low">Thấp</span></td>
                        <td>13:00</td>
                        <td>15:00 (Hôm nay)</td>
                        <td><span class="status-badge status-badge--pending"><i class="fa-solid fa-clock"></i> Chờ xử lý</span></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;">
                                <span class="task-indicator task-indicator--medium"></span>
                                <div>
                                    <div class="task-name">Thu gom rác văn phòng</div>
                                    <div class="task-desc">Khu vực văn phòng điều hành</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="task-area"><i class="fa-solid fa-location-dot"></i> Tầng 5</span></td>
                        <td><span class="priority-badge priority-badge--medium">Trung bình</span></td>
                        <td>07:30</td>
                        <td>08:30</td>
                        <td>
                            <span class="status-badge status-badge--done"><i class="fa-solid fa-check"></i> Đã hoàn thành</span>
                            <span class="check-done"><i class="fa-solid fa-check"></i></span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="table-footer">
                <span class="table-footer__info">Hiển thị 4 trên tổng số 12 công việc</span>
                <div class="table-footer__nav">
                    <button><i class="fa-solid fa-chevron-left"></i></button>
                    <button><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
