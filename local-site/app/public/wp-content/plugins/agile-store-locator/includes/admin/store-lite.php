<?php

namespace AgileStoreLocator\Admin;

use AgileStoreLocator\Admin\Base;

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * The store manager functionality of the plugin.
 *
 * @link       https://agilestorelocator.com
 * @since      1.4.3
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/Admin/Store
 */

class Store extends Base {


  /**
   * [__construct description]
   */
  public function __construct() {
    
    parent::__construct();
  }

  /**
   * [admin_delete_all_stores Delete All Stores, Logos and Category Relations]
   * @return [type] [description]
   */
  public function admin_delete_all_stores() {
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $response = new \stdclass();
    $response->success = false;
    
    global $wpdb;
    $prefix = ASL_PREFIX;
    
    $wpdb->query("TRUNCATE TABLE `{$prefix}stores_categories`");
    $wpdb->query("TRUNCATE TABLE `{$prefix}stores`");
    //$wpdb->query("TRUNCATE TABLE `{$prefix}storelogos`");
  
    $response->success  = true;
    $response->msg      = esc_attr__('All Stores are deleted','asl_locator');

    return $this->send_response($response);
  }


  /**
   * [get_store_list GET List of Stores]
   * @return [type] [description]
   */
  public function get_store_list() {
    
    global $wpdb;

    $start      = isset( $_REQUEST['iDisplayStart'])?$_REQUEST['iDisplayStart']:0;   
    $params     = isset($_REQUEST)?$_REQUEST:null;
    $categories = isset($_REQUEST['categories'])?intval($_REQUEST['categories']):null;


    $acolumns = array(
      ASL_PREFIX.'stores.id', ASL_PREFIX.'stores.id ',ASL_PREFIX.'stores.id ','title','description',
      'lat','lng','street','state','city',
      'phone','email','website','postal_code','is_disabled',
      ASL_PREFIX.'stores.id','marker_id', 'logo_id', 'pending',
      ASL_PREFIX.'stores.created_on'/*,'country_id'*/
    );

    $columnsFull = array(
      ASL_PREFIX.'stores.id as id',ASL_PREFIX.'stores.id as id',ASL_PREFIX.'stores.id as id','title','description','lat','lng','street','state', ASL_PREFIX.'countries.country','city','phone','email','website','postal_code','is_disabled',ASL_PREFIX.'stores.created_on', 'pending'
    );
      
    //  All the prepare parameters  
    $prepare_params = [];

    $clause = array();

    if(isset($_REQUEST['filter'])) {

      //  Get all searchable columns
      $searchable_columns = \AgileStoreLocator\Model\Store::get_searchable_columns();

      //  Loop over the filters
      foreach($_REQUEST['filter'] as $key => $value) {

        if($value != '') {

          $value    = sanitize_text_field($value);
          $key      = sanitize_text_field($key);

          //  $key must be within the allowed attributes
          $key = in_array($key, $searchable_columns)? $key: 'id';

          //  Disabled
          if($key == 'is_disabled')
          {
            $value = ($value == 'yes')? 1: 0;
            $clause[] = ASL_PREFIX."stores.{$key} = 1";
            continue;
          }
          //  Marker ID
          elseif($key == 'marker_id' || $key == 'logo_id')
          {
            
            $clause[] = ASL_PREFIX."stores.{$key} = %s";
            $prepare_params[] = $value;
            continue;
          }
          // Country Clause
          elseif($key == 'country')
          {
            
            $clause[] = ASL_PREFIX."countries.{$key} LIKE %s";
            $prepare_params[] = "%$value%";
            continue;
          }
          
          //  Other columns
          $prepare_params[] = "%$value%";
          $clause[] = ASL_PREFIX."stores.{$key} LIKE %s";
        }
      } 
    }
    

    //iDisplayStart::Limit per page
    $sLimit = "";
    $displayStart = isset($_REQUEST['iDisplayStart'])?intval($_REQUEST['iDisplayStart']):0;
    
    if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
    {
      $sLimit = "LIMIT ".$displayStart.", ".
        intval( $_REQUEST['iDisplayLength'] );
    }
    else
      $sLimit = "LIMIT ".$displayStart.", 20 ";

    /*
     * Ordering
     */
    $sOrder = "";
    if ( isset( $_REQUEST['iSortCol_0'] ) )
    {
      $sOrder = "ORDER BY  ";

      for ( $i=0 ; $i < intval( $_REQUEST['iSortingCols'] ) ; $i++ )
      {
        if (isset($_REQUEST['iSortCol_'.$i]))
        {
          $sort_dir = (isset($_REQUEST['sSortDir_0']) && $_REQUEST['sSortDir_0'] == 'asc')? 'ASC': 'DESC';
          $sOrder .= $acolumns[ intval( $_REQUEST['iSortCol_'.$i] )  ]." ".$sort_dir;
          break;
        }
      }
      

      //$sOrder = substr_replace( $sOrder, "", -2 );
      if ( $sOrder == "ORDER BY" )
      {
        $sOrder = "";
      }
    }


    //  When Pending isn't required, filter the pending stores
    if(!(isset($_REQUEST['filter']) && isset($_REQUEST['filter']['pending']))) {

      $clause[] = '('.ASL_PREFIX."stores.pending IS NULL OR ".ASL_PREFIX."stores.pending = 0)";
    }

    //  When Categories filter is applied
    if($categories) {
      $clause[]    = ASL_PREFIX.'stores_categories.category_id = '.$categories;
    }
    
    //  Add the lang Filter
    $clause[] = ASL_PREFIX."stores.lang = '{$this->lang}'";

    $sWhere = implode(' AND ', $clause);
    
    if($sWhere != '')$sWhere = ' WHERE '.$sWhere;
    
    $fields = implode(',', $columnsFull);
    

    $fields  .= ',marker_id,logo_id,group_concat(category_id) as categories,'.ASL_PREFIX.'countries.country';

    ###get the fields###
    $sql      =   ("SELECT $fields FROM ".ASL_PREFIX."stores LEFT JOIN ".ASL_PREFIX."stores_categories ON ".ASL_PREFIX."stores.id = ".ASL_PREFIX."stores_categories.store_id LEFT JOIN ".ASL_PREFIX."countries ON ".ASL_PREFIX."stores.country = ".ASL_PREFIX."countries.id ");

    //  Count Stores
    $sqlCount = "SELECT COUNT(DISTINCT(".ASL_PREFIX."stores.id)) 'count' FROM ".ASL_PREFIX."stores LEFT JOIN ".ASL_PREFIX."stores_categories ON ".ASL_PREFIX."stores.id = ".ASL_PREFIX."stores_categories.store_id LEFT JOIN ".ASL_PREFIX."countries ON ".ASL_PREFIX."stores.country = ".ASL_PREFIX."countries.id ";
    
    

    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "{$sql} {$sWhere} GROUP BY ".ASL_PREFIX."stores.id {$sOrder} {$sLimit}";
    

    //  When we have prepare parameters run the prepare
    if(count($prepare_params) > 0) {
      $sQuery = $wpdb->prepare($sQuery, $prepare_params);
    }

    //  backup of query for debug
    $dQuery = $sQuery;

    $data_output = $wpdb->get_results($sQuery);
    $wpdb->show_errors = true;
    $error = $wpdb->last_error;
      
    /* Data set length after filtering */
    $sQuery = "{$sqlCount} {$sWhere}";

    //  When we have prepare parameters run the prepare
    if(count($prepare_params) > 0) {
      $sQuery = $wpdb->prepare($sQuery, $prepare_params);
    }

    $r = $wpdb->get_results($sQuery);
    $iFilteredTotal = $r[0]->count;
    
    $iTotal = $iFilteredTotal;

    /*
     * Output
     */
    $sEcho  = isset($_REQUEST['sEcho'])?intval($_REQUEST['sEcho']):1;
    $output = array(
      "sEcho" => intval($_REQUEST['sEcho']),
      "iTotalRecords" => $iTotal,
      "query" => $dQuery,
      'orderby' => $sOrder,
      "iTotalDisplayRecords" => $iFilteredTotal,
      "aaData" => array()
    );

    if($error) {

      $output['error'] = $error;
      $output['query'] = $dQuery;
    }


    $days_in_words = array('0'=>'Sun','1'=>'Mon','2'=>'Tues','3'=>'Wed','4'=>'Thur','5'=>'Fri','6'=>'Sat');
      
    //  Loop over the stores
    foreach($data_output as $aRow) {
        
      $row = $aRow;

      $edit_url = 'admin.php?page=edit-agile-store&store_id='.$row->id;

      $row->action = '<div class="edit-options"><a class="row-cpy" title="Duplicate" data-id="'.$row->id.'"><svg width="30" height="30"><use xlink:href="#i-clipboard"></use></svg></a>
      <a href="'.$edit_url.'"><svg width="24" height="24"><use xlink:href="#i-edit"></use></svg></a>
      <a title="Delete" data-id="'.$row->id.'" class="glyphicon-trash"><svg width="30" height="30"><use xlink:href="#i-trash"></use></svg></a></div>';

      //  Show a approve button
      if(isset($row->pending) && $row->pending == '1') {

        $row->action .= '<button data-id="'.$row->id.'" data-loading-text="'.esc_attr__('Approving...','asl_locator').'" class="btn btn-approve btn-success" type="button">'.esc_attr__('Approve','asl_locator').'</button>';
      }

      $row->check  = '<div class="custom-control custom-checkbox"><input type="checkbox" data-id="'.$row->id.'" class="custom-control-input" id="asl-chk-'.$row->id.'"><label class="custom-control-label" for="asl-chk-'.$row->id.'"></label></div>';

      //Show country with state
      /*if($row->state && isset($row->iso_code_2))
        $row->state = $row->state.', '.$row->iso_code_2;*/

      $output['aaData'][] = $row;

        //get the categories
      if($aRow->categories) {

        $categories_ = $wpdb->get_results("SELECT category_name FROM ".ASL_PREFIX."categories WHERE id IN ($aRow->categories)");

        $cnames = array();
        foreach($categories_ as $cat_)
          $cnames[] = $cat_->category_name;

        $aRow->categories = implode(', ', $cnames);
      }  
    }

    return $this->send_response($output);
    
    /*switch (json_last_error()) {
        case JSON_ERROR_NONE:
            echo ' - No errors';
        break;
        case JSON_ERROR_DEPTH:
            echo ' - Maximum stack depth exceeded';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Underflow or the modes mismatch';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Syntax error, malformed JSON';
        break;
        case JSON_ERROR_UTF8:
            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
        default:
            echo ' - Unknown error';
        break;
    }*/
  }


