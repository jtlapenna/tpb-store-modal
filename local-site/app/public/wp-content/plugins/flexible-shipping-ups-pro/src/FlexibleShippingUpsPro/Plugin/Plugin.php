<?php
/**
 * Plugin main class.
 *
 * @package WPDesk\FlexibleShippingUpsPro\Plugin
 */

namespace WPDesk\FlexibleShippingUpsPro\Plugin;

use UpsProVendor\Octolize\Blocks\PickupPoint\IntegrationData;
use UpsProVendor\Octolize\Blocks\PickupPoint\Registrator;
use UpsProVendor\Octolize\Csat\Csat;
use UpsProVendor\Octolize\PluginUpdateReminder\RemindersFactory;
use UpsProVendor\Octolize\ShippingExtensions\ShippingExtensions;
use UpsProVendor\Octolize\Tracker\DeactivationTracker\OctolizeProReasonsFactory;
use UpsProVendor\Octolize\Tracker\TrackerInitializer;
use UpsProVendor\Octolize\Ups\RestApi\RestApiClient;
use UpsProVendor\Octolize\WooCommerceShipping\Ups\OAuth\RestApiTokenFactory;
use UpsProVendor\Psr\Log\LoggerAwareInterface;
use UpsProVendor\Psr\Log\LoggerAwareTrait;
use UpsProVendor\Psr\Log\NullLogger;
use UpsProVendor\WPDesk\AbstractShipping\Settings\SettingsValuesAsArray;
use UpsProVendor\WPDesk\Logger\SimpleLoggerFactory;
use UpsProVendor\WPDesk\Notice\AjaxHandler;
use UpsProVendor\WPDesk\Persistence\Adapter\WooCommerce\WooCommerceSessionContainer;
use UpsProVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin;
use UpsProVendor\WPDesk\PluginBuilder\Plugin\HookableCollection;
use UpsProVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
use UpsProVendor\WPDesk\PluginBuilder\Plugin\TemplateLoad;
use UpsProVendor\WPDesk\UpsProShippingService\UpsProSurepostShippingService;
use UpsProVendor\WPDesk\UpsShippingService\UpsSurepostShippingService;
use UpsProVendor\WPDesk\WooCommerce\CurrencySwitchers;
use UpsProVendor\WPDesk\WooCommerceShipping\ActivePayments;
use UpsProVendor\WPDesk\WooCommerceShipping\CollectionPoints\CachedCollectionPointsProvider;
use UpsProVendor\WPDesk\WooCommerceShipping\CustomFields\ApiStatus\FieldApiStatusAjax;
use UpsProVendor\WPDesk\WooCommerceShipping\EstimatedDelivery\EstimatedDeliveryDatesDisplay;
use UpsProVendor\WPDesk\UpsProShippingService\UpsProShippingService;
use UpsProVendor\WPDesk\UpsShippingService\UpsApi\UpsAccessPoints;
use UpsProVendor\WPDesk\UpsShippingService\UpsServices;
use UpsProVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
use UpsProVendor\WPDesk\View\Renderer\Renderer;
use UpsProVendor\WPDesk\View\Renderer\SimplePhpRenderer;
use UpsProVendor\WPDesk\View\Resolver\ChainResolver;
use UpsProVendor\WPDesk\View\Resolver\DirResolver;
use UpsProVendor\WPDesk\View\Resolver\WPThemeResolver;
use UpsProVendor\WPDesk\WooCommerceShipping\Assets;
use UpsProVendor\WPDesk\WooCommerceShipping\CollectionPoints\CheckoutHandler;
use UpsProVendor\WPDesk\WooCommerceShipping\PluginShippingDecisions;
use UpsProVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\CollectionPoint\CollectionPointRateMethod;
use UpsProVendor\WPDesk\WooCommerceShipping\ShopSettings;
use UpsProVendor\WPDesk\WooCommerceShipping\ThirdParty\Germanized\TaxSettingsNotice;
use UpsProVendor\WPDesk\WooCommerceShipping\Ups\Advertisement\AjaxActions;
use UpsProVendor\WPDesk\WooCommerceShipping\Ups\Advertisement\UpsLabels;
use UpsProVendor\WPDesk\WooCommerceShipping\Ups\AjaxCollectionPoints;
use UpsProVendor\WPDesk\WooCommerceShipping\Ups\AuthCodeNotice;
use UpsProVendor\WPDesk\WooCommerceShipping\Ups\MetaDataInterpreters\PackedPackagesAdminMetaDataInterpreter;
use UpsProVendor\WPDesk\WooCommerceShipping\Ups\Tracker;
use UpsProVendor\WPDesk\WooCommerceShipping\Ups\UpsAdminOrderMetaDataDisplay;
use UpsProVendor\WPDesk\WooCommerceShipping\Ups\UpsFrontOrderMetaDataDisplay;
use UpsProVendor\WPDesk\WooCommerceShipping\Ups\XmlApiNotice;
use UpsProVendor\WPDesk\WooCommerceShippingPro\CustomFields\ShippingBoxes;
use UpsProVendor\WPDesk_Plugin_Info;
use UpsProVendor\WPDesk\UpsShippingService\UpsShippingService;
use WPDesk\FlexibleShippingUpsPro\ShippingMethod\UpsProShippingMethod;
use WPDesk\FlexibleShippingUpsPro\ShippingMethod\UpsProSurepostShippingMethod;
use WPDesk\FlexibleShippingUpsPro\Tracker\ProTracker;

