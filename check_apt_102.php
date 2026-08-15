<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Apartment;
use App\Models\Invoice;

$apts = Apartment::where('apartment_number', 'like', '%102%')
    ->orWhere('apartment_code', 'like', '%102%')
    ->get();

echo "Found " . $apts->count() . " apartment(s) matching 102:\n";
foreach ($apts as $apt) {
    echo "----------------------------------------\n";
    echo "Apartment ID: {$apt->id} | Number: {$apt->apartment_number} | Code: {$apt->apartment_code} | Owner: " . optional(optional($apt->ownerResident)->user)->name . "\n";
    
    $invoices = Invoice::where('apartment_id', $apt->id)->get();
    echo "Total invoices for this apartment: " . $invoices->count() . "\n";
    foreach ($invoices as $inv) {
        $totalDue = (float) ($inv->total_due_at_issue > 0 ? $inv->total_due_at_issue : $inv->total_amount);
        $rem = max(0, $totalDue - (float) $inv->paid_amount);
        echo "   - Bill ID: {$inv->id} ({$inv->invoice_code}): status={$inv->status}, total={$inv->total_amount}, total_due_at_issue={$inv->total_due_at_issue}, paid={$inv->paid_amount}, rem={$rem}\n";
    }
}