  /**
   * [validate_coordinates Validate that all the coordinates are Valid]
   * @return [type] [description]
   */
  public function validate_coordinates() {

    global $wpdb;

    $response  = new \stdclass();
    $response->success = false; 

    //  initial message
    $message = esc_attr__('Success! All coordinates looks correct values', 'asl_locator');

    //  get the stores
    $invalid_stores = $wpdb->get_results("SELECT id FROM ".ASL_PREFIX."stores WHERE (lat = '' AND lng = '') OR (lat IS NULL AND lng IS NULL) OR !(lat BETWEEN -90.10 AND 90.10) OR !(lng BETWEEN -180.10 AND 180.10) OR !(lat REGEXP '^[+-]?[0-9]*([0-9]\\.|[0-9]|\\.[0-9])[0-9]*(e[+-]?[0-9]+)?$') OR !(lng REGEXP '^[+-]?[0-9]*([0-9]\\.|[0-9]|\\.[0-9])[0-9]*(e[+-]?[0-9]+)?$')");

    //  Validate the Count difference
    if($invalid_stores) {

      $coord_with_err = count($invalid_stores);

      //  When less than 10, show the numbers
      if($coord_with_err < 10) {

        //  get the store IDs
        $store_ids = array_map(function($value) { return $value->id;}, $invalid_stores);

        $store_ids = implode(',', $store_ids);

        $coord_with_err .= ' ('.$store_ids.')';
      }

      //  prepare the message
      if($coord_with_err)
        $message = esc_attr__("Error! Wrong coordinates of {$coord_with_err} stores", 'asl_locator');
    }

    // Check the Default Coordinates
    $sql = "SELECT `key`,`value` FROM ".ASL_PREFIX."configs WHERE `key` = 'default_lat' || `key` = 'default_lng'";
    $all_configs_result = $wpdb->get_results($sql);


    $all_configs = array();

    foreach($all_configs_result as $c) {
      $all_configs[$c->key] = $c->value;
    }

    $is_valid  = \AgileStoreLocator\Helper::validate_coordinate($all_configs['default_lat'], $all_configs['default_lng']);

    //  Default Lat/Lng are invalid
    if(!$is_valid) {

      $message .= '<br>'.esc_attr__('Default Lat & Default Lng values are invalid!', 'asl_locator');
    }

    //  All Passed
    if(!$invalid_stores && $is_valid) {

      $response->success = true;
    }


    $response->msg = $message;
    

    return $this->send_response($response);
  }