/**
 * Main plugin class. The most important flow decisions are made here.
 *
 * @package WPDesk\FlexibleShippingUps
 */
class Plugin extends AbstractPlugin implements LoggerAwareInterface, HookableCollection {

	use LoggerAwareTrait;
	use HookableParent;
	use TemplateLoad;

	const PRIORITY_BEFORE_SHARED_HELPER = -35;

	/**
	 * Scripts version.
	 *
	 * @var string
	 */
	private $scripts_version = '1';

	/**
	 * Renderer.
	 *
	 * @var Renderer
	 */
	private $renderer;

	/**
	 * @var RestApiClient
	 */
	private $rest_api_client;

	/**
	 * Plugin constructor.
	 *
	 * @param WPDesk_Plugin_Info $plugin_info Plugin info.
	 */
	public function __construct( WPDesk_Plugin_Info $plugin_info ) {
		$this->plugin_info     = $plugin_info;
		$this->scripts_version = FLEXIBLE_SHIPPING_UPS_PRO_VERSION . '-' . $this->scripts_version;
		parent::__construct( $this->plugin_info );
	}

	/**
	 * Init base variables for plugin
	 */
	public function init_base_variables() {
		$this->plugin_url       = $this->plugin_info->get_plugin_url();
		$this->plugin_path      = $this->plugin_info->get_plugin_dir();
		$this->template_path    = $this->plugin_info->get_text_domain();
		$this->plugin_namespace = $this->plugin_info->get_text_domain();
	}

