<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        // ── Đảm bảo có admin user ──────────────────────────────────
        $admin = User::first();
        if (! $admin) {
            $admin = User::create([
                'name'              => 'Quản Trị Viên',
                'email'             => 'admin@example.com',
                'phone'             => '0900000001',
                'password'          => Hash::make('password123'),
                'role'              => 'admin',
                'status'            => 'active',
                'email_verified_at' => now(),
            ]);
            $this->command->info('Created admin user.');
        }

        // ── Đảm bảo có Block/Floor/Apartment data ─────────────────
        if (Apartment::count() === 0) {
            $this->command->info('Seeding blocks, floors, apartments...');
            $this->seedBuilding();
        }

        // ── Seed Invoices ──────────────────────────────────────────
        if (Invoice::count() > 0) {
            $this->command->warn('Invoices already exist. Skipping.');
            return;
        }

        $apartments = Apartment::all();
        $types      = ['electricity', 'water', 'management_fee', 'parking', 'other'];
        $counter    = 1;

        for ($monthsAgo = 5; $monthsAgo >= 0; $monthsAgo--) {
            $billingMonth = Carbon::now()->subMonths($monthsAgo)->startOfMonth();
            $dueDate      = $billingMonth->copy()->addDays(20);

            foreach ($apartments->take(8) as $apartment) {
                foreach ($types as $type) {
                    [$amount, $items] = $this->generateAmountAndItems($type, $billingMonth);

                    $statusRoll = rand(1, 10);
                    if ($monthsAgo === 0) {
                        $status = $statusRoll <= 4 ? 'paid' : 'unpaid';
                    } elseif ($monthsAgo === 1) {
                        $status = $statusRoll <= 7 ? 'paid' : 'overdue';
                    } else {
                        $status = $statusRoll <= 9 ? 'paid' : 'overdue';
                    }

                    $paidAt = $status === 'paid'
                        ? $billingMonth->copy()->addDays(rand(1, 18))
                        : null;

                    $code = sprintf('INV-%d-%04d', $billingMonth->year, $counter++);

                    $invoice = Invoice::create([
                        'apartment_id'   => $apartment->id,
                        'created_by'     => $admin->id,
                        'invoice_code'   => $code,
                        'title'          => Invoice::typeLabel($type) . ' tháng ' . $billingMonth->format('m/Y'),
                        'type'           => $type,
                        'amount'         => $amount,
                        'billing_month'  => $billingMonth->toDateString(),
                        'due_date'       => $dueDate->toDateString(),
                        'status'         => $status,
                        'paid_at'        => $paidAt,
                        'payment_method' => $status === 'paid' ? (rand(0, 1) ? 'transfer' : 'cash') : null,
                    ]);

                    foreach ($items as $item) {
                        InvoiceItem::create([
                            'invoice_id'  => $invoice->id,
                            'description' => $item['description'],
                            'unit'        => $item['unit'],
                            'unit_price'  => $item['unit_price'],
                            'quantity'    => $item['quantity'],
                            'subtotal'    => round($item['unit_price'] * $item['quantity'], 2),
                        ]);
                    }
                }
            }
        }

        $this->command->info('Invoice seeder completed: ' . ($counter - 1) . ' invoices created.');
    }

    private function seedBuilding(): void
    {
        $blockNames = ['Block A', 'Block B'];
        foreach ($blockNames as $name) {
            $block = Block::create(['name' => $name, 'description' => "Tòa $name"]);
            for ($f = 1; $f <= 5; $f++) {
                $floor = Floor::create(['block_id' => $block->id, 'floor_number' => $f]);
                for ($r = 1; $r <= 4; $r++) {
                    $aptNum = $f . str_pad($r, 2, '0', STR_PAD_LEFT);
                    Apartment::create([
                        'floor_id'         => $floor->id,
                        'apartment_number' => $aptNum,
                        'area'             => rand(45, 120),
                        'status'           => 'occupied',
                    ]);
                }
            }
        }
        $this->command->info('Building structure created: 2 blocks, 10 floors, 40 apartments.');
    }

    private function generateAmountAndItems(string $type, Carbon $month): array
    {
        switch ($type) {
            case 'electricity':
                $kwh       = rand(120, 350);
                $unitPrice = rand(2000, 2500);
                $amount    = $kwh * $unitPrice;
                $items     = [
                    ['description' => 'Điện năng tiêu thụ', 'unit' => 'kWh', 'unit_price' => $unitPrice, 'quantity' => $kwh],
                ];
                break;

            case 'water':
                $m3        = rand(8, 25);
                $unitPrice = rand(8000, 10000);
                $amount    = $m3 * $unitPrice;
                $items     = [
                    ['description' => 'Nước sinh hoạt', 'unit' => 'm³', 'unit_price' => $unitPrice, 'quantity' => $m3],
                ];
                break;

            case 'management_fee':
                $amount = rand(300, 600) * 1000;
                $items  = [
                    ['description' => 'Phí quản lý tháng ' . $month->format('m/Y'), 'unit' => 'tháng', 'unit_price' => $amount, 'quantity' => 1],
                ];
                break;

            case 'parking':
                $slots   = rand(1, 2);
                $perSlot = 200000;
                $amount  = $slots * $perSlot;
                $items   = [
                    ['description' => 'Phí gửi xe tháng ' . $month->format('m/Y'), 'unit' => 'chỗ', 'unit_price' => $perSlot, 'quantity' => $slots],
                ];
                break;

            default:
                $amount = rand(50, 200) * 1000;
                $items  = [
                    ['description' => 'Phí dịch vụ khác', 'unit' => 'lần', 'unit_price' => $amount, 'quantity' => 1],
                ];
                break;
        }

        return [$amount, $items];
    }
}
