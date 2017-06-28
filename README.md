# WooCommerce External Product Embed
This plugin provides a shortcode to embed products from a separate store on your site. It connects to another website running WooCommerce, and displays products from that site through the use of a shortcode.

Here is a screenshot of the products being displayed on the [frontend](http://cld.wthms.co/4TlMMw) and the settings in the [backend](http://cld.wthms.co/CeLShM).

## Installation
1. Go to the [releases tab](https://github.com/WPprodigy/woocommerce-external-product-embed/releases) in this repo.
2. Find the "Latest release" and click to download `woocommerce-external-product-embed.zip`.
3. Starting at the Plugins page in the WordPress Admin, select "Add New" and then "Upload Plugin".
3. Upload and Activate the plugin.
3. Go to Settings > WooCommerce External Products.
4. Add in the [credentials](https://docs.woocommerce.com/document/woocommerce-rest-api/) to connect to external site.

### A few notes about this plugin:
* You must have an admin account for the website running WooCommerce, or access to WooCommerce API keys.
* The external website must be using WooCommerce version 3.0 or later.

## Usage

The shortcode you can use to embed products onto your WordPress pages/posts is `[wcepe_products]`. Just using this shortcode on a page won't do anything though by default, you will need to add attributes to tell the shortcode what products you want to show.

You can show specific products by listing the IDs or SKUs like this:
- `[wcepe_products ids='96,99']`
- `[wcepe_products skus='sku1, sku2']`

You can show products from a specific category or categories by listing the category ID(s):
- `[wcepe_products category='102,119']`

You can show product that are recently added, on sale, or featured like this:
- `[wcepe_products recent=true]`
- `[wcepe_products on_sale=true]`
- `[wcepe_products featured=true]`

And best of all, you can combine any of the above! For example, you can show on-sale products from a specific category, or you can show featured products that are also on-sale.

There are some additional attributes that can also effect what is displayed.

- `orderby` - Can be set to title, menu_order, date, rand, or id.
- `order` - Set to asc or desc.
- `number` - Will determine how many products are shown.
- `columns` - Determine how many columns should be when displayed.
- `button` - Enter custom text for the "add to cart" button.
- `hide` - List certain parts of the product display you would like to hide. Can be set to image, title, rating, onsale, price, and/or button.

An example shortcode using the above attributes would look like this:
- `[wcepe_products orderby='title' order='desc' number='12' columns='4' hide='image,rating,onsale' number=12 button='View Product']`

You of course don't need to type all of that out every time. Here are the default settings for each attribute, along with the options:

```
'orderby'  => 'title', // title, menu_order, date, rand, or id.
'order'    => 'desc', // asc or desc
'number'   => 12,
'columns'  => '4', // 1-6
'recent'   => false,
'on_sale'  => false,
'featured' => false,
'hide'     => '', // image, title, rating, onsale, price, and/or button
'button'   => 'View Product',

'ids'      => '', // comma separated IDs
'skus'     => '', // comma separated SKUs
'category' => '', // comma separated category IDs
```

Note that some attributes can contradict each other. For example, using a list of `ids` and `skus` in the same shortcode will result in products only showing if both an ID and a SKU match for it.

## Frequently Asked Questions

#### What about the old shortcodes?
`[external_product]` and `[recent_external_products]` have been deprecated. They should still work on version 3.0, but you can't use many of the above options and eventually they will be removed from the plugin.

#### Where do I get the "consumer_key" and "consumer_secret"
Here is a guide on [how to generate API keys](https://docs.woocommerce.com/document/woocommerce-rest-api/)

#### How can I edit the html output?
You can copy the file in this plugin located at `templates/product-content.php`, and paste it into the root of your child theme like this: `child-theme-name/wcepe/product-content.php`. Then you can edit this file to your liking.

The `wcepe_products_loop_wrapper_open` and `wcepe_products_loop_wrapper_open` filters may also be of use if you are customizing the HTML.

#### Where can I find the Product ID or Category ID?
To find the Product ID, go to the products page in WooCommerce, hover over the product and the ID will appear as seen [here](http://cld.wthms.co/1RbKGK).

To find the Category ID, go to Products > Categories and click "Edit" for a specific category. Then look in page's URL for `tag_ID=X`, where X is the category ID. Here is [an example](http://cld.wthms.co/1Tgp0W).

#### Why don't the products look good on my website?
I tried my best to replicate WooCommerce's default styling, but ultimately it is impossible to be compatible with all WordPress themes. So you may need to customize the CSS some.

When WooCommerce is also enabled on the website where this plugin is installed, I disable the plugin's stylesheet so WooCommerce can take over the styling. You can also disable the stylesheet and use only your own styles if you would like to.

## Common Problems
Here are a few steps you can take if things don't seem to be working.

1. Make sure you aren't using "smart quotes" in the shortcode. To fix this, go to the Text Editor in WordPress and retype all of the quotation marks.

2. If the display looks incorrect, make sure you did not embed the shortcode between `<pre>` tags. To remove these tags, edit the page and click on the "Text Editor" like in the above step.

3. Ensure the external website has the REST API enabled at WooCommerce > Settings > API.

4. There may be a plugin or theme conflict on the site running WooCommerce that is interferring with the REST API. The best way to test this is to disable all plugins and switch to a default theme temporarily on the WooCommerce website. Or, you can try connecting to another demo/staging site for a quick test.

## Customizations: Code Snippets & Hooks/Filters
* Manipulate the product data: Coming Soon
* Hooks/Filters Reference: Coming Soon

## Development
If you want to work on the master branch of this plugin and submit a PR, you'll need to use Composer to get the WC API - PHP Client dependency.

1. Install [Composer](https://getcomposer.org/).
2. In terminal, cd into this plugin's directory.
3. Run `composer install`.

## Changelog
= 3.0 =
* New shortcode with many more options: [wcepe_products]
* Exponential performance improvements.
* Better error handling in both the admin and on the frontend.
* Switched to using official WC PHP Library.
* Updated to the newest version of the WooCommerce API, v2.
* Only make one API call per shortcode, not per product.
* Only create one transient per shortcode, not per product.
* Updated frontend styles.

= 2.0 =
* Feature - New shortcode: [recent_external_products]
* Feature - Added extensibility throughout the whole plugin.
* Tweak - Combined all product-related data into a single transient.
* Tweak - Reworked the admin page for a cleaner interface.
* Tweak - Updated to the new WooCommerce API.

= 1.0 =
* Initial Release!

## Upgrade Notice
3.0 is a major rewrite. The previous shortcodes will still work, but the template has changed. If you made customizations to the template, you will need to switch to using the new template file and folder structure.