  /**
   * [remove_duplicates Remove all the duplicate rows]
   * @return [type] [description]
   */
  public function remove_duplicates() {

    global $wpdb;

    $response           = new \stdclass();
    $response->success  = false;

    $asl_prefix   = ASL_PREFIX; 

    $remove_query = "DELETE s1 FROM {$asl_prefix}stores s1
                    INNER JOIN {$asl_prefix}stores s2 
                    WHERE s1.id < s2.id AND s1.title = s2.title AND s1.lat = s2.lat AND s1.lng = s2.lng;";

    //  All Count
    $all_count   = $wpdb->get_results("SELECT COUNT(*) AS c FROM ".ASL_PREFIX."stores");

    //  Previous count
    $all_count   = $all_count[0];

    //  Remove the duplicates
    if($wpdb->query($remove_query)) {
      
      //  All Count
      $new_count     = $wpdb->get_results("SELECT COUNT(*) AS c FROM ".ASL_PREFIX."stores");

      //  Previous count
      $new_count     = $new_count[0];

      $removed       = $all_count->c - $new_count->c;

      $response->msg = $removed.' '.esc_attr__('Duplicate stores removed','asl_locator');

      $response->success = true;
    }
    else {
     
      $response->error = esc_attr__('No Duplicate deleted!','asl_locator');//$form_data
      $response->msg   = $wpdb->show_errors();
    }


    return $this->send_response($response);
  }
  
