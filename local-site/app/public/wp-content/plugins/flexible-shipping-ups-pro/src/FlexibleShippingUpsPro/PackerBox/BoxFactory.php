<?php
/**
 * Box factory.
 *
 * @package WPDesk\FlexibleShippingFedex
 */

namespace WPDesk\FlexibleShippingUpsPro\PackerBox;

use UpsProVendor\WPDesk\Packer\Box;
use UpsProVendor\WPDesk\Packer\BoxFactory\Boxes;
use UpsProVendor\WPDesk\Packer\BoxFactory\BoxesWithUnit;
use UpsProVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;

/**
 * Box factory for universal unit.
 *
 * @package WPDesk\PackerBox
 */
class BoxFactory implements BoxesWithUnit {

	const UNITS_METRIC = UpsSettingsDefinition::UNITS_METRIC;

	/**
	 * Boxes.
	 *
	 * @var Boxes
	 */
	private $factory;

	/**
	 * Is metric.
	 *
	 * @var bool
	 */
	private $is_metric;

	/**
	 * Returns true when metric units are used in boxes.
	 *
	 * @return bool
	 */
	public function is_metric() {
		return $this->is_metric;
	}

	/**
	 * BoxFactory constructor.
	 *
	 * @param string $length_unit metric or imperial .
	 */
	public function __construct( $length_unit ) {
		$this->is_metric = self::UNITS_METRIC === $length_unit;
		if ( ! $this->is_metric ) {
			$this->factory = new ImperialBoxFactory();
		} else {
			$this->factory = new MetricBoxFactory();
		}
	}

	/**
	 * Get boxes array.
	 *
	 * @return Box[]
	 */
	public function get_boxes() {
		return $this->factory->get_boxes();
	}

}
