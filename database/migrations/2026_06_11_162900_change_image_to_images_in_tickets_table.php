<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Bước 1: Thêm cột mới 'images' kiểu JSON
        Schema::table('tickets', function (Blueprint $table) {
            $table->json('images')->nullable()->after('description');
        });

        // Bước 2: Chuyển dữ liệu cũ từ 'image' sang 'images'
        DB::table('tickets')->whereNotNull('image')->orderBy('id')->each(function ($ticket) {
            DB::table('tickets')->where('id', $ticket->id)->update([
                'images' => json_encode([$ticket->image]),
            ]);
        });

        // Bước 3: Xoá cột cũ 'image'
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('image', 255)->nullable()->after('description');
        });

        DB::table('tickets')->whereNotNull('images')->orderBy('id')->each(function ($ticket) {
            $images = json_decode($ticket->images, true);
            DB::table('tickets')->where('id', $ticket->id)->update([
                'image' => $images[0] ?? null,
            ]);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('images');
        });
    }
};
