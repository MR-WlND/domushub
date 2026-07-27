@extends('layouts.resident.master')

@section('title', 'Hóa đơn – DomusHub')

@section('content')
<div class="inv-container">

    {{-- HEADER --}}
    <div class="inv-header">
        <div>
            <p class="inv-eyebrow">Tài chính căn hộ</p>
            <h1 class="inv-title">Lịch sử hóa đơn</h1>
        </div>
    </div>

    {{-- SYSTEM ALERTS --}}
    @if(session('success'))
        <div class="inv-alert inv-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="inv-alert inv-alert--error">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif
    @push('styles')
    <style>
        .inv-chart-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .inv-chart-card__header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        .inv-chart-card__title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 4px 0;
        }
        .inv-chart-card__subtitle {
            font-size: 0.88rem;
            color: #64748b;
            margin: 0;
        }
        .inv-chart-card__badge {
            font-size: 0.78rem;
            font-weight: 600;
            color: #0284c7;
            background: #e0f2fe;
            padding: 6px 12px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
        }
    </style>
    @endpush

    {{-- WATER CONSUMPTION CHART CARD --}}
    <div class="inv-chart-card">
        <div class="inv-chart-card__header">
            <div>
                <h3 class="inv-chart-card__title">Biểu đồ tiêu thụ nước</h3>
                <p class="inv-chart-card__subtitle">Lượng nước tiêu thụ (m³) ghi nhận từ hệ thống</p>
            </div>
            <div class="inv-chart-card__badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 6px;"><path d="M12 2c-5.33 7.23-8 11.23-8 14a8 8 0 1 0 16 0c0-2.77-2.67-6.77-8-14zm0 15c-1.66 0-3-1.34-3-3 0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5c0 .55.45 1 1 1s1-.45 1-1c0-2.21-1.79-4-4-4-.55 0-1-.45-1-1s.45-1 1-1c3.31 0 6 2.69 6 6 0 1.66-1.34 3-3 3z"/></svg>
                Đồng hồ Nước
            </div>
        </div>
        <div class="inv-chart-wrap" style="position: relative; height: 260px; width: 100%; display: flex; align-items: center; justify-content: center;">
            @if(empty($waterChartData))
                <div style="text-align: center; color: #94a3b8; font-size: 0.95rem; font-weight: 500; padding: 20px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16" style="margin-bottom: 12px; color: #cbd5e1; display: block; margin-left: auto; margin-right: auto;"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5a.5.5 0 0 1-.771-.409v-5A.5.5 0 0 1 6.271 5.055z"/></svg>
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

    {{-- EMPTY STATE --}}
    @if($invoices->isEmpty())
        <div class="inv-empty">
            <div class="inv-empty__icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <h3 class="inv-empty__title">Không tìm thấy hóa đơn</h3>
            <p class="inv-empty__desc">Hiện tại căn hộ của bạn không có hóa đơn nào phù hợp với bộ lọc đã chọn.</p>
        </div>
    @else
        {{-- INVOICES LIST --}}
        <div class="inv-list">
            @foreach($invoices as $invoice)
                <div class="inv-card inv-card--paid">
                    <div class="inv-card__accent"></div>



                    {{-- Body info --}}
                    <div class="inv-card__body">
                        <h3 class="inv-card__title">{{ $invoice->title }}</h3>
                        <p class="inv-card__subtitle">
                            Căn hộ: {{ $invoice->apartment->apartment_number ?? '—' }} 
                            ({{ optional(optional($invoice->apartment)->floor)->block->name ?? '' }})
                        </p>
                        
                        {{-- Chi tiết hóa đơn --}}
                        @if($invoice->details->isNotEmpty())
                            <div class="inv-card__details">
                                @foreach($invoice->details as $detail)
                                    <span class="inv-card__detail-badge">
                                        {{ $detail->servicePrice->name ?? 'Phí khác' }}: {{ number_format($detail->amount) }}đ
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <div class="inv-card__meta">
                            <span class="inv-meta-item">
                                Kỳ thanh toán: {{ str_pad($invoice->billing_month->month, 2, '0', STR_PAD_LEFT) }}/{{ $invoice->billing_year }}
                            </span>
                            @if($invoice->status === 'paid' && $invoice->payments->isNotEmpty())
                                @php $payment = $invoice->payments->first(); @endphp
                                <span class="inv-meta-item">
                                    Thanh toán lúc: {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '—' }}
                                </span>
                                @if($payment->payment_method === 'vnpay')
                                <span class="inv-meta-item">
                                    Mã GD: <strong style="color: #0f172a;">{{ $payment->vnp_txn_ref ?: explode('|', $payment->transaction_code)[0] }}</strong>
                                </span>
                                @endif
                            @else
                                <span class="inv-meta-item">
                                    Hạn chót: {{ $invoice->due_date->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Amount & actions --}}
                    <div class="inv-card__right">
                        <div class="inv-card__price">
                            {{ number_format($invoice->total_amount) }}đ
                        </div>

                        <div class="inv-card__status-box">
                            <span class="inv-status inv-status--paid">Đã thanh toán</span>
                        </div>

                        <a href="{{ route('resident.invoices.show', $invoice->id) }}" class="inv-btn" style="background-color: #f1f5f9; color: #475569;">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        @if($invoices->hasPages())
            <div class="inv-pagination">
                {{ $invoices->links() }}
            </div>
        @endif
    @endif
</div>

@include('resident.invoices.partials.style')
@endsection
