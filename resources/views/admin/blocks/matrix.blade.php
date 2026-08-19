@extends('layouts.admin.master')

@section('page_title', 'Sơ đồ Tòa nhà')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@section('content')

    <div class="matrix-page" style="padding: 24px; max-width: 1400px; margin: 0 auto; min-height: calc(100vh - 64px);">
        
        {{-- Breadcrumb & Actions --}}
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
            <div>
                <nav style="display: flex; gap: 8px; font-size: 13px; color: #64748b; margin-bottom: 8px;">
                    <a href="{{ portal_route('dashboard') }}" style="color: #64748b; text-decoration: none;">Trang chủ</a>
                    <span style="color: #cbd5e1;">/</span>
                    <a href="{{ portal_route('blocks.index') }}" style="color: #64748b; text-decoration: none;">Quản lý Hạ tầng</a>
                    <span style="color: #cbd5e1;">/</span>
                    <a href="{{ portal_route('blocks.show', $block) }}" style="color: #64748b; text-decoration: none;">{{ $block->name }}</a>
                    <span style="color: #cbd5e1;">/</span>
                    <span style="color: #0f172a; font-weight: 600;">Sơ đồ</span>
                </nav>
                <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 12px;">
                    Sơ đồ Tòa nhà {{ $block->name }}
                </h1>
            </div>
            
            <div style="display: flex; gap: 12px;">
                <a href="{{ portal_route('blocks.show', $block) }}" style="background: white; border: 1px solid #e2e8f0; color: #475569; padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">Quay lại</a>
            </div>
        </div>

        {{-- Legend & Stats --}}
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 24px;">
            
            <div style="display: flex; gap: 24px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 16px; height: 16px; border-radius: 4px; background: #10b981; border: 1px solid #059669;"></div>
                    <span style="font-size: 14px; font-weight: 600; color: #334155;">Đang ở ({{ $stats['occupied'] }})</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 16px; height: 16px; border-radius: 4px; background: #f59e0b; border: 1px solid #d97706;"></div>
                    <span style="font-size: 14px; font-weight: 600; color: #334155;">Đang trống ({{ $stats['vacant'] }})</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <div style="width: 16px; height: 16px; border-radius: 4px; background: #ef4444; border: 1px solid #dc2626;"></div>
                    <span style="font-size: 14px; font-weight: 600; color: #334155;">Bảo trì ({{ $stats['maintenance'] }})</span>
                </div>
            </div>

            <div style="display: flex; gap: 24px; font-size: 14px; font-weight: 600; color: #64748b;">
                <div>Tổng số tầng: <span style="color: #0f172a; font-weight: 800;">{{ $stats['floors'] }}</span></div>
                <div>Tổng số căn: <span style="color: #0f172a; font-weight: 800;">{{ $stats['apartments'] }}</span></div>
            </div>
        </div>

        {{-- Matrix Grid --}}
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow-x: auto;">
            
            @if ($floors->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 16px; min-width: max-content;">
                    @foreach ($floors as $floor)
                        <div style="display: flex; align-items: stretch; gap: 16px;">
                            {{-- Floor Header --}}
                            <div style="width: 120px; flex-shrink: 0; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 12px;">
                                <div style="font-size: 15px; font-weight: 800; color: #0f172a;">{{ $floor->name ?? 'Tầng ' . $floor->floor_number }}</div>
                                <div style="font-size: 12px; font-weight: 500; color: #64748b; margin-top: 2px;">{{ $floor->apartments->count() }} căn</div>
                            </div>

                            {{-- Apartments --}}
                            <div style="display: flex; flex-wrap: wrap; gap: 12px; flex-grow: 1;">
                                @forelse ($floor->apartments as $apt)
                                    @php
                                        // Colors based on status
                                        if ($apt->status === 'occupied') {
                                            $bg = '#10b981'; $border = '#059669'; $text = '#ffffff';
                                        } elseif ($apt->status === 'maintenance') {
                                            $bg = '#ef4444'; $border = '#dc2626'; $text = '#ffffff';
                                        } else {
                                            $bg = '#f59e0b'; $border = '#d97706'; $text = '#ffffff';
                                        }
                                    @endphp
                                    <a href="{{ portal_route('apartments.show', $apt->id) }}" title="{{ $apt->apartment_number }} - {{ $apt->area }}m2" style="text-decoration: none;">
                                        <div style="width: 70px; height: 70px; background: {{ $bg }}; border: 1px solid {{ $border }}; border-radius: 8px; display: flex; flex-direction: column; justify-content: center; align-items: center; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                            <div style="font-size: 15px; font-weight: 800; color: {{ $text }};">{{ $apt->apartment_number }}</div>
                                        </div>
                                    </a>
                                @empty
                                    <div style="display: flex; align-items: center; color: #94a3b8; font-size: 13px; font-style: italic;">
                                        Không có căn hộ nào ở tầng này.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 40px; color: #64748b; font-size: 15px;">
                    Chưa có dữ liệu tầng/căn hộ cho tòa nhà này.
                </div>
            @endif

        </div>

    </div>

@endsection
