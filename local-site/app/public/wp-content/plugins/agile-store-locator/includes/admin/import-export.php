<?php

namespace AgileStoreLocator\Admin;


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

use AgileStoreLocator\Admin\Base;

/**
 * The Import/Export Manager functionality of the admin
 *
 * @link       https://agilestorelocator.com
 * @since      4.7.32
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/Admin/ImportExport
 */

class ImportExport extends Base {


  /**
   * [__construct description]
   */
  public function __construct() {
    
    parent::__construct();
  }


  /**
   * [validate_api_key Validateyour Google API Key]
   * @return [type] [description]
   */
  public function validate_api_key() {

    global $wpdb;

    $response = new \stdclass();
    $response->success = false;

    //Get the API KEY
    $sql      = "SELECT `key`,`value` FROM ".ASL_PREFIX."configs WHERE `key` = 'server_key'";
    $configs_result = $wpdb->get_results($sql);

    if(isset($configs_result) && isset($configs_result[0])) {

      $api_key    = $configs_result[0]->value;

      if($api_key) {

        //Test Address
        $street   = '1848 Provincial Road';
        $city     = 'Winsdor';
        $state    = 'ON';
        $zip      = 'N8W 5W3';
        $country  = 'Canada';

        $_address = $street.', '.$zip.'  '.$city.' '.$state.' '.$country;

        $results = \AgileStoreLocator\Helper::getLnt($_address, $api_key, true);

        $response->result = $results;

        if($results && isset($results['body'])) {

          $results  = json_decode($results['body'], true);
          
          if(isset($results['error_message'])) {

            $response->msg    = $results['error_message'];
          }
          else {

            $response->msg     = esc_attr__('Valid API Key','asl_locator'); 
            $response->success = true;  
          }
        }

        //$response->msg    = esc_attr__('API Key is Valid','asl_locator');
        
      }
      else
        $response->msg = esc_attr__('Server Google API Key is Missing','asl_locator');
    }
    else
        $response->msg = esc_attr__('Server Google API Key is not saved.','asl_locator');

    return $this->send_response($response);
  }

  /**
   * [fill_missing_coords Fetch the Missing Coordinates]
   * @return [type] [description]
   */
  public function fill_missing_coords() {
  
    ini_set('memory_limit', '256M');
    ini_set('max_execution_time', 0);
    
    global $wpdb;

    $response  = new \stdclass();
    $response->success = false;
    $response->summary = array();

    //Get the API Key
    $sql = "SELECT `key`,`value` FROM ".ASL_PREFIX."configs WHERE `key` = 'server_key'";
    $configs_result = $wpdb->get_results($sql);
    $api_key    = $configs_result[0]->value;

    if($api_key) {

      //Get the Stores
      $stores = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."stores WHERE (lat = '' OR lng = '') OR (lat = '0.0' OR lng = '0.0') OR (lat IS NULL OR lng IS NULL) OR !(lat BETWEEN -90.10 AND 90.10) OR !(lng BETWEEN -180.10 AND 180.10) OR !(lat REGEXP '^[+-]?[0-9]*([0-9]\\.|[0-9]|\\.[0-9])[0-9]*(e[+-]?[0-9]+)?$') OR !(lng REGEXP '^[+-]?[0-9]*([0-9]\\.|[0-9]|\\.[0-9])[0-9]*(e[+-]?[0-9]+)?$')");

      foreach($stores as $store) {

        $coordinates = \AgileStoreLocator\Helper::getCoordinates($store->street, $store->city, $store->state, $store->postal_code, $store->country, $api_key);

        if($coordinates) {

          if($wpdb->update( ASL_PREFIX.'stores', array('lat' => $coordinates['lat'], 'lng' => $coordinates['lng']),array('id'=> $store->id )))
          {
            $response->summary[] = 'Store ID: '.$store->id.", LAT/LNG Fetch Success, Address: ".implode(', ', array($store->street, $store->city, $store->state, $store->postal_code));
          }
          else
            $response->summary[] = '<span class="red">Store ID: '.$store->id.", LAT/LNG Error Save, Address: ".implode(', ', array($store->street, $store->city, $store->state, $store->postal_code)).'</span>';

        }
        else
          $response->summary[] = '<span class="red">Store ID: '.$store->id.", LAT/LNG Fetch Failed, Address: ".implode(', ', array($store->street, $store->city, $store->state, $store->postal_code)).'</span>';
        
      }

      if(!$stores || count($stores) == 0) {

        $response->summary[] = esc_attr__('Missing Coordinates are not Found in Store Listing','asl_locator');
      }

      $response->msg      = esc_attr__('Missing Coordinates Request Completed','asl_locator');
      $response->success  = true;
    }
    else
      $response->msg    = esc_attr__('Google Server API Key is Missing.','asl_locator');

  
    return $this->send_response($response);
  }

  
}