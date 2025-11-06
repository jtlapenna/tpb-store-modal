<?php
defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'ADF_Composite_Product_Admin' ) ) {
	class ADF_Composite_Product_Admin {

		public function __construct() {
		
			add_action( 'admin_enqueue_scripts', array( $this, 'afcpb_add_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'afcpb_enqueue_scripts_for_component_img' ) );
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'afcpb_composite_product_tab_fn' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'afcpb_tab_content' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'afcpb_save_fields_product_level' ) );
			add_action( 'woocommerce_product_options_shipping', array( $this, 'afcpb_shipping_option_to_products' ) , 10 );
			add_action( 'woocommerce_process_product_meta', array( $this, 'afcpb_save_option_to_products' )  );

			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 50 );
			add_action( 'woocommerce_settings_tabs_settings_tab_composite_product', array( $this, 'settings_tab' ));
			add_action( 'woocommerce_update_options_settings_tab_composite_product', array( $this, 'update_settings' ));
		}

		
		public function add_settings_tab( $settings_tabs ) {
			$settings_tabs['settings_tab_composite_product'] = __( 'Configurable Product Settings', 'af_comp_product' );
			return $settings_tabs;
		}
		public function settings_tab() {
			woocommerce_admin_fields( $this->get_settings() );
		}
		public function update_settings() {
			woocommerce_update_options( $this->get_settings() );
		}
		public function get_settings() {
			$settings = array(
				'section_title' => array(
					'name'     =>  '',
					'type'     => 'title',
					'id'       => 'wc_settings_tab_composite_product_section_title',
				),
				'af_cp_view_product' => array(
					'name'      => __( 'View Product', 'af_comp_product' ),
					'type'      => 'text',
					'class'     => '',
					'desc' =>  __( 'Enter text for view product button.', 'af_comp_product' ),
					'id'   => 'wc_settings_tab_composite_product_af_cp_view_product',
				),
				'section_end' => array(
					'type' => 'sectionend',
					'id' => 'wc_settings_tab_composite_product_section_end',
				),
			);

			return $settings;
		}

		public function afcpb_add_scripts() {
			wp_enqueue_style( 'comp-product-admin-css', plugins_url('../assets/css/af-comp-product-admin.css', __FILE__ ), false, '1.0.0' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'comp-product-admin-script', plugins_url( '../assets/js/af-comp-product-admin.js', __FILE__ ), false, '1.0.0' , false );

			$afcpb_ajax_data = array(
				'admin_url' => admin_url('admin-ajax.php'),
				'nonce'     => wp_create_nonce('afcpb_files_nonce'),
			);

			wp_localize_script(
				'comp-product-admin-script',
				'af_comp_product',
				$afcpb_ajax_data
			);

			wp_enqueue_style( 'select2-css', plugins_url( 'assets/css/select2.css', WC_PLUGIN_FILE ) , array(), '5.7.2' );

			wp_enqueue_script('select2-js', plugins_url( 'assets/js/select2/select2.min.js', WC_PLUGIN_FILE ), array( 'jquery' ), '4.0.3', true );
		}

		public function afcpb_enqueue_scripts_for_component_img() {
			wp_enqueue_media();
			wp_enqueue_script( 'comp-product-admin-script', plugins_url( '../assets/js/af-comp-product-admin.js', __FILE__ ), false, '1.0.0' , false );
		}
		
		public function afcpb_composite_product_tab_fn( $tabs ) {
			$tabs['af_composite_product_gen_settings'] = array(
				'label'  => esc_html__( 'Configurable Settings', 'af_comp_product' ),
				'target' => 'af_composite_product_gen_settings_options',
				'class'  => 'show_if_af_composite_product',
			);
			$tabs['af_composite_product_scenarios']    = array(
				'label'  => esc_html__( 'Conditional Logic', 'af_comp_product' ),
				'target' => 'af_composite_product_scenarios_options',
				'class'  => 'show_if_af_composite_product',
			);
			$tabs['af_composite_product_components']   = array(
				'label'  => esc_html__( 'Components', 'af_comp_product' ),
				'target' => 'af_composite_product_components_options',
				'class'  => 'show_if_af_composite_product',
			);

			
			return $tabs;
		}

		public function afcpb_tab_content() {
			?>
				<div id='af_composite_product_gen_settings_options' class='panel woocommerce_options_panel'>
					<div class='options_group'>
						<?php
							include AFCPB_DIR_PATH . '/includes/product-settings/af-general-settings.php';
						?>
					</div>
				</div>
				<div id='af_composite_product_scenarios_options' class='panel woocommerce_options_panel'>
					<div class='options_group'>
						<?php
							include AFCPB_DIR_PATH . '/includes/product-settings/scenario/af-scenarios.php';
						?>
					</div>
				</div>
				<div id='af_composite_product_components_options' class='panel woocommerce_options_panel'>
					<div class='options_group'>
						<?php
							include AFCPB_DIR_PATH . '/includes/product-settings/component/af-product-components.php';
						?>
					</div>
				</div>
			<?php
		}
		
		public function afcpb_save_fields_product_level( $post_id ) {
			include AFCPB_DIR_PATH . '/includes/product-settings/af-save-meta-fields.php';
		}

		public function afcpb_shipping_option_to_products() {
			global $post, $product;

			wp_nonce_field('af_cp_shipping_fields_nonce', 'af_cp_shipping_fields_nonce'); 

			$af_comp_get_current_post_id=$post->ID;

			$af_comp_product = wc_get_product( $af_comp_get_current_post_id );
	


			$af_comp_product_type=$af_comp_product->get_type();

			if ( ( !empty($post) )&&( 'af_composite_product'==$af_comp_product_type ) ) {
				echo '</div><div class="options_group af_cp_package_type ">'; 
				woocommerce_wp_select( 
					array( 
						'id'          => 'af_cp_shipping_page_type', 
						'label'       => esc_html__( 'Select shipping type', 'af_comp_product' ),
						'description' => '<br><br>' . esc_html__( 'Choose a shipping type for configurable product. ', 'af_comp_product' ) 
										. '<br><span class="af_cp_composite_desc af_comp_product_hidden">' . esc_html__( 'Product will ship as whole configurable product.', 'af_comp_product' ) . '</span>'
										. '<span class="af_cp_component_desc af_comp_product_hidden">' . esc_html__( 'Component products will be ship individually.', 'af_comp_product' ) . '</span>',
						'options'     =>  array(
							'composite' => esc_html__( 'Ship whole composite Product' , 'af_comp_product' ),
							'component' => esc_html__( 'Ship components' , 'af_comp_product' ),
						),
					)
					);
			}
		}

		public function afcpb_save_option_to_products( $post_id ) {

			if ( empty( $_POST['af_cp_shipping_fields_nonce'] ) || !wp_verify_nonce(sanitize_text_field($_POST['af_cp_shipping_fields_nonce']), 'af_cp_shipping_fields_nonce')) {

				wp_die( esc_html__('Security Violated Error!', 'af_comp_product') );
			}

			if ( isset( $_POST['af_cp_shipping_page_type'] ) ) {
				update_post_meta( $post_id, 'af_cp_shipping_page_type', sanitize_meta( '' , wp_unslash( $_POST['af_cp_shipping_page_type'] ) , '' ) );
			}
		}
	}
	new ADF_Composite_Product_Admin();
}