  /**
   * [duplicate_store to  Duplicate the store]
   * @return [type] [description]
   */
  public function duplicate_store() {

    global $wpdb;

    $response  = new \stdclass();
    $response->success = false;

    $store_id = isset($_REQUEST['store_id'])? intval($_REQUEST['store_id']): 0;


    $result = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."stores WHERE id = ".$store_id);   

    if($result && $result[0]) {

      $result = (array)$result[0];

      unset($result['id']);
      unset($result['created_on']);
      unset($result['updated_on']);

      //insert into stores table
      if($wpdb->insert( ASL_PREFIX.'stores', $result)){
        $response->success = true;
        $new_store_id = $wpdb->insert_id;

        //get categories and copy them
        $s_categories = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."stores_categories WHERE store_id = ".$store_id);

        /*Save Categories*/
        foreach ($s_categories as $_category) { 

          $wpdb->insert(ASL_PREFIX.'stores_categories', 
            array('store_id'=>$new_store_id,'category_id'=>$_category->category_id),
            array('%s','%s'));      
        }

        
        //SEnd the response
        $response->msg = esc_attr__('Store duplicated successfully.','asl_locator');
      }
      else
      {
        $response->error = esc_attr__('Error occurred while saving Store','asl_locator');//$form_data
        $response->msg   = $wpdb->show_errors();
      } 

    }

    return $this->send_response($response);
  }
  
  /**
   * [add_new_store POST METHODS for Add New Store]
   */
  public function add_new_store() {

    global $wpdb;

    $response  = new \stdclass();
    $response->success = false;

    $form_data     = stripslashes_deep($_REQUEST['data']);
    

    //  lang
    $form_data['lang']    = $this->lang;

    //  These two fields are allowed limited HTML tags
    $description_field    = $this->clean_input_html($form_data['description']);
    $description_field_2  = $this->clean_input_html($form_data['description_2']);

    //  Custom Field
    $custom_fields        = (isset($_REQUEST['asl-custom']) && $_REQUEST['asl-custom'])? stripslashes_deep($_REQUEST['asl-custom']): null;
    $custom_fields        = ($custom_fields && is_array($custom_fields) && count($custom_fields) > 0)? json_encode($this->clean_input_array($custom_fields)): null;
  
    //  Clean the array
    $form_data = $this->clean_input_array($form_data);
    
    //  Add them again after the input cleaning
    $form_data['description']   = $description_field;
    $form_data['description_2'] = $description_field_2;

    // Prevent dublication of slug (update function)
    $form_data['slug']    = \AgileStoreLocator\Schema\Slug::slugify($form_data);

    
    //  Add the Custom Fields, already cleaned
    $form_data['custom']  = ($custom_fields)? $custom_fields: null;


    // Insert into stores table
    if($wpdb->insert( ASL_PREFIX.'stores', $form_data)) {

      $response->success = true;

      $store_id   = $wpdb->insert_id;
      $categories = (isset($_REQUEST['sl-category']) && $_REQUEST['sl-category'])? ($_REQUEST['sl-category']): null;

      // Save Categories
      if($categories)
        foreach ($categories as $category) {

        $wpdb->insert(ASL_PREFIX.'stores_categories', 
          array(
            'store_id'    => $store_id,
            'category_id' => $category
          ),
          array('%s','%s')
        );
      }

      //  Add a filter for asl-wc to modify the data
      if(isset($_REQUEST['sl_wc']))
        apply_filters( 'asl_woocommerce_store_settings', $_REQUEST['sl_wc'], $store_id);

      $response->msg = esc_attr__('Store added successfully.','asl_locator');
    }
    else {

      $wpdb->show_errors  = true;
      $response->error    = esc_attr__('Error occurred while saving Store','asl_locator');
      $response->msg      = $wpdb->print_error();
    }
    
    return $this->send_response($response);  
  }

  /**
   * [update_store update Store]
   * @return [type] [description]
   */
  public function update_store() {

    global $wpdb;

    $response  = new \stdclass();
    $response->success = false;

    $form_data = stripslashes_deep($_REQUEST['data']);
    $update_id = isset($_REQUEST['updateid'])? intval($_REQUEST['updateid']) : 0;


    //  Custom Field
    $custom_fields        = (isset($_REQUEST['asl-custom']) && $_REQUEST['asl-custom'])? stripslashes_deep($_REQUEST['asl-custom']): null;
    $custom_fields        = ($custom_fields && is_array($custom_fields) && count($custom_fields) > 0)? json_encode($this->clean_input_array($custom_fields)): null;
    
    
    //  When Update Id is there
    if($update_id && is_numeric($update_id)) {

      //  These two fields are allowed limited HTML tags
      $description_field    = $this->clean_input_html($form_data['description']);
      $description_field_2  = $this->clean_input_html($form_data['description_2']);

      $store_data = array(
        'title'         => $form_data['title'],
        'phone'         => $form_data['phone'],
        'fax'           => $form_data['fax'],
        'email'         => $form_data['email'],
        'street'        => $form_data['street'],
        'postal_code'   => $form_data['postal_code'],
        'city'          => $form_data['city'],
        'state'         => $form_data['state'],
        'lat'           => $form_data['lat'],
        'lng'           => $form_data['lng'],
        'website'       => $this->fixURL($form_data['website']),
        'country'       => $form_data['country'],
        'is_disabled'   => (isset($form_data['is_disabled']) && $form_data['is_disabled'])?'1':'0',
        'logo_id'     => '',
        'marker_id'   => '',
        'brand'       => '',
        'special'     => '',
        'logo_id'   => $form_data['logo_id'],
        'open_hours'  => $form_data['open_hours'],
        'ordr'      => $form_data['ordr'],
        'updated_on'  => date('Y-m-d H:i:s')
      );

      //  Clean the array
      $store_data = $this->clean_input_array($store_data);

      //  Add them again after the input cleaning
      $store_data['description']   = $description_field;
      $store_data['description_2'] = $description_field_2;

      //  Add the Custom Fields, already cleaned
      $store_data['custom'] = ($custom_fields)? $custom_fields: null;
      
      //  Update into stores table
      $wpdb->update(ASL_PREFIX."stores", $store_data, array('id' => $update_id));

      
      $sql = "DELETE FROM ".ASL_PREFIX."stores_categories WHERE store_id = ".$update_id;
      $wpdb->query($sql);

      $categories = (isset($_REQUEST['sl-category']) && $_REQUEST['sl-category'])? ($_REQUEST['sl-category']): null;

      // Save Categories
      if($categories)
        foreach ($categories as $category) {

        $wpdb->insert(ASL_PREFIX.'stores_categories', 
          array(
            'store_id'    => $update_id,
            'category_id' => $category
          ),
          array('%s','%s'));  
      }
      
      //  Add a filter for asl-wc to modify the data
      if(isset($_REQUEST['sl_wc'])) {
        apply_filters( 'asl_woocommerce_store_settings', $_REQUEST['sl_wc'], $update_id);
      }
    
      
      $response->msg      = esc_attr__('Store updated successfully.','asl_locator');
      $response->success  = true;
    }
    else {

      $response->msg      = esc_attr__('Error! Update id not found.','asl_locator');
    }


    return $this->send_response($response);
  }


  /**
   * [delete_store To delete the store/stores]
   * @return [type] [description]
   */
  public function delete_store() {

    global $wpdb;

    $response  = new \stdclass();
    $response->success = false;

    $multiple = isset($_REQUEST['multiple'])? $_REQUEST['multiple']: null;
    
    $delete_sql;

    //  For Multiple rows
    if($multiple) {

      $item_ids      = implode(",", array_map( 'intval', $_POST['item_ids'] ));
      $delete_sql    = "DELETE FROM ".ASL_PREFIX."stores WHERE id IN (".$item_ids.")";
    }
    else {

      $store_id      = intval($_REQUEST['store_id']);
      $delete_sql    = "DELETE FROM ".ASL_PREFIX."stores WHERE id = ".$store_id;
    }

    //  Delete Store?
    if($wpdb->query($delete_sql)) {

      $response->success = true;
      $response->msg = ($multiple)?__('Stores deleted successfully.','asl_locator'):esc_attr__('Store deleted successfully.','asl_locator');
    }
    else {
      $response->error = esc_attr__('Error occurred while saving record','asl_locator');//$form_data
      $response->msg   = $wpdb->show_errors();
    }
    
    return $this->send_response($response);
  }


  /**
   * [store_status To Change the Status of Store]
   * @return [type] [description]
   */
  public function store_status() {

    global $wpdb;

    $response  = new \stdclass();
    $response->success = false;

    $status = (isset($_REQUEST['status']) && $_REQUEST['status'] == '1')?'0':'1';
    
    $status_title  = ($status == '1')? esc_attr__('Disabled','asl_locator'): esc_attr__('Enabled','asl_locator'); 
    $delete_sql;

    $item_ids      = implode(",", array_map( 'intval', $_POST['item_ids'] ));
    $update_sql    = "UPDATE ".ASL_PREFIX."stores SET is_disabled = {$status} WHERE id IN (".$item_ids.")";

    if($wpdb->query($update_sql)) {

      $response->success  = true;
      $response->msg      = esc_attr__('Selected Stores','asl_locator').' '.$status_title;
    }
    else {
      $response->error = esc_attr__('Error occurred while Changing Status','asl_locator');
      $response->msg   = $wpdb->show_errors();
    }
    
    return $this->send_response($response);
  }
  
}