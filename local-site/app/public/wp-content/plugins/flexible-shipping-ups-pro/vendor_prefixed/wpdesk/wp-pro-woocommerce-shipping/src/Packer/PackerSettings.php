<?php

namespace UpsProVendor\WPDesk\WooCommerceShippingPro\Packer;

use UpsProVendor\WPDesk\AbstractShipping\Settings\DefinitionModifier\SettingsDefinitionModifierAfter;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use UpsProVendor\WPDesk\Packer\Box;
use UpsProVendor\WpDesk\WooCommerce\ShippingMethod\PackerBoxesFactory;
use UpsProVendor\WPDesk\WooCommerceShipping\CustomFields\CustomField;
use UpsProVendor\WPDesk\WooCommerceShippingPro\CustomFields\ShippingBoxes;
/**
 * Settings required for packer.
 *
 * @package WPDesk\WooCommerceShippingPro\Packer
 */
class PackerSettings
{
    const OPTION_PACKAGING_METHOD = 'packing_method';
    const OPTION_SHIPPING_BOXES = 'shipping_boxes';
    const PACKING_METHOD_WEIGHT = 'weight';
    const PACKING_METHOD_BOX = 'box';
    const PACKING_METHOD_BOX_3D = 'box_3d';
    const PACKING_METHOD_SEPARATELY = 'separately';
    /**
     * @var string
     */
    private $info_url;
    /**
     * @var string
     */
    private string $shipping_boxes_description;
    /**
     * @var bool
     */
    private $shipping_service_id;
    /**
     * PackerSettings constructor.
     *
     * @param string $info_url Url with info about packages size.
     * @param string $description Description.
     * @param bool $shipping_service_id Shipping service Id.
     */
    public function __construct($info_url, $description = '')
    {
        $this->info_url = $info_url;
        $this->shipping_boxes_description = $description;
    }
    /**
     * @param \WC_Settings_API $settings
     *
     * @return string One of packaging method names
     */
    public function get_packaging_method(\WC_Settings_API $settings)
    {
        return $settings->get_option(self::OPTION_PACKAGING_METHOD, self::PACKING_METHOD_WEIGHT);
    }
    /**
     * @param Box[] $boxes
     *
     * @return Box[]
     */
    private function prepare_boxes_for_factory(array $boxes)
    {
        $prepared = [];
        foreach ($boxes as $box) {
            $prepared[trim($box->get_internal_data()['id'], '_')] = $box;
        }
        return $prepared;
    }
    /**
     * Get shipping boxes saved data.
     *
     * @param \WC_Settings_API $settings .
     * @param Box[] $default_boxes
     *
     * @return Box[]
     */
    public function get_shipping_boxes(\WC_Settings_API $settings, array $default_boxes)
    {
        return PackerBoxesFactory::create_packer_boxes_from_settings($settings->get_option(self::OPTION_SHIPPING_BOXES, '[]'), $default_boxes);
    }
    /**
     * Add packaging fields to instance settings.
     *
     * @param SettingsDefinition $definition
     * @param string $add_after Id of settings field after which add the settings.
     *
     * @return SettingsDefinition
     */
    public function add_packaging_fields(SettingsDefinition $definition, $add_after = 'fallback')
    {
        $description = '';
        if (!empty($this->info_url)) {
            $description = sprintf(
                // Translators: link to packages.
                __('Select the package type the ordered products will be matched to. You can choose one or as many different packagings as you need. If selected, filling in the products\' weight and dimensions fields is required. %1$sLearn more about the sizes and package types →%2$s', 'flexible-shipping-ups-pro'),
                '<a href="' . $this->info_url . '" target="_blank">',
                '</a>'
            );
        }
        if (!empty($this->shipping_boxes_description)) {
            $description .= '<br/>' . $this->shipping_boxes_description;
        }
        $definition = new SettingsDefinitionModifierAfter($definition, $add_after, self::OPTION_SHIPPING_BOXES, ['title' => __('Shipping boxes', 'flexible-shipping-ups-pro'), 'type' => ShippingBoxes::get_type_name(), 'class' => 'no-flat-rate', 'description' => $description, 'desc_tip' => \false, 'default' => '']);
        $packing_options = [self::PACKING_METHOD_WEIGHT => __('Pack into one box by weight', 'flexible-shipping-ups-pro'), self::PACKING_METHOD_BOX_3D => __('Pack into custom boxes (3D bin packing)', 'flexible-shipping-ups-pro'), self::PACKING_METHOD_BOX => __('Pack into custom boxes (volume packing)', 'flexible-shipping-ups-pro'), self::PACKING_METHOD_SEPARATELY => __('Pack items separately', 'flexible-shipping-ups-pro')];
        return new SettingsDefinitionModifierAfter($definition, $add_after, self::OPTION_PACKAGING_METHOD, ['title' => __('Parcel Packing Method', 'flexible-shipping-ups-pro'), 'type' => 'select', 'options' => $packing_options, 'desc_tip' => __('Define the way how the ordered products should be packed. Changing your choice here may affect the rates.', 'flexible-shipping-ups-pro'), 'custom_attributes' => ['data-descriptions' => json_encode([self::PACKING_METHOD_WEIGHT => __('Packs all items into a single box.', 'flexible-shipping-ups-pro'), self::PACKING_METHOD_BOX_3D => __('Optimizes item placement in custom boxes using a 3D algorithm that considers item dimensions and available space.', 'flexible-shipping-ups-pro'), self::PACKING_METHOD_BOX => __('Groups items by total volume and fits them into boxes to maximize capacity. Also ensures each item can physically fit into an empty box.', 'flexible-shipping-ups-pro'), self::PACKING_METHOD_SEPARATELY => __('Packs each item in a separate box.', 'flexible-shipping-ups-pro')])], 'class' => 'no-flat-rate oct-packer-settings oct-dynamic-description', 'description' => ' ', 'default' => self::PACKING_METHOD_WEIGHT]);
    }
}
