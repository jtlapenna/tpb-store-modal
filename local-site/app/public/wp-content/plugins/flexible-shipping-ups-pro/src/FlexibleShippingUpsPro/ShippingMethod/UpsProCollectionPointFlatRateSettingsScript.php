<?php
/**
 * Flat rate setting script.
 *
 * @package WPDesk\FlexibleShippingUpsPro\ShippingMethod
 */

namespace WPDesk\FlexibleShippingUpsPro\ShippingMethod;

/**
 * Can render flat rate script.
 */
class UpsProCollectionPointFlatRateSettingsScript {

	/**
	 * Render script.
	 *
	 * @return string
	 */
	public function render() {
		ob_start();
		include __DIR__ . '/view/flat-rate-script.php';
		return ob_get_clean();
	}

}
