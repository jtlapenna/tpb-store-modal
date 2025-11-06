<?php

namespace UpsProVendor\WPDesk\UpsProShippingService;

use UpsProVendor\WPDesk\AbstractShipping\Exception\SettingsFieldNotExistsException;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use UpsProVendor\WPDesk\UpsProShippingService\DeliveryConfirmation\DeliveryConfirmationSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\DestinationAddressType\DestinationAddressTypeSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\InstanceCustomOrigin\CustomOriginSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\InstanceCustomOrigin\InstanceCustomOriginSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\RateAdjustmentsTitle\RateAdjustmentsTitleSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\PickupType\HandlingFeesSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
use UpsProVendor\WPDesk\UpsShippingService\UpsSurepostSettingsDefinition;
use UpsProVendor\WPDesk\WooCommerceShipping\ShopSettings;
/**
 * Settings definitions.
 */
class UpsProSurepostSettingsDefinition extends SettingsDefinition
{
    /**
     * UPS settings definition.
     *
     * @var UpsSettingsDefinition
     */
    private $ups_surepost_settings_definition;
    /**
     * UpsProSettingsDefinition constructor.
     *
     * @param UpsSurepostSettingsDefinition $ups_surepost_settings_definition UPS settings definition.
     */
    public function __construct(UpsSurepostSettingsDefinition $ups_surepost_settings_definition, ShopSettings $shop_settings)
    {
        $ups_surepost_settings_definition = new RateAdjustmentsTitleSettingsDefinitionDecorator($ups_surepost_settings_definition, $shop_settings, UpsSurepostSettingsDefinition::SUREPOST_SERVICES);
        $ups_surepost_settings_definition = new DestinationAddressTypeSettingsDefinitionDecorator($ups_surepost_settings_definition);
        $ups_surepost_settings_definition = new HandlingFeesSettingsDefinitionDecorator($ups_surepost_settings_definition);
        $ups_surepost_settings_definition = new CustomOriginSettingsDefinitionDecorator($ups_surepost_settings_definition, HandlingFeesSettingsDefinitionDecorator::HANDLING_FEES);
        $ups_surepost_settings_definition = new InstanceCustomOriginSettingsDefinitionDecorator($ups_surepost_settings_definition);
        $this->ups_surepost_settings_definition = $ups_surepost_settings_definition;
    }
    /**
     * Get form fields.
     *
     * @return array
     *
     * @throws SettingsFieldNotExistsException .
     */
    public function get_form_fields()
    {
        return $this->ups_surepost_settings_definition->get_form_fields();
    }
    /**
     * Validate settings.
     *
     * @param SettingsValues $settings Settings.
     *
     * @return bool
     */
    public function validate_settings(SettingsValues $settings)
    {
        return $this->ups_surepost_settings_definition->validate_settings($settings);
    }
}
