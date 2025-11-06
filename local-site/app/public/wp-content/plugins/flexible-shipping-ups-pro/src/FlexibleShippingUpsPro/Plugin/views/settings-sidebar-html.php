<?php
/**
 * Settings sidebar.
 *
 * @package WPDesk\FlexibleShippingUpsPro
 *
 * @var $url string .
 */

?>
<div class="wpdesk-metabox">
	<div class="wpdesk-stuffbox">
		<h3 class="title"><?php esc_html_e( 'Make everything easier with Flexible Shipping UPS Labels!', 'flexible-shipping-ups-pro' ); ?></h3>
		<div class="inside">
			<div class="main">
				<p><?php esc_html_e( 'Extend the current UPS PRO functionalities and:', 'flexible-shipping-ups-pro' ); ?></p>
				<ul>
					<li>
						<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Create the shipments', 'flexible-shipping-ups-pro' ); ?>
					</li>
					<li>
						<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Generate the UPS shipping labels', 'flexible-shipping-ups-pro' ); ?>
					</li>
					<li>
						<span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Add the tracking links', 'flexible-shipping-ups-pro' ); ?>
					</li>
				</ul>

				<p><?php esc_html_e( 'Now you can do it all directly from your WooCommerce store!', 'flexible-shipping-ups-pro' ); ?></p>

				<a class="button button-primary" href="<?php echo esc_url( $url ); // @phpstan-ignore-line ?>"
				   target="_blank"><?php esc_html_e( 'Buy now UPS Labels →', 'flexible-shipping-ups-pro' ); ?></a>
			</div>
		</div>
	</div>
</div>
