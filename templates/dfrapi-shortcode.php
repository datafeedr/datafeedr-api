<?php
/**
 * @var Dfrapi_Api_Response $response
 */
?>
<pre><?php print_r( $response->safe_request_data() ); ?></pre>
<div><strong>Products: </strong><?php esc_html_e( $response->number_of_results_returned() ); ?></div>
<div><strong>Updated: </strong><?php esc_html_e( $response->get_date() ); ?></div>
<div style="background-color:#dcdcdc;">
	<?php foreach ( $response->records() as $k => $product ) : ?>
		<?php
		$name        = $product['name'] ?? 'Product';
		$image       = $product['image'] ?? $product['thumbnail'] ?? '';
		$merchant    = $product['merchant'];
		$tags        = $product['tags'] ?? 'n/a';
		$barcode     = $product['barcode'] ?? 'n/a';
		$id          = absint( $product['_id'] );
		$merchant_id = absint( $product['merchant_id'] );
		$source_id   = absint( $product['source_id'] );
		$reg_price   = esc_html( dfrapi_get_price( $product['price'], ( $product['currency'] ?? 'USD' ), 'shortcode' ) );
		$final_price = esc_html( dfrapi_get_price( $product['finalprice'], ( $product['currency'] ?? 'USD' ), 'shortcode' ) );
		$is_on_sale  = (bool) $product['onsale'];
		?>
        <div href="'<?php echo esc_url( dfrapi_url( $product ) ); ?>"
             style="display:flex;flex-direction:row;align-items: center; justify-content:space-between; font-size:10px;background-color:#fff;padding:1rem;margin:1px;text-decoration:none !important;border:0;">
            <div style="width:15%;padding-right: 1rem;">
                <img src="<?php echo esc_url( $image ); ?>" alt="Product image">
            </div>
            <div style="width:70%;">
                <strong><?php esc_html_e( $name ); ?></strong><br/>
				<?php esc_html_e( $merchant ); ?> - <?php esc_html_e( $source_id ); ?>
                / <?php esc_html_e( $merchant_id ); ?> / <?php esc_html_e( $id ); ?>
                <br>Barcode: <?php esc_html_e( $barcode ); ?>
                <br>Tags: <?php esc_html_e( $tags ); ?>
            </div>
            <div style="width:15%;text-align: right;">
				<?php if ( $is_on_sale ) : ?>
                    <div style="text-decoration: line-through"><?php esc_html_e( $reg_price ); ?></div>
				<?php endif; ?>
                <div><?php esc_html_e( $final_price ); ?></div>
            </div>
        </div>
	<?php endforeach; ?>
</div>
