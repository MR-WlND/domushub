<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            // Thiếu các cột đã dùng trong Model
            if (!Schema::hasColumn('visitors', 'registered_by')) {
                $table->foreignId('registered_by')->nullable()->constrained('users')->nullOnDelete()->after('apartment_id');
            }
            if (!Schema::hasColumn('visitors', 'status')) {
                $table->string('status', 30)->default('pending')->after('qr_token');
            }
            if (!Schema::hasColumn('visitors', 'note')) {
                $table->text('note')->nullable()->after('status');
            }
            if (!Schema::hasColumn('visitors', 'vehicle_plate')) {
                $table->string('vehicle_plate', 20)->nullable()->after('note');
            }
            if (!Schema::hasColumn('visitors', 'vehicle_type')) {
                $table->string('vehicle_type', 30)->nullable()->after('vehicle_plate');
            }
            if (!Schema::hasColumn('visitors', 'walk_in')) {
                $table->boolean('walk_in')->default(false)->after('vehicle_type');
            }
            if (!Schema::hasColumn('visitors', 'resident_to_meet')) {
                $table->string('resident_to_meet', 100)->nullable()->after('walk_in');
            }
            if (!Schema::hasColumn('visitors', 'confirmed_by_resident')) {
                $table->foreignId('confirmed_by_resident')->nullable()->constrained('users')->nullOnDelete()->after('resident_to_meet');
            }
            if (!Schema::hasColumn('visitors', 'face_image')) {
                $table->string('face_image', 255)->nullable()->after('confirmed_by_resident');
            }
        });
    }

    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $columns = [
                'registered_by', 'status', 'note', 'vehicle_plate',
                'vehicle_type', 'walk_in', 'resident_to_meet',
                'confirmed_by_resident', 'face_image',
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('visitors', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

