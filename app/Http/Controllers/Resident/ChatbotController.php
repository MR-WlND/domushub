<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\Content;
use Gemini\Enums\Role;
use Exception;

class ChatbotController extends Controller
{
    /**
     * Gửi tin nhắn đến Chatbot và nhận phản hồi.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = $request->input('message');

        try {
            // Lấy thông tin căn hộ, xe đăng ký, phản ánh sự cố và hóa đơn của cư dân hiện tại
            $user = auth()->user();
            $apartmentIds = $user->residents()->whereNull('deleted_at')->pluck('apartment_id')->toArray();
            
            // 1. Căn hộ
            $apartments = \App\Models\Resident::where('user_id', $user->id)
                ->whereNull('deleted_at')
                ->with('apartment.floor.block')
                ->get()
                ->map(function($r) {
                    if ($r->apartment && $r->apartment->floor && $r->apartment->floor->block) {
                        return "Căn hộ {$r->apartment->apartment_number} (Tầng {$r->apartment->floor->floor_number}, Block {$r->apartment->floor->block->name})";
                    }
                    return $r->apartment ? "Căn hộ {$r->apartment->apartment_number}" : null;
                })
                ->filter()
                ->implode(', ');

            $apartmentContext = $apartments ? "Tên cư dân: {$user->name}. Căn hộ: $apartments.\n" : "Tên cư dân: {$user->name}. Chưa đăng ký căn hộ.\n";

            // 2. Phương tiện đăng ký
            $vehicles = \App\Models\Vehicle::whereIn('apartment_id', $apartmentIds)
                ->with('parkingLot')
                ->get();
            $vehicleContext = "";
            if ($vehicles->isNotEmpty()) {
                $vehicleContext = "Danh sách phương tiện đăng ký của cư dân này:\n";
                foreach ($vehicles as $vehicle) {
                    $parking = $vehicle->parkingLot ? "Lốt đỗ: {$vehicle->parkingLot->code}" : "Chưa phân lốt";
                    $vehicleContext .= "- Loại xe: {$vehicle->typeLabel()}, Biển số: {$vehicle->license_plate}, Hiệu: {$vehicle->brand}, Trạng thái: {$vehicle->statusLabel()}, {$parking}\n";
                }
            } else {
                $vehicleContext = "Cư dân này hiện tại chưa đăng ký phương tiện nào.\n";
            }

            // 3. Phản ánh sự cố (tối đa 5 cái gần nhất)
            $tickets = \App\Models\Ticket::where('sender_id', $user->id)
                ->with('handler')
                ->latest()
                ->take(5)
                ->get();
            $ticketContext = "";
            if ($tickets->isNotEmpty()) {
                $ticketContext = "Danh sách 5 phản ánh sự cố gần nhất cư dân đã gửi:\n";
                foreach ($tickets as $ticket) {
                    $handlerName = $ticket->handler ? $ticket->handler->name : 'Chưa phân công';
                    $ticketContext .= "- Mã phản ánh #{$ticket->id}: Tiêu đề '{$ticket->title}', Trạng thái: {$ticket->statusLabel()}, Mức độ ưu tiên: {$ticket->priorityLabel()}, Người phụ trách: {$handlerName}\n";
                }
                $ticketContext .= "Khi cư dân hỏi về tiến độ sửa chữa hoặc thông tin phản ánh cụ thể, hãy cung cấp đúng trạng thái trên và hướng dẫn họ vào mục Phản ánh sự cố để xem hoặc gửi phản ánh mới.\n";
            } else {
                $ticketContext = "Cư dân này hiện chưa gửi phản ánh sự cố nào lên hệ thống.\n";
            }

            // 4. Hóa đơn gần đây (tối đa 10 hóa đơn mới nhất bao gồm cả đã đóng và chưa đóng)
            $recentInvoices = \App\Models\Invoice::whereIn('apartment_id', $apartmentIds)
                ->latest()
                ->take(10)
                ->get();

            $invoiceContext = "";
            if ($recentInvoices->isNotEmpty()) {
                $invoiceContext = "Danh sách 10 hóa đơn gần nhất của cư dân (bao gồm cả đã đóng và chưa đóng):\n";
                foreach ($recentInvoices as $invoice) {
                    $dueDate = $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') : 'Chưa có';
                    $billingMonth = $invoice->billing_month instanceof \Carbon\Carbon ? $invoice->billing_month->format('m') : $invoice->billing_month;
                    $statusLabel = \App\Models\Invoice::statusLabel($invoice->status);
                    $invoiceContext .= "- Mã HĐ: {$invoice->invoice_code}, Tiêu đề: {$invoice->title}, Kỳ thanh toán: Tháng {$billingMonth}/{$invoice->billing_year}, Số tiền cần đóng: " . number_format($invoice->total_amount) . " VNĐ, Đã đóng: " . number_format($invoice->paid_amount) . " VNĐ, Trạng thái: {$statusLabel}, Hạn đóng: {$dueDate}\n";
                }
                $invoiceContext .= "Khi cư dân hỏi về số tiền cần đóng, hóa đơn chưa thanh toán hoặc đối chiếu các hóa đơn cũ, hãy dùng thông tin trên để thông báo. Hướng dẫn họ có thể thanh toán trực tuyến qua cổng VNPay ở mục Hóa đơn cho những hóa đơn có trạng thái Chưa thanh toán hoặc Quá hạn.\n";
            } else {
                $invoiceContext = "Cư dân này hiện tại chưa có hóa đơn nào trên hệ thống.\n";
            }

            // 5. Ngữ cảnh thời gian hiện tại
            $days = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
            $dayOfWeek = $days[now()->dayOfWeek];
            $currentTimeContext = "Thời gian hiện tại của hệ thống: {$dayOfWeek}, ngày " . now()->format('d/m/Y H:i') . ".\n\n";

            // Định hình tính cách và thông tin cơ sở của Ban quản lý tòa nhà
            $systemInstruction = "Bạn là Trợ lý ảo DomusHub của Ban quản lý tòa nhà chung cư DomusHub. Nhiệm vụ của bạn là hỗ trợ cư dân nhiệt tình, lịch sự và thân thiện.\n\n"
                . "Thông tin cơ bản về DomusHub:\n"
                . "- Dự án DomusHub cung cấp hệ thống quản lý căn hộ, bãi đỗ xe, hóa đơn dịch vụ, bảng tin nội bộ, và tiếp nhận phản ánh sự cố.\n"
                . "- Phí dịch vụ: Phí quản lý chung cư cố định là 10.000đ/m2/tháng.\n"
                . "- Phí gửi xe: Xe máy là 100.000đ/tháng, xe ô tô là 1.200.000đ/tháng.\n"
                . "- Báo cáo sự cố: Nếu có vấn đề kỹ thuật (hỏng bóng đèn, tắc nước, hỏng thang máy...), cư dân có thể gửi phản ánh trực tiếp bằng cách chọn mục [Phản ánh sự cố](/resident/tickets) trên thanh menu để nhân viên kỹ thuật tòa nhà đến xử lý.\n"
                . "- Thời gian làm việc của Ban quản lý: Từ 8:00 đến 17:30 tất cả các ngày trong tuần (trừ Chủ Nhật). Hotline khẩn cấp: 1900.1234 (hoạt động 24/7).\n"
                . "- Thanh toán trực tuyến: Hệ thống hỗ trợ cư dân đóng các hóa đơn (điện, nước, phí dịch vụ) trực tuyến nhanh chóng qua cổng thanh toán VNPay được tích hợp sẵn ở mục [Hóa đơn](/resident/invoices).\n\n"
                . "Ngữ cảnh thông tin:\n"
                . $currentTimeContext
                . $apartmentContext
                . $vehicleContext . "\n"
                . $ticketContext . "\n"
                . $invoiceContext . "\n"
                . "Quy tắc trả lời:\n"
                . "1. Trả lời ngắn gọn, rõ ràng, tập trung vào câu hỏi của cư dân. Sử dụng tiếng Việt chuẩn.\n"
                . "2. Sử dụng các emoji phù hợp (như 😊, 🚗, 🔧, 💡) để tăng tính thân thiện, gần gũi.\n"
                . "3. Hãy chèn các liên kết dạng Markdown chuẩn khi hướng dẫn cư dân di chuyển đến các trang chức năng. Danh sách đường dẫn:\n"
                . "   - Trang thanh toán hóa đơn: [Hóa đơn](/resident/invoices)\n"
                . "   - Trang lịch sử hóa đơn: [Lịch sử hóa đơn](/resident/invoices/history)\n"
                . "   - Trang gửi phản ánh sự cố: [Phản ánh sự cố](/resident/tickets)\n"
                . "   - Trang quản lý/đăng ký xe: [Phương tiện](/resident/vehicles)\n"
                . "   - Trang hồ sơ cá nhân: [Hồ sơ](/resident/profile)\n"
                . "4. Tuyệt đối không bịa đặt thông tin không có trong tài liệu này. Nếu không biết, hãy hướng dẫn cư dân gọi điện tới Hotline 1900.1234 để được hỗ trợ trực tiếp.";

            // Lấy lịch sử chat từ Session
            $history = session()->get('chatbot_history', []);
            $formattedHistory = [];

            // Chuyển đổi lịch sử chat sang định dạng Content object của Gemini SDK
            foreach ($history as $chat) {
                $role = ($chat['role'] === 'model') ? Role::MODEL : Role::USER;
                $formattedHistory[] = Content::parse(part: $chat['parts'], role: $role);
            }

            // Khởi tạo model và phiên chat với System Instruction
            $chatSession = Gemini::generativeModel(model: 'gemini-2.5-flash')
                ->withSystemInstruction(Content::parse($systemInstruction))
                ->startChat(history: $formattedHistory);

            // Gửi tin nhắn mới đến Gemini và nhận câu trả lời
            $response = $chatSession->sendMessage($message);
            $replyText = $response->text();

            // Lưu tin nhắn mới vào lịch sử trong Session
            $history[] = ['role' => 'user', 'parts' => $message];
            $history[] = ['role' => 'model', 'parts' => $replyText];

            // Giới hạn lịch sử chat (tối đa 20 tin nhắn gần nhất) để tránh đầy Session
            if (count($history) > 20) {
                $history = array_slice($history, -20);
            }

            session()->put('chatbot_history', $history);

            return response()->json([
                'success' => true,
                'reply' => $replyText,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể kết nối tới máy chủ AI: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xóa lịch sử hội thoại trong Session.
     */
    public function clearHistory()
    {
        session()->forget('chatbot_history');
        
        return response()->json([
            'success' => true,
            'message' => 'Lịch sử trò chuyện đã được làm mới.',
        ]);
    }
}
