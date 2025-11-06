<?php

namespace AgileStoreLocator\Admin;


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
*
* AjaxHandler Responsible for handling all the AJAX Requests for the admin calls
*
* @package    AgileStoreLocator
* @subpackage AgileStoreLocator/Admin/AjaxHandler
* @author     AgileStoreLocator Team <support@agilelogix.com>
*/
class AjaxHandler {

  /**
   * [__construct Register the main route to handle AJAX]
   */
  public function __construct() {

    //  Admin request asl ajax handler for all requests
    add_action('wp_ajax_asl_ajax_handler', [$this, 'handle_request']); 
  }

  /**
   * [$ajax_actions All the AJAX actions]
   * @var array
   */
  private $ajax_actions = [];


  /**
   * [add_routes Add all the admin routes that handle the AJAX]
   */
  public function add_routes() {

    /*For Stores*/
    $this->register_route('update_store', 'Store' ,'update_store');
    $this->register_route('add_store', 'Store', 'add_new_store');  
    $this->register_route('delete_all_stores', 'Store', 'admin_delete_all_stores');  
    $this->register_route('get_store_list', 'Store', 'get_store_list');  
    $this->register_route('delete_store', 'Store', 'delete_store');  
    $this->register_route('duplicate_store', 'Store', 'duplicate_store');  
    $this->register_route('remove_duplicates', 'Store', 'remove_duplicates');  
    $this->register_route('validate_coords', 'Store', 'validate_coordinates'); 
    $this->register_route('store_status', 'Store', 'store_status');  
    $this->register_route('approve_stores', 'Store', 'approve_stores');

    /*Categories*/
    $this->register_route('add_categories', 'Category', 'add_category');
    $this->register_route('delete_category', 'Category', 'delete_category');
    $this->register_route('update_category', 'Category', 'update_category');
    $this->register_route('get_category_byid', 'Category', 'get_category_by_id');
    $this->register_route('get_categories', 'Category', 'get_categories');  
    
    /*Logo*/
    $this->register_route('get_logos', 'Logo', 'get_logos');  
    $this->register_route('get_logo_byid', 'Logo', 'get_logo_by_id');
    $this->register_route('update_logo', 'Logo', 'update_logo');
    $this->register_route('delete_logo', 'Logo', 'delete_logo');
    $this->register_route('upload_logo', 'Logo', 'upload_logo');


    /*Markers*/
    $this->register_route('delete_marker', 'Marker', 'delete_marker');
    $this->register_route('get_marker_byid', 'Marker', 'get_marker_by_id');
    $this->register_route('get_markers', 'Marker', 'get_markers');  
    
    //  Settings
    $this->register_route('export_configs', 'Setting', 'export_configs');
    $this->register_route('import_configs', 'Setting', 'import_configs');
    $this->register_route('save_setting', 'Setting', 'save_setting');
    $this->register_route('load_custom_template', 'Setting', 'load_custom_template');
    $this->register_route('save_custom_template', 'Setting', 'save_custom_template');
    $this->register_route('reset_custom_template', 'Setting', 'reset_custom_template');
    $this->register_route('expertise_level', 'Setting', 'expertise_level');

    $this->register_route('save_custom_fields', 'Setting', 'save_custom_fields'); 
    $this->register_route('load_ui_settings', 'Setting', 'load_ui_settings');
    $this->register_route('sl_theme_ui_save', 'Setting', 'sl_theme_ui_save');
    $this->register_route('reset_ui_template', 'Setting', 'reset_ui_template');

    /*Import and settings*/
    $this->register_route('fill_missing_coords', 'ImportExport', 'fill_missing_coords');
    $this->register_route('validate_api_key', 'ImportExport', 'validate_api_key');   
    

    
    $this->register_route('change_options', 'Setting', 'change_options');

    // label
    $this->register_route('set_label', 'Label', 'set_label');  

    //  KML files
    $this->register_route('add_kml', 'GoogleMap', 'upload_kml_file');
    $this->register_route('delete_kml', 'GoogleMap', 'remove_kml_file');
    $this->register_route('save_custom_map', 'GoogleMap', 'save_custom_map');
  }

  /**
   * [register_route Register the AJAX calls for the plugin]
   * @param  [type] $handle        [description]
   * @param  [type] $context_class [description]
   * @param  [type] $action        [description]
   * @return [type]                [description]
   */
  public function register_route($handle, $context_class, $action) {

    $this->ajax_actions[$handle] = [$context_class, $action];
  }


  /**
   * [handle_request Handle the AJAX Request]
   * @return [type] [description]
   */
  public function handle_request() {

    //  Disable Error when AJAX
    error_reporting(0);

    //  sl-action
    $route  = isset($_REQUEST['sl-action'])? sanitize_text_field($_REQUEST['sl-action']): ''; 

    //  Make sure that user is logged in
    if(!current_user_can( ASL_PERMISSION )) {
      return $this->json_response(['error' => esc_attr__('Error! path is forbidden.', 'asl_locator')]);
    }

    //  Using it you can change the route
    $route = apply_filters('asl_admin_route_filter', $route);

    // Get the nounce
    $nounce =  isset($_REQUEST['asl-nounce'])? sanitize_text_field($_REQUEST['asl-nounce']): null;

    //  nouce validation for CSRF
    if(!$nounce || !wp_verify_nonce($nounce, 'asl-nounce')) {

      return $this->json_response(['nouce' => $_REQUEST['asl-nounce'], 'error' => esc_attr__('Error! request verification fail.','asl_locator')]);
    }

    //  validate the route
    if(isset($this->ajax_actions[$route])) {

      $sl_request = $this->ajax_actions[$route]; 


      $class_name = '\\'.__NAMESPACE__ . '\\' .$sl_request[0];
      $class_inst = new $class_name;
      
      //  is callable method?
      if(!is_callable([$class_inst, $sl_request[1]])) {
        return $this->json_response(['error' => esc_attr__('Error! method not exist!','asl_locator')]);
      }

      //  Result of the execution
      $results  = null;

      try {
          
        //  Execute the method
        $results = call_user_func([$class_inst, $sl_request[1]]);

      } 
      //  Caught in exception
      catch (\Exception $e) {
          
        $results = ['msg' => $e->getMessage()];
      }

      $this->json_response($results);
    }

    //  route not found
    $this->json_response(['error' => esc_attr__('Error! route not found.','asl_locator')]);
  }


  /**
   * [json_response Send the $data as JSON]
   * @param  [type] $data [description]
   * @return [type]       [description]
   */
  public function json_response($data) {

    echo wp_send_json($data);
    die;
  }
}

