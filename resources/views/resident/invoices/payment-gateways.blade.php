@extends('layouts.resident.master')

@section('title', 'Thanh toán – DomusHub')

@section('content')
<div class="inv-container">
    <div style="max-width: 560px; margin: 0 auto;">

        <a href="{{ route('resident.invoices.index') }}" style="display: inline-flex; align-items: center; gap: 6px; color: #2563eb; font-size: 0.9rem; text-decoration: none; margin-bottom: 20px;">
            ← Quay lại danh sách hóa đơn
        </a>

        {{-- Tóm tắt hóa đơn --}}
        <div class="inv-card inv-card--unpaid" style="margin-bottom: 28px;">
            <div class="inv-card__accent"></div>
            <div class="inv-card__body">
                <h3 class="inv-card__title">{{ $invoice->title }}</h3>
                <div class="inv-card__meta">
                    <span class="inv-meta-item">Kỳ tính: Tháng {{ str_pad($invoice->billing_month->month, 2, '0', STR_PAD_LEFT) }}/{{ $invoice->billing_year }}</span>
                    <span class="inv-meta-item">Hạn chót: {{ $invoice->due_date->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="inv-card__right">
                <div class="inv-card__price inv-card__price--pending">{{ number_format($invoice->total_amount) }}đ</div>
            </div>
        </div>

        {{-- BƯỚC 1: Chọn cổng --}}
        <div id="step-select">
            <p class="inv-eyebrow" style="margin-bottom: 12px;">Bước 1</p>
            <h2 class="inv-title" style="font-size: 1.3rem; margin-bottom: 20px;">Chọn phương thức thanh toán</h2>

            <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px;">
                @foreach($gateways as $key => $gateway)
                <div class="gateway-card" data-gateway="{{ $key }}" style="cursor: pointer; border: 2px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; background: #fff; transition: border-color 0.2s, background 0.2s;">
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="width: 52px; height: 52px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 26px; flex-shrink: 0;">
                            @if($key === 'mbbank') 🏦 @else 💜 @endif
                        </div>
                        <div>
                            <div style="font-size: 1rem; font-weight: 700; color: #0f172a;">{{ $gateway['name'] }}</div>
                            <div style="font-size: 0.85rem; color: #64748b; margin-top: 2px;">{{ $gateway['description'] }}</div>
                        </div>
                    </div>
                    <svg class="gateway-check" style="width: 22px; height: 22px; color: #2563eb; opacity: 0; flex-shrink: 0; transition: opacity 0.2s;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                @endforeach
            </div>

            <button id="btn-continue" disabled style="width: 100%; padding: 13px; background: #2563eb; color: #fff; font-size: 1rem; font-weight: 600; border: none; border-radius: 8px; cursor: not-allowed; opacity: 0.5; transition: opacity 0.2s;">
                Tiếp tục →
            </button>
        </div>

        {{-- BƯỚC 2: QR --}}
        <div id="step-qr" style="display: none;">
            <p class="inv-eyebrow" style="margin-bottom: 12px;">Bước 2</p>
            <h2 class="inv-title" style="font-size: 1.3rem; margin-bottom: 4px;">Quét mã QR để thanh toán</h2>
            <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 20px;">Thanh toán qua <strong id="qr-gateway-name"></strong></p>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 28px; text-align: center; margin-bottom: 20px;">
                <div id="qr-loading" style="color: #64748b; font-size: 0.9rem;">Đang tạo mã QR...</div>
                <img id="qr-image" src="" alt="QR Code" style="display: none; width: 220px; height: 220px; margin: 0 auto;">
            </div>

            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 14px 18px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 0.9rem; color: #475569;">Số tiền thanh toán</span>
                <span style="font-size: 1.2rem; font-weight: 700; color: #2563eb;">{{ number_format($invoice->total_amount) }}đ</span>
            </div>

            <div id="qr-instructions" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 14px 18px; font-size: 0.875rem; color: #1f2937; margin-bottom: 20px; line-height: 1.8;"></div>

            {{-- Trạng thái polling --}}
            <div id="polling-status" style="display: flex; align-items: center; justify-content: center; gap: 10px; padding: 14px; background: #fefce8; border: 1px solid #fde68a; border-radius: 8px; margin-bottom: 20px;">
                <svg id="polling-spinner" style="width:18px;height:18px;animation:spin 1s linear infinite;color:#ca8a04;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span id="polling-text" style="font-size: 0.875rem; font-weight: 600; color: #92400e;">Đang chờ xác nhận thanh toán...</span>
            </div>

            <button type="button" onclick="backToSelect()" style="width: 100%; padding: 12px; background: #e2e8f0; color: #0f172a; font-weight: 600; border: none; border-radius: 8px; cursor: pointer;">
                ← Chọn lại
            </button>
        </div>

        {{-- BƯỚC 3: Thành công --}}
        <div id="step-success" style="display: none; text-align: center; padding: 40px 20px;">
            <div style="font-size: 4rem; margin-bottom: 16px;">✅</div>
            <h2 style="font-size: 1.4rem; font-weight: 700; color: #15803d; margin-bottom: 8px;">Thanh toán thành công!</h2>
            <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 24px;">Hệ thống đã xác nhận giao dịch của bạn.</p>
            <a href="{{ route('resident.invoices.index') }}" class="inv-btn inv-btn--primary" style="display: inline-block; padding: 12px 28px; text-decoration: none; border-radius: 8px;">
                Về danh sách hóa đơn
            </a>
        </div>

    </div>
</div>

<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

@push('scripts')
<script>
(function () {
    const generateUrl = '{{ route('resident.invoices.generate-qr', $invoice->id) }}';
    const checkUrl    = '{{ route('resident.invoices.check-payment', $invoice->id) }}';
    const csrfToken   = '{{ csrf_token() }}';

    let selectedGateway = null;
    let pollInterval    = null;
    let currentTransactionCode = null;

    const gatewayNames = @json(array_column($gateways, 'name', null));
    const gatewayInstructions = {
        mbbank: '📌 <strong>Nội dung chuyển khoản rất quan trọng!</strong><br>Hệ thống tự động dùng nội dung để xác nhận.<br><br>1. Mở app <strong>MB Bank</strong> → Quét mã QR<br>2. Kiểm tra <strong>nội dung chuyển khoản</strong> có mã giao dịch<br>3. Xác nhận chuyển khoản<br>4. Hệ thống tự động xác nhận trong vài giây',
        momo:   '📌 <strong>Nội dung chuyển khoản rất quan trọng!</strong><br>Hệ thống tự động dùng nội dung để xác nhận.<br><br>1. Mở app <strong>Momo</strong> → Quét mã QR<br>2. Kiểm tra <strong>nội dung</strong> có mã giao dịch<br>3. Xác nhận thanh toán<br>4. Hệ thống tự động xác nhận trong vài giây',
    };

    // Chọn cổng
    document.querySelectorAll('.gateway-card').forEach(card => {
        card.addEventListener('click', function () {
            document.querySelectorAll('.gateway-card').forEach(c => {
                c.style.borderColor = '#e2e8f0';
                c.style.background  = '#fff';
                c.querySelector('.gateway-check').style.opacity = '0';
            });
            this.style.borderColor = '#3b82f6';
            this.style.background  = '#f0f9ff';
            this.querySelector('.gateway-check').style.opacity = '1';
            selectedGateway = this.dataset.gateway;
            const btn = document.getElementById('btn-continue');
            btn.disabled = false; btn.style.opacity = '1'; btn.style.cursor = 'pointer';
        });
    });

    // Generate QR
    document.getElementById('btn-continue').addEventListener('click', async function () {
        if (!selectedGateway) return;
        this.textContent = 'Đang tạo mã QR...';
        this.disabled = true;
        try {
            const res  = await fetch(generateUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ gateway: selectedGateway }),
            });
            const data = await res.json();
            if (!data.success) {
                alert('Lỗi: ' + (data.message || 'Không thể tạo mã QR'));
                this.textContent = 'Tiếp tục →'; this.disabled = false; this.style.opacity = '1';
                return;
            }
            currentTransactionCode = data.transaction_code;
            showQRStep(data);
        } catch (e) {
            alert('Lỗi kết nối. Vui lòng thử lại.');
            this.textContent = 'Tiếp tục →'; this.disabled = false; this.style.opacity = '1';
        }
    });

    function showQRStep(data) {
        document.getElementById('step-select').style.display = 'none';
        document.getElementById('step-qr').style.display     = 'block';
        document.getElementById('qr-gateway-name').textContent = gatewayNames[selectedGateway] || selectedGateway;
        document.getElementById('qr-instructions').innerHTML   = gatewayInstructions[selectedGateway] || '';

        const img = document.getElementById('qr-image');
        if (data.qr_url) {
            img.src = data.qr_url;
            img.style.display = 'block';
            document.getElementById('qr-loading').style.display = 'none';
        }

        startPolling();
    }

    window.backToSelect = function () {
        clearInterval(pollInterval);
        document.getElementById('step-qr').style.display     = 'none';
        document.getElementById('step-select').style.display = 'block';
        const btn = document.getElementById('btn-continue');
        btn.textContent = 'Tiếp tục →'; btn.disabled = false; btn.style.opacity = '1';
    };

    // Polling kiểm tra trạng thái mỗi 4 giây
    function startPolling() {
        clearInterval(pollInterval);
        let count = 0;
        pollInterval = setInterval(async () => {
            if (++count > 75) { // 5 phút
                clearInterval(pollInterval);
                document.getElementById('polling-text').textContent = 'Hết thời gian chờ. Vui lòng thử lại.';
                document.getElementById('polling-spinner').style.display = 'none';
                return;
            }
            await checkStatus();
        }, 4000);
    }

    async function checkStatus() {
        try {
            const res  = await fetch(checkUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ transaction_code: currentTransactionCode }),
            });
            const data = await res.json();

            if (data.status === 'success') {
                clearInterval(pollInterval);
                showSuccess();
            }
        } catch (e) { /* silent */ }
    }

    function showSuccess() {
        document.getElementById('step-qr').style.display      = 'none';
        document.getElementById('step-success').style.display = 'block';
    }
})();
</script>
@endpush
@endsection
