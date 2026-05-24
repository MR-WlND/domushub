@extends('layouts.security.master')

@section('page_title', 'Xe Ra — DomusHub')

@section('content')
<div>
    <p class="dashboard-eyebrow">Quản lý bãi xe</p>
    <h1 class="dashboard-page__title">Quét QR — Xe Ra</h1>
</div>

<div class="dashboard-grid" style="margin-top: 2rem;">
    <div class="dashboard-card dashboard-card--primary">
        <p class="dashboard-card__label">Hướng dẫn</p>
        <h2>Quét mã QR xe ra</h2>
        <p>Đưa camera vào mã QR trên vé xe để ghi nhận xe ra khỏi bãi.</p>
    </div>
</div>
@endsection
