<?php

namespace Tests\Unit\Models;

use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class AnnouncementModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Scope published chỉ lấy thông báo có status = published.
     */
    public function test_announcement_published_scope(): void
    {
        $admin = $this->makeAdmin();

        Announcement::create([
            'user_id'  => $admin->id,
            'title'    => 'Thông báo 1',
            'content'  => 'Nội dung 1',
            'category' => 'general',
            'status'   => 'published',
        ]);

        Announcement::create([
            'user_id'  => $admin->id,
            'title'    => 'Thông báo nháp',
            'content'  => 'Nội dung 2',
            'category' => 'general',
            'status'   => 'draft',
        ]);

        $published = Announcement::published()->get();
        $this->assertCount(1, $published);
        $this->assertEquals('Thông báo 1', $published->first()->title);
    }
}
