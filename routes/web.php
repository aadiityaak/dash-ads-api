<?php

use Illuminate\Support\Facades\Route;
use App\Models\Setting;

Route::get('/', function () { //tampilkan copyright
    return 'Your IP Address: ' . $_SERVER['REMOTE_ADDR'] . '<br><small>Copyright © ' . date('Y') . ' ' . Setting::get('app_name', 'Velocity Developer') . '</small>';
});

require __DIR__ . '/auth.php';