	/**
	 * Init plugin
	 */
	public function init() {
		parent::init();

		$settings = $this->get_global_ups_settings();

		$this->init_logger();

		$this->init_renderer();

		$this->add_hookable( new ActivationDate() );

		$this->add_hookable( new OrderCounter() );

		$this->add_hookable( new RateNotice() );

		$this->add_hookable( new ShippingExtensions( $this->plugin_info ) );

		$this->add_hookable( new AjaxHandler( trailingslashit( $this->get_plugin()->get_plugin_url() ) . 'vendor_prefixed/wpdesk/wp-notice/assets' ) );

		$this->add_hookable( new SettingsSidebar() );

		add_action(
			'init',
			function () {
				$admin_meta_data_interpreter = new UpsAdminOrderMetaDataDisplay();
				$admin_meta_data_interpreter->add_interpreter( new PackedPackagesAdminMetaDataInterpreter() );
				/** Add hidden meta for backward compatibility */
				$admin_meta_data_interpreter->add_hidden_order_item_meta_key( 'ups_delivery_date' );
				$admin_meta_data_interpreter->add_hidden_order_item_meta_key( 'ups_time_in_transit' );
				$admin_meta_data_interpreter->add_hidden_order_item_meta_key( 'ups_days_to_arrival_date' );
				$admin_meta_data_interpreter->init_interpreters();
				$admin_meta_data_interpreter->hooks();
				( new EstimatedDeliveryDatesDisplay( $this->renderer, UpsShippingService::UNIQUE_ID ) )->hooks();

				$meta_data_interpreter = new UpsFrontOrderMetaDataDisplay( $this->renderer );
				$meta_data_interpreter->init_interpreters();
				$meta_data_interpreter->hooks();
			}
		);

		$this->add_hookable( new CurrencySwitchers\ShippingIntegrations( UpsShippingService::UNIQUE_ID ) );
		$this->add_hookable( new CurrencySwitchers\FilterConvertersFactory( UpsShippingService::UNIQUE_ID ) );

		$this->add_hookable( new ActivePayments\Integration( UpsShippingService::UNIQUE_ID ) );

		// REST API.
		$rest_api_token        = ( new RestApiTokenFactory() )->create(
			$settings[ UpsSettingsDefinition::AUTHORIZATION_TYPE ] ?? UpsSettingsDefinition::AUTH_CODE,
			$settings[ UpsSettingsDefinition::CLIENT_ID ] ?? '',
			$settings[ UpsSettingsDefinition::CLIENT_SECRET ] ?? '',
			$this->logger
		);
		$this->rest_api_client = RestApiClient::create( $rest_api_token );

		$this->add_hookable( new TaxSettingsNotice( 'UPS Live Rates', 'https://octol.io/ups-germanized' ) );

		$this->add_hookable( new XmlApiNotice( $this->get_global_ups_settings() ) );

		$this->add_hookable( new AuthCodeNotice( $this->get_global_ups_settings() ) );

		$this->add_hookable( new UpsLabels() );
		$this->add_hookable( new AjaxActions() );

		$this->add_hookable( new RemindersFactory( $this->plugin_info->get_plugin_dir(), $this->plugin_info->get_plugin_file_name(), $this->plugin_info->get_plugin_name() ) );

		$this->add_hookable(
			Csat::create_for_shipping_method_instance(
				UpsShippingService::UNIQUE_ID,
				UpsShippingService::UNIQUE_ID . '_pro',
				__DIR__ . '/views/csat.php',
				'woocommerce_after_settings_shipping'
			)
		);

		$this->add_hookable(
			Csat::create_for_shipping_method_instance(
				UpsSurepostShippingService::UNIQUE_ID,
				UpsShippingService::UNIQUE_ID . '_pro',
				__DIR__ . '/views/csat.php',
				'woocommerce_after_settings_shipping'
			)
		);

		$this->init_tracker();

		$this->hooks();

		$this->init_ups_services();

		$this->init_checkout_blocks();
	}

	private function init_checkout_blocks() {
		$integration_data = ( new IntegrationData() )
			->set_integration_name( 'flexible-shipping-ups' )
			->set_meta_data_name( 'flexible_shipping_ups_collection_point' )
			->set_ajax_action( AjaxCollectionPoints::AJAX_ACTION )
			->set_ajax_url( admin_url( 'admin-ajax.php' ) )
			->set_nonce_name( AjaxCollectionPoints::AJAX_ACTION )
			->set_flexible_shipping_integration( 'flexible-shipping-ups' );
		( new Registrator(
			$integration_data,
			$this->plugin_path,
			$this->get_plugin_file_path()
		) )->hooks();

		add_action(
			'init',
			function () {
				// Dummy texts.
				__( 'Select pickup point', 'flexible-shipping-ups-pro' ); // phpcs:ignore
			}
		);
	}

	private function init_tracker() {
		$this->add_hookable(
			TrackerInitializer::create_from_plugin_info_for_shipping_method(
				$this->plugin_info,
				UpsShippingService::UNIQUE_ID,
				new OctolizeProReasonsFactory(
					'https://octol.io/ups-docs-exit-pop-up',
					'https://octol.io/ups-contact-exit-pop-up'
				)
			)
		);
		$this->add_hookable( new Tracker() );
		$this->add_hookable( new ProTracker() );
	}

	private function init_logger(): void {
		$global_ups_woocommerce_options  = $this->get_global_ups_settings();
		$global_ups_woocommerce_settings = new SettingsValuesAsArray( $global_ups_woocommerce_options );

		$this->setLogger(
			$global_ups_woocommerce_settings->get_value( UpsSettingsDefinition::DEBUG_MODE, 'no' ) === 'yes'
				? ( new SimpleLoggerFactory( 'ups' ) )->getLogger()
				: new NullLogger()
		);
	}

