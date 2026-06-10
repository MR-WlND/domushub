@extends('layouts.resident.master')

@section('title', 'Lịch sử thanh toán – DomusHub')

@section('content')
<div class="inv-container">

    <div class="inv-header">
        <div>
            <p class="inv-eyebrow">Tài chính căn hộ</p>
            <h1 class="inv-title">Lịch sử thanh toán</h1>
        </div>
        <a href="{{ route('resident.invoices.index') }}" class="inv-btn inv-btn--primary" style="text-decoration: none;">
            ← Hóa đơn
        </a>
    </div>

    @if($payments->isEmpty())
        <div class="inv-empty">
            <div class="inv-empty__icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
            </div>
            <h3 class="inv-empty__title">Chưa có giao dịch nào</h3>
            <p class="inv-empty__desc">Các giao dịch thanh toán thành công sẽ hiển thị tại đây.</p>
        </div>
    @else
        {{-- Tổng kết --}}
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 16px 20px; margin-bottom: 20px; display: flex; gap: 32px; flex-wrap: wrap;">
            <div>
                <div style="font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em;">Tổng giao dịch</div>
                <div style="font-size: 1.4rem; font-weight: 700; color: #1e40af;">{{ $payments->total() }}</div>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em;">Tổng đã thanh toán</div>
                <div style="font-size: 1.4rem; font-weight: 700; color: #15803d;">{{ number_format($payments->sum('amount')) }}đ</div>
            </div>
        </div>

        {{-- Bảng sao kê --}}
        <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="text-align: left; padding: 11px 16px; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #e2e8f0;">Mã GD</th>
                        <th style="text-align: left; padding: 11px 16px; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #e2e8f0;">Hóa đơn</th>
                        <th style="text-align: left; padding: 11px 16px; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #e2e8f0;">Phương thức</th>
                        <th style="text-align: right; padding: 11px 16px; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #e2e8f0;">Số tiền</th>
                        <th style="text-align: left; padding: 11px 16px; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #e2e8f0;">Thời gian</th>
                        <th style="text-align: center; padding: 11px 16px; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #e2e8f0;">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 13px 16px; font-size: 0.8rem; font-family: monospace; color: #3b82f6; font-weight: 600;">
                            {{ $payment->transaction_code ?? '—' }}
                        </td>
                        <td style="padding: 13px 16px; font-size: 0.85rem; color: #1e293b;">
                            {{ $payment->invoice->title ?? '—' }}
                            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 2px;">
                                Tháng {{ optional($payment->invoice)->billing_month?->format('m/Y') ?? '—' }}
                            </div>
                        </td>
                        <td style="padding: 13px 16px;">
                            @php
                                $method = match($payment->payment_method) {
                                    'mbbank' => ['label' => 'MB Bank', 'bg' => '#eff6ff', 'color' => '#1d4ed8'],
                                    'momo'   => ['label' => 'Momo',    'bg' => '#fdf4ff', 'color' => '#7e22ce'],
                                    'cash'   => ['label' => 'Tiền mặt','bg' => '#f0fdf4', 'color' => '#15803d'],
                                    default  => ['label' => ucfirst($payment->payment_method), 'bg' => '#f8fafc', 'color' => '#475569'],
                                };
                            @endphp
                            <span style="font-size: 0.75rem; font-weight: 600; padding: 3px 8px; border-radius: 4px; background: {{ $method['bg'] }}; color: {{ $method['color'] }};">
                                {{ $method['label'] }}
                            </span>
                        </td>
                        <td style="padding: 13px 16px; font-size: 0.95rem; font-weight: 700; color: #15803d; text-align: right;">
                            +{{ number_format($payment->amount) }}đ
                        </td>
                        <td style="padding: 13px 16px; font-size: 0.82rem; color: #64748b;">
                            {{ $payment->paid_at ? $payment->paid_at->format('H:i d/m/Y') : '—' }}
                        </td>
                        <td style="padding: 13px 16px; text-align: center;">
                            <span style="font-size: 0.72rem; font-weight: 600; padding: 3px 8px; border-radius: 4px; background: #dcfce7; color: #15803d;">
                                ✓ Thành công
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($payments->hasPages())
            <div style="padding: 14px 16px; border-top: 1px solid #e2e8f0; display: flex; justify-content: center; gap: 5px;">
                @if(!$payments->onFirstPage())
                    <a href="{{ $payments->previousPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; border: 1px solid #e2e8f0; color: #475569; background: #fff; text-decoration: none;">‹ Trước</a>
                @endif
                @foreach($payments->getUrlRange(max(1,$payments->currentPage()-2), min($payments->lastPage(),$payments->currentPage()+2)) as $page => $url)
                    <a href="{{ $url }}" style="padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; border: 1px solid {{ $page == $payments->currentPage() ? '#2563eb' : '#e2e8f0' }}; color: {{ $page == $payments->currentPage() ? '#fff' : '#475569' }}; background: {{ $page == $payments->currentPage() ? '#2563eb' : '#fff' }}; text-decoration: none;">{{ $page }}</a>
                @endforeach
                @if($payments->hasMorePages())
                    <a href="{{ $payments->nextPageUrl() }}" style="padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; border: 1px solid #e2e8f0; color: #475569; background: #fff; text-decoration: none;">Sau ›</a>
                @endif
            </div>
            @endif
        </div>
    @endif
</div>
@endsection
