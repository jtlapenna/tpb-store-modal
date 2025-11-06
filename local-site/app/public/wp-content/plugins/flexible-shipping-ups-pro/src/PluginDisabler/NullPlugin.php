<?php
/**
 *
 * Null plugin.
 *
 * @package WPDesk\FreeDisabler
 */

namespace WPDesk\FreeDisabler;

use UpsFreeVendor\WPDesk\PluginBuilder\Plugin\SlimPlugin;

/**
 * Can be injected into UpsFreeVendor plugin builder to disable plugin.
 *
 * @package WPDesk\FreeDisabler
 */
final class NullPlugin extends SlimPlugin {
	/**
	 * Some null text-domain.
	 *
	 * @return string
	 */
	public function get_text_domain() {
		return 'null-text-domain';
	}

	/**
	 * Disabled init.
	 */
	public function init() {
		// do nothing.
	}

}
