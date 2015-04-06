# WooCommerce External Product Embed

This plugin provides a shortcode to embed products from another store on your site. It connects to another website running WooCommerce, and display products from that site via the shortcodes provided.

## Installation

1. Upload `woocommerce-external-product-embed` to the `/wp-content/plugins/` directory of the site you want to display the products on.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > WooCommerce External Products
4. Add in the [credentials](http://docs.woothemes.com/document/woocommerce-rest-api/) to connect to external site

### A few notes about this plugin:

*   It requires an admin account for the website running WooCommerce
*   It must make an authorized connection to the external site
*   This plugin stores transients in the wp-options table of your database

## Frequently Asked Questions

#### What is the shortcode for this plugin?

`[external_product id=“99”]`

#### Where can I find the “ID” field?

To find the Product ID, go to the Products screen, hover over the product and the ID will appear as shown below.
![Show Product ID](http://docs.woothemes.com/wp-content/uploads/2012/01/Find-Product-ID-in-WooCommerce-950x281.png)

#### Where do I get the "consumer_key" and "consumer_secret"

[Generating API keys](http://docs.woothemes.com/document/woocommerce-rest-api/)

#### Can I control what gets displayed by the shortcode?

There are quite a few attributes that can be added to the shortcode. The only required attribute is “id”. Here is an example of all the attributes:

`[external_product id=“99” image=“show” title=“show” price=“show” rating=“show” button=“Custom Text Here”]`

Adding “hide” to any of those attributes will hide that element, including the button. 

#### Can I add multiple products per shortcode?

Yep! [external_products id=“10,11,12,13,14,15”]
