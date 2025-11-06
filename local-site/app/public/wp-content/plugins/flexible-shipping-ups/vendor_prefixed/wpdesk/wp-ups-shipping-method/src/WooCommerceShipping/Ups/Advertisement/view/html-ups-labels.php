<?php

namespace UpsFreeVendor;

/**
 * @var string $url
 */
use UpsFreeVendor\WPDesk\WooCommerceShipping\Ups\Advertisement\AjaxActions;
if (!\defined('ABSPATH')) {
    exit;
}
?><div class="ups-label-advertisement">
	<?php 
\esc_html_e('Save time with automated shipping fulfillment, generate and download the print-ready shipping labels and track parcels directly in your store.', 'flexible-shipping-ups');
?>
	<a href="<?php 
echo \esc_url($url);
?>" target="_blank"><?php 
echo \esc_html__('Get UPS Label plugin', 'flexible-shipping-ups');
?></a> <a href="#" class="ups-label-advertisement-close"><?php 
\esc_html_e('[x]', 'flexible-shipping-ups');
?></a>
</div>
<script>
	jQuery( document ).ready( function( $ ) {
		$( '.ups-label-advertisement-close' ).on( 'click', function( e ) {
			e.preventDefault();
			$( '.ups-label-advertisement' ).remove();
			$.ajax( {
				url: '<?php 
echo \esc_url(\admin_url('admin-ajax.php'));
?>',
				type: 'POST',
				data: {
					action: '<?php 
echo \esc_js(AjaxActions::AJAX_ACTION);
?>',
					security: '<?php 
echo \wp_create_nonce(AjaxActions::AJAX_ACTION);
?>'
				}
			} );
		} );
	} );
</script>
<?php 
