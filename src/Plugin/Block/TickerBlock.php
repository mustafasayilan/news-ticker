<?php

namespace Drupal\news_ticker\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\news_ticker\Service\TickerService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Ticker' block.
 *
 * @Block(
 *   id = "news_ticker",
 *   admin_label = @Translation("News Ticker"),
 *   category = @Translation("Custom")
 * )
 */
class TickerBlock extends BlockBase implements BlockPluginInterface {

  /**
   * The Ticker service.
   *
   * @var \Drupal\news_ticker\Service\TickerService
   */
  protected $tickerService;

  /**
   * Constructs a new TickerBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\news_ticker\Service\TickerService $ticker_service
   *   The Ticker service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, TickerService $ticker_service = NULL) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->tickerService = $ticker_service ?: \Drupal::service('news_ticker.ticker_service');
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('news_ticker.ticker_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
        'selected_list' => 0,
      ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $selectedList = $config['selected_list'];

    $listItems = [];
    if ($selectedList) {
      $listItems = $this->tickerService->getListItems([$selectedList]);
    }

    $build = [
      '#theme' => 'ticker_block',
      '#items' => $listItems,
      '#attached' => [
        'library' => [
          'news_ticker/ticker',
        ],
      ],
    ];

    return $build;
  }


  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $options = $this->tickerService->getListOptions();

    $config = $this->getConfiguration();

    $form['selected_list'] = [
      '#type' => 'select',
      '#title' => $this->t('Select List'),
      '#options' => $options,
      '#default_value' => $config['selected_list'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);

    $values = $form_state->getValues();
    $this->configuration['selected_list'] = $values['selected_list'];
  }

}
