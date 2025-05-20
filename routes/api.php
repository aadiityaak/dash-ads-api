<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\TermsController;
use App\Http\Controllers\GoogleAdsController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    // return $request->user();
    $results = $request->user();

    // Dapatkan semua permissions
    $permissons = $request->user()->getPermissionsViaRoles();

    //collection permissions
    $results['user_permissions'] = collect($permissons)->pluck('name');

    unset($results->roles);

    return $results;
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResources([
        'posts'         => PostsController::class,
        'terms'         => TermsController::class
    ]);

    Route::get('/ads/status', [GoogleAdsController::class, 'status']);
    Route::post('/ads/disconnect', [GoogleAdsController::class, 'disconnect']);

    Route::get('/ads', [GoogleAdsController::class, 'index'])->name('ads.index');
});

Route::get('/ads/google-auth', [GoogleAdsController::class, 'redirectToGoogle'])->name('ads.google-auth');
Route::get('/ads/google-callback', [GoogleAdsController::class, 'handleGoogleCallback'])->name('ads.google-callback');
Route::get('/ads/search-terms', [GoogleAdsController::class, 'fetchSearchTerms'])->name('ads.fetch-search-terms');

require __DIR__ . '/api-dash.php';
