<?php

namespace Drupal\news_ticker\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class DeleteListForm extends ConfirmFormBase {

  protected $list_id;

  public function buildForm(array $form, FormStateInterface $form_state, $list_id = NULL) {
    $this->list_id = $list_id;
    return parent::buildForm($form, $form_state);
  }

  public function getFormId() {
    return 'delete_list_form';
  }

  public function getQuestion() {
    return t('Are you sure you want to delete this list?');
  }

  public function getCancelUrl() {
    return new Url('news_ticker.add_form');
  }

  public function getRedirectUrl() {
    return new Url('news_ticker.add_form');
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->deleteList();

    $this->messenger()
      ->addStatus($this->t('The list has been deleted successfully.'));
    $form_state->setRedirectUrl($this->getRedirectUrl());
  }

  private function deleteList() {
    $connection = \Drupal::database();

    // First, delete all items in the list.
    $connection->delete('news_ticker_list_items')
      ->condition('list_id', $this->list_id)
      ->execute();

    // Then, delete the list itself.
    $connection->delete('news_ticker_lists')
      ->condition('list_id', $this->list_id)
      ->execute();
  }


}
