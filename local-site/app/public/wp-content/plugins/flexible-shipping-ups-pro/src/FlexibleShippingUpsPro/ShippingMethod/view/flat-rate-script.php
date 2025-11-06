<?php
/**
 * Flat rate settings script.
 *
 * @package WPDesk\FlexibleShippingUpsPro\ShippingMethod
 */

use UpsProVendor\WPDesk\UpsProShippingService\CollectionPointFlatRate\CollectionPointFlatRateSettingsDefinitionDecorator;

?><script type="text/javascript">
	jQuery( document ).ready( function () {
		let $access_point_field = jQuery('#woocommerce_flexible_shipping_ups_access_point');
		let access_point_value_flat_rate = '<?php echo esc_attr( CollectionPointFlatRateSettingsDefinitionDecorator::OPTION_ACCESS_POINT_FLAT_RATE ); ?>';
		let $no_flat_rate_fields = jQuery( '.<?php echo esc_attr( \WPDesk\FlexibleShippingUpsPro\ShippingMethod\UpsProCollectionPointFlatRateSettings::FS_UPS_NO_FLAT_RATE ); ?>' ).closest('tr');
		let $flat_rate_fields = jQuery( '.<?php echo esc_attr( \WPDesk\FlexibleShippingUpsPro\ShippingMethod\UpsProCollectionPointFlatRateSettings::FS_UPS_FLAT_RATE ); ?>' ).closest('tr');
		let $no_flat_rate_h3 = jQuery( 'h3.<?php echo esc_attr( \WPDesk\FlexibleShippingUpsPro\ShippingMethod\UpsProCollectionPointFlatRateSettings::FS_UPS_NO_FLAT_RATE ); ?>' );
		let $flat_rate_h3 = jQuery( 'h3.<?php echo esc_attr( \WPDesk\FlexibleShippingUpsPro\ShippingMethod\UpsProCollectionPointFlatRateSettings::FS_UPS_FLAT_RATE ); ?>' );
		let $shipping_boxes = jQuery( '.settings-field-boxes' ).closest('tr');

		function fs_ups_pro_access_point_change() {
			let access_point_value = $access_point_field.val();
			if ( access_point_value === access_point_value_flat_rate ) {
				$no_flat_rate_fields.hide();
				$no_flat_rate_h3.hide();
				$shipping_boxes.hide();
				$no_flat_rate_h3.next('p').hide();
				$flat_rate_fields.show();
				$flat_rate_h3.show();
				$flat_rate_h3.next('p').show();
			} else {
				$flat_rate_fields.hide();
				$flat_rate_h3.hide();
				$flat_rate_h3.next('p').hide();
				$no_flat_rate_fields.show();
				$no_flat_rate_h3.show();
				$no_flat_rate_h3.next('p').show();
				jQuery( '#woocommerce_flexible_shipping_ups_fallback' ).change();
				jQuery( '#woocommerce_flexible_shipping_ups_custom_services' ).change();
				jQuery( '#woocommerce_flexible_shipping_ups_surepost_custom_services' ).change();
				jQuery( '#woocommerce_flexible_shipping_ups_price_adjustment_type' ).change();
				jQuery( '#woocommerce_flexible_shipping_ups_delivery_dates' ).change();
				jQuery( '#woocommerce_flexible_shipping_ups_packing_method' ).change();
			}
			jQuery( '.custom_origin' ).change();
		}

		$access_point_field.change( function () {
			fs_ups_pro_access_point_change()
		});

		let $delivery_dates_field = jQuery( '#woocommerce_flexible_shipping_ups_delivery_dates' );

		function update_lead_max_cutoff_fields_visibility() {
			let delivery_dates_val = $delivery_dates_field.val();
			let lead_time_tr = jQuery( '#woocommerce_flexible_shipping_ups_lead_time' ).closest( 'tr' );
			let maximum_transit_time_tr = jQuery( '#woocommerce_flexible_shipping_ups_maximum_transit_time' ).closest( 'tr' );
			let cutoff_time_tr = jQuery('#woocommerce_flexible_shipping_ups_cutoff_time' ).closest( 'tr' );
			let blackout_lead_days = jQuery('#woocommerce_flexible_shipping_ups_blackout_lead_days' ).closest( 'tr' );

			if ( delivery_dates_val === 'none' ) {
				lead_time_tr.hide();
				maximum_transit_time_tr.hide();
				cutoff_time_tr.hide();
				blackout_lead_days.hide();
			} else {
				lead_time_tr.show();
				maximum_transit_time_tr.show();
				cutoff_time_tr.show();
				blackout_lead_days.show();
			}
		}

		$delivery_dates_field.change(function() {
			update_lead_max_cutoff_fields_visibility();
		});

		update_lead_max_cutoff_fields_visibility();
		fs_ups_pro_access_point_change();

	});
</script>
