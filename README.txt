=== WooCommerce External Product Embed ===
Author: Caleb Burks
Tags: woocommerce, products, embed, external'
Requires at least: 1.0
Tested up to: 4.1.1
Stable tag: 4.1.1
License: GPL v3 or later
License URI: http://www.gnu.org/licenses/old-licenses/gpl-3.0.html

Provides a shortcode to embed products from another store.

== Description ==

This plugin can be used to connect to another website running WooCommerce, and display products from that site on the site that this plugin is installed on. 

A few notes about this plugin:

*   It requires an admin account for the website running WooCommerce
*   It must make an authorized connection to the external site
*   And this plugin stores transients in the wp-options table

== Installation ==

1. Upload `woocommerce-external-product-embed` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Settings > WooCommerce External Products
1. Add in the credentials to connect to external site

== Frequently Asked Questions ==

= What is the shortcode for this plugin? =

`[external_product id=“99”]`

= Can I control what gets displayed from the shortcode? =

There are quite a few attributes that can be added to the shortcode. The only required attribute is “id”. Here is an example of all the attributes:

`[external_product id=“99” image=“show” title=“show” price=“show” rating=“show” button=“Custom Text Here”]`

Adding “hide” to any of those attributes will hide that element, including button. 

= Where can I find the “ID” field? =

**Insert Link here**

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0 =
* No changes yet!

= 0.1 =
* Fixes will be added when I come across them:)

== Upgrade Notice ==

= 1.0 =
Official Release (yet to happen)

= 0.1 =
Initial testing
