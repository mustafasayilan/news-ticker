<?php

namespace Drupal\news_ticker\Service;

use Drupal\Core\Database\Connection;

/**
 * Service class for the Ticker block.
 */
class TickerService {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new TickerService object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * Retrieves the available list options for the Ticker block form.
   *
   * @return array
   *   An array of list options.
   */
  public function getListOptions() {
    $query = $this->database->select('news_ticker_lists', 'ntl');
    $query->fields('ntl', ['list_id', 'list_name']);
    $results = $query->execute()->fetchAllKeyed();

    return $results;
  }

  /**
   * Retrieves the items for the selected lists.
   *
   * @param array $listIds
   *   An array of list IDs.
   *
   * @return array
   *   An array of list items.
   */
  public function getListItems(array $listIds) {

    $listItems = [];

    $query = $this->database->select('news_ticker_list_items', 'ntli');
    $query->fields('ntli');
    $query->condition('ntli.list_id', $listIds, 'IN');
    $results = $query->execute()->fetchAll();

    foreach ($results as $result) {
      $listItems[] = [
        'title' => $result->title,
        'url' => $result->url,
        'description' => $result->description,
      ];
    }

    return $listItems;
  }


}
