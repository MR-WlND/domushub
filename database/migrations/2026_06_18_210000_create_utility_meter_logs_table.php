<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('utility_meter_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utility_meter_id')->nullable()->constrained('utility_meters')->nullOnDelete();
            $table->foreignId('apartment_id')->constrained('apartments')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type'); // 'electricity', 'water'
            $table->integer('record_month');
            $table->integer('record_year');
            $table->integer('old_value');
            $table->integer('new_value');
            $table->string('action'); // 'recorded', 'updated', 'approved', 'rejected'
            $table->text('reject_reason')->nullable();
            $table->timestamps();
        });

        // Seed data from existing utility_meters
        try {
            $meters = DB::table('utility_meters')->get();
            foreach ($meters as $meter) {
                // 1. Log recorded action
                DB::table('utility_meter_logs')->insert([
                    'utility_meter_id' => $meter->id,
                    'apartment_id'     => $meter->apartment_id,
                    'user_id'          => $meter->recorded_by,
                    'type'             => $meter->type,
                    'record_month'     => $meter->record_month,
                    'record_year'      => $meter->record_year,
                    'old_value'        => $meter->old_value,
                    'new_value'        => $meter->new_value,
                    'action'           => 'recorded',
                    'created_at'       => $meter->created_at ?? now(),
                    'updated_at'       => $meter->created_at ?? now(),
                ]);

                // 2. Log approved/rejected action if applicable
                if ($meter->status === 'approved') {
                    DB::table('utility_meter_logs')->insert([
                        'utility_meter_id' => $meter->id,
                        'apartment_id'     => $meter->apartment_id,
                        'user_id'          => null, // Not explicitly stored for approval in original table, set to null
                        'type'             => $meter->type,
                        'record_month'     => $meter->record_month,
                        'record_year'      => $meter->record_year,
                        'old_value'        => $meter->old_value,
                        'new_value'        => $meter->new_value,
                        'action'           => 'approved',
                        'created_at'       => $meter->updated_at ?? now(),
                        'updated_at'       => $meter->updated_at ?? now(),
                    ]);
                } elseif ($meter->status === 'rejected') {
                    DB::table('utility_meter_logs')->insert([
                        'utility_meter_id' => $meter->id,
                        'apartment_id'     => $meter->apartment_id,
                        'user_id'          => $meter->rejected_by,
                        'type'             => $meter->type,
                        'record_month'     => $meter->record_month,
                        'record_year'      => $meter->record_year,
                        'old_value'        => $meter->old_value,
                        'new_value'        => $meter->new_value,
                        'action'           => 'rejected',
                        'reject_reason'    => $meter->reject_reason,
                        'created_at'       => $meter->updated_at ?? now(),
                        'updated_at'       => $meter->updated_at ?? now(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Log warning or ignore if table utility_meters has issues during seeding
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utility_meter_logs');
    }
};
