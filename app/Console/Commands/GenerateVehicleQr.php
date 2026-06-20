<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vehicle;

class GenerateVehicleQr extends Command
{
    protected $signature = 'vehicles:generate-qr';
    protected $description = 'Sinh QR code cho tất cả xe active/pending_renewal chưa có QR hoặc file bị thiếu';

    public function handle()
    {
        $vehicles = Vehicle::whereIn('status', ['active', 'pending_renewal'])
            ->withoutTrashed()
            ->get();

        $dir = storage_path('app/public/qr/vehicles');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $count = 0;

        foreach ($vehicles as $vehicle) {
            $content  = strtoupper(str_replace([' ', '-'], '', $vehicle->license_plate));
            $filename = $content . '.svg';
            $filePath = $dir . '/' . $filename;

            // Sinh file QR nếu chưa tồn tại
            if (!file_exists($filePath)) {
                if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                    \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                        ->size(300)
                        ->errorCorrection('H')
                        ->generate($content, $filePath);
                }
            }

            // Cập nhật DB
            $vehicle->update(['qr_code' => 'qr/vehicles/' . $filename]);
            $this->line("✓ {$vehicle->license_plate} → {$filename}");
            $count++;
        }

        $this->info("Hoàn tất! Đã sinh QR cho {$count} xe.");
    }
}
