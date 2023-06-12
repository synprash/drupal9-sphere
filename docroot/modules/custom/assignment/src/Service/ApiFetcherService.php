<?php

namespace Drupal\assignmemt\Service;

use GuzzleHttp\ClientInterface;

/**
 * Service for fetching API responses.
 */
class ApiFetcherService {

  protected $httpClient;

  public function __construct(ClientInterface $httpClient) {
    $this->httpClient = $httpClient;
  }

  /**
   * Fetches API response based on the provided URL.
   *
   * @param string $url
   *   The URL of the API.
   *
   * @return array
   *   The API response data.
   */
  public function fetchApiData($url) {
    $response = $this->httpClient->get($url);
    return json_decode($response->getBody(), true);
  }

}
