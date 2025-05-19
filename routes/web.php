<?php

use Illuminate\Support\Facades\Route;
use App\Models\Setting;
use App\Http\Controllers\GoogleAdsController;


Route::get('/', function () { //tampilkan copyright
    return 'Your IP Address: ' . $_SERVER['REMOTE_ADDR'] . '<br><small>Copyright © ' . date('Y') . ' ' . Setting::get('app_name', 'Velocity Developer') . '</small>';
});

Route::get('/ads/google-auth', [GoogleAdsController::class, 'redirectToGoogle'])->name('ads.google.auth');
Route::get('/ads/google-callback', [GoogleAdsController::class, 'handleGoogleCallback'])->name('ads.google.callback');

require __DIR__ . '/auth.php';
