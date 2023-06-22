<?php

namespace Drupal\news_ticker\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;


class AddListForm extends FormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  public function getFormId() {
    return 'news_ticker_add_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['list_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('List name'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add list'),
    ];

    // Fetch existing lists from the database
    $query = $this->connection->select('news_ticker_lists', 'ntl');
    $query->fields('ntl', ['list_id', 'list_name']);
    $results = $query->execute()->fetchAll();

    if ($results) {
      $header = [
        $this->t('List ID'),
        $this->t('List Name'),
        $this->t('Operations'),
      ];

      $rows = [];
      foreach ($results as $result) {
        $rows[] = [
          'data' => [
            'list_id' => $result->list_id,
            'list_name' => [
              'data' => [
                '#type' => 'link',
                '#title' => $result->list_name,
                '#url' => Url::fromRoute('news_ticker.list_detail', ['list_id' => $result->list_id]),
              ],
            ],
            'operations' => $this->generateOperationsLinks($result->list_id),
          ],
        ];
      }

      $form['existing_lists'] = [
        '#type' => 'container',
        '#theme' => 'add_list_form_existing_lists',
        '#rows' => $rows,
        '#empty' => $this->t('No lists found.'),
      ];

    }

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (empty(trim($form_state->getValue('list_name')))) {
      $form_state->setErrorByName('list_name', $this->t('Please enter a list name.'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $list_name = $form_state->getValue('list_name');

    // Add the list to the database
    $this->connection->insert('news_ticker_lists')
      ->fields([
        'list_name' => $list_name,
      ])
      ->execute();

    $this->messenger()->addStatus($this->t('List added successfully.'));
  }

  /**
   * Generate operation links for the existing lists.
   *
   * @param int $list_id
   *   The list ID.
   *
   * @return array
   *   An array of link render arrays.
   */
  protected function generateOperationsLinks($list_id) {
    $links = [];

    $links['edit'] = [
      'title' => $this->t('Edit'),
      'url' => \Drupal\Core\Url::fromRoute('news_ticker.edit_form', ['list_id' => $list_id]),
    ];

    $links['delete'] = [
      'title' => $this->t('Delete'),
      'url' => \Drupal\Core\Url::fromRoute('news_ticker.delete_form', ['list_id' => $list_id]),
    ];

    return [
      '#type' => 'operations',
      '#links' => $links,
    ];
  }




}

