<?php
/**
 * Imperial box factory.
 *
 * @package WPDesk\FlexibleShippingFedex
 */

namespace WPDesk\FlexibleShippingUpsPro\PackerBox;

use UpsProVendor\WPDesk\Packer\Box;
use UpsProVendor\WPDesk\Packer\Box\BoxImplementation;
use UpsProVendor\WPDesk\Packer\BoxFactory\BoxesWithUnit;

/**
 * Imperial box factory.
 */
class ImperialBoxFactory implements BoxesWithUnit {

	const BOXES = [
		'01' => [
			'name'   => 'UPS Letter',
			'length' => '12.5',
			'width'  => '9.5',
			'height' => '0.25',
			'weight' => '0.5',
		],
		'03' => [
			'name'   => 'Tube',
			'length' => '38',
			'width'  => '6',
			'height' => '6',
			'weight' => '100', // No limit, but use 100.
		],
		'24' => [
			'name'   => '25KG Box',
			'length' => '19.375',
			'width'  => '17.375',
			'height' => '14',
			'weight' => '55.1156',
		],
		'25' => [
			'name'   => '10KG Box',
			'length' => '16.5',
			'width'  => '13.25',
			'height' => '10.75',
			'weight' => '22.0462',
		],
		'2a' => [
			'name'   => 'Small Express Box',
			'length' => '13',
			'width'  => '11',
			'height' => '2',
			'weight' => '100', // No limit, but use 100.
		],
		'2b' => [
			'name'   => 'Medium Express Box',
			'length' => '15',
			'width'  => '11',
			'height' => '3',
			'weight' => '100', // No limit, but use 100.
		],
		'2c' => [
			'name'   => 'Large Express Box',
			'length' => '18',
			'width'  => '13',
			'height' => '3',
			'weight' => '30',
		],
	];

	/**
	 * Returns true when metric units are used in boxes.
	 *
	 * @return bool
	 */
	public function is_metric() {
		return false;
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
