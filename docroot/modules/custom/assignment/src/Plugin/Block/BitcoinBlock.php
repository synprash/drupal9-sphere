<?php

namespace Drupal\assignment\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Cache\Cache;
use Drupal\assignment\Service\ApiFetcherService;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a block to display today's Bitcoin prices in different currencies.
 *
 * @Block(
 *   id = "bitcoin_prices_block",
 *   admin_label = @Translation("Bitcoin Prices Block"),
 * )
 */
class BitcoinBlock extends BlockBase implements ContainerFactoryPluginInterface
{

    protected $apiFetcherService;

     /**
      * The configuration factory.
      *
      * @var \Drupal\Core\Config\ConfigFactoryInterface
      */
    protected $configFactory;


    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        ApiFetcherService $apiFetcherService,
        ConfigFactoryInterface $config_factory
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->apiFetcherService = $apiFetcherService;
        $this->configFactory     = $config_factory;

    }//end __construct()


    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('assignment.api_fetcher'),
            $container->get('config.factory'),
        );

    }//end create()


    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $build = [];

        // Fetch API data.
        $config  = $this->configFactory->get('assignment.settings');
        $apiData = $this->apiFetcherService->fetchApiData($config->get('bitcoin_api_url'));

        if (!empty($apiData['bpi'])) {
            // Build the prices list.
            $prices = [];
            foreach ($apiData['bpi'] as $currency => $data) {
                $prices[] = [
                    '#type'   => 'item',
                    '#markup' => $data['rate'].' '.$currency,
                ];
            }

            // Add the prices list to the block build.
            $build = [
                '#theme' => 'item_list',
                '#title' => 'Current Prices of Bitcoin : ',
                '#items' => $prices,
                '#cache' => [
                    'tags' => $this->getCacheTags(),
                ],
            ];
        }

        return $build;

    }//end build()


    /**
     * {@inheritdoc}
     */
    public function getCacheTags()
    {
        return Cache::mergeTags(parent::getCacheTags(), ['bitcoin_prices']);

    }//end getCacheTags()


}//end class
