@extends('layouts.resident.master')

@section('title', 'Lịch sử hóa đơn – DomusHub')

@section('content')
<div class="pay-dashboard">
    @push('styles')
    <style>
    @media (max-width: 1024px) {
        div.pay-card.chart-card {
            background: #fff !important;
            box-shadow: 0px 4px 20px rgba(0, 35, 111, 0.03) !important;
            border: 1px solid #f1f5f9 !important;
            border-radius: 16px !important;
            padding: 16px !important;
        }
        tr.pay-row.history-row {
            display: block !important;
            padding: 16px !important;
        }
        tr.pay-row.history-row > td:not(.desktop-only) {
            border: none !important;
            padding: 0 !important;
            display: block !important;
        }
        tr.pay-row.history-row > td.desktop-only {
            display: none !important;
        }
    }
    @media (max-width: 768px) {
        .inv-chart-wrap { padding: 16px 12px !important; }
    }
    </style>
    @endpush

    {{-- HEADER --}}
    <div class="pay-dashboard__header flex-between" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 class="pay-page__title">Lịch sử hóa đơn</h1>
            <p class="pay-page__subtitle">Xem lại các khoản phí bạn đã thanh toán.</p>
        </div>
        <div class="desktop-only">
            <a href="{{ route('resident.invoices.index') }}" class="btn-history-link" style="background: #ffffff; border: 1px solid #e2e8f0; padding: 10px 20px; border-radius: 6px; color: #475569; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem; transition: background 0.2s;">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    {{-- SYSTEM ALERTS --}}
    @if(session('success'))
        <div class="pay-alert pay-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="pay-alert pay-alert--error">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- WATER CONSUMPTION CHART CARD --}}
    <div class="pay-card chart-card" style="margin-bottom: 24px;">
        <div class="pay-card__header flex-between" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
            <div style="flex: 1;">
                <h2 class="pay-card__title">Biểu đồ tiêu thụ nước</h2>
                <p style="font-size: 0.85rem; color: #64748b; margin: 4px 0 0 0;">Lượng nước tiêu thụ (m³) ghi nhận từ hệ thống</p>
            </div>
            <div style="background: #e0f2fe; color: #0284c7; padding: 6px 12px; border-radius: 99px; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;">
                <i class="fa-solid fa-droplet" style="margin-right: 6px;"></i> Đồng hồ Nước
            </div>
        </div>
        <div class="inv-chart-wrap" style="padding: 24px; position: relative; height: 260px; width: 100%; display: flex; align-items: center; justify-content: center;">
            @if(empty($waterChartData))
                <div style="text-align: center; color: #94a3b8; font-size: 0.95rem; font-weight: 500; padding: 20px;">
                    <i class="fa-solid fa-chart-line" style="font-size: 40px; color: #cbd5e1; display: block; margin: 0 auto 12px auto;"></i>
                    Chưa có dữ liệu tiêu thụ nước được ghi nhận cho căn hộ của bạn.
                </div>
            @else
                <canvas id="waterUsageChart"></canvas>
            @endif
        </div>
    </div>

    @if(!empty($waterChartData))
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('waterUsageChart').getContext('2d');
            
            // Gradient background for chart line
            const gradient = ctx.createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, 'rgba(14, 165, 233, 0.3)');
            gradient.addColorStop(1, 'rgba(14, 165, 233, 0.02)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($waterChartLabels) !!},
                    datasets: [{
                        label: 'Nước tiêu thụ (m³)',
                        data: {!! json_encode($waterChartData) !!},
                        borderColor: '#0284c7',
                        borderWidth: 3,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#0284c7',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return `Tiêu thụ: ${context.parsed.y} m³`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                color: '#64748b',
                                callback: function(value) {
                                    return value + ' m³';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                color: '#64748b'
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
    @endif

    {{-- INVOICES LIST --}}
    <div class="pay-card">
        <div class="pay-card__header">
            <h2 class="pay-card__title">Hóa đơn đã thanh toán</h2>
        </div>
        
        @if($invoices->isEmpty())
            <div class="pay-empty">
                <div class="pay-empty__icon"><i class="fa-regular fa-folder-open"></i></div>
                <h3 class="pay-empty__title">Không tìm thấy hóa đơn</h3>
                <p class="pay-empty__desc">Hiện tại căn hộ của bạn không có hóa đơn nào phù hợp với bộ lọc đã chọn.</p>
            </div>
        @else
            <div class="pay-table-wrapper">
                <table class="pay-table">
                    <thead>
                        <tr>
                            <th>HÓA ĐƠN</th>
                            <th>KỲ HẠN</th>
                            <th class="text-right">SỐ TIỀN (VNĐ)</th>
                            <th style="width: 40px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr class="pay-row history-row" onclick="window.location='{{ route('resident.invoices.show', $invoice->id) }}'" style="cursor: pointer;">
                                <!-- DESKTOP VIEW -->
                                <td class="td-service desktop-only">
                                    <div class="pay-service-cell" style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 36px; height: 36px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #64748b;">
                                            <i class="fa-solid fa-file-invoice"></i>
                                        </div>
                                        <div style="display: flex; flex-direction: column; justify-content: center;">
                                            <div style="font-weight: 600; color: #0f172a; font-size: 1rem; margin-bottom: 2px;">
                                                <span>{{ $invoice->title }}</span>
                                            </div>
                                            <div style="color: #64748b; font-size: 0.85rem;">
                                                Căn hộ: {{ $invoice->apartment->apartment_number ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="desktop-only" style="font-size: 0.9rem;">
                                    <div style="color: #64748b; font-size: 0.9rem; margin-bottom: 2px;">Kỳ hạn: Tháng {{ str_pad($invoice->billing_month->month, 2, '0', STR_PAD_LEFT) }}/{{ $invoice->billing_year }}</div>
                                    @if($invoice->status === 'paid' && $invoice->payments->isNotEmpty())
                                        @php $payment = $invoice->payments->first(); @endphp
                                        <div style="color: #475569; font-size: 0.85rem;">TT: {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '—' }}</div>
                                    @else
                                        <div style="color: #475569; font-size: 0.85rem;">Hạn: {{ $invoice->due_date->format('d/m/Y') }}</div>
                                    @endif
                                </td>
                                <td class="td-price desktop-only">
                                    <div class="pay-amount-cell" style="display: flex; flex-direction: column; align-items: flex-end; text-align: right; width: 100%;">
                                        <div class="pay-amount-val" style="white-space: nowrap; font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 6px;">
                                            {{ number_format($invoice->total_amount, 0, ',', '.') }} đ
                                        </div>
                                        <span class="pay-status-badge badge-paid" style="white-space: nowrap; background: #e2e8f0; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.5px;">ĐÃ THANH TOÁN</span>
                                    </div>
                                </td>
                                <td class="text-center text-muted desktop-only">
                                    <i class="fa-solid fa-chevron-right" style="color: #94a3b8;"></i>
                                </td>

                                <!-- MOBILE VIEW -->
                                <td class="mobile-only" style="width: 100%;">
                                    <div style="display: flex; gap: 14px; align-items: flex-start; width: 100%;">
                                        <div style="width: 44px; height: 44px; background: #f1f5f9; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #64748b; flex-shrink: 0;">
                                            <i class="fa-solid fa-file-invoice" style="font-size: 1.1rem;"></i>
                                        </div>
                                        <div style="flex: 1; display: flex; flex-direction: column;">
                                            <div style="font-weight: 500; font-size: 0.95rem; color: #0f172a; margin-bottom: 6px; line-height: 1.4;">
                                                {{ $invoice->title }}
                                            </div>
                                            <div style="color: #94a3b8; font-size: 0.8rem; margin-bottom: 6px;">
                                                Căn hộ: {{ $invoice->apartment->apartment_number ?? 'N/A' }}
                                            </div>
                                            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                                <div style="color: #94a3b8; font-size: 0.8rem; line-height: 1.6;">
                                                    <div>Kỳ hạn: Tháng {{ str_pad($invoice->billing_month->month, 2, '0', STR_PAD_LEFT) }}/{{ $invoice->billing_year }}</div>
                                                    @if($invoice->status === 'paid' && $invoice->payments->isNotEmpty())
                                                        @php $payment = $invoice->payments->first(); @endphp
                                                        <div>TT: {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '—' }}</div>
                                                    @else
                                                        <div>Hạn: {{ $invoice->due_date->format('d/m/Y') }}</div>
                                                    @endif
                                                </div>
                                                <i class="fa-solid fa-chevron-right" style="color: #cbd5e1; font-size: 1rem;"></i>
                                            </div>
                                            
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
                                                <div style="font-size: 1.15rem; font-weight: 700; color: #0f172a;">
                                                    {{ number_format($invoice->total_amount, 0, ',', '.') }} đ
                                                </div>
                                                <span style="background: #f8fafc; color: #475569; padding: 6px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px;">ĐÃ THANH TOÁN</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- PAGINATION --}}
            @if($invoices->hasPages())
                <div style="padding: 16px 24px; border-top: 1px solid #f1f5f9;">
                    {{ $invoices->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

@include('resident.invoices.partials.style')
@endsection
