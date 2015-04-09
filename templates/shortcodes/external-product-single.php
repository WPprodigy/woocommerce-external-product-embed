<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* 
 * Reference for variables in this file.
 * 
 * $product_link returns the url to the product.
 * $product_image returns the url to the product's featured image.
 * $product_title returns the products title.
 * $product_rating returns a percentage to fill the stars.
 * $product_price returns the sale / normal price of the product.
 * $button returns the button text, or whether to hide it. 
 */

?>

<li class="wcepe_external_product">
	
	<?php if ( $show_image == 'show' ) : ?>
		<a href="<?php echo $product_link ?>" target="_blank">
			<img class="wcepe_external_product_image" src="<?php echo $product_image; ?>" >
		</a>
	<?php endif; ?>

	<?php if ( $show_title == 'show' ) : ?>
		<h3 class="wcepe_external_product_title">
			<a href="<?php echo $product_link ?>" target="_blank"><?php echo $product_title; ?></a>
		</h3>
	<?php endif; ?>

	<?php if ( $show_rating == 'show' ) : ?>
		<p class="wcepe_external_product_rating">
			<span style="width:<?php echo $product_rating ?>"></span>
		</p>
	<?php endif; ?>

	<?php if ( $show_price == 'show' ) : ?>
		<span class="wcepe_external_product_price"><?php echo $product_price ?></span>
	<?php endif; ?>

	<?php if ( $button != 'hide' ) : ?>
		<span class="wcepe_external_product_button">
			<a href=<?php echo $product_link ?> class="button" target="_blank"><?php echo $button ?></a>
		</span>
	<?php endif; ?>

</li>