	/**
	 * Init UPS services.
	 *
	 * @internal
	 */
	private function init_ups_services() {
		$global_ups_woocommerce_options  = $this->get_global_ups_settings();
		$global_ups_woocommerce_settings = new SettingsValuesAsArray( $global_ups_woocommerce_options );

		$origin_country = $this->get_origin_country_code( $global_ups_woocommerce_options );

		$ups_service = apply_filters(
			'flexible_shipping_ups_pro_shipping_service',
			new UpsProShippingService(
				$this->logger,
				new ShopSettings( UpsProShippingService::UNIQUE_ID ),
				$origin_country,
				$this->rest_api_client
			)
		);

		$api_ajax_status_handler = new FieldApiStatusAjax( $ups_service, $global_ups_woocommerce_settings, $this->logger );
		$api_ajax_status_handler->hooks();

		$plugin_shipping_decisions = new PluginShippingDecisions( $ups_service, $this->logger );
		$plugin_shipping_decisions->set_field_api_status_ajax( $api_ajax_status_handler );

		UpsProShippingMethod::set_plugin_shipping_decisions( $plugin_shipping_decisions );

		$ups_surepost_service = apply_filters(
			'flexible_shipping_ups_pro_surepost_shipping_service',
			new UpsProSurepostShippingService(
				$this->logger,
				new ShopSettings( UpsProSurepostShippingService::UNIQUE_ID ),
				$origin_country,
				$this->rest_api_client
			)
		);

		$plugin_surepost_shipping_decisions = new PluginShippingDecisions( $ups_surepost_service, $this->logger );

		UpsProSurepostShippingMethod::set_plugin_shipping_decisions( $plugin_surepost_shipping_decisions );
	}

	/**
	 * @internal
	 */
	public function init_ups_access_points() {
		$global_ups_woocommerce_options = $this->get_global_ups_settings();

		$access_points_provider = new UpsAccessPoints(
			$global_ups_woocommerce_options[ UpsSettingsDefinition::ACCESS_KEY ],
			$global_ups_woocommerce_options[ UpsSettingsDefinition::USER_ID ],
			$global_ups_woocommerce_options[ UpsSettingsDefinition::PASSWORD ],
			$this->logger,
			$this->rest_api_client,
			$global_ups_woocommerce_options[ UpsSettingsDefinition::API_TYPE ] ?? UpsSettingsDefinition::API_TYPE_XML
		);

		if ( function_exists( 'wc_empty_cart' ) ) {
			WC()->initialize_session();
			$access_points_provider = new CachedCollectionPointsProvider(
				$access_points_provider,
				new WooCommerceSessionContainer( WC()->session ),
				self::class . $this->scripts_version
			);
		}

		$collection_points_checkout_handler = new CheckoutHandler(
			$access_points_provider,
			UpsShippingService::UNIQUE_ID,
			$this->renderer,
			__( 'UPS Access Point', 'flexible-shipping-ups-pro' ),
			__( 'Access point unavailable for selected shipping address!', 'flexible-shipping-ups-pro' ),
			__( 'The closest point based on the billing address or shipping address.', 'flexible-shipping-ups-pro' ),
			true
		);
		$collection_points_checkout_handler->hooks();

		CollectionPointRateMethod::set_collection_points_checkout_handler( $collection_points_checkout_handler );

		$assets = new Assets( $this->get_plugin_url() . 'vendor_prefixed/wpdesk/wp-woocommerce-shipping/assets', 'ups' );
		$assets->hooks();

		$ajax = new AjaxCollectionPoints( $access_points_provider );
		$ajax->hooks();
	}

	/**
	 * Init renderer.
	 */
	private function init_renderer() {
		$resolver = new ChainResolver();
		$resolver->appendResolver( new WPThemeResolver( $this->get_template_path() ) );
		$resolver->appendResolver( new DirResolver( trailingslashit( $this->plugin_path ) . 'templates' ) );
		$resolver->appendResolver( new DirResolver( trailingslashit( $this->plugin_path ) . 'vendor_prefixed/wpdesk/wp-woocommerce-shipping/templates' ) );
		$resolver->appendResolver( new DirResolver( trailingslashit( $this->plugin_path ) . 'vendor_prefixed/wpdesk/wp-ups-shipping-method/templates' ) );
		$this->renderer = new SimplePhpRenderer( $resolver );
	}

