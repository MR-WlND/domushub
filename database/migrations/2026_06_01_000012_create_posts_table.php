<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 200);
            $table->text('content');
            $table->string('image', 255)->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->enum('status', ['draft', 'pending_review', 'published', 'hidden', 'removed'])->default('published');
            $table->enum('ai_flagged', ['clean', 'flagged', 'spam', 'suspicious'])->default('clean');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
