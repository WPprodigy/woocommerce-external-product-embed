<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* 
 * Reference for variables in this file.
 * 
 * $product['title']  returns the products title.
 * $product['image']  returns the url to the product's featured image.
 * $product['price']  returns the sale / normal price of the product.
 * $product['link']   returns the url to the product.
 * $product['rating'] returns a pixel-width to fill the rating stars.
 * $button returns the button text, or whether to hide it. 
 */

?>

<li class="wcepe_external_product">
	
	<?php if ( $image == 'show' ) : ?>
		<a href="<?php echo $product['link'] ?>" target="_blank">
			<img class="wcepe_external_product_image" src="<?php echo $product['image']; ?>" >
		</a>
	<?php endif; ?>

	<?php if ( $title == 'show' ) : ?>
		<h3 class="wcepe_external_product_title">
			<a href="<?php echo $product['link'] ?>" target="_blank"><?php echo $product['title']; ?></a>
		</h3>
	<?php endif; ?>

	<?php if ( $rating == 'show' ) : ?>
		<p class="wcepe_external_product_rating">
			<span style="width:<?php echo $product['rating'] ?>"></span>
		</p>
	<?php endif; ?>

	<?php if ( $price == 'show' ) : ?>
		<span class="wcepe_external_product_price"><?php echo $product['price'] ?></span>
	<?php endif; ?>

	<?php if ( $button != 'hide' ) : ?>
		<span class="wcepe_external_product_button">
			<a href=<?php echo $product['link'] ?> class="button" target="_blank"><?php echo $button ?></a>
		</span>
	<?php endif; ?>

</li>