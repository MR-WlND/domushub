<?php

namespace Tests\Feature\Ticket;

use App\Models\Ticket;
use App\Models\TicketProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class TicketProgressTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    private function makeAssignedTicket(): array
    {
        $apartment  = $this->makeApartment();
        $resident   = $this->makeResident($apartment);
        $technician = $this->makeTechnician();

        $ticket = Ticket::create([
            'apartment_id' => $apartment->id,
            'sender_id'    => $resident->id,
            'handler_id'   => $technician->id,
            'ticket_type'  => 'complaint',
            'title'        => 'Thang máy hỏng',
            'description'  => 'Thang máy tầng 5 không hoạt động',
            'priority'     => 'urgent',
            'status'       => 'assigned',
        ]);

        return [$ticket, $technician, $resident];
    }

    /**
     * Kỹ thuật viên cập nhật tiến độ ticket sang in_progress.
     */
    public function test_technician_can_update_ticket_to_in_progress(): void
    {
        [$ticket, $technician] = $this->makeAssignedTicket();
        $this->actingAs($technician);

        $response = $this->post(route('admin.tickets.update-progress', $ticket->id), [
            'comment' => 'Đã kiểm tra, cần đặt linh kiện thay thế',
            'status'  => 'in_progress',
        ]);

        $response->assertRedirect();
        $this->assertEquals('in_progress', $ticket->fresh()->status);
        $this->assertDatabaseHas('ticket_progress', [
            'ticket_id' => $ticket->id,
            'status'    => 'in_progress',
        ]);
    }

    /**
     * Kỹ thuật viên đánh dấu ticket hoàn thành (cần comment + ảnh).
     */
    public function test_technician_can_mark_ticket_completed_with_proof(): void
    {
        Storage::fake('public');

        [$ticket, $technician] = $this->makeAssignedTicket();
        $ticket->update(['status' => 'in_progress']);

        $this->actingAs($technician);

        $image = UploadedFile::fake()->image('proof.jpg');

        $response = $this->post(route('admin.tickets.update-progress', $ticket->id), [
            'comment'     => 'Đã sửa xong, thang máy hoạt động bình thường',
            'status'      => 'completed',
            'image_proof' => $image,
        ]);

        $response->assertRedirect();
        $this->assertEquals('completed', $ticket->fresh()->status);
        $this->assertNotNull($ticket->fresh()->completed_at);
    }

    /**
     * Kỹ thuật viên hoàn thành mà không có ảnh thì validation fail.
     */
    public function test_technician_cannot_complete_without_image_proof(): void
    {
        [$ticket, $technician] = $this->makeAssignedTicket();
        $ticket->update(['status' => 'in_progress']);

        $this->actingAs($technician);

        $response = $this->post(route('admin.tickets.update-progress', $ticket->id), [
            'comment' => 'Đã sửa xong',
            'status'  => 'completed',
            // Không có image_proof
        ]);

        $response->assertSessionHasErrors(['image_proof']);
        $this->assertEquals('in_progress', $ticket->fresh()->status);
    }

    /**
     * Resident có thể gửi đánh giá khi ticket hoàn thành.
     */
    public function test_resident_can_give_feedback_on_completed_ticket(): void
    {
        [$ticket, $technician, $resident] = $this->makeAssignedTicket();
        $ticket->update(['status' => 'completed']);

        $this->actingAs($resident);

        $response = $this->post(route('resident.tickets.feedback', $ticket->id), [
            'rating'           => 5,
            'feedback_comment' => 'Kỹ thuật viên làm việc rất nhanh và chuyên nghiệp!',
        ]);

        $response->assertRedirect();
        $this->assertEquals(5, $ticket->fresh()->rating);
    }
}
