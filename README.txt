=== WooCommerce External Product Embed ===
Author: Caleb Burks
Tags: woocommerce, products, embed, external
Requires at least: 4.0
Tested up to: 4.1
Text Domain: wcepe
License: GPL v3 or later
License URI: http://www.gnu.org/licenses/old-licenses/gpl-3.0.html

Provides a shortcode to embed products from another store.

== Description ==

This plugin provides a shortcode to embed products from another store on your site. It connects to another website running WooCommerce, and display products from that site via the shortcodes provided.

A few notes about this plugin:

*   It requires an admin account for the website running WooCommerce
*   It must make an authorized connection to the external site
*   This plugin stores transients in the wp-options table of your database

== Installation ==

1. Upload `woocommerce-external-product-embed` to the `/wp-content/plugins/` directory of the site you want to display the products on.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > WooCommerce External Products
4. Add in the [credentials](http://docs.woothemes.com/document/woocommerce-rest-api/) to connect to external site.

== Frequently Asked Questions ==

= What are the shortcodes for this plugin? =

`[external_product id=“99”]`
`[recent_external_products number=“10”]`

= Can I control what gets displayed from the shortcode? =

There are quite a few attributes that can be added to the shortcode. The only required attribute is “id” for the ‘external_product’ shortcode. Here is an example of all the attributes on the ‘external_product’ shortcode:

`[external_product id=“99” image=“show” title=“show” price=“show” rating=“show” button=“Custom Text Here”]`

Adding “hide” to any of those attributes will hide that element, including the button. 

= Where can I find the “ID” field? =

http://docs.woothemes.com/wp-content/uploads/2012/01/Find-Product-ID-in-WooCommerce-950x281.png

= Can I add multiple products per shortcode? =

Yep! `[external_product id=“10,11,12,13,14,15”]`

=  How can I edit the html output? =

You can copy the file in this plugin located at `templates/shortcodes/external-product-single.php`, and paste it into the root of your child theme like so: `theme-name/woocommerce-external-product-embed/shortcodes/external-product-single.php`. Then you can edit this file as you wish.

== Changelog ==

= 2.0 =
* Feature - New shortcode: [recent_external_products]
* Feature - Added extensibility throughout the whole plugin.
* Tweak - Combined all product-related data into a single transient.
* Tweak - Reworked the admin page for a cleaner interface.
* Tweak - Updated to the new WooCommerce API.

= 1.0 =
Initial Release!

== Upgrade Notice ==

= 2.0 =
2.0 is a major rewrite. More efficient, faster, and comes with a new shortcode! The previous shortcodes will still work, but the admin settings will be lost. So when updating, be sure to head over to the settings in order to re-enter the API details.
