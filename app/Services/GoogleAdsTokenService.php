<?php

namespace App\Services;

use Google_Client;
use App\Models\User;

class GoogleAdsTokenService
{
  public static function getValidAccessToken(User $user)
  {
    if ($user->google_ads_token_expiry < now()) {
      $client = new Google_Client();
      $client->setClientId(config('services.google.client_id'));
      $client->setClientSecret(config('services.google.client_secret'));
      $client->refreshToken($user->google_ads_refresh_token);

      $newToken = $client->getAccessToken();

      $user->update([
        'google_ads_access_token' => $newToken['access_token'],
        'google_ads_token_expiry' => now()->addSeconds($newToken['expires_in']),
      ]);
    }

    return $user->google_ads_access_token;
  }
}
