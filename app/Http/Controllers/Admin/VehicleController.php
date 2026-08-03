<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\SystemLogger;
use App\Models\Block;
use App\Models\Vehicle;
use App\Models\ParkingLot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VehicleController extends Controller
{
    /**
     * Danh sách toàn bộ phương tiện — hỗ trợ filter + pagination
     */
    public function index(Request $request)
    {
        $query = Vehicle::with([
            'apartment.floor.block',
            'apartment.residents.user',
            'parkingLot',
        ])->withoutTrashed();

        // Filter: Tòa nhà
        if ($request->filled('block_id')) {
            $query->whereHas('apartment.floor', function ($q) use ($request) {
                $q->where('block_id', $request->block_id);
            });
        }

        // Filter: Loại xe (hỗ trợ nhiều loại: "motorbike,electric_bike")
        if ($request->filled('vehicle_type')) {
            $types = explode(',', $request->vehicle_type);
            $query->whereIn('vehicle_type', $types);
        }

        // Filter: Trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $vehicles      = $query->latest()->paginate(15)->withQueryString();
        $blocks        = Block::orderBy('name')->get();
        $availableLots = ParkingLot::where('status', 'available')->where('lot_type', 'car')->get();

        return view('admin.vehicles.index', compact('vehicles', 'blocks', 'availableLots'));
    }

    // =========================================================================
    // THÊM MỚI PHƯƠNG TIỆN (ADMIN)
    // =========================================================================
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'apartment_id'  => 'required|exists:apartments,id',
            'vehicle_type'  => 'required|in:car,motorbike,electric_bike',
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate',
            'brand'         => 'nullable|string|max:100',
        ], [
            'license_plate.unique' => 'Biển số này đã được đăng ký trong hệ thống.',
        ]);

        $validated['status'] = 'pending'; // Mặc định là chờ duyệt theo chuẩn

        $vehicle = Vehicle::create($validated);

        SystemLogger::log('Tạo phương tiện mới', 'Admin đăng ký xe ' . $vehicle->license_plate . ' cho căn hộ ID: ' . $vehicle->apartment_id);

        return back()->with('success', 'Đã thêm phương tiện mới thành công! Vui lòng bấm "Duyệt" để tạo hóa đơn và cấp mã QR.');
    }

    // =========================================================================
    // QUẢN LÝ LỐT ĐỖ (chỉ dành cho ô tô)
    // =========================================================================

    public function assignLot(Request $request, Vehicle $vehicle)
    {
        // Chỉ gán lốt cho ô tô
        if (!$vehicle->isCar()) {
            return back()->withErrors(['vehicle' => 'Chỉ có thể gán lốt cho ô tô.']);
        }

        $request->validate([
            'parking_lot_id' => 'required|exists:parking_lots,id'
        ]);

        $lot = ParkingLot::findOrFail($request->parking_lot_id);

        if ($lot->status !== 'available') {
            return back()->withErrors(['parking_lot_id' => 'Lốt đỗ này đã có người sử dụng.']);
        }

        DB::transaction(function () use ($lot, $vehicle) {
            $lot->update(['status' => 'occupied', 'apartment_id' => $vehicle->apartment_id]);
            $vehicle->update(['parking_lot_id' => $lot->id, 'status' => 'active']);
        });

        $this->generateVehicleQr($vehicle);

        return back()->with('success', 'Đã gán lốt ' . $lot->lot_number . ' cho xe ' . $vehicle->license_plate . '. Phương tiện đang hoạt động.');
    }

    public function releaseLot(Vehicle $vehicle)
    {
        if (!$vehicle->parking_lot_id) {
            return back()->withErrors(['vehicle' => 'Xe này không có lốt đỗ để giải phóng.']);
        }

        if ($vehicle->isInside()) {
            return back()->withErrors(['vehicle' => 'Không thể thu hồi lốt khi xe đang ở trong hầm.']);
        }

        DB::transaction(function () use ($vehicle) {
            $lot = ParkingLot::find($vehicle->parking_lot_id);
            if ($lot) {
                $lot->update(['status' => 'available', 'apartment_id' => null]);
            }
            $vehicle->update(['parking_lot_id' => null, 'status' => 'pending']);
        });



        return back()->with('success', 'Đã thu hồi lốt đỗ của xe ' . $vehicle->license_plate);
    }

    // =========================================================================
    // DUYỆT XE MÁY / XE ĐIỆN
    // =========================================================================

    public function approve(Vehicle $vehicle)
    {
        if (!$vehicle->isPending()) {
            return back()->withErrors(['vehicle' => 'Chỉ có thể duyệt xe đang ở trạng thái chờ duyệt.']);
        }

        if ($vehicle->isCar()) {
            return back()->withErrors(['vehicle' => 'Ô tô cần được gán lốt đỗ trước khi duyệt.']);
        }

        $vehicle->update(['status' => 'active']);
        $this->generateVehicleQr($vehicle);

        return back()->with('success', 'Đã duyệt xe ' . $vehicle->license_plate . '. Phương tiện đang hoạt động.');
    }

    // =========================================================================
    // KHÓA / MỞ KHÓA PHƯƠNG TIỆN
    // =========================================================================

    public function lock(Vehicle $vehicle)
    {
        if ($vehicle->isInactive()) {
            return back()->withErrors(['vehicle' => 'Xe này đã ngừng sử dụng, không thể khóa.']);
        }

        if ($vehicle->isLocked()) {
            return back()->withErrors(['vehicle' => 'Xe này đã bị khóa rồi.']);
        }

        $vehicle->update(['status' => 'locked']);



        return back()->with('success', 'Đã khóa xe ' . $vehicle->license_plate . '.');
    }

    public function unlock(Vehicle $vehicle)
    {
        if (!$vehicle->isLocked()) {
            return back()->withErrors(['vehicle' => 'Xe này không đang bị khóa.']);
        }

        $vehicle->update(['status' => 'active']);



        return back()->with('success', 'Đã mở khóa xe ' . $vehicle->license_plate . '.');
    }

    // =========================================================================
    // XÓA PHƯƠNG TIỆN
    // =========================================================================

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->isInside()) {
            return back()->withErrors(['vehicle' => 'Không thể xóa xe đang ở trong hầm.']);
        }

        $plate = $vehicle->license_plate;

        // Giải phóng lốt đỗ nếu có
        if ($vehicle->parking_lot_id) {
            $lot = ParkingLot::find($vehicle->parking_lot_id);
            if ($lot) {
                $lot->update(['status' => 'available', 'apartment_id' => null]);
            }
        }

        $vehicle->delete();

        SystemLogger::log('Xóa phương tiện', 'Biển số: ' . $plate);

        return back()->with('success', 'Đã xóa xe ' . $plate . ' khỏi hệ thống.');
    }

    // =========================================================================
    // TẠO HÓA ĐƠN PHÍ GỬI XE
    // =========================================================================

    /**
     * Tạo hóa đơn phí gửi xe khi admin duyệt xe.
     * Cư dân thanh toán xong → xe được kích hoạt + sinh QR.
     */
    private function createParkingFeeInvoice(Vehicle $vehicle): void
    {
        // Tìm service_price phù hợp với loại xe
        $servicePrice = \App\Models\ServicePrice::where('type', 'parking_fee')
            ->where('status', 'active')
            ->where(function ($q) use ($vehicle) {
                $q->where('vehicle_type', $vehicle->vehicle_type)
                  ->orWhereNull('vehicle_type');
            })
            ->orderByRaw("CASE WHEN vehicle_type = ? THEN 0 ELSE 1 END", [$vehicle->vehicle_type])
            ->first();

        if (!$servicePrice) {
            // Fallback: nếu không có giá, kích hoạt xe luôn (miễn phí)
            $vehicle->update(['status' => 'active']);
            $this->generateVehicleQr($vehicle);
            return;
        }

        $amount = $servicePrice->unit_price;
        $now = now();

        // Tạo hóa đơn
        $invoice = \App\Models\Invoice::create([
            'apartment_id'       => $vehicle->apartment_id,
            'title'              => 'Phí gửi xe - ' . $vehicle->license_plate,
            'billing_month'      => $now->month,
            'billing_year'       => $now->year,
            'due_date'           => $now->copy()->addDays(7),
            'total_amount'       => $amount,
            'paid_amount'        => 0,
            'previous_debt'      => 0,
            'current_amount'     => $amount,
            'total_due_at_issue' => $amount,
            'status'             => 'unpaid',
        ]);

        // Tạo chi tiết hóa đơn
        \App\Models\InvoiceDetail::create([
            'bill_id'          => $invoice->id,
            'service_price_id' => $servicePrice->id,
            'quantity'         => 1,
            'amount'           => $amount,
            'status'           => 'unpaid',
            'note'             => 'Phí gửi xe ' . $vehicle->typeLabel() . ' - ' . $vehicle->license_plate,
        ]);

        SystemLogger::log('Tạo hóa đơn phí gửi xe', $vehicle->license_plate . ' - ' . number_format($amount) . 'đ');
    }

    // =========================================================================
    // QR GENERATION
    // =========================================================================

    /**
     * Sinh QR image cho xe và lưu path vào cột qr_code.
     * Content của QR là license_plate (bảo vệ quét → tìm xe theo biển số).
     */
    private function generateVehicleQr(Vehicle $vehicle): void
    {
        try {
            $dir = storage_path('app/public/qr/vehicles');
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            // QR content = biển số viết hoa, không dấu cách và gạch ngang
            $content  = strtoupper(str_replace([' ', '-'], '', $vehicle->license_plate));
            $filename = $content . '.svg';
            $filePath = $dir . '/' . $filename;

            if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                    ->size(300)
                    ->errorCorrection('H')
                    ->generate($content, $filePath);
            }

            // Lưu đường dẫn tương đối (dùng cho asset())
            $vehicle->update(['qr_code' => 'qr/vehicles/' . $filename]);

        } catch (\Throwable $e) {
            Log::warning('Vehicle QR generation failed for ' . $vehicle->license_plate . ': ' . $e->getMessage());
            // Fallback: lưu content làm qr_code để scanner vẫn hoạt động
            $content = strtoupper(str_replace([' ', '-'], '', $vehicle->license_plate));
            $vehicle->update(['qr_code' => $content]);
        }
    }
}
