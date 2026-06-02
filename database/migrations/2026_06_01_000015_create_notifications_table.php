<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('type', 100);
            $table->string('notifiable_type', 100);
            $table->unsignedBigInteger('notifiable_id');
            $table->json('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['notifiable_type', 'notifiable_id'], 'idx_notifications_notifiable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
