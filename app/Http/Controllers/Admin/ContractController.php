<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Helpers\SystemLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ContractController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staffs,id',
            'contract_number' => 'required|string|max:255|unique:contracts,contract_number',
            'type' => 'nullable|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'contract_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $filePath = null;
        if ($request->hasFile('contract_file')) {
            $filePath = $request->file('contract_file')->store('contracts', 'public');
        }

        $contract = Contract::create([
            'staff_id' => $request->staff_id,
            'contract_number' => $request->contract_number,
            'type' => $request->type,
            'base_salary' => $request->base_salary ?? 0,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'file_path' => $filePath,
        ]);

        SystemLogger::log('Thêm hợp đồng mới', 'Số HĐ: ' . $contract->contract_number . ' cho nhân sự ID ' . $request->staff_id);

        return redirect()->back()->with('success', 'Đã thêm hợp đồng mới thành công!');
    }

    public function destroy(Contract $contract)
    {
        $contractNumber = $contract->contract_number;
        
        if ($contract->file_path && Storage::disk('public')->exists($contract->file_path)) {
            Storage::disk('public')->delete($contract->file_path);
        }

        $contract->delete();
        SystemLogger::log('Xóa hợp đồng', 'Số HĐ: ' . $contractNumber);

        return redirect()->back()->with('success', 'Đã xóa hợp đồng thành công!');
    }
}
