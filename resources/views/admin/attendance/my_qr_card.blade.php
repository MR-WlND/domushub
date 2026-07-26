@extends('layouts.admin.master')

@section('page_title', 'Thẻ QR Chấm Công Của Tôi')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? '')
@section('user_role_label', strtoupper(auth()->user()->role ?? ''))

@push('styles')
    @vite(['resources/css/pages/admin/attendance/index.css'])
    <style>
        .my-qr-card-container {
            max-width: 440px;
            margin: 20px auto;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(30, 58, 138, 0.12);
            overflow: hidden;
            border: 1px solid #e8edf5;
            text-align: center;
        }

        .my-qr-card-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%);
            padding: 32px 24px;
            color: #fff;
            position: relative;
        }

        .my-qr-card-header__eyebrow {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .12em;
            color: #93c5fd;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .my-qr-card-header__title {
            font-size: 20px;
            font-weight: 800;
            margin: 0;
        }

        .my-qr-card-body {
            padding: 32px 28px;
        }

        .my-qr-avatar {
            width: 84px;
            height: 84px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            color: #fff;
            font-size: 32px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: -60px auto 16px;
            border: 4px solid #fff;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.25);
            overflow: hidden;
        }

        .my-qr-name { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
        .my-qr-role { font-size: 14px; font-weight: 700; color: #2563eb; margin-bottom: 4px; }
        .my-qr-email { font-size: 13px; color: #64748b; margin-bottom: 24px; }

        .my-qr-code-box {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 20px;
            padding: 20px;
            display: inline-block;
            margin-bottom: 20px;
        }

        .my-qr-code-img {
            width: 220px;
            height: 220px;
            display: block;
            margin: 0 auto;
        }

        .my-qr-code-text {
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            margin-top: 10px;
            font-family: monospace;
            letter-spacing: .05em;
        }

        .my-qr-instructions {
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 24px;
        }

        .my-qr-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
    </style>
@endpush

@section('content')
<div class="attendance-page">

    {{-- Breadcrumb --}}
    <div class="attendance-page__header">
        <div>
            <p class="attendance-page__eyebrow">Tài khoản cá nhân › Thẻ nhân viên</p>
            <h1>Thẻ QR Chấm Công Của Tôi</h1>
        </div>
    </div>

    {{-- Employee QR Card --}}
    <div class="my-qr-card-container" id="printableCard">
        <div class="my-qr-card-header">
            <div class="my-qr-card-header__eyebrow">DOMUSHUB PROPERTY MANAGEMENT</div>
            <h2 class="my-qr-card-header__title">THẺ NHÂN VIÊN CHẤM CÔNG</h2>
        </div>

        <div class="my-qr-card-body">
            <div class="my-qr-avatar">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" style="width:100%; height:100%; object-fit:cover;">
                @else
                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                @endif
            </div>

            <div class="my-qr-name">{{ $user->name }}</div>
            <div class="my-qr-role">{{ $displayRole }}</div>
            <div class="my-qr-email">{{ $user->email }}</div>

            <div class="my-qr-code-box">
                <img class="my-qr-code-img"
                     src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($qrData) }}"
                     alt="Mã QR Chấm công {{ $user->name }}">
                <div class="my-qr-code-text">{{ $qrData }}</div>
            </div>

            <div class="my-qr-instructions">
                📲 <strong>Hướng dẫn:</strong> Giơ hình ảnh mã QR này trước <strong>Camera Máy Quét QR tại Sảnh BQL</strong> để tự động Check-In / Check-Out ca làm việc.
            </div>

            <div class="my-qr-actions">
                <button type="button" class="att-btn att-btn--primary" onclick="window.print()">
                    🖨 In Thẻ Nhân Viên
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
