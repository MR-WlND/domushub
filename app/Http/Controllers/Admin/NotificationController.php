<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function recent(Request $request)
    {
        // Lấy các payment success trong 24h gần nhất
        $since = now()->subHours(24);

        $payments = Payment::with(['invoice.apartment'])
            ->where('status', 'success')
            ->where('paid_at', '>=', $since)
            ->orderByDesc('paid_at')
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'id'               => $p->id,
                'transaction_code' => $p->transaction_code,
                'amount'           => number_format($p->amount) . 'đ',
                'method'           => match($p->payment_method) {
                    'mbbank' => 'MB Bank',
                    'momo'   => 'Momo',
                    'cash'   => 'Tiền mặt',
                    default  => $p->payment_method,
                },
                'invoice_title'    => $p->invoice->title ?? '—',
                'apartment'        => $p->invoice->apartment->apartment_number ?? '—',
                'paid_at'          => $p->paid_at?->diffForHumans(),
                'paid_at_full'     => $p->paid_at?->format('H:i d/m/Y'),
            ]);

        // Đếm số payment mới kể từ lần admin check cuối (lưu trong session)
        $lastChecked = session('admin_notif_last_checked', now()->subHours(24));
        $unreadCount = Payment::where('status', 'success')
            ->where('paid_at', '>=', $lastChecked)
            ->count();

        return response()->json([
            'payments'     => $payments,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markRead()
    {
        session(['admin_notif_last_checked' => now()]);
        return response()->json(['success' => true]);
    }
}
