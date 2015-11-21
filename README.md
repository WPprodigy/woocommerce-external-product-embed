# WooCommerce External Product Embed
This plugin provides a shortcode to embed products from another store on your site. It connects to another website running WooCommerce, and displays products from that site via the shortcodes provided.

## Installation
1. Choose "Download Zip" in the top right corner.
2. Select "Add New" and then "Upload Plugin" starting at the Plugins page in WordPress.
3. Activate the plugin.
3. Go to Settings > WooCommerce External Products.
4. Add in the [credentials](http://docs.woothemes.com/document/woocommerce-rest-api/) to connect to external site.

### A few notes about this plugin:
* It requires an admin account for the website running WooCommerce.
* It must make an authorized connection to the external site.

## Code Snippets
* Manipulate the product data: https://gist.github.com/WPprodigy/afd0db56bf3f67379237
* Add custom settings: https://gist.github.com/WPprodigy/96dbaeab8820cc85af42

## Frequently Asked Questions

#### What are the shortcodes for this plugin?
* `[external_product id='99']`
* `[recent_external_products number='10']`

#### Where do I get the "consumer_key" and "consumer_secret"
[How to generate API keys](http://docs.woothemes.com/document/woocommerce-rest-api/)

#### Can I control what gets displayed by the shortcode?
There are quite a few attributes that can be added to the shortcode. The only required attribute is "id" for the 'external_product' shortcode. Here is an example of all the attributes available for each shortcode:

* `[external_product id="99,22" image="show" title="show" price="show" rating="show" button="Custom Text Here"]`
* `[recent_external_products number="4" image="show" title="show" price="show" rating="show" button="Custom Text Here"]`

Adding "hide" to any of those attributes will hide that element, including the button.

#### Can I add multiple products per shortcode?
Yep! `[external_product id="10,11,12,13,14,15"]`

#### How can I edit the html output?
You can copy the file in this plugin located at `templates/shortcodes/external-product-single.php`, and paste it into the root of your child theme like so: `child-theme-name/woocommerce-external-product-embed/shortcodes/external-product-single.php`. Then you can edit this file to your liking.

#### Where can I find the Product "ID" field?
To find the Product ID, go to the products page in WooCommerce, hover over the product and the ID will appear as shown below.
![How to find the product ID](http://docs.woothemes.com/wp-content/uploads/2012/01/Find-Product-ID-in-WooCommerce-950x281.png)

## Basic Debugging
Here are a few steps you can take if things don't seem to be working.

1. Make sure the shortcode is not using "smart quotes". Keep them dumb. tl;dr - smart quotes are evil. To fix this, go to the Text Editor in WordPress and retype all of the quotes.

2. If the display looks incorrect, make sure you did not embed the shortcode between `<pre>` tags. To remove these tags, edit the page, and click on the "Text Editor" like in the above step.

3. There may be a plugin or theme conflict on the site running WooCommerce that is interferring with the API. The best way to test this is to disable all plugins and switch to a default theme temporarily on the WooCommerce website.

4. It is possible that the web host is not allowing remote connections to be made. You can contact your host and ask if this is the case.

## Changelog
= 2.0 =
* Feature - New shortcode: [recent_external_products]
* Feature - Added extensibility throughout the whole plugin.
* Tweak - Combined all product-related data into a single transient.
* Tweak - Reworked the admin page for a cleaner interface.
* Tweak - Updated to the new WooCommerce API.

= 1.0 =
* Initial Release!

## Upgrade Notice
= 2.0 =
2.0 is a major rewrite. More efficient, faster, and comes with a new shortcode! The previous shortcodes will still work, but the admin settings will be lost. So when updating, be sure to head over to the settings in order to re-enter the API details.
