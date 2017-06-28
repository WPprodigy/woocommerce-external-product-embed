<?php
/**
 * The template for displaying product content within loops.
 * This template can be overridden by copying it to yourtheme/wcepe/content-product.php.
 *
 * @version 3.0.0
 */

 /*
 * Reference for variables in this file.
 *
 * $product is an array that looks like the following:
 * Array (
 * 	[title]  => Product Title
 * 	[image]  => Array (
 *		[src]  => https://example.com/image.jpg
 *		[name] => Image Title
 *		[alt]  => Image Alt Text
 * )
 * 	[price]   => $2.00 (contains html and sale prices)
 * 	[link]    => https://example.com/product/woo-product/
 * 	[rating]  => 77px
 * 	[on_sale] => 1
 * )
 *
 * $atts contains all of the attributes passed into the shortcode.
 * The two keys used in this template will look like this:
 * Array (
 * 	[button] => View Product
 * 	[parts_to_hide] => Array (
 *		[0] => image
 *		[1] => title
 *		[2] => rating
 *		[3] => onsale
 *		[4] => price
 *		[5] => button
 * 	)
 * )
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<li class="wcepe_external_product product">

	<a href="<?php echo $product['link'] ?>" class="woocommerce-LoopProduct-link" target="_blank">

		<?php if ( ! in_array( 'image', $atts['parts_to_hide'] ) ) : ?>
			<img src="<?php echo $product['image']['src']; ?>" title="<?php echo $product['image']['name']; ?>" alt="<?php echo $product['image']['alt']; ?>">
		<?php endif; ?>

		<?php if ( ! in_array( 'title', $atts['parts_to_hide'] ) ) : ?>
			<h2 class="woocommerce-loop-product__title"><?php echo $product['title']; ?></h2>
		<?php endif; ?>

		<?php if ( $product['rating'] && ! in_array( 'rating', $atts['parts_to_hide'] ) ) : ?>
			<p class="star-rating">
				<span style="width:<?php echo $product['rating'] ?>"></span>
			</p>
		<?php endif; ?>

		<?php if ( $product['on_sale'] && ! in_array( 'onsale', $atts['parts_to_hide'] ) ) : ?>
			<span class="onsale">Sale!</span>
		<?php endif; ?>

		<?php if ( ! in_array( 'price', $atts['parts_to_hide'] ) ) : ?>
			<span class="price"><?php echo $product['price'] ?></span>
		<?php endif; ?>
	</a>

	<?php if ( ! in_array( 'button', $atts['parts_to_hide'] ) ) : ?>
		<a href=<?php echo $product['link'] ?> class="button add_to_cart_button" target="_blank"><?php echo $atts['button'] ?></a>
	<?php endif; ?>

</li>
