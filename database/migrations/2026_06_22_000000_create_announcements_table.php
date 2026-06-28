<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('title', 255);
                $table->text('content');
                $table->enum('category', ['maintenance', 'warning', 'general', 'event'])->default('general');
                $table->enum('status', ['draft', 'published', 'archived'])->default('published');
                $table->boolean('pinned')->default(false);
                $table->string('image_path', 255)->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
