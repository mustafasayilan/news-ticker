<?php

namespace Drupal\news_ticker\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class AddItemForm extends FormBase {

  public function getFormId() {
    return 'news_ticker_add_item_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state, $list_id = NULL) {
    $form['list_id'] = [
      '#type' => 'hidden',
      '#value' => $list_id,
    ];
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#required' => TRUE,
    ];

    $form['url'] = [
      '#type' => 'url',
      '#title' => $this->t('URL'),
      '#required' => TRUE,
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Item'),
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Add any custom form validation if needed.
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $title = $form_state->getValue('title');
    $url = $form_state->getValue('url');
    $description = $form_state->getValue('description');
    $list_id = $form_state->getValue('list_id');

    // item array includes list_id now
    $item = [
      'title' => $title,
      'url' => $url,
      'description' => $description,
      'list_id' => $list_id, // Include the list_id when saving the item.
    ];

    $this->saveItem($item);

    $this->messenger()->addStatus($this->t('Item added successfully.'));
  }


  private function saveItem(array $item) {
    $connection = \Drupal::database();
    $connection->insert('news_ticker_list_items')
      ->fields([
        'list_id' => $item['list_id'],
        'title' => $item['title'],
        'url' => $item['url'],
        'description' => $item['description'],
      ])
      ->execute();
  }


}
