@extends('layouts.admin.master')

@section('page_title', 'Chỉnh sửa Hóa đơn ' . $invoice->invoice_code)

@section('content')
<div class="invoice-edit-page" style="max-width: 900px; margin: 0 auto; padding: 10px 0;">
    {{-- Header with back button --}}
    <div class="detail-header-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <a href="{{ portal_route('invoices.show', $invoice) }}" class="btn-back" style="display: inline-flex; align-items: center; gap: 8px; color: #00236f; text-decoration: none; font-weight: 600; font-size: 0.95rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Quay lại chi tiết
        </a>
    </div>

    {{-- Main Edit Card --}}
    <div class="detail-card" style="background-color: #fff; border: 1px solid #cbd5e1; border-radius: 12px; box-shadow: 0px 4px 12px rgba(30,58,138,.05); overflow: hidden; padding: 24px;">
        <h2 style="font-size: 1.3rem; font-weight: 700; color: #00236f; margin: 0 0 24px 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">Chỉnh sửa hóa đơn #{{ $invoice->invoice_code }}</h2>

        <form method="POST" action="{{ portal_route('invoices.update', $invoice) }}">
            @csrf
            @method('PUT')

            {{-- General Invoice Meta fields --}}
            <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px;">
                <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 0.8rem; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.04em;">Tiêu đề hóa đơn</label>
                    <input type="text" name="title" value="{{ old('title', $invoice->title) }}" required class="form-input" style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; color: #0f172a; outline: none; background: #fff;">
                    @error('title')
                        <span style="font-size: 0.8rem; color: #ba1a1a;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="display: flex; flex-direction: column; gap: 6px;">
                    <label style="font-size: 0.8rem; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.04em;">Hạn thanh toán</label>
                    <input type="date" name="due_date" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required class="form-input" style="padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; color: #0f172a; outline: none; background: #fff;">
                    @error('due_date')
                        <span style="font-size: 0.8rem; color: #ba1a1a;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Line Items Table --}}
            <h3 class="section-title" style="font-size: 1.05rem; font-weight: 700; color: #00236f; margin: 0 0 16px 0;">Chi tiết các dịch vụ / Mức phí</h3>
            <div style="overflow-x: auto; margin-bottom: 24px; border: 1px solid #e2e8f0; border-radius: 8px;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <th style="padding: 12px 16px; font-size: 0.78rem; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.05em;">Tên phí / Dịch vụ</th>
                            <th style="padding: 12px 16px; font-size: 0.78rem; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; width: 120px;">Số lượng</th>
                            <th style="padding: 12px 16px; font-size: 0.78rem; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; width: 160px;">Thành tiền (đ)</th>
                            <th style="padding: 12px 16px; font-size: 0.78rem; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.05em;">Ghi chú dòng phí</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->details as $index => $detail)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 14px 16px; font-size: 0.92rem; font-weight: 600; color: #0f172a; vertical-align: middle;">
                                    {{ $detail->servicePrice->name ?? ($detail->note ?? 'Phí phát sinh') }}
                                    <input type="hidden" name="details[{{ $index }}][id]" value="{{ $detail->id }}">
                                </td>
                                <td style="padding: 10px 16px; vertical-align: middle;">
                                    <input type="number" step="any" name="details[{{ $index }}][quantity]" value="{{ old('details.'.$index.'.quantity', $detail->quantity) }}" required class="form-input" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; text-align: center;">
                                </td>
                                <td style="padding: 10px 16px; vertical-align: middle;">
                                    <input type="number" step="1" name="details[{{ $index }}][amount]" value="{{ old('details.'.$index.'.amount', (int)$detail->amount) }}" required class="form-input" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; text-align: right; font-weight: 700; color: #0f172a;">
                                </td>
                                <td style="padding: 10px 16px; vertical-align: middle;">
                                    <input type="text" name="details[{{ $index }}][note]" value="{{ old('details.'.$index.'.note', $detail->note) }}" class="form-input" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;" placeholder="Nhập ghi chú điều chỉnh...">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer Action Buttons --}}
            <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                <a href="{{ portal_route('invoices.show', $invoice) }}" class="btn-cancel" style="padding: 10px 20px; border-radius: 8px; border: 1px solid #cbd5e1; background: #fff; color: #475569; text-decoration: none; font-size: 0.9rem; font-weight: 600; text-align: center; cursor: pointer; transition: all 0.2s;">
                    Hủy bỏ
                </a>
                <button type="submit" class="btn-submit" style="padding: 10px 24px; border-radius: 8px; border: none; background: #00236f; color: #fff; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(0, 35, 111, 0.2);">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .form-input:focus {
        border-color: #00236f !important;
        box-shadow: 0 0 0 2px rgba(0, 35, 111, 0.1) !important;
    }
    .btn-cancel:hover {
        background-color: #f8fafc !important;
        color: #0f172a !important;
    }
    .btn-submit:hover {
        background-color: #1d4ed8 !important;
    }
</style>
@endsection
