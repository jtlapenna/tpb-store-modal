<?php

/**
 * Settings definition.
 *
 * @package WPDesk\UpsProShippingService
 */
namespace UpsProVendor\WPDesk\UpsProShippingService;

use UpsProVendor\WPDesk\AbstractShipping\Exception\SettingsFieldNotExistsException;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsDecorators\BlackoutLeadDaysSettingsDefinitionDecoratorFactory;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use UpsProVendor\WPDesk\UpsProShippingService\CollectionPointFlatRate\CollectionPointFlatRateSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\CutoffTime\CutoffTimeSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\DatesAndTimes\DatesAndTimesSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\DeliveryConfirmation\DeliveryConfirmationSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\DestinationAddressType\DestinationAddressTypeSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\DestinationAddressType\EstimatedDeliverySettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\InstanceCustomOrigin\CustomOriginSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\InstanceCustomOrigin\InstanceCustomOriginSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\LeadTime\LeadTimeSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\MaximumTransitTime\MaximumTransitTimeSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\RateAdjustmentsTitle\RateAdjustmentsTitleSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\PickupType\HandlingFeesSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsProShippingService\SimpleRate\SimpleRateSettingsDefinitionDecorator;
use UpsProVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
use UpsProVendor\WPDesk\WooCommerceShipping\ShopSettings;
/**
 * Settings definitions.
 */
class UpsProSettingsDefinition extends SettingsDefinition
{
    /**
     * UPS settings definition.
     *
     * @var UpsSettingsDefinition
     */
    private $ups_settings_definition;
    /**
     * UpsProSettingsDefinition constructor.
     *
     * @param UpsSettingsDefinition $ups_settings_definition UPS settings definition.
     */
    public function __construct(UpsSettingsDefinition $ups_settings_definition, ShopSettings $shop_settings)
    {
        $ups_settings_definition = new RateAdjustmentsTitleSettingsDefinitionDecorator($ups_settings_definition, $shop_settings);
        $ups_settings_definition = new DestinationAddressTypeSettingsDefinitionDecorator($ups_settings_definition);
        $ups_settings_definition = new HandlingFeesSettingsDefinitionDecorator($ups_settings_definition);
        $ups_settings_definition = new DatesAndTimesSettingsDefinitionDecorator($ups_settings_definition);
        $ups_settings_definition = new EstimatedDeliverySettingsDefinitionDecorator($ups_settings_definition);
        $ups_settings_definition = new MaximumTransitTimeSettingsDefinitionDecorator($ups_settings_definition);
        $ups_settings_definition = new LeadTimeSettingsDefinitionDecorator($ups_settings_definition);
        $ups_settings_definition = new CutoffTimeSettingsDefinitionDecorator($ups_settings_definition);
        $ups_settings_definition = (new BlackoutLeadDaysSettingsDefinitionDecoratorFactory())->create_decorator($ups_settings_definition, CutoffTimeSettingsDefinitionDecorator::OPTION_CUTOFF_TIME, \false);
        $ups_settings_definition = new CollectionPointFlatRateSettingsDefinitionDecorator($ups_settings_definition);
        $ups_settings_definition = new DeliveryConfirmationSettingsDefinitionDecorator($ups_settings_definition);
        $ups_settings_definition = new CustomOriginSettingsDefinitionDecorator($ups_settings_definition, BlackoutLeadDaysSettingsDefinitionDecoratorFactory::OPTION_ID);
        $ups_settings_definition = new InstanceCustomOriginSettingsDefinitionDecorator($ups_settings_definition);
        $ups_settings_definition = new SimpleRateSettingsDefinitionDecorator($ups_settings_definition);
        $this->ups_settings_definition = $ups_settings_definition;
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
        return $this->ups_settings_definition->get_form_fields();
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
        return $this->ups_settings_definition->validate_settings($settings);
    }
}