	/**
	 * Init hooks.
	 */
	public function hooks() {
		parent::hooks();
		add_filter( 'woocommerce_shipping_methods', [ $this, 'woocommerce_shipping_methods_filter' ], 20, 1 );
		add_action( 'woocommerce_init', [ $this, 'init_ups_countries' ] );
		add_action( 'woocommerce_init', [ $this, 'init_ups_access_points' ] );

		add_action(
			'init',
			function () {
				( new UpgradeOnboarding( $this->plugin_info ) )->init_upgrade_onboarding();
			}
		);

		$this->hooks_on_hookable_objects();
	}

	/**
	 * Init UPS services.
	 */
	public function init_ups_countries() {
		UpsServices::set_eu_countries( WC()->countries->get_european_union_countries() );
	}

	/**
	 * Get global UPS settings.
	 *
	 * @return array
	 */
	private function get_global_ups_settings() {
		return get_option(
			'woocommerce_' . UpsShippingService::UNIQUE_ID . '_settings',
			[
				UpsSettingsDefinition::ACCESS_KEY    => '',
				UpsSettingsDefinition::PASSWORD      => '',
				UpsSettingsDefinition::USER_ID       => '',
				UpsSettingsDefinition::CUSTOM_ORIGIN => 'no',
			]
		);
	}

	/**
	 * Get origin country code.
	 *
	 * @param array $global_ups_woocommerce_options .
	 *
	 * @return string
	 */
	private function get_origin_country_code( $global_ups_woocommerce_options ) {

		$origin_country_code = '';
		if ( isset( $global_ups_woocommerce_options[ UpsSettingsDefinition::CUSTOM_ORIGIN ] ) && 'yes' === $global_ups_woocommerce_options[ UpsSettingsDefinition::CUSTOM_ORIGIN ] ) {
			$country_state_code  = explode( ':', $global_ups_woocommerce_options[ UpsSettingsDefinition::ORIGIN_COUNTRY ] );
			$origin_country_code = $country_state_code[0];
		} else {
			$woocommerce_default_country = explode( ':', get_option( 'woocommerce_default_country', '' ) );
			if ( ! empty( $woocommerce_default_country[0] ) ) {
				$origin_country_code = $woocommerce_default_country[0];
			}
		}
		return $origin_country_code;
	}

	/**
	 * Adds shipping method to Woocommerce.
	 *
	 * @param array $methods Methods.
	 *
	 * @return array
	 */
	public function woocommerce_shipping_methods_filter( $methods ) {
		$methods['flexible_shipping_ups']          = UpsProShippingMethod::class;
		$methods['flexible_shipping_ups_surepost'] = UpsProSurepostShippingMethod::class;
		return $methods;
	}

	/**
	 * Quick links on plugins page.
	 *
	 * @param array $links .
	 *
	 * @return array
	 */
	public function links_filter( $links ) {
		$is_pl        = 'pl_PL' === get_locale();
		$docs_link    = $is_pl ? 'https://octol.io/ups-docs-pl' : 'https://octol.io/ups-docs';
		$support_link = $is_pl ? 'https://octol.io/ups-pro-support-pl' : 'https://octol.io/ups-pro-support';
		$settings_url = admin_url( 'admin.php?page=wc-settings&tab=shipping&section=flexible_shipping_ups' );

		$plugin_links = [
			'<a href="' . $settings_url . '">' . __( 'Settings', 'flexible-shipping-ups-pro' ) . '</a>',
			'<a href="' . $docs_link . '" target="_blank">' . __( 'Docs', 'flexible-shipping-ups-pro' ) . '</a>',
			'<a href="' . $support_link . '" target="_blank">' . __( 'Support', 'flexible-shipping-ups-pro' ) . '</a>',
		];

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Admin enqueue scripts.
	 */
	public function admin_enqueue_scripts() {
		parent::admin_enqueue_scripts();

		ShippingBoxes::enqueue_scripts( $this->get_plugin_assets_url() );
	}
}
