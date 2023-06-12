<?php

namespace Drupal\assignment\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\NodeInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;

/**
 * Provides a custom block to display API data.
 *
 * @Block(
 *   id = "assignment_api_data_block",
 *   admin_label = @Translation("API Data Block"),
 *   category = @Translation("Custom Blocks")
 * )
 */
class ApiDataBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The current node.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $routeMatch;

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new MyModuleApiDataBlock object.
   *
   * @param array $configuration
   *   The block configuration.
   * @param string $plugin_id
   *   The plugin ID for the block.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   * @param \Drupal\node\NodeInterface $current_node
   *   The current node.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition,
      ClientInterface $http_client, RouteMatchInterface $routeMatch, ConfigFactoryInterface $config_factory ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
    $this->routeMatch = $routeMatch;
    $this->configFactory = $config_factory;
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client'),
      $container->get('current_route_match'),
      $container->get('config.factory')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function build() {

    // Get the current node from the route match.
    $node = $this->routeMatch->getParameter('node');
    if ($node instanceof NodeInterface) {
       // Check if the current node is of the desired content type.
        $contentType = $node->getType();

        if ($contentType !== 'entries') {
          return [];
        }

    } else {
      return [];
    }

    // Get current node information and fetch records based entry type.
    $nodeId = $node->id();
    $category = $node->get('field_entry_type')->getValue();

    $categoryVal = !empty($category[0]['value']) ? $category[0]['value'] : '';
    $config = $this->configFactory->get('assignment.settings');
    $apiUrl = $config->get('api_url'). "?category=". $categoryVal ;

    try {
      $response = $this->httpClient->get($apiUrl ,['connect_timeout' => 30]);

      $data = json_decode($response->getBody(), TRUE);


      $rows = [];
      foreach ($data['entries'] as $entry) {
        $rows[] = [
          $entry['API'],
          $entry['Description'],
          $entry['Auth'],
          $entry['HTTPS'],
          $entry['Cors'],
          [
            'data' => [
              '#type' => 'link',
              '#title' => 'API Link',
              '#url' => Url::fromUri($entry['Link']),
            ],
          ],
        ];
      }

      $output['api_data_table'] = [
        '#theme' => 'table',
        '#header' => [
          'Name',
          'Description',
          'Auth',
          'HTTPS',
          'Cors',
          'Link',
        ],
        '#rows' => $rows,
        '#attributes' => [
          'class' => ['my-custom-table'],
        ],
        '#cache' => [
          'keys' => ['assignment_api_data_block', $nodeId],
          'tags' => ['node:' . $nodeId],
          'contexts' => ['url.path'],
          'max-age' => Cache::PERMANENT,
        ],
        '#empty' => 'No API data available.',
      ];

      return $output;
    } catch (\Exception $e) {
      \Drupal::messenger()->addError($e->getMessage());
      return [];
    }
  }

}
