<?php

namespace Drupal\news_ticker\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EditListForm extends FormBase {

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
    return 'news_ticker_edit_list_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $list_id = NULL) {
    // Fetch the list details from the database
    $query = $this->connection->select('news_ticker_lists', 'ntl');
    $query->fields('ntl', ['list_id', 'list_name']);
    $query->condition('ntl.list_id', $list_id);
    $list = $query->execute()->fetchObject();

    if (!$list) {
      return [
        '#markup' => $this->t('List not found.'),
      ];
    }

    $form['list_id'] = [
      '#type' => 'value',
      '#value' => $list->list_id,
    ];

    $form['list_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('List name'),
      '#default_value' => $list->list_name,
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save changes'),
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (empty(trim($form_state->getValue('list_name')))) {
      $form_state->setErrorByName('list_name', $this->t('Please enter a list name.'));
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $list_id = $form_state->getValue('list_id');
    $list_name = $form_state->getValue('list_name');

    // Update the list in the database
    $this->connection->update('news_ticker_lists')
      ->fields([
        'list_name' => $list_name,
      ])
      ->condition('list_id', $list_id)
      ->execute();

    $this->messenger()->addStatus($this->t('List updated successfully.'));
    $form_state->setRedirect('news_ticker.add_form');
  }

}
