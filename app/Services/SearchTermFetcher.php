<?php

namespace App\Services;

// require __DIR__ . '/../vendor/autoload.php';

use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Lib\V19\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\V19\Services\SearchGoogleAdsRequest;
use Google\ApiCore\ApiException;

class SearchTermFetcher
{

  public function fetch($startDate, $endDate)
  {

    $oAuth2Credential = (new OAuth2TokenBuilder())
      ->withClientId(env('GOOGLE_ADS_CLIENT_ID'))
      ->withClientSecret(env('GOOGLE_ADS_CLIENT_SECRET'))
      ->withRefreshToken(env('GOOGLE_ADS_REFRESH_TOKEN'))
      ->build();

    $googleAdsClient = (new GoogleAdsClientBuilder())
      ->withDeveloperToken(env('GOOGLE_ADS_DEVELOPER_TOKEN'))
      ->withLoginCustomerId(env('GOOGLE_ADS_LOGIN_CUSTOMER_ID'))
      ->withOAuth2Credential($oAuth2Credential)
      ->build();

    $query = "
            SELECT
                search_term_view.search_term,
                metrics.impressions,
                metrics.clicks,
                metrics.cost_micros,
                metrics.conversions,
                search_term_view.status
            FROM search_term_view
            WHERE segments.date BETWEEN '$startDate' AND '$endDate'
            LIMIT 1000
        ";


    try {
      $googleAdsServiceClient = $googleAdsClient->getGoogleAdsServiceClient();
      $response = $googleAdsServiceClient->search(SearchGoogleAdsRequest::build(env('GOOGLE_ADS_CUSTOMER_ID'), $query));

      // Collect the results
      $results = [];
      foreach ($response->iterateAllElements() as $row) {
        $results[] = [
          'term' => $row->getSearchTermView()->getSearchTerm(),
          'impressions' => $row->getMetrics()->getImpressions(),
          'clicks' => $row->getMetrics()->getClicks(),
          'cost' => $row->getMetrics()->getCostMicros() / 1_000_000, // Convert micros to currency
          'conversions' => $row->getMetrics()->getConversions(),
          'status' => $row->getSearchTermView()->getStatus(),
        ];
      }

      return $results;
    } catch (ApiException $e) {
      return ['error' => $e->getMessage()];
    }
  }
}
