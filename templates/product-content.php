<?php
/**
 * The template for displaying product content within loops.
 * This template can be overridden by copying it to yourtheme/wcepe/content-product.php.
 *
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( empty( $product ) ) {
	return;
}
?>

<li class="wcepe_external_product product">

	<a href="<?php echo $product['link'] ?>" class="woocommerce-LoopProduct-link" target="_blank">
		<img src="<?php echo $product['image']['src']; ?>" title="<?php echo $product['image']['name']; ?>" alt="<?php echo $product['image']['alt']; ?>">

		<h2 class="woocommerce-loop-product__title"><?php echo $product['title']; ?></h2>

		<?php if ( $product['rating'] ) : ?>
			<p class="star-rating">
				<span style="width:<?php echo $product['rating'] ?>"></span>
			</p>
		<?php endif; ?>

		<?php if ( $product['on_sale'] ) : ?>
			<span class="onsale">Sale!</span>
		<?php endif; ?>

		<span class="price"><?php echo $product['price'] ?></span>
	</a>

	<a href=<?php echo $product['link'] ?> class="button add_to_cart_button" target="_blank">View Product</a>

</li>
