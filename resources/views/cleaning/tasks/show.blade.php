<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DomusHub – Chi tiết công việc</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',system-ui,sans-serif;background:#F4F7FE;color:#1B2559;min-height:100vh;display:flex;}

        .sidebar{width:240px;background:white;border-right:1px solid #E9EDF7;display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:50;padding:28px 0;}
        .sidebar-brand{padding:0 24px;margin-bottom:32px;display:flex;align-items:center;gap:12px;}
        .sidebar-brand-icon{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#3652D9,#6B8AFF);display:flex;align-items:center;justify-content:center;color:white;font-size:15px;}
        .sidebar-brand-text h2{font-size:16px;font-weight:800;color:#082B7A;letter-spacing:-.3px;line-height:1.2;}
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

        .main{margin-left:240px;flex:1;min-height:100vh;display:flex;flex-direction:column;}

        .topbar{background:white;padding:16px 32px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #E9EDF7;position:sticky;top:0;z-index:40;}
        .topbar-left{display:flex;align-items:center;gap:16px;}
        .topbar-back{width:36px;height:36px;border-radius:10px;border:1px solid #E9EDF7;display:flex;align-items:center;justify-content:center;color:#707EAE;text-decoration:none;transition:.2s;font-size:14px;}
        .topbar-back:hover{border-color:#3652D9;color:#3652D9;background:#F4F7FE;}
        .topbar-search{display:flex;align-items:center;gap:10px;background:#F4F7FE;border-radius:10px;padding:10px 16px;width:300px;}
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

        .content{padding:28px 32px;flex:1;display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start;}

        /* Left column */
        .task-detail{display:flex;flex-direction:column;gap:20px;}
        .task-breadcrumb{font-size:12px;color:#A3AED0;}
        .task-breadcrumb a{color:#3652D9;font-weight:600;text-decoration:none;}
        .task-breadcrumb a:hover{text-decoration:underline;}
        .task-title-row{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;}
        .task-title{font-size:24px;font-weight:800;color:#1B2559;letter-spacing:-.3px;}
        .task-actions{display:flex;gap:10px;flex-shrink:0;}
        .btn-start{display:inline-flex;align-items:center;gap:8px;background:#3652D9;color:white;border:none;padding:11px 20px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;transition:.2s;font-family:inherit;}
        .btn-start:hover{background:#2a43b8;transform:translateY(-1px);box-shadow:0 6px 16px rgba(54,82,217,.3);}
        .btn-complete{display:inline-flex;align-items:center;gap:8px;background:white;color:#05CD99;border:1.5px solid #05CD99;padding:11px 20px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;transition:.2s;font-family:inherit;}
        .btn-complete:hover{background:#E6F9F0;transform:translateY(-1px);}
        .task-meta{display:flex;align-items:center;gap:16px;}
        .status-pill{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;font-size:11.5px;font-weight:700;background:#EEF2FF;color:#3652D9;}
        .status-pill i{font-size:10px;}
        .meta-text{font-size:12px;color:#A3AED0;display:flex;align-items:center;gap:5px;}
        .meta-text i{font-size:11px;}

        .detail-card{background:white;border-radius:14px;padding:28px;box-shadow:0 2px 12px rgba(54,82,217,.04);}
        .detail-card__title{font-size:16px;font-weight:700;color:#1B2559;margin-bottom:14px;}
        .detail-card__text{font-size:13.5px;color:#4A5568;line-height:1.8;}
        .detail-card__info{display:flex;gap:32px;margin-top:20px;padding-top:18px;border-top:1px solid #E9EDF7;}
        .info-item{display:flex;align-items:center;gap:10px;}
        .info-item__icon{width:36px;height:36px;border-radius:10px;background:#F4F7FE;display:flex;align-items:center;justify-content:center;color:#3652D9;font-size:14px;}
        .info-item__label{font-size:11px;color:#A3AED0;font-weight:600;text-transform:uppercase;letter-spacing:.3px;}
        .info-item__value{font-size:13px;color:#1B2559;font-weight:700;}

        .checklist-card{background:white;border-radius:14px;padding:28px;box-shadow:0 2px 12px rgba(54,82,217,.04);}
        .checklist-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;}
        .checklist-header h3{font-size:16px;font-weight:700;color:#1B2559;}
        .checklist-progress{display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:20px;font-size:11.5px;font-weight:700;background:#E6F9F0;color:#05CD99;}
        .checklist{list-style:none;padding:0;margin:0;}
        .checklist li{display:flex;align-items:center;gap:14px;padding:14px 16px;border-bottom:1px solid #F4F7FE;font-size:14px;color:#1B2559;transition:.15s;border-radius:8px;}
        .checklist li:last-child{border-bottom:none;}
        .checklist li:hover{background:#FAFCFE;}
        .checklist li.done{color:#A3AED0;text-decoration:line-through;}
        .check-box{width:22px;height:22px;border-radius:6px;border:2px solid #D8E0F0;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:.2s;flex-shrink:0;}
        .check-box:hover{border-color:#3652D9;}
        .check-box.checked{background:#3652D9;border-color:#3652D9;}
        .check-box.checked i{color:white;font-size:10px;}

        /* Right column */
        .right-col{display:flex;flex-direction:column;gap:20px;}
        .assignee-card{background:white;border-radius:14px;padding:22px;box-shadow:0 2px 12px rgba(54,82,217,.04);}
        .assignee-card__header{font-size:11px;font-weight:700;color:#A3AED0;text-transform:uppercase;letter-spacing:.5px;margin-bottom:16px;}
        .assignee-profile{display:flex;align-items:center;gap:12px;margin-bottom:16px;}
        .assignee-avatar{width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#3652D9,#6B8AFF);display:flex;align-items:center;justify-content:center;color:white;font-size:16px;font-weight:700;}
        .assignee-name{font-size:14px;font-weight:700;color:#1B2559;}
        .assignee-role{font-size:12px;color:#A3AED0;}
        .assignee-chat{width:32px;height:32px;border-radius:8px;border:1px solid #E9EDF7;display:flex;align-items:center;justify-content:center;color:#A3AED0;font-size:13px;cursor:pointer;transition:.2s;margin-left:auto;}
        .assignee-chat:hover{border-color:#3652D9;color:#3652D9;}
        .assignee-meta{display:flex;flex-direction:column;gap:10px;padding-top:14px;border-top:1px solid #E9EDF7;}
        .assignee-meta-row{display:flex;justify-content:space-between;align-items:center;}
        .assignee-meta-label{font-size:12px;color:#A3AED0;}
        .assignee-meta-value{font-size:12px;font-weight:700;color:#1B2559;}
        .priority-dot{display:inline-flex;align-items:center;gap:5px;}
        .priority-dot::before{content:"";width:8px;height:8px;border-radius:50%;}
        .priority-dot--high::before{background:#EE5D50;}
        .priority-dot--medium::before{background:#FFB547;}
        .priority-dot--low::before{background:#05CD99;}

        .image-card{background:white;border-radius:14px;overflow:hidden;box-shadow:0 2px 12px rgba(54,82,217,.04);}
        .image-placeholder{width:100%;height:150px;background:linear-gradient(135deg,#1B2559,#3652D9);display:flex;align-items:center;justify-content:center;position:relative;}
        .image-placeholder i{font-size:32px;color:rgba(255,255,255,.3);}
        .image-placeholder__label{position:absolute;bottom:12px;left:16px;font-size:11.5px;font-weight:600;color:rgba(255,255,255,.8);display:flex;align-items:center;gap:6px;}
        .image-card__footer{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;}
        .image-card__footer span{font-size:12px;color:#707EAE;}
        .image-card__footer a{font-size:12px;color:#3652D9;font-weight:600;text-decoration:none;}
        .image-card__footer a:hover{text-decoration:underline;}

        .notes-card{background:linear-gradient(135deg,#F4F7FE,#EEF2FF);border-radius:14px;padding:22px;border:1px solid #D8E0F0;}
        .notes-card__header{font-size:11px;font-weight:700;color:#A3AED0;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;}
        .notes-card textarea{width:100%;min-height:80px;border:1.5px solid #D8E0F0;border-radius:10px;padding:12px 14px;font-size:13px;color:#1B2559;font-family:inherit;background:white;resize:vertical;transition:.2s;}
        .notes-card textarea:focus{outline:none;border-color:#3652D9;box-shadow:0 0 0 3px rgba(54,82,217,.08);}
        .notes-card textarea::placeholder{color:#A3AED0;}
        .btn-note{display:inline-flex;align-items:center;gap:8px;background:white;border:1.5px solid #E9EDF7;color:#1B2559;padding:10px 18px;border-radius:10px;font-size:12.5px;font-weight:700;cursor:pointer;transition:.2s;margin-top:12px;font-family:inherit;width:100%;justify-content:center;}
        .btn-note:hover{border-color:#3652D9;color:#3652D9;background:#F4F7FE;}

        @media(max-width:1100px){.content{grid-template-columns:1fr;}}
        @media(max-width:768px){.sidebar{display:none;}.main{margin-left:0;}.content{padding:20px 16px;}.task-title-row{flex-direction:column;}.task-actions{width:100%;}.detail-card__info{flex-direction:column;gap:14px;}}
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon"><i class="fa-solid fa-building"></i></div>
        <div class="sidebar-brand-text"><h2>Chung cư Số</h2><p>Nhân viên vệ sinh</p></div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('cleaning.dashboard') }}"><i class="fa-solid fa-grid-2"></i> Bảng điều khiển</a>
        <a href="{{ route('cleaning.tasks') }}" class="active"><i class="fa-solid fa-clipboard-list"></i> Công việc hàng ngày</a>
        <a href="{{ route('cleaning.report') }}"><i class="fa-solid fa-triangle-exclamation"></i> Báo cáo sự cố</a>
    </nav>
    <div class="sidebar-footer"><a href="#"><i class="fa-solid fa-circle-question"></i> Hỗ trợ</a></div>
</aside>

<div class="main">
    <header class="topbar">
        <div class="topbar-left">
            <a href="{{ route('cleaning.tasks') }}" class="topbar-back"><i class="fa-solid fa-arrow-left"></i></a>
            <div class="topbar-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Tìm kiếm công việc...">
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-user">
                <div class="topbar-user-info">
                    <div class="name">{{ auth()->user()->name }}</div>
                    <div class="role">NHÂN VIÊN VỆ SINH</div>
                </div>
                <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            </div>
        </div>
    </header>

    <div class="content">
        <div class="task-detail">
            <div>
                <div class="task-breadcrumb"><a href="{{ route('cleaning.tasks') }}">Công việc hàng ngày</a> &gt; <span style="color:#3652D9;font-weight:600;">#TSK-{{ $task['id'] ?? '4492' }}</span></div>
                <div class="task-title-row">
                    <h1 class="task-title">{{ $task['title'] ?? 'Vệ sinh sâu sảnh chính' }}</h1>
                    <div class="task-actions">
                        <button class="btn-start"><i class="fa-solid fa-play"></i> Bắt đầu công việc</button>
                        <button class="btn-complete"><i class="fa-solid fa-circle-check"></i> Đánh dấu hoàn thành</button>
                    </div>
                </div>
                <div class="task-meta">
                    <span class="status-pill"><i class="fa-solid fa-rotate"></i> {{ $task['status_label'] ?? 'Đang thực hiện' }}</span>
                    <span class="meta-text"><i class="fa-solid fa-clock"></i> Cập nhật lần cuối: 10 phút trước</span>
                </div>
            </div>

            <div class="detail-card">
                <h2 class="detail-card__title">Mô tả công việc</h2>
                <p class="detail-card__text">{{ $task['description'] ?? 'Thực hiện quy trình vệ sinh chuyên sâu tại khu vực sảnh chính. Tập trung vào việc khử trùng toàn bộ các bề mặt tiếp xúc cao như tay nắm cửa, quầy lễ tân và nút bấm thang máy. Thực hiện đánh bóng sàn đá marble bằng thiết bị chuyên dụng và lau sạch hệ thống cửa kính mặt tiền để đảm bảo vẻ ngoài sang trọng của tòa nhà.' }}</p>
                <div class="detail-card__info">
                    <div class="info-item">
                        <div class="info-item__icon"><i class="fa-solid fa-location-dot"></i></div>
                        <div>
                            <div class="info-item__label">Khu vực</div>
                            <div class="info-item__value">{{ $task['area'] ?? 'Tầng trệt - Cửa chính' }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-item__icon"><i class="fa-solid fa-clock"></i></div>
                        <div>
                            <div class="info-item__label">Thời gian</div>
                            <div class="info-item__value">{{ $task['time'] ?? '09:00 AM - 11:00 AM' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="checklist-card">
                <div class="checklist-header">
                    <h3>Danh sách kiểm tra</h3>
                    <span class="checklist-progress" id="progress-label">{{ $task['done_steps'] ?? 2 }}/{{ $task['total_steps'] ?? 5 }} Hoàn thành</span>
                </div>
                <ul class="checklist">
                    @foreach(($task['checklist'] ?? ['Quét sàn' => true, 'Lau sàn' => true, 'Đổ rác' => false, 'Lau cửa kính' => false, 'Khử trùng tay vịn' => false]) as $item => $done)
                    <li class="{{ $done ? 'done' : '' }}">
                        <div class="check-box {{ $done ? 'checked' : '' }}">{!! $done ? '<i class="fa-solid fa-check"></i>' : '' !!}</div>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="right-col">
            <div class="assignee-card">
                <div class="assignee-card__header">Người giao việc</div>
                <div class="assignee-profile">
                    <div class="assignee-avatar">M</div>
                    <div>
                        <div class="assignee-name">Marcus Chen</div>
                        <div class="assignee-role">Quản lý tòa nhà</div>
                    </div>
                    <div class="assignee-chat"><i class="fa-solid fa-comment"></i></div>
                </div>
                <div class="assignee-meta">
                    <div class="assignee-meta-row">
                        <span class="assignee-meta-label">Ngày tạo</span>
                        <span class="assignee-meta-value">{{ now()->format('d/m/Y') }}</span>
                    </div>
                    <div class="assignee-meta-row">
                        <span class="assignee-meta-label">Độ ưu tiên</span>
                        <span class="assignee-meta-value"><span class="priority-dot priority-dot--{{ $task['priority'] ?? 'high' }}"></span> {{ $task['priority_label'] ?? 'Cao' }}</span>
                    </div>
                </div>
            </div>

            <div class="image-card">
                <div class="image-placeholder">
                    <i class="fa-solid fa-image"></i>
                    <div class="image-placeholder__label"><i class="fa-solid fa-camera"></i> Hình ảnh khu vực</div>
                </div>
                <div class="image-card__footer">
                    <span>{{ $task['area'] ?? 'Sảnh chính tầng trệt' }}</span>
                    <a href="#">Xem bản đồ</a>
                </div>
            </div>

            <div class="notes-card">
                <div class="notes-card__header">Ghi chú nhanh</div>
                <textarea placeholder="Nhập ghi chú hoặc vấn đề phát sinh..."></textarea>
                <button class="btn-note"><i class="fa-solid fa-floppy-disk"></i> Lưu ghi chú</button>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.check-box').forEach(box => {
    box.addEventListener('click', function() {
        this.classList.toggle('checked');
        const li = this.closest('li');
        li.classList.toggle('done');
        this.innerHTML = this.classList.contains('checked') ? '<i class="fa-solid fa-check"></i>' : '';
        const total = document.querySelectorAll('.checklist li').length;
        const done = document.querySelectorAll('.checklist li.done').length;
        document.getElementById('progress-label').textContent = done + '/' + total + ' Hoàn thành';
    });
});
</script>
</body>
</html>
