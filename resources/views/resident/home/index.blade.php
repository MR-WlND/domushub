@extends('layouts.resident.master')

@section('title', 'Resident Dashboard')

@section('content')
    <section class="resident-dashboard">
        <div class="resident-dashboard__hero">
            <div>
                <p class="resident-dashboard__eyebrow">Cư dân</p>
                <h2 class="resident-dashboard__title">Bảng điều khiển Cư dân</h2>
                <p class="resident-dashboard__intro">
                    Chào mừng bạn trở lại, {{ auth()->user()->name ?? 'Cư dân' }}. Đây là trang tổng quan đơn giản dành cho
                    cư dân.
                </p>
            </div>
            <span class="resident-dashboard__badge">Hoạt động</span>
        </div>
    </section>
@endsection
