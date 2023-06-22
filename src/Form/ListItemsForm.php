<?php
namespace Drupal\news_ticker\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ListItemsForm extends FormBase {

protected $routeMatch;

public function __construct(RouteMatchInterface $route_match) {
$this->routeMatch = $route_match;
}

public static function create(ContainerInterface $container) {
return new static(
$container->get('current_route_match')
);
}

public function getFormId() {
return 'news_ticker_list_items_form';
}

public function buildForm(array $form, FormStateInterface $form_state) {
$list_id = $this->routeMatch->getParameter('list_id');

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

$form['item_list'] = [
'#type' => 'table',
'#header' => [
$this->t('Title'),
$this->t('URL'),
$this->t('Description'),
],
'#empty' => $this->t('No items found.'),
];

// Retrieve the items from the database for the specified list.
$items = $this->getItemsByListId($list_id);

// Populate the item list table.
foreach ($items as $item) {
$form['item_list'][] = [
'title' => [
'#plain_text' => $item->title,
],
'url' => [
'#type' => 'link',
'#title' => $item->url,
'#url' => Url::fromUri($item->url),
],
'description' => [
'#plain_text' => $item->description,
],
];
}

return $form;
}

public function validateForm(array &$form, FormStateInterface $form_state) {
// Add any custom form validation if needed.
}

public function submitForm(array &$form, FormStateInterface $form_state) {
$list_id = $this->routeMatch->getParameter('list_id');
$title = $form_state->getValue('title');
$url = $form_state->getValue('url');
$description = $form_state->getValue('description');

// Save the item to the database or perform any other necessary actions.
$this->saveItem($list_id, $title, $url, $description);

// Display a success message to the user.
$this->messenger()->addStatus($this->t('Item added successfully.'));
}

private function getItemsByListId($list_id) {
// Query the database to retrieve the items for the specified list.
// Implement your own logic based on your database structure.
// Example code:
// $query = \Drupal::database()->select('news_ticker_items', 'nti');
// $query->fields('nti', ['title', 'url', 'description']);
// $query->condition('list_id', $list_id);
// $items = $query->execute()->fetchAll();

// Return the items.
// return $items;

// Mock data for demonstration purposes.
$items = [
(object) [
'title' => 'Item 1',
'url' => 'https://example.com/item-1',
'description' => 'Description for Item 1',
],
(object) [
'title' => 'Item 2',
'url' => 'https://example.com/item-2',
'description' => 'Description for Item 2',
],
];

return $items;
}

private function saveItem($list_id, $title, $url, $description) {
// Save the item to the database.
// Implement your own logic based on your database structure.
}

}
