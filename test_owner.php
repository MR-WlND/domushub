<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

foreach (App\Models\Apartment::take(10)->get() as $apt) {
    echo "Apt {$apt->id} ({$apt->apartment_number}): ownerNameAttr={$apt->owner_name}\n";
    $res = App\Models\Resident::with('user')->where('apartment_id', $apt->id)->get();
    foreach ($res as $r) {
        echo "   [Resident] rel={$r->relationship}, user=" . ($r->user?->name) . "\n";
    }
    $users = App\Models\User::where('apartment_id', $apt->id)->get();
    foreach ($users as $u) {
        echo "   [User] id={$u->id}, name={$u->name}\n";
    }
}
