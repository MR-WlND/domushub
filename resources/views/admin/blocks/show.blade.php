@extends('layouts.admin.master')

@section('page_title', 'Chi tiết Toà nhà')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@section('content')

    <div class="blocks-page" style="padding: 24px; max-width: 1400px; margin: 0 auto; background: #f8fafc; min-height: calc(100vh - 64px);">
        
        {{-- Breadcrumb & Actions --}}
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
            <div>
                <nav style="display: flex; gap: 8px; font-size: 13px; color: #64748b; margin-bottom: 8px;">
                    <a href="{{ portal_route('dashboard') }}" style="color: #64748b; text-decoration: none;">Trang chủ</a>
                    <span style="color: #cbd5e1;">/</span>
                    <a href="{{ portal_route('blocks.index') }}" style="color: #64748b; text-decoration: none;">Quản lý Hạ tầng</a>
                    <span style="color: #cbd5e1;">/</span>
                    <span style="color: #0f172a; font-weight: 600;">Chi tiết</span>
                </nav>
                <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 12px;">
                    {{ $block->name }}
                    @if (($block->status ?? 'active') === 'active')
                        <span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 600;">Đang vận hành</span>
                    @elseif(($block->status ?? 'active') === 'maintenance')
                        <span style="background: #fef3c7; color: #b45309; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 600;">Bảo trì</span>
                    @else
                        <span style="background: #fee2e2; color: #b91c1c; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 600;">Ngưng hoạt động</span>
                    @endif
                </h1>
            </div>
            
            <div style="display: flex; gap: 12px;">
                <a href="{{ portal_route('blocks.index') }}" style="background: white; border: 1px solid #e2e8f0; color: #475569; padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">Quay lại</a>
                <a href="{{ portal_route('blocks.edit', $block) }}" style="background: #1d4ed8; border: 1px solid #1d4ed8; color: white; padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s; display: flex; align-items: center; gap: 8px;" onmouseover="this.style.background='#1e40af'" onmouseout="this.style.background='#1d4ed8'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    Sửa thông tin
                </a>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div style="background: #dcfce7; color: #166534; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 500; border: 1px solid #bbf7d0;">
                {{ $message }}
            </div>
        @endif

        {{-- Top Info Grid (2 Columns) --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; align-items: start;">
            
            {{-- General Info Card --}}
            <article style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 20px;">
                <div>
                    <h2 style="margin: 0 0 20px 0; font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#64748b" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Thông tin chung
                    </h2>
                    
                    {{-- Image --}}
                    <div style="border-radius: 8px; overflow: hidden; background: #f1f5f9; aspect-ratio: 16/9; display: flex; align-items: center; justify-content: center;">
                        @if($block->image)
                            <img src="{{ asset('storage/' . $block->image) }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        @else
                            <div style="text-align: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="2" style="margin-bottom: 8px; margin-left: auto; margin-right: auto;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                <div style="color: #94a3b8; font-size: 13px; font-weight: 500;">Chưa có ảnh phối cảnh</div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Info --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #f1f5f9; border-left: 3px solid #3b82f6;">
                        <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Mã tòa nhà</div>
                        <div style="font-size: 16px; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ $block->code ?? 'Chưa có' }}</div>
                    </div>
                    <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border: 1px solid #f1f5f9; border-left: 3px solid #10b981;">
                        <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Cấu trúc thiết kế</div>
                        <div style="font-size: 15px; font-weight: 700; color: #0f172a; margin-top: 4px; display: flex; align-items: center; gap: 4px;">
                            <span>{{ $block->total_floors ?? 0 }} nổi</span>
                            <span style="color: #cbd5e1;">|</span>
                            <span>{{ $block->total_basements ?? 0 }} hầm</span>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Right Column (Stats & Amenities) --}}
            <div style="display: flex; flex-direction: column; gap: 24px;">
                
                {{-- Stats Card --}}
                <article style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 20px;">
                    <h2 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#64748b" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002-2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        Tỉ lệ lấp đầy
                    </h2>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div style="background: #f8fafc; border: 1px solid #f1f5f9; padding: 16px; border-radius: 8px;">
                            <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Tổng Tầng</div>
                            <div style="font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ $stats['floors'] }}</div>
                        </div>
                        <div style="background: #f8fafc; border: 1px solid #f1f5f9; padding: 16px; border-radius: 8px;">
                            <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Tổng Căn hộ</div>
                            <div style="font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ $stats['apartments'] }}</div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                        <div style="background: #f0fdf4; border: 1px solid #dcfce7; padding: 16px; border-radius: 8px; display: flex; flex-direction: column; justify-content: center;">
                            <div style="font-size: 12px; font-weight: 700; color: #166534; text-transform: uppercase;">Đang ở</div>
                            <div style="font-size: 22px; font-weight: 800; color: #15803d; margin-top: 4px;">{{ $stats['occupied'] }}</div>
                        </div>
                        <div style="background: #fffbeb; border: 1px solid #fef3c7; padding: 16px; border-radius: 8px; display: flex; flex-direction: column; justify-content: center;">
                            <div style="font-size: 12px; font-weight: 700; color: #b45309; text-transform: uppercase;">Đang trống</div>
                            <div style="font-size: 22px; font-weight: 800; color: #b45309; margin-top: 4px;">{{ $stats['vacant'] }}</div>
                        </div>
                        <div style="background: #fef2f2; border: 1px solid #fee2e2; padding: 16px; border-radius: 8px; display: flex; flex-direction: column; justify-content: center;">
                            <div style="font-size: 12px; font-weight: 700; color: #991b1b; text-transform: uppercase;">Bảo trì</div>
                            <div style="font-size: 22px; font-weight: 800; color: #b91c1c; margin-top: 4px;">{{ $stats['maintenance'] }}</div>
                        </div>
                    </div>
                </article>

                {{-- Amenities Section --}}
                <article style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <h2 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#64748b" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" /></svg>
                        Tiện ích tòa nhà
                    </h2>
                    
                    @php
                        $amenityMap = [
                            'elevator' => ['icon' => '🛗', 'label' => 'Thang máy'],
                            'parking' => ['icon' => 'P', 'label' => 'Hầm gửi xe'],
                            'gym' => ['icon' => '🏋️‍♂️', 'label' => 'Phòng Gym'],
                            'playground' => ['icon' => '😀', 'label' => 'Sân chơi trẻ em'],
                            'pool' => ['icon' => '🏊', 'label' => 'Bể bơi'],
                            'security' => ['icon' => '🛡️', 'label' => 'An ninh 24/7'],
                        ];
                    @endphp
                    
                    <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                        @if(is_array($block->amenities) && count($block->amenities) > 0)
                            @foreach($block->amenities as $amenity)
                                @php
                                    $mapped = $amenityMap[$amenity] ?? ['icon' => '✨', 'label' => ucfirst($amenity)];
                                @endphp
                                <span style="background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 500;">
                                    {{ $mapped['label'] }}
                                </span>
                            @endforeach
                        @else
                            <span style="color: #94a3b8; font-size: 14px;">Tòa nhà chưa cấu hình tiện ích.</span>
                        @endif
                    </div>
                </article>

            </div>
        </div>

        {{-- Floors Table (Card Design) --}}
        <article style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden;">
            
            <div style="padding: 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#64748b" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    Sơ đồ Tầng
                </h2>
                <a href="{{ portal_route('floors.create', ['block_id' => $block->id]) }}" style="background: transparent; color: #2563eb; font-weight: 600; font-size: 14px; text-decoration: none; display: flex; align-items: center; gap: 4px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Thêm tầng
                </a>
            </div>

            <div style="padding: 0; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 16px 24px; text-align: left; font-size: 13px; font-weight: 700; color: #475569;">Tầng</th>
                            <th style="padding: 16px 24px; text-align: left; font-size: 13px; font-weight: 700; color: #475569;">Số căn hộ</th>
                            <th style="padding: 16px 24px; text-align: left; font-size: 13px; font-weight: 700; color: #475569;">Trạng thái</th>
                            <th style="padding: 16px 24px; text-align: right; font-size: 13px; font-weight: 700; color: #475569;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($floors->count() > 0)
                            @foreach ($floors as $floor)
                            <tr style="border-top: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 16px 24px;">
                                    <a href="{{ portal_route('floors.show', $floor->id) }}" style="font-weight: 700; color: #2563eb; font-size: 15px; text-decoration: none; display: block;">{{ $floor->name ?? 'Tầng ' . $floor->floor_number }}</a>
                                    <div style="color: #64748b; font-size: 12px; margin-top: 4px;">Loại: {{ $floor->floor_type_label ?? 'Cư dân' }}</div>
                                </td>
                                <td style="padding: 16px 24px; font-weight: 500; color: #334155; font-size: 14px;">
                                    {{ $floor->apartments_count }} căn
                                </td>
                                <td style="padding: 16px 24px;">
                                    @if (($floor->status ?? 'active') === 'active')
                                        <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #334155;">
                                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #16a34a;"></div>
                                            Hoàn tất
                                        </div>
                                    @elseif(($floor->status ?? 'active') === 'maintenance')
                                        <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #334155;">
                                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #d97706;"></div>
                                            Bảo trì
                                        </div>
                                    @else
                                        <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #334155;">
                                            <div style="width: 8px; height: 8px; border-radius: 50%; background: #dc2626;"></div>
                                            Ngưng
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 16px 24px; text-align: right;">
                                    <div style="display: flex; gap: 16px; justify-content: flex-end; align-items: center;">
                                        <a href="{{ portal_route('floors.edit', $floor->id) }}" style="color: #2563eb; text-decoration: none;" title="Sửa">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </a>
                                        <form action="{{ portal_route('floors.destroy', $floor->id) }}" method="POST" onsubmit="return confirm('Xóa tầng này?')" style="margin:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="color: #dc2626; border: none; background: transparent; cursor: pointer; padding: 0;" title="Xóa">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr style="border-top: 1px solid #f1f5f9;">
                                <td colspan="4" style="padding: 40px; text-align: center; color: #475569; font-size: 14px;">
                                    Chưa có tầng nào cho tòa nhà này
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </article>

    </div>

@endsection

@push('scripts')
@endpush
