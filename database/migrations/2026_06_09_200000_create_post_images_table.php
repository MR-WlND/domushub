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
        // 1. Tạo bảng post_images
        Schema::create('post_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->string('image_path', 255);
            $table->timestamps();
        });

        // 2. Di cư dữ liệu ảnh cũ (từ posts sang post_images)
        if (Schema::hasColumn('posts', 'image')) {
            $postsWithImage = DB::table('posts')->whereNotNull('image')->where('image', '!=', '')->get();
            
            $now = \Carbon\Carbon::now();
            foreach ($postsWithImage as $post) {
                DB::table('post_images')->insert([
                    'post_id' => $post->id,
                    'image_path' => $post->image,
                    'created_at' => $post->created_at ?? $now,
                    'updated_at' => $post->updated_at ?? $now,
                ]);
            }

            // 3. Drop cột image trên bảng posts
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Khôi phục lại cột image trên bảng posts
        if (!Schema::hasColumn('posts', 'image')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('image', 255)->nullable()->after('content');
            });
        }

        // 2. Chuyển ảnh đầu tiên từ post_images về cột image trên posts
        $postImages = DB::table('post_images')->orderBy('id', 'asc')->get()->groupBy('post_id');
        foreach ($postImages as $postId => $images) {
            $firstImage = $images->first();
            if ($firstImage) {
                DB::table('posts')->where('id', $postId)->update([
                    'image' => $firstImage->image_path
                ]);
            }
        }

        // 3. Drop bảng post_images
        Schema::dropIfExists('post_images');
    }
};
