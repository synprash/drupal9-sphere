<?php

namespace Drupal\assignment\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\assignment\Service\ApiFetcherService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block to display today's Bitcoin prices in different currencies.
 *
 * @Block(
 *   id = "bitcoin_prices_block",
 *   admin_label = @Translation("Bitcoin Prices Block"),
 * )
 */
class BitcoinBlock extends BlockBase {

  protected $apiFetcherService;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, ApiFetcherService $apiFetcherService) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->apiFetcherService = $apiFetcherService;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('your_module.api_fetcher')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    // Fetch API data.
    $apiData = $this->apiFetcherService->fetchApiData('https://api.coindesk.com/v1/bpi/currentprice.json');

    if (!empty($apiData['bpi'])) {
      // Build the prices list.
      $prices = [];
      foreach ($apiData['bpi'] as $currency => $data) {
        $prices[] = [
          '#type' => 'item',
          '#markup' => $data['rate'] . ' ' . $currency,
        ];
      }

      // Add the prices list to the block build.
      $build = [
        '#theme' => 'item_list',
        '#items' => $prices,
        '#cache' => [
          'tags' => $this->getCacheTags(),
        ],
      ];
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), ['bitcoin_prices']);
  }

}
