<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Google_Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\SearchTermFetcher;

class GoogleAdsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $connected = !empty($user->google_ads_refresh_token);

        return view('ads.index', compact('connected'));
    }

    public function fetchSearchTerms(Request $request)
    {
        $user = User::find(1);

        if (empty($user->google_ads_refresh_token)) {
            return response()->json(['error' => 'Akun Google Ads belum terhubung.'], 403);
        }

        $startDate = $request->input('start_date', now()->subDays(7)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        try {
            $fetcher = new SearchTermFetcher();
            $results = $fetcher->fetch($startDate, $endDate);

            if (isset($results['error'])) {
                return response()->json(['error' => $results['error']], 500);
            }

            return response()->json(['data' => $results]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function redirectToGoogle()
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_ADS_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_ADS_CLIENT_SECRET'));
        $client->setRedirectUri(route('ads.google.callback')); // gunakan route, bukan string
        $client->addScope('https://www.googleapis.com/auth/adwords');
        $client->setAccessType('offline'); // penting untuk dapatkan refresh token
        $client->setPrompt('consent');     // paksa user untuk pilih akun dan setujui akses

        return redirect($client->createAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        if (!$request->has('code')) {
            return redirect()->route('ads.google.auth')->withErrors(['google' => 'Authorization code tidak ditemukan.']);
        }

        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_ADS_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_ADS_CLIENT_SECRET'));
        $client->setRedirectUri(route('ads.google.callback'));

        try {
            $token = $client->fetchAccessTokenWithAuthCode($request->code);
        } catch (\Exception $e) {
            return redirect()->route('ads.google.auth')->withErrors(['google' => 'Gagal mengambil token: ' . $e->getMessage()]);
        }

        if (isset($token['error'])) {
            return redirect()->route('ads.google.auth')->withErrors(['google' => 'Gagal authorize: ' . $token['error_description']]);
        }

        $user = User::find(1);
        // log($token);
        Log::info($token);
        Log::info($user);
        $user->update([
            'google_ads_access_token'    => $token['access_token'] ?? null,
            'google_ads_refresh_token'   => $token['refresh_token'] ?? $user->google_ads_refresh_token,
            'google_ads_token_expiry'    => now()->addSeconds($token['expires_in'] ?? 3600),
        ]);

        // redirect ke frontend http://localhost:3007/ads/keywords 
        return redirect(env('FRONTEND_URL') . '/ads/keywords');
    }

    public function status(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'connected' => !empty($user->google_ads_refresh_token),
        ]);
    }

    public function disconnect(Request $request)
    {
        $user = $request->user();

        $user->update([
            'google_ads_access_token'    => null,
            'google_ads_refresh_token'   => null,
            'google_ads_token_expiry'    => null,
        ]);

        return response()->json(['message' => 'Google Ads telah terputus.']);
    }
}
