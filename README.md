# News Ticker for Drupal

News Ticker is a newsletter module developed for Drupal. This module allows you to display news updates on your Drupal site in a scrolling fashion.

## Features

- Scrolling news ticker
- Customizable styles via CSS
- Easy integration with your Drupal site

## Installation

You can install the module in two ways:

### Option 1: Download from this repository

1. Download the module from this repository.
2. Place the module in your Drupal site's module directory (e.g., `/sites/all/modules/`).
3. Enable the module in the Drupal module administration interface.

### Option 2: Using Composer

1. Open your terminal or command prompt.
2. Navigate to your Drupal project's root directory.
3. Run the following command:
composer require msayilan/news-ticker

4. Enable the module in the Drupal module administration interface.

## Usage

After installing the module, you can add the news ticker to your site by following these steps:

1. Navigate to "Manage News Ticker" in the Structure menu.
2. Create lists from there and enter into lists by clicking on their names, then add items to the lists.
3. Add a section on the page where you want to display the lists and click on "Add block."
4. Add the "News Ticker" block and select the desired list from there.
5. Save your changes.

To customize the layout of the content items, you need to enable the Layout Builder for the respective content type. Follow these steps:

1. Go to "Structure" in the Drupal administration menu.
2. Select "Content types" and choose the desired content type.
3. Click on the "Manage display" tab.
4. Under the desired content item, click on the "Layout" or "Manage layout" button.
5. In the Layout Builder interface, you can customize the layout of the content item.
6. Save your changes.

You can also allow each content item to have its layout customized by selecting the "Allow each content item to have its layout customized" option.

The news ticker should now appear on your site with the customized layout.

## License

This project is licensed under the MIT License. Users are free to modify and distribute it as they see fit, as long as they provide attribution with links. For details, see the LICENSE file.
