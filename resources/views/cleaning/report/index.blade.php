<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DomusHub – Báo cáo sự cố</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',system-ui,sans-serif;background:#F4F7FE;color:#1B2559;min-height:100vh;display:flex;}

        /* ===== SIDEBAR ===== */
        .sidebar{
            width:240px;background:white;border-right:1px solid #E9EDF7;
            display:flex;flex-direction:column;position:fixed;
            top:0;left:0;bottom:0;z-index:50;padding:28px 0;
        }
        .sidebar-brand{padding:0 24px;margin-bottom:32px;display:flex;align-items:center;gap:12px;}
        .sidebar-brand-icon{
            width:36px;height:36px;border-radius:10px;
            background:linear-gradient(135deg,#3652D9,#6B8AFF);
            display:flex;align-items:center;justify-content:center;
            color:white;font-size:15px;
        }
        .sidebar-brand-text h2{font-size:16px;font-weight:800;color:#082B7A;letter-spacing:-.3px;line-height:1.2;}
        .sidebar-brand-text p{font-size:10.5px;color:#A3AED0;font-weight:500;}
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
        .sidebar-footer{padding:16px 24px;border-top:1px solid #E9EDF7;margin-top:auto;}
        .sidebar-footer a{
            display:flex;align-items:center;gap:10px;
            font-size:13px;color:#707EAE;text-decoration:none;font-weight:500;
        }
        .sidebar-footer a:hover{color:#3652D9;}

        /* ===== MAIN ===== */
        .main{margin-left:240px;flex:1;min-height:100vh;display:flex;flex-direction:column;}

        /* ===== TOP BAR ===== */
        .topbar{
            background:white;padding:16px 32px;
            display:flex;align-items:center;justify-content:space-between;
            border-bottom:1px solid #E9EDF7;position:sticky;top:0;z-index:40;
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
        .content{padding:28px 32px;flex:1;display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start;}

        /* ===== FORM CARD ===== */
        .form-card{
            background:white;border-radius:14px;padding:32px;
            box-shadow:0 2px 12px rgba(54,82,217,.04);
        }
        .form-card__title{font-size:20px;font-weight:700;color:#1B2559;margin-bottom:6px;}
        .form-card__sub{font-size:13px;color:#A3AED0;margin-bottom:28px;}

        .form-group{margin-bottom:22px;}
        .form-label{
            display:block;font-size:13px;font-weight:600;color:#1B2559;
            margin-bottom:8px;
        }
        .form-label .required{color:#EE5D50;}
        .form-input{
            width:100%;height:48px;border:1.5px solid #E9EDF7;border-radius:10px;
            padding:0 16px;font-size:14px;color:#1B2559;font-family:inherit;
            background:#FAFCFE;transition:.2s;
        }
        .form-input:focus{outline:none;border-color:#3652D9;background:white;box-shadow:0 0 0 3px rgba(54,82,217,.08);}
        .form-input::placeholder{color:#A3AED0;}
        .form-textarea{
            width:100%;min-height:120px;border:1.5px solid #E9EDF7;border-radius:10px;
            padding:14px 16px;font-size:14px;color:#1B2559;font-family:inherit;
            background:#FAFCFE;resize:vertical;transition:.2s;
        }
        .form-textarea:focus{outline:none;border-color:#3652D9;background:white;box-shadow:0 0 0 3px rgba(54,82,217,.08);}
        .form-textarea::placeholder{color:#A3AED0;}
        .form-select{
            width:100%;height:48px;border:1.5px solid #E9EDF7;border-radius:10px;
            padding:0 16px;font-size:14px;color:#1B2559;font-family:inherit;
            background:#FAFCFE;appearance:none;cursor:pointer;
            background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23A3AED0' viewBox='0 0 16 16'%3E%3Cpath d='M4 6l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat:no-repeat;background-position:right 16px center;
            transition:.2s;
        }
        .form-select:focus{outline:none;border-color:#3652D9;background-color:white;box-shadow:0 0 0 3px rgba(54,82,217,.08);}

        /* Priority radio group */
        .form-row{display:flex;gap:20px;align-items:flex-end;}
        .form-row .form-group{flex:1;}
        .priority-group{display:flex;gap:8px;height:48px;align-items:center;}
        .priority-option{
            display:flex;align-items:center;gap:6px;
            padding:10px 16px;border-radius:10px;border:1.5px solid #E9EDF7;
            cursor:pointer;transition:.2s;font-size:13px;font-weight:600;color:#707EAE;
            background:white;
        }
        .priority-option:hover{border-color:#3652D9;}
        .priority-option input{display:none;}
        .priority-option input:checked + .priority-dot + .priority-text{color:#1B2559;}
        .priority-option:has(input:checked){border-color:#3652D9;background:#F4F7FE;}
        .priority-dot{width:8px;height:8px;border-radius:50%;}
        .priority-dot--low{background:#05CD99;}
        .priority-dot--medium{background:#FFB547;}
        .priority-dot--high{background:#EE5D50;}

        /* Upload area */
        .upload-area{
            border:2px dashed #D8E0F0;border-radius:12px;
            padding:32px;text-align:center;cursor:pointer;
            transition:.2s;background:#FAFCFE;
        }
        .upload-area:hover{border-color:#3652D9;background:#F4F7FE;}
        .upload-area i{font-size:28px;color:#A3AED0;margin-bottom:12px;}
        .upload-area p{font-size:13px;color:#707EAE;font-weight:500;}
        .upload-area span{font-size:11.5px;color:#A3AED0;display:block;margin-top:4px;}
        .upload-preview{display:flex;gap:10px;margin-top:14px;flex-wrap:wrap;}
        .upload-thumb{
            width:64px;height:64px;border-radius:8px;overflow:hidden;
            border:2px solid #E9EDF7;position:relative;
        }
        .upload-thumb img{width:100%;height:100%;object-fit:cover;}
        .upload-thumb-add{
            width:64px;height:64px;border-radius:8px;
            border:2px dashed #D8E0F0;display:flex;align-items:center;justify-content:center;
            color:#A3AED0;font-size:18px;cursor:pointer;transition:.2s;
        }
        .upload-thumb-add:hover{border-color:#3652D9;color:#3652D9;}

        /* Submit button */
        .btn-submit{
            display:inline-flex;align-items:center;gap:10px;
            background:#3652D9;color:white;border:none;
            padding:14px 28px;border-radius:10px;
            font-size:14px;font-weight:700;cursor:pointer;
            transition:.2s;margin-top:8px;font-family:inherit;
        }
        .btn-submit:hover{background:#2a43b8;transform:translateY(-1px);box-shadow:0 6px 16px rgba(54,82,217,.3);}

        /* ===== RIGHT SIDEBAR ===== */
        .right-col{display:flex;flex-direction:column;gap:20px;}

        .info-card{
            background:white;border-radius:14px;padding:22px;
            box-shadow:0 2px 12px rgba(54,82,217,.04);
        }
        .info-card--emergency{
            background:linear-gradient(135deg,#FFF8F0,#FFF2E5);
            border:1px solid #FFE0B5;
        }
        .info-card--emergency .info-card__icon{
            width:40px;height:40px;border-radius:50%;
            background:#FF9B05;display:flex;align-items:center;justify-content:center;
            color:white;font-size:16px;margin-bottom:12px;
        }
        .info-card--emergency h3{font-size:14px;font-weight:700;color:#1B2559;margin-bottom:6px;}
        .info-card--emergency p{font-size:12.5px;color:#707EAE;line-height:1.6;margin-bottom:10px;}
        .info-card--emergency .hotline{
            display:flex;align-items:center;gap:8px;
            font-size:13px;font-weight:700;color:#3652D9;
        }
        .info-card--emergency .hotline i{font-size:14px;}

        .info-card__header{
            font-size:11px;font-weight:700;color:#A3AED0;
            text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;
        }
        .guide-list{list-style:none;padding:0;margin:0;}
        .guide-list li{
            display:flex;align-items:flex-start;gap:10px;
            margin-bottom:14px;font-size:12.5px;color:#4A5568;line-height:1.6;
        }
        .guide-list li:last-child{margin-bottom:0;}
        .guide-dot{
            flex-shrink:0;width:22px;height:22px;border-radius:50%;
            background:#F4F7FE;display:flex;align-items:center;justify-content:center;
            font-size:10px;color:#3652D9;margin-top:1px;
        }
        .guide-list strong{color:#1B2559;}

        .stat-card__label{font-size:11px;font-weight:700;color:#A3AED0;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;}
        .stat-card__row{display:flex;align-items:baseline;gap:12px;margin-bottom:8px;}
        .stat-card__value{font-size:28px;font-weight:800;color:#3652D9;}
        .stat-card__unit{font-size:13px;color:#707EAE;font-weight:500;}
        .stat-card__change{font-size:11.5px;color:#05CD99;font-weight:600;display:flex;align-items:center;gap:4px;}
        .stat-card__change i{font-size:9px;}
        .stat-card__bar{
            width:100%;height:6px;border-radius:3px;background:#E9EDF7;margin-top:10px;overflow:hidden;
        }
        .stat-card__bar-fill{height:100%;border-radius:3px;background:linear-gradient(90deg,#3652D9,#6B8AFF);}

        /* ===== RESPONSIVE ===== */
        @media(max-width:1100px){
            .content{grid-template-columns:1fr;}.right-col{order:-1;display:grid;grid-template-columns:repeat(3,1fr);gap:16px;}
        }
        @media(max-width:768px){
            .sidebar{display:none;}.main{margin-left:0;}.content{padding:20px 16px;}
            .topbar-search{width:180px;}.form-row{flex-direction:column;gap:0;}
            .right-col{grid-template-columns:1fr;}
        }
    </style>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon"><i class="fa-solid fa-building"></i></div>
        <div class="sidebar-brand-text">
            <h2>Chung cư Số</h2>
            <p>Nhân viên vệ sinh</p>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('cleaning.dashboard') }}"><i class="fa-solid fa-grid-2"></i> Bảng điều khiển</a>
        <a href="{{ route('cleaning.tasks') }}"><i class="fa-solid fa-clipboard-list"></i> Công việc hàng ngày</a>
        <a href="{{ route('cleaning.report') }}" class="active"><i class="fa-solid fa-triangle-exclamation"></i> Báo cáo sự cố</a>
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
            <input type="text" placeholder="Tìm kiếm tài sản, báo cáo...">
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

        <!-- FORM -->
        <div class="form-card">
            <h1 class="form-card__title">Chi tiết sự cố</h1>
            <p class="form-card__sub">Vui lòng cung cấp thông tin chính xác để bộ phận kỹ thuật xử lý kịp thời.</p>

            <form method="POST" action="#" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label class="form-label">Tiêu đề sự cố <span class="required">*</span></label>
                    <input type="text" class="form-input" name="title" placeholder="Ví dụ: Rò rỉ nước tại hành lang tầng 5" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Vị trí / Khu vực <span class="required">*</span></label>
                        <select class="form-select" name="location" required>
                            <option value="">Chọn khu vực</option>
                            <option value="tang-tret">Tầng trệt / Sảnh chính</option>
                            <option value="tang-1">Tầng 1</option>
                            <option value="tang-2">Tầng 2</option>
                            <option value="tang-3">Tầng 3</option>
                            <option value="tang-4">Tầng 4</option>
                            <option value="tang-5">Tầng 5</option>
                            <option value="tang-ham">Tầng hầm</option>
                            <option value="san-thuong">Sân thượng</option>
                            <option value="ho-boi">Hồ bơi</option>
                            <option value="phong-gym">Phòng Gym</option>
                            <option value="khac">Khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mức độ ưu tiên</label>
                        <div class="priority-group">
                            <label class="priority-option">
                                <input type="radio" name="priority" value="low" checked>
                                <span class="priority-dot priority-dot--low"></span>
                                <span class="priority-text">Thấp</span>
                            </label>
                            <label class="priority-option">
                                <input type="radio" name="priority" value="medium">
                                <span class="priority-dot priority-dot--medium"></span>
                                <span class="priority-text">Trung bình</span>
                            </label>
                            <label class="priority-option">
                                <input type="radio" name="priority" value="high">
                                <span class="priority-dot priority-dot--high"></span>
                                <span class="priority-text">Cao</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Mô tả chi tiết</label>
                    <textarea class="form-textarea" name="description" placeholder="Mô tả cụ thể tình trạng, thời gian phát hiện và các lưu ý khác..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Ảnh minh chứng</label>
                    <div class="upload-area" onclick="document.getElementById('file-input').click()">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <p>Kéo thả hoặc nhấp để tải ảnh lên</p>
                        <span>Định dạng JPG, PNG (Tối đa 10MB)</span>
                    </div>
                    <input type="file" id="file-input" name="images[]" multiple accept="image/*" style="display:none;" onchange="previewFiles(this)">
                    <div class="upload-preview" id="upload-preview">
                        <div class="upload-thumb-add" onclick="document.getElementById('file-input').click()">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    Gửi báo cáo <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="right-col">

            <!-- Emergency card -->
            <div class="info-card info-card--emergency">
                <div class="info-card__icon"><i class="fa-solid fa-phone"></i></div>
                <h3>Cần trợ giúp gấp?</h3>
                <p>Nếu đây là tình huống đe dọa đến an toàn hoặc tài sản lớn, vui lòng liên hệ trực tiếp.</p>
                <div class="hotline"><i class="fa-solid fa-phone-volume"></i> Số máy lẻ: 911</div>
            </div>

            <!-- Guide card -->
            <div class="info-card">
                <div class="info-card__header">Hướng dẫn báo cáo</div>
                <ul class="guide-list">
                    <li>
                        <span class="guide-dot"><i class="fa-solid fa-bullseye"></i></span>
                        <div><strong>Rõ ràng:</strong> Tiêu đề nên tóm tắt ngắn gọn vấn đề đang xảy ra.</div>
                    </li>
                    <li>
                        <span class="guide-dot"><i class="fa-solid fa-camera"></i></span>
                        <div><strong>Minh chứng:</strong> Luôn đính kèm ảnh chụp thực tế để giúp bộ phận kỹ thuật định vị nhanh hơn.</div>
                    </li>
                    <li>
                        <span class="guide-dot"><i class="fa-solid fa-flag"></i></span>
                        <div><strong>Ưu tiên:</strong> Chỉ đánh dấu "Cao" cho các sự cố khẩn cấp (vỡ ống nước, mất điện toàn khu, v.v.)</div>
                    </li>
                </ul>
            </div>

            <!-- Stat card -->
            <div class="info-card">
                <div class="stat-card__label">Trạng thái xử lý trung bình</div>
                <div class="stat-card__row">
                    <span class="stat-card__value">2.4</span>
                    <span class="stat-card__unit">giờ</span>
                    <span class="stat-card__change"><i class="fa-solid fa-arrow-down"></i> -12% so với tuần trước</span>
                </div>
                <div class="stat-card__bar"><div class="stat-card__bar-fill" style="width:72%;"></div></div>
            </div>

        </div>

    </div>
</div>

<script>
function previewFiles(input) {
    const preview = document.getElementById('upload-preview');
    // Keep the add button
    const addBtn = preview.querySelector('.upload-thumb-add');
    preview.innerHTML = '';
    
    if (input.files) {
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const thumb = document.createElement('div');
                thumb.className = 'upload-thumb';
                thumb.innerHTML = '<img src="' + e.target.result + '" alt="preview">';
                preview.insertBefore(thumb, preview.lastElementChild);
            };
            reader.readAsDataURL(file);
        });
    }
    
    preview.appendChild(addBtn || createAddBtn());
}

function createAddBtn() {
    const btn = document.createElement('div');
    btn.className = 'upload-thumb-add';
    btn.innerHTML = '<i class="fa-solid fa-camera"></i>';
    btn.onclick = () => document.getElementById('file-input').click();
    return btn;
}
</script>

</body>
</html>
