<?php

namespace Drupal\news_ticker\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * EditItemForm.
 */
class EditItemForm extends FormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new EditItemForm object.
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
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'news_ticker_edit_item_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $list_id = NULL, $item_id = NULL) {
    // Load the item from the database
    $item = $this->loadItem($list_id, $item_id);

    $form['list_id'] = [
      '#type' => 'hidden',
      '#value' => $list_id,
    ];
    $form['item_id'] = [
      '#type' => 'hidden',
      '#value' => $item_id,
    ];
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $item['title'],
      '#required' => TRUE,
    ];
    $form['url'] = [
      '#type' => 'url',
      '#title' => $this->t('URL'),
      '#default_value' => $item['url'],
      '#required' => TRUE,
    ];
    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $item['description'],
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Changes'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validation is optional.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Update the item in the database.
    $time = REQUEST_TIME;
    $list_id = $form_state->getValue('list_id');
    $item_id = $form_state->getValue('item_id');
    $this->database->update('news_ticker_list_items')
      ->fields([
        'title' => $form_state->getValue('title'),
        'url' => $form_state->getValue('url'),
        'description' => $form_state->getValue('description'),
        // 'changed' => $time,
      ])
      ->condition('list_id', $list_id)
      ->condition('item_id', $item_id)
      ->execute();

    // Redirect back to the list items page.
    $form_state->setRedirect('news_ticker.list_detail', ['list_id' => $list_id]);

    \Drupal::messenger()
      ->addMessage($this->t('The news ticker item has been updated.'));
  }


  /**
   * Load an item from the database.
   *
   * @param int $list_id
   *   The list ID.
   * @param int $item_id
   *   The item ID.
   *
   * @return array
   *   The item data.
   */
  protected function loadItem($list_id, $item_id) {
    $query = $this->database->select('news_ticker_list_items', 'n')
      ->fields('n')
      ->condition('list_id', $list_id)
      ->condition('item_id', $item_id)
      ->range(0, 1);
    return $query->execute()->fetchAssoc();
  }

}
