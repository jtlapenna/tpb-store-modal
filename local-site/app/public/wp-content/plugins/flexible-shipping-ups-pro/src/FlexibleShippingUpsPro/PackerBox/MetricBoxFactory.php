<?php
/**
 * Matric box factory.
 *
 * @package WPDesk\FlexibleShippingFedex
 */

namespace WPDesk\FlexibleShippingUpsPro\PackerBox;

use UpsProVendor\WPDesk\Packer\Box;
use UpsProVendor\WPDesk\Packer\Box\BoxImplementation;
use UpsProVendor\WPDesk\Packer\BoxFactory\BoxesWithUnit;

/**
 * Metric box factory.
 */
class MetricBoxFactory implements BoxesWithUnit {

	const BOXES = [
		'01' => [
			'name'   => 'UPS Letter',
			'length' => '31.75',
			'width'  => '24',
			'height' => '0.25',
			'weight' => '0.5',
		],
		'03' => [
			'name'   => 'Tube',
			'length' => '96.5',
			'width'  => '15',
			'height' => '15',
			'weight' => '100', // No limit, but use 100.
		],
		'21' => [
			'name'   => 'Express Box',
			'length' => '46',
			'width'  => '31.5',
			'height' => '9.5',
			'weight' => '15',
		],
		'24' => [
			'name'   => '25KG Box',
			'length' => '50',
			'width'  => '45',
			'height' => '34',
			'weight' => '25',
		],
		'25' => [
			'name'   => '10KG Box',
			'length' => '42',
			'width'  => '34',
			'height' => '27',
			'weight' => '10',
		],
	];

	/**
	 * Returns true when metric units are used in boxes.
	 *
	 * @return bool
	 */
	public function is_metric() {
		return true;
	}

	/**
	 * Get boxes array.
	 *
	 * @return Box[]
	 */
	public function get_boxes() {
		$boxes = [];
		foreach ( self::BOXES as $box_id => $box ) {
			$boxes[] = new BoxImplementation(
				$box['length'],
				$box['width'],
				$box['height'],
				0,
				$box['weight'],
				$box_id,
				$box['name']
			);
		}
		return $boxes;
	}


}
