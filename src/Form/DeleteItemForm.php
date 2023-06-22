<?php

namespace Drupal\news_ticker\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class DeleteItemForm extends ConfirmFormBase {

  protected $list_id;

  protected $item_id;

  public function buildForm(array $form, FormStateInterface $form_state, $list_id = NULL, $item_id = NULL) {
    $this->list_id = $list_id;
    $this->item_id = $item_id;
    return parent::buildForm($form, $form_state);
  }

  public function getFormId() {
    return 'delete_item_form';
  }

  public function getQuestion() {
    return t('Are you sure you want to delete this item?');
  }

  public function getCancelUrl() {
    return new Url('news_ticker.list_detail', ['list_id' => $this->list_id]);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->deleteItem();
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  private function deleteItem() {
    $connection = \Drupal::database();
    $connection->delete('news_ticker_list_items')
      ->condition('item_id', $this->item_id) // assuming 'title' is unique and is used as the identifier here.
      ->execute();
  }


}
