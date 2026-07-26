@extends('layouts.admin.master')

@section('page_title', 'Máy Quét Mã QR Chấm Công Tự Động')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Manager')
@section('user_role_label', strtoupper(auth()->user()->role ?? 'MANAGER'))

@push('styles')
    @vite(['resources/css/pages/admin/attendance/index.css'])
    <style>
        .qr-card { background:#fff; border:1px solid #e8edf5; border-radius:16px; padding:24px; box-shadow:0 4px 20px rgba(30,58,138,.06); }
        #reader { width:100%; max-width:480px; margin:0 auto; border-radius:14px; overflow:hidden; border:2px solid #3b82f6; }
        .scan-feed-item { display:flex; align-items:center; gap:12px; padding:12px 16px; border-radius:10px; background:#f8faff; margin-bottom:8px; border:1px solid #e8edf5; }
        .scan-feed-item--in { border-left:4px solid #16a34a; background:#f0fdf4; }
        .scan-feed-item--out { border-left:4px solid #dc2626; background:#fef2f2; }
    </style>
@endpush

@php
    $roleLabels = [
        'admin'      => 'Quản trị viên',
        'manager'    => 'Quản lý BQL',
        'staff'      => 'Kế toán',
        'technician' => 'Kỹ thuật viên',
        'security'   => 'Bảo vệ',
        'cleaning'   => 'Vệ sinh',
    ];
@endphp

@section('content')
<div class="attendance-page">

    {{-- Header --}}
    <div class="attendance-page__header">
        <div>
            <p class="attendance-page__eyebrow">Nhân sự › Chấm công</p>
            <h1>Máy Quét Mã QR Chấm Công Tự Động</h1>
            <p style="margin:4px 0 0; color:#64748b; font-size:14px;">
                Chấm công thời gian thực tại Sảnh BQL ngày {{ now()->isoFormat('dddd, DD/MM/YYYY') }}
            </p>
        </div>
        <div class="attendance-page__actions">
            <a href="{{ portal_route('attendance.checkin') }}" class="att-btn att-btn--secondary">
                ‹ Bảng chấm công tổng
            </a>
            <a href="{{ portal_route('attendance.index') }}" class="att-btn att-btn--secondary">
                Lịch sử chấm công
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="att-alert att-alert--success" style="font-size:15px; padding:16px 20px;">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <strong>{{ session('success') }}</strong>
        </div>
    @endif
    @if (session('info'))
        <div style="background:#eff6ff; border:1px solid #bfdbfe; color:#1d4ed8; padding:16px 20px; border-radius:10px; margin-bottom:20px; font-weight:600; font-size:15px;">
            ℹ {{ session('info') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="att-alert att-alert--danger" style="font-size:15px; padding:16px 20px;">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <strong>{{ $errors->first() }}</strong>
        </div>
    @endif

    {{-- Main 2-column Grid --}}
    <div style="display:grid; grid-template-columns: 1fr 380px; gap:20px; align-items:start;">

        {{-- Left: QR Camera Scanner --}}
        <div class="qr-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <h3 style="margin:0; font-size:17px; color:#0f172a; display:flex; align-items:center; gap:8px;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                    </svg>
                    Camera Quét Thẻ QR Nhân Viên
                </h3>
                <span style="font-size:12px; background:#dcfce7; color:#15803d; font-weight:700; padding:4px 10px; border-radius:50px;">
                    ● Đang hoạt động
                </span>
            </div>

            {{-- Camera container --}}
            <div id="reader"></div>

            {{-- Config bar for scanner --}}
            <form method="POST" action="{{ portal_route('attendance.qr-scan.store') }}" id="qrScanForm" style="margin-top:20px;">
                @csrf
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                    <div>
                        <label class="att-form-label">Ca mặc định khi check-in</label>
                        <select name="shift" id="shiftSelect" class="att-form-control" style="height:40px;">
                            @foreach($shifts as $sk => $sl)
                                <option value="{{ $sk }}" {{ $sk === 'full_day' ? 'selected' : '' }}>{{ $sl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="att-form-label">Vị trí trực mặc định</label>
                        <select name="work_location" id="locSelect" class="att-form-control" style="height:40px;">
                            @foreach($workLocations as $loc)
                                <option value="{{ $loc }}" {{ $loop->first ? 'selected' : '' }}>{{ $loc }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Manual Code Input (Backup) --}}
                <div style="display:flex; gap:10px;">
                    <input type="text" name="qr_code" id="qrCodeInput" class="att-form-control" style="height:44px;"
                           placeholder="Nhập thủ công Mã QR, Email, SĐT hoặc ID nhân viên..." autofocus required>
                    <button type="submit" class="att-btn att-btn--primary" style="height:44px; padding:0 24px; flex-shrink:0;">
                        Quét Mã
                    </button>
                </div>
            </form>
        </div>

        {{-- Right: Live Scan Feed --}}
        <div class="qr-card">
            <h3 style="margin-top:0; margin-bottom:16px; font-size:16px; color:#0f172a; display:flex; align-items:center; gap:8px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="12 8 12 12 14 14"/><circle cx="12" cy="12" r="10"/>
                </svg>
                Lịch sử Quét hôm nay
            </h3>

            <div style="display:flex; flex-direction:column; gap:8px;">
                @forelse($recentScans as $sc)
                    @php
                        $isOut = (bool) $sc->check_out_at;
                    @endphp
                    <div class="scan-feed-item {{ $isOut ? 'scan-feed-item--out' : 'scan-feed-item--in' }}">
                        <div style="width:36px; height:36px; border-radius:50%; background:#e2e8f0; font-weight:700; color:#334155; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:13px;">
                            {{ strtoupper(mb_substr($sc->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:700; font-size:13px; color:#0f172a;">{{ $sc->user->name ?? '—' }}</div>
                            <div style="font-size:11px; color:#64748b;">
                                {{ $roleLabels[$sc->user->role ?? ''] ?? ($sc->user->role ?? '') }} • {{ $sc->shift }}
                            </div>
                        </div>
                        <div style="text-align:right;">
                            @if($isOut)
                                <span style="font-size:11px; font-weight:700; color:#dc2626; display:block;">🔴 CHECK-OUT</span>
                                <span style="font-size:12px; font-weight:700;">{{ $sc->check_out_at->format('H:i') }}</span>
                            @else
                                <span style="font-size:11px; font-weight:700; color:#16a34a; display:block;">🟢 CHECK-IN</span>
                                <span style="font-size:12px; font-weight:700;">{{ $sc->check_in_at->format('H:i') }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align:center; padding:30px; color:#94a3b8; font-size:13px;">
                        Chưa có bản ghi quét mã nào trong ngày hôm nay.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrcodeScanner = null;
let isProcessing = false;

function onScanSuccess(decodedText, decodedResult) {
    if (isProcessing) return;
    isProcessing = true;

    // Fill into input field and submit form
    const input = document.getElementById('qrCodeInput');
    input.value = decodedText;

    // Beep sound
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(880, audioCtx.currentTime); // A5 note
        osc.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.15);
    } catch(e) {}

    // Submit form via standard form submit or fetch
    document.getElementById('qrScanForm').submit();
}

function onScanFailure(error) {
    // continuous scan attempt
}

document.addEventListener('DOMContentLoaded', function () {
    html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { fps: 10, qrbox: { width: 250, height: 250 } },
        /* verbose= */ false
    );
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
});
</script>
@endpush
