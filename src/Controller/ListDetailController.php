<?php

namespace Drupal\news_ticker\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for displaying the detail of a list.
 */
class ListDetailController extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a ListDetailController object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Displays the detail of a list.
   *
   * @param int $list_id
   *   The ID of the list.
   *
   * @return array|\Symfony\Component\HttpFoundation\Response
   *   The render array or response.
   */
  public function view($list_id) {
    // Load the list details from the database
    $list_details = $this->loadListDetails($list_id);
    $list_items = $this->loadListItems($list_id);
    $list_name = $this->getListName($list_id);
    $form = \Drupal::formBuilder()
      ->getForm('Drupal\news_ticker\Form\AddItemForm', $list_id);

    // Prepare the list of items to be rendered as a table
    $item_table = [
      '#type' => 'table',
      '#header' => [
        $this->t('Title'),
        $this->t('URL'),
        $this->t('Description'),
        $this->t('Operations'),
      ],
    ];

    foreach ($list_items as $item) {
      $item_table[] = [
        'title' => ['#markup' => $item['title']],
        'url' => ['#markup' => $item['url']],
        'description' => ['#markup' => $item['description']],
        'operations' => [
          '#type' => 'operations',
          '#links' => [
            'edit' => [
              'title' => $this->t('Edit'),
              'url' => Url::fromRoute('news_ticker.edit_item', [
                'list_id' => $list_id,
                'item_id' => $item['item_id'],
              ]),
            ],
            'delete' => [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('news_ticker.delete_item', [
                'list_id' => $list_id,
                'item_id' => $item['item_id'],
              ]),
            ],
          ],
        ],
      ];
    }



    $build = [
      'list_name' => [
        '#type' => 'markup',
        '#markup' => '<h1>' . $this->t('List Name: @list_name', ['@list_name' => $list_name]) . '</h1>',
      ],
      'add_item_form' => $form,
      'list_details' => [
        '#theme' => 'list_detail_item',
        '#list_details' => $list_details,

      ],
      'list_items' => $item_table,
    ];

    return $build;
  }


  protected function loadListDetails($list_id) {
    $query = $this->database->select('news_ticker_lists', 'ntl');
    $query->join('news_ticker_list_items', 'ntli', 'ntli.list_id = ntl.list_id');
    $query->fields('ntli', ['item_id', 'title', 'url', 'description']);
    $query->condition('ntl.list_id', $list_id);
    $results = $query->execute()->fetchAll();

    $list_details = [];
    foreach ($results as $result) {
      $list_details[] = [
        'item_id' => $result->item_id,
        'title' => $result->title,
        'url' => $result->url,
        'description' => $result->description,
      ];
    }

    return $list_details;
  }


  protected function loadListItems($list_id) {
    $query = $this->database->select('news_ticker_list_items', 'ntli');
    $query->fields('ntli');
    $query->condition('ntli.list_id', $list_id);
    $results = $query->execute()->fetchAll();

    $list_items = [];
    foreach ($results as $result) {
      $list_items[] = [
        'item_id' => $result->item_id,  // Ekledim.
        'title' => $result->title,
        'url' => $result->url,
        'description' => $result->description,
      ];
    }

    return $list_items;
  }

  protected function getListName($list_id) {
    $query = $this->database->select('news_ticker_lists', 'ntl');
    $query->addField('ntl', 'list_name');
    $query->condition('ntl.list_id', $list_id);
    $list_name = $query->execute()->fetchField();

    return $list_name ?: $this->t('Unknown List');
  }

}
