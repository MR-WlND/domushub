@extends('layouts.admin.master')

@section('page_title', 'Quản lý Hóa đơn')

@section('content')
<div class="inv-admin">

    {{-- Header --}}
    <div class="inv-admin__header">
        <div>
            <p class="inv-admin__eyebrow">Tài chính</p>
            <h1 class="inv-admin__title">Quản lý Hóa đơn</h1>
        </div>
        <div style="display:flex;gap:10px">
            <a href="{{ route('admin.invoices.batch') }}" class="inv-admin__btn inv-admin__btn--primary">Xuất hàng loạt</a>
            <a href="{{ route('admin.invoices.create') }}" class="inv-admin__btn" style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0">Tạo đơn lẻ</a>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="inv-admin__alert inv-admin__alert--success">{{ session('success') }}</div>
    @endif

    {{-- Bộ lọc --}}
    <div class="inv-admin__filter-bar">
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="inv-admin__filter-form">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm mã HD, tiêu đề, căn hộ..."
                   class="inv-admin__input">

            <select name="status" class="inv-admin__select" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="paid" {{ request('status')=='paid' ? 'selected' : '' }}>Đã thanh toán</option>
                <option value="unpaid" {{ request('status')=='unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                <option value="overdue" {{ request('status')=='overdue' ? 'selected' : '' }}>Quá hạn</option>
            </select>

            <button type="submit" class="inv-admin__btn inv-admin__btn--primary">Lọc</button>
            @if(request()->hasAny(['search','status','type','month']))
                <a href="{{ route('admin.invoices.index') }}" class="inv-admin__btn inv-admin__btn--ghost">Xóa lọc</a>
            @endif
        </form>
    </div>

    {{-- Bảng hóa đơn --}}
    <div class="inv-admin__card">
        <div class="inv-admin__card-header">
            <span>{{ $invoices->total() }} hóa đơn</span>
            <span class="inv-admin__muted">Trang {{ $invoices->currentPage() }}/{{ $invoices->lastPage() }}</span>
        </div>

        <table class="inv-admin__table">
            <thead>
                <tr>
                    <th>Mã HD</th>
                    <th>Căn hộ</th>
                    <th>Tiêu đề</th>
                    <th>Số tiền</th>
                    <th>Tháng</th>
                    <th>Hạn TT</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td>
                        <span class="inv-admin__code">{{ $inv->invoice_code }}</span>
                    </td>
                    <td>
                        {{ optional($inv->apartment)->apartment_number ?? '—' }}
                        <div class="inv-admin__sub">{{ optional(optional(optional($inv->apartment)->floor)->block)->name ?? '' }}</div>
                    </td>
                    <td>{{ $inv->title }}</td>
                    <td class="inv-admin__amount">{{ number_format($inv->total_amount) }}đ</td>
                    <td class="inv-admin__muted">{{ $inv->billing_month->format('m/Y') }}</td>
                    <td class="inv-admin__muted">{{ $inv->due_date->format('d/m/Y') }}</td>
                    <td>
                        @if($inv->status === 'paid')
                            <span class="inv-admin__badge inv-admin__badge--paid">Đã TT</span>
                        @elseif($inv->status === 'overdue')
                            <span class="inv-admin__badge inv-admin__badge--overdue">Quá hạn</span>
                        @else
                            <span class="inv-admin__badge inv-admin__badge--unpaid">Chưa TT</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.invoices.show', $inv) }}" class="inv-admin__btn inv-admin__btn--sm inv-admin__btn--ghost" style="color: #2563eb; border-color: #bfdbfe; margin-right: 5px;">Chi tiết</a>
                        @if($inv->status !== 'paid')
                        <button type="button" class="inv-admin__btn inv-admin__btn--sm inv-admin__btn--success"
                                title="Ghi nhận thanh toán nhanh"
                                onclick="openPaymentModal({{ $inv->id }}, '{{ $inv->invoice_code }}', '{{ addslashes($inv->title) }}', {{ $inv->remaining_amount ?: $inv->total_amount }}, '{{ addslashes($inv->apartment->owner_name ?? '') }}', 'Căn {{ optional($inv->apartment)->apartment_number ?? '—' }} ({{ optional(optional(optional($inv->apartment)->floor)->block)->name ?? '' }})', 'Tháng {{ $inv->billing_month->format('m/Y') }}')">
                            Thu tiền
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="inv-admin__empty">Không có hóa đơn nào</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Phân trang --}}
        @if($invoices->hasPages())
        <div class="inv-admin__pagination">
            @if(!$invoices->onFirstPage())
                <a href="{{ $invoices->previousPageUrl() }}" class="inv-admin__page-btn">‹ Trước</a>
            @endif
            @foreach($invoices->getUrlRange(max(1,$invoices->currentPage()-2), min($invoices->lastPage(),$invoices->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="inv-admin__page-btn {{ $page==$invoices->currentPage() ? 'inv-admin__page-btn--active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($invoices->hasMorePages())
                <a href="{{ $invoices->nextPageUrl() }}" class="inv-admin__page-btn">Sau ›</a>
            @endif
        </div>
        @endif
    </div>
</div>

{{-- Quick Payment Modal --}}
<div class="modal-overlay" id="quickPaymentModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Thanh toán nhanh</h3>
            <button class="modal-close" onclick="closePaymentModal()">&times;</button>
        </div>
        <form id="quickPaymentForm" method="POST" action="" enctype="multipart/form-data" onsubmit="return confirmQuickPayment(event);">
            @csrf
            @method('PATCH')
            <input type="hidden" name="amount" id="modal_amount">
            <div class="modal-body">
                <p id="modalInvoiceInfo" style="font-size: 13px; color: #64748b; margin-bottom: 16px; font-weight: 500;"></p>

                <div class="form-field">
                    <label for="modal_payment_method">Phương thức thanh toán</label>
                    <select name="payment_method" id="modal_payment_method" class="inv-admin__select" style="width:100%">
                        <option value="transfer">🏦 Chuyển khoản ngân hàng</option>
                        <option value="cash">💵 Tiền mặt</option>
                        <option value="other">💳 Khác</option>
                    </select>
                </div>

                <div class="form-field" style="margin-top: 15px;">
                    <label for="modal_payer_name">Người nộp tiền <span style="font-weight:400;text-transform:none;color:#757682;">(tuỳ chọn)</span></label>
                    <input type="text" name="payer_name" id="modal_payer_name" class="inv-admin__input" style="width:100%" placeholder="Tên người thanh toán...">
                </div>

                <div class="form-field" style="margin-top: 15px;">
                    <label for="modal_proof_image">Minh chứng thanh toán <span style="font-weight:400;text-transform:none;color:#757682;">(Ảnh chụp bill / Biên lai)</span></label>
                    <div class="custom-file-upload-box" style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; background: #fff; display: flex; align-items: center; justify-content: space-between; gap: 10px; min-height: 42px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);">
                        <span id="file_name_label_modal_proof_image" style="color: #64748b; font-size: 0.85rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 55%; font-weight: 500;">Chưa chọn tệp nào</span>
                        <div style="display: flex; gap: 6px; flex-shrink: 0;">
                            <button type="button" onclick="document.getElementById('modal_proof_image').click()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                📁 Chọn tệp
                            </button>
                            <button type="button" onclick="startCamera('modal_proof_image')" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                📸 Mở Camera
                            </button>
                        </div>
                    </div>
                    <input type="file" name="proof_image" id="modal_proof_image" class="form-input" style="display: none;" accept="image/*" capture="environment" onchange="updateFileNameLabel(this, 'modal_proof_image')">

                    <div class="camera-wrapper" style="margin-top: 10px;">
                        <div id="camera_container_modal_proof_image" style="display:none; margin-top:10px; position:relative; background:#000; border-radius:8px; overflow:hidden;">
                            <video id="video_modal_proof_image" autoplay playsinline style="width:100%; max-height:250px; object-fit:cover; display:block;"></video>
                            <div style="position:absolute; bottom:10px; left:50%; transform:translateX(-50%); display:flex; gap:8px; z-index:10;">
                                <button type="button" onclick="captureSnapshot('modal_proof_image')" style="background:#10b981; color:#fff; border:none; padding:6px 14px; border-radius:20px; font-weight:600; cursor:pointer; font-size:0.8rem;">📸 Chụp</button>
                                <button type="button" onclick="stopCamera('modal_proof_image')" style="background:#ef4444; color:#fff; border:none; padding:6px 14px; border-radius:20px; font-weight:600; cursor:pointer; font-size:0.8rem;">✕ Đóng</button>
                            </div>
                        </div>
                        <div id="preview_container_modal_proof_image" style="display:none; margin-top:10px; position:relative; max-width: 150px;">
                            <img id="img_preview_modal_proof_image" src="" style="width:100%; border-radius:6px; border:1px solid #cbd5e1; display:block;">
                            <button type="button" onclick="removeCapturedPhoto('modal_proof_image')" style="position:absolute; top:-5px; right:-5px; background:#ef4444; color:#fff; border:none; border-radius:50%; width:20px; height:20px; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:bold; cursor:pointer; padding:0;">×</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="inv-admin__btn" onclick="closePaymentModal()" style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0">Hủy</button>
                <button type="submit" class="inv-admin__btn inv-admin__btn--success">Xác nhận</button>
            </div>
        </form>
    </div>
</div>

<style>
.inv-admin { max-width: 1100px; margin: 0 auto; padding: 24px 20px; }

/* Header */
.inv-admin__header { display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px; }
.inv-admin__eyebrow { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin: 0 0 4px; font-weight: 600; }
.inv-admin__title { font-size: 1.6rem; font-weight: 700; color: #0f172a; margin: 0; }

/* Alert */
.inv-admin__alert { padding: 12px 16px; border-radius: 8px; font-size: 0.9rem; font-weight: 500; margin-bottom: 16px; }
.inv-admin__alert--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }

/* Filter */
.inv-admin__filter-bar { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; }
.inv-admin__filter-form { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
.inv-admin__input {
    flex: 1; min-width: 200px; padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 6px;
    font-size: 0.85rem; color: #1e293b; outline: none; background: #f8fafc;
}
.inv-admin__input:focus { border-color: #3b82f6; }
.inv-admin__select {
    padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 6px;
    font-size: 0.85rem; color: #1e293b; background: #f8fafc; cursor: pointer;
}

/* Buttons */
.inv-admin__btn {
    padding: 7px 14px; border-radius: 6px; font-size: 0.85rem; font-weight: 600;
    cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center;
}
.inv-admin__btn--primary { background: #2563eb; color: #fff; }
.inv-admin__btn--primary:hover { background: #1d4ed8; }
.inv-admin__btn--ghost { background: none; color: #ef4444; border: 1px solid #fecaca; }
.inv-admin__btn--ghost:hover { background: #fef2f2; }
.inv-admin__btn--success { background: #16a34a; color: #fff; }
.inv-admin__btn--success:hover { background: #15803d; }
.inv-admin__btn--sm { padding: 4px 10px; font-size: 0.78rem; }

/* Card / Table */
.inv-admin__card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
.inv-admin__card-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem; font-weight: 600; color: #334155;
}
.inv-admin__muted { color: #94a3b8; font-size: 0.8rem; }

.inv-admin__table { width: 100%; border-collapse: collapse; }
.inv-admin__table th {
    text-align: left; padding: 10px 14px; font-size: 0.75rem; font-weight: 600;
    color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #e2e8f0; background: #f8fafc;
}
.inv-admin__table td { padding: 12px 14px; font-size: 0.85rem; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.inv-admin__table tr:hover td { background: #f8fafc; }
.inv-admin__sub { font-size: 0.75rem; color: #94a3b8; }
.inv-admin__code { font-family: monospace; font-size: 0.8rem; color: #3b82f6; font-weight: 600; }
.inv-admin__amount { font-weight: 700; color: #0f172a; }

/* Badges */
.inv-admin__badge { font-size: 0.72rem; font-weight: 600; padding: 3px 8px; border-radius: 4px; }
.inv-admin__badge--paid { background: #dcfce7; color: #15803d; }
.inv-admin__badge--unpaid { background: #fef3c7; color: #b45309; }
.inv-admin__badge--overdue { background: #fee2e2; color: #b91c1c; }

/* Empty */
.inv-admin__empty { text-align: center; padding: 40px 20px; color: #94a3b8; font-size: 0.95rem; }

/* Pagination */
.inv-admin__pagination { display: flex; justify-content: center; gap: 5px; padding: 14px; border-top: 1px solid #e2e8f0; }
.inv-admin__page-btn {
    padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;
    border: 1px solid #e2e8f0; color: #475569; background: #fff; text-decoration: none;
}
.inv-admin__page-btn:hover { background: #f1f5f9; }
.inv-admin__page-btn--active { background: #2563eb; color: #fff; border-color: #2563eb; }

/* Modal */
.modal-overlay {
    display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.45);
    z-index: 1000; align-items: center; justify-content: center;
}
.modal-overlay.active { display: flex; }
.modal-content {
    background: #fff; border-radius: 12px; width: 100%; max-width: 440px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15); overflow: hidden;
}
.modal-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 16px 20px; border-bottom: 1px solid #e2e8f0;
}
.modal-title { font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0; }
.modal-close {
    background: none; border: none; font-size: 1.4rem; cursor: pointer;
    color: #94a3b8; line-height: 1; padding: 0 4px;
}
.modal-close:hover { color: #475569; }
.modal-body { padding: 20px; }
.modal-footer {
    display: flex; justify-content: flex-end; gap: 10px;
    padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #f8fafc;
}
.form-field label { display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 6px; }
</style>

<script>
    const activeStreams = {};

    let currentInvoiceAmount = 0;
    let currentInvoiceCode = '';
    let currentApartmentInfo = '';
    let currentBillingMonth = '';

    function updateFileNameLabel(input, inputId) {
        const label = document.getElementById('file_name_label_' + inputId);
        if (input.files && input.files.length > 0) {
            label.textContent = input.files[0].name;
            label.style.color = '#0f172a';
        } else {
            label.textContent = 'Chưa chọn tệp nào';
            label.style.color = '#64748b';
        }
    }

    async function startCamera(inputId) {
        stopCamera(inputId);

        const video = document.getElementById('video_' + inputId);
        const container = document.getElementById('camera_container_' + inputId);
        
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: 'environment' } 
            });
            activeStreams[inputId] = stream;
            video.srcObject = stream;
            container.style.display = 'block';
        } catch (err) {
            alert('Không thể truy cập camera. Vui lòng cấp quyền camera cho trang web hoặc chọn ảnh từ thiết bị.');
            console.error('Error accessing camera: ', err);
        }
    }

    function stopCamera(inputId) {
        if (activeStreams[inputId]) {
            activeStreams[inputId].getTracks().forEach(track => track.stop());
            delete activeStreams[inputId];
        }
        const container = document.getElementById('camera_container_' + inputId);
        if (container) {
            container.style.display = 'none';
        }
        const video = document.getElementById('video_' + inputId);
        if (video) {
            video.srcObject = null;
        }
    }

    function captureSnapshot(inputId) {
        const video = document.getElementById('video_' + inputId);
        if (!video || !video.srcObject) return;

        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob((blob) => {
            if (!blob) return;

            const file = new File([blob], "captured_bill.png", { type: "image/png" });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            document.getElementById(inputId).files = dataTransfer.files;

            const previewImg = document.getElementById('img_preview_' + inputId);
            const previewContainer = document.getElementById('preview_container_' + inputId);
            previewImg.src = URL.createObjectURL(blob);
            previewContainer.style.display = 'block';

            const label = document.getElementById('file_name_label_' + inputId);
            if (label) {
                label.textContent = 'captured_bill.png (Chụp từ Camera)';
                label.style.color = '#0f172a';
            }

            stopCamera(inputId);
        }, 'image/png');
    }

    function removeCapturedPhoto(inputId) {
        document.getElementById(inputId).value = '';
        const label = document.getElementById('file_name_label_' + inputId);
        if (label) {
            label.textContent = 'Chưa chọn tệp nào';
            label.style.color = '#64748b';
        }
        const previewContainer = document.getElementById('preview_container_' + inputId);
        if (previewContainer) {
            previewContainer.style.display = 'none';
        }
        const previewImg = document.getElementById('img_preview_' + inputId);
        if (previewImg) {
            previewImg.src = '';
        }
    }

    function confirmQuickPayment(event) {
        const methodSelect = document.getElementById('modal_payment_method');
        const methodVal = methodSelect.options[methodSelect.selectedIndex].text;
        const payerVal = document.getElementById('modal_payer_name').value || 'Cư dân';
        
        const formattedAmount = Number(currentInvoiceAmount).toLocaleString('vi-VN') + ' đ';

        const msg = `Vui lòng xác nhận thông tin thanh toán nhanh:\n\n` +
                    `• Hóa đơn: ${currentInvoiceCode}\n` +
                    `• Căn hộ: ${currentApartmentInfo}\n` +
                    `• Kỳ hóa đơn: ${currentBillingMonth}\n` +
                    `• Số tiền thu: ${formattedAmount}\n` +
                    `• Phương thức: ${methodVal}\n` +
                    `• Người nộp tiền: ${payerVal}\n\n` +
                    `Bạn có chắc chắn muốn ghi nhận thanh toán này?`;
                    
        return confirm(msg);
    }

    function openPaymentModal(invoiceId, invoiceCode, title, amount, ownerName, apartmentInfo, billingMonth) {
        const modal = document.getElementById('quickPaymentModal');
        const form = document.getElementById('quickPaymentForm');
        const info = document.getElementById('modalInvoiceInfo');

        currentInvoiceAmount = amount;
        currentInvoiceCode = invoiceCode;
        currentApartmentInfo = apartmentInfo;
        currentBillingMonth = billingMonth;

        form.action = `/admin/invoices/${invoiceId}/mark-paid`;
        document.getElementById('modal_amount').value = amount;
        document.getElementById('modal_payer_name').value = ownerName;
        info.innerHTML = `Hóa đơn: <strong>${invoiceCode}</strong><br>Nội dung: ${title}<br>Số tiền: <span style="color:#2563eb;font-weight:700">${Number(amount).toLocaleString('vi-VN')} đ</span>`;

        modal.classList.add('active');
    }

    function closePaymentModal() {
        stopCamera('modal_proof_image');
        document.getElementById('quickPaymentModal').classList.remove('active');
    }

    // Close modal on click overlay
    document.getElementById('quickPaymentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePaymentModal();
        }
    });
</script>

@endsection
