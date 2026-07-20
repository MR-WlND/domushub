<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlyStatisticsSeeder extends Seeder
{
    /**
     * Tạo dữ liệu thống kê cho tất cả 12 tháng năm 2026.
     * Bao gồm: Tickets, Residents, Bills, BillDetails, Payments, UtilityMeters
     */
    public function run(): void
    {
        $year = 2026;

        // Lấy danh sách ID hiện có
        $apartmentIds = DB::table('apartments')->pluck('id')->toArray();
        $occupiedAptIds = DB::table('apartments')->where('status', 'occupied')->pluck('id')->toArray();
        $residentUserIds = DB::table('users')->where('role', 'resident')->pluck('id')->toArray();
        $technicianIds = DB::table('users')->where('role', 'technician')->pluck('id')->toArray();
        $adminId = DB::table('users')->where('role', 'admin')->value('id');
        $servicePrices = DB::table('service_prices')->where('status', 'active')->get();

        // ═══════════════════════════════════════════════════════════════
        //  1. TICKETS - Phản ánh: 5-15 phản ánh mỗi tháng
        // ═══════════════════════════════════════════════════════════════
        $this->command->info('Seeding tickets cho 12 tháng...');

        $ticketStatuses = ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $ticketTitles = [
            'Ống nước bị rò rỉ',
            'Đèn hành lang bị hỏng',
            'Thang máy kêu lạ',
            'Cửa kính bị nứt',
            'Điều hòa không hoạt động',
            'Rác không được thu gom',
            'Tiếng ồn từ tầng trên',
            'Cống thoát nước bị tắc',
            'Khóa cửa bị kẹt',
            'Sàn nhà bị trầy xước',
            'Bồn cầu bị nghẹt',
            'Quạt trần kêu to',
            'Ban công bị thấm nước',
            'Bóng đèn cầu thang cháy',
            'Hệ thống phòng cháy lỗi',
        ];

        for ($month = 1; $month <= 12; $month++) {
            // Số ticket tăng dần theo mùa (T6-T8 nhiều hơn do mùa hè)
            $ticketCount = $month >= 6 && $month <= 8 ? rand(10, 15) : rand(5, 10);

            for ($i = 0; $i < $ticketCount; $i++) {
                $day = rand(1, min(28, cal_days_in_month(CAL_GREGORIAN, $month, $year)));
                $createdAt = Carbon::create($year, $month, $day, rand(7, 22), rand(0, 59), rand(0, 59));

                // Xác định status dựa trên tháng (tháng cũ → nhiều completed hơn)
                if ($month < date('m')) {
                    $statusWeights = [0, 0, 1, 15, 2]; // mostly completed
                } elseif ($month == date('m')) {
                    $statusWeights = [3, 3, 4, 6, 1]; // mixed
                } else {
                    $statusWeights = [5, 3, 2, 0, 0]; // mostly pending/assigned
                }
                $status = $this->weightedRandom($ticketStatuses, $statusWeights);

                $priority = $priorities[array_rand($priorities)];
                $aptId = $occupiedAptIds[array_rand($occupiedAptIds)];
                $senderId = $residentUserIds[array_rand($residentUserIds)];
                $handlerId = $status !== 'pending' ? $technicianIds[array_rand($technicianIds)] : null;

                // Rating chỉ cho completed tickets
                $rating = null;
                $feedbackComment = null;
                if ($status === 'completed' && rand(1, 100) <= 75) {
                    $rating = $this->weightedRandom([1, 2, 3, 4, 5], [2, 5, 10, 30, 53]);
                    $comments = [
                        1 => 'Rất không hài lòng, xử lý quá chậm.',
                        2 => 'Chưa giải quyết triệt để vấn đề.',
                        3 => 'Tạm ổn nhưng cần cải thiện.',
                        4 => 'Khá hài lòng với cách xử lý.',
                        5 => 'Rất tốt, xử lý nhanh và chuyên nghiệp!',
                    ];
                    $feedbackComment = $comments[$rating];
                }

                $updatedAt = $status === 'completed'
                    ? (clone $createdAt)->addHours(rand(2, 72))
                    : (clone $createdAt)->addHours(rand(1, 24));

                DB::table('tickets')->insert([
                    'apartment_id'     => $aptId,
                    'sender_id'        => $senderId,
                    'handler_id'       => $handlerId,
                    'title'            => $ticketTitles[array_rand($ticketTitles)],
                    'description'      => 'Phản ánh được tạo tự động cho tháng ' . $month . '/' . $year,
                    'priority'         => $priority,
                    'status'           => $status,
                    'rating'           => $rating,
                    'feedback_comment' => $feedbackComment,
                    'created_at'       => $createdAt,
                    'updated_at'       => $updatedAt,
                ]);
            }
        }

        // ═══════════════════════════════════════════════════════════════
        //  2. RESIDENTS - Cư dân: 2-6 đăng ký mới mỗi tháng
        // ═══════════════════════════════════════════════════════════════
        $this->command->info('Seeding residents cho 12 tháng...');

        $relationships = ['owner', 'tenant', 'family_member'];
        $relWeights = [30, 40, 30];
        $usedPairs = DB::table('residents')->select('user_id', 'apartment_id')
            ->get()
            ->map(fn($r) => $r->user_id . '-' . $r->apartment_id)
            ->toArray();

        for ($month = 1; $month <= 12; $month++) {
            $residentCount = rand(2, 6);
            $added = 0;
            $attempts = 0;
            while ($added < $residentCount && $attempts < 50) {
                $attempts++;
                $aptId = $apartmentIds[array_rand($apartmentIds)];
                $userId = $residentUserIds[array_rand($residentUserIds)];
                $pairKey = $userId . '-' . $aptId;

                if (in_array($pairKey, $usedPairs)) {
                    continue;
                }
                $usedPairs[] = $pairKey;

                $day = rand(1, min(28, cal_days_in_month(CAL_GREGORIAN, $month, $year)));
                $createdAt = Carbon::create($year, $month, $day, rand(8, 17), rand(0, 59), 0);
                $rel = $this->weightedRandom($relationships, $relWeights);

                DB::table('residents')->insert([
                    'user_id'       => $userId,
                    'apartment_id'  => $aptId,
                    'relationship'  => $rel,
                    'start_date'    => $createdAt->toDateString(),
                    'created_at'    => $createdAt,
                    'updated_at'    => $createdAt,
                ]);
                $added++;
            }
        }

        // ═══════════════════════════════════════════════════════════════
        //  3. BILLS + BILL_DETAILS + PAYMENTS - Hóa đơn cho tất cả tháng
        // ═══════════════════════════════════════════════════════════════
        $this->command->info('Seeding bills, bill_details, payments cho 12 tháng...');

        for ($month = 1; $month <= 12; $month++) {
            // Mỗi tháng tạo hóa đơn cho 10-20 căn hộ occupied
            $selectedApts = collect($occupiedAptIds)->shuffle()->take(rand(10, min(20, count($occupiedAptIds))));

            foreach ($selectedApts as $aptId) {
                $dueDate = Carbon::create($year, $month, 25);
                $totalAmount = 0;
                $billDetails = [];

                // Tính tiền cho từng dịch vụ
                foreach ($servicePrices as $sp) {
                    $quantity = 1;
                    $amount = 0;

                    switch ($sp->type) {
                        case 'electricity':
                            $quantity = rand(80, 350); // kWh
                            $amount = $quantity * $sp->unit_price;
                            break;
                        case 'water':
                            $quantity = rand(5, 20); // m3
                            $amount = $quantity * $sp->unit_price;
                            break;
                        case 'management_fee':
                            $quantity = 1;
                            $amount = $sp->unit_price;
                            break;
                        case 'parking':
                            $quantity = rand(0, 2);
                            $amount = $quantity * $sp->unit_price;
                            break;
                        case 'internet':
                            $quantity = rand(0, 1);
                            $amount = $quantity * $sp->unit_price;
                            break;
                        default:
                            $quantity = rand(0, 1);
                            $amount = $quantity * rand(50000, 200000);
                            break;
                    }

                    if ($amount > 0) {
                        $billDetails[] = [
                            'service_price_id' => $sp->id,
                            'quantity'         => $quantity,
                            'amount'           => $amount,
                        ];
                        $totalAmount += $amount;
                    }
                }

                // Xác định trạng thái thanh toán
                if ($month < date('m') - 1) {
                    $statusRoll = rand(1, 100);
                    $billStatus = $statusRoll <= 80 ? 'paid' : ($statusRoll <= 95 ? 'partial_paid' : 'unpaid');
                } elseif ($month == date('m') - 1) {
                    $statusRoll = rand(1, 100);
                    $billStatus = $statusRoll <= 50 ? 'paid' : ($statusRoll <= 80 ? 'partial_paid' : 'unpaid');
                } else {
                    $billStatus = 'unpaid';
                }

                $paidAmount = match($billStatus) {
                    'paid'         => $totalAmount,
                    'partial_paid' => round($totalAmount * rand(30, 70) / 100),
                    default        => 0,
                };

                $createdAt = Carbon::create($year, $month, rand(1, 5), 9, 0, 0);

                $billId = DB::table('bills')->insertGetId([
                    'apartment_id'  => $aptId,
                    'title'         => 'Hóa đơn T' . str_pad($month, 2, '0', STR_PAD_LEFT) . '/' . $year,
                    'billing_month' => $month,
                    'billing_year'  => $year,
                    'due_date'      => $dueDate,
                    'total_amount'  => $totalAmount,
                    'paid_amount'   => $paidAmount,
                    'created_by'    => $adminId,
                    'status'        => $billStatus,
                    'created_at'    => $createdAt,
                    'updated_at'    => $createdAt,
                ]);

                // Chèn bill_details
                foreach ($billDetails as $detail) {
                    DB::table('bill_details')->insert([
                        'bill_id'          => $billId,
                        'service_price_id' => $detail['service_price_id'],
                        'quantity'         => $detail['quantity'],
                        'amount'           => $detail['amount'],
                        'status'           => $billStatus === 'paid' ? 'paid' : 'pending',
                        'created_at'       => $createdAt,
                    ]);
                }

                // Chèn payment nếu đã thanh toán
                if ($paidAmount > 0) {
                    $paymentMethods = ['bank_transfer', 'cash', 'vnpay'];
                    $paidAt = (clone $createdAt)->addDays(rand(3, 20));

                    DB::table('payments')->insert([
                        'bill_id'          => $billId,
                        'amount'           => $paidAmount,
                        'payment_method'   => $paymentMethods[array_rand($paymentMethods)],
                        'transaction_code' => 'TXN' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . rand(10000, 99999),
                        'receipt_code'     => 'RC' . $year . str_pad($month, 2, '0', STR_PAD_LEFT) . rand(1000, 9999),
                        'status'           => 'success',
                        'paid_at'          => $paidAt,
                        'payer_name'       => 'Cư dân căn hộ #' . $aptId,
                        'recorded_by'      => $adminId,
                        'created_at'       => $paidAt,
                        'updated_at'       => $paidAt,
                    ]);
                }
            }
        }

        // ═══════════════════════════════════════════════════════════════
        //  4. UTILITY_METERS - Chỉ số điện nước cho tất cả tháng
        // ═══════════════════════════════════════════════════════════════
        $this->command->info('Seeding utility_meters cho 12 tháng...');

        foreach ($occupiedAptIds as $aptId) {
            $prevElec = rand(100, 500);
            $prevWater = rand(10, 50);

            for ($month = 1; $month <= 12; $month++) {
                $day = rand(1, 5);
                $recordedAt = Carbon::create($year, $month, $day, rand(8, 12), 0, 0);
                $status = $month < date('m') ? 'approved' : 'pending';

                // Điện
                $elecUsage = rand(80, 350);
                $newElec = $prevElec + $elecUsage;
                DB::table('utility_meters')->insert([
                    'apartment_id'    => $aptId,
                    'type'            => 'electricity',
                    'old_value'       => $prevElec,
                    'new_value'       => $newElec,
                    'usage_amount'    => $elecUsage,
                    'record_month'    => $month,
                    'record_year'     => $year,
                    'recorded_by'     => $adminId,
                    'status'          => $status,
                    'created_at'      => $recordedAt,
                    'updated_at'      => $recordedAt,
                ]);
                $prevElec = $newElec;

                // Nước
                $waterUsage = rand(5, 20);
                $newWater = $prevWater + $waterUsage;
                DB::table('utility_meters')->insert([
                    'apartment_id'    => $aptId,
                    'type'            => 'water',
                    'old_value'       => $prevWater,
                    'new_value'       => $newWater,
                    'usage_amount'    => $waterUsage,
                    'record_month'    => $month,
                    'record_year'     => $year,
                    'recorded_by'     => $adminId,
                    'status'          => $status,
                    'created_at'      => $recordedAt,
                    'updated_at'      => $recordedAt,
                ]);
                $prevWater = $newWater;
            }
        }

        $this->command->info('✅ Đã tạo dữ liệu thống kê cho tất cả 12 tháng năm ' . $year . '!');
    }

    /**
     * Chọn ngẫu nhiên có trọng số
     */
    private function weightedRandom(array $items, array $weights)
    {
        $totalWeight = array_sum($weights);
        $rand = rand(1, $totalWeight);
        $cumulative = 0;

        foreach ($items as $i => $item) {
            $cumulative += $weights[$i];
            if ($rand <= $cumulative) {
                return $item;
            }
        }

        return end($items);
    }
}
