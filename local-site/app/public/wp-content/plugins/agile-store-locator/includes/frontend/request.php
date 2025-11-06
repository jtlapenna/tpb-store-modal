<?php

namespace AgileStoreLocator\Frontend;

use AgileStoreLocator\Activator;


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * The public-facing functionality of the plugin is for the AJAX Requests.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/frontend
 * @author     AgileLogix <support@agilelogix.com>
 */

class Request {


	/**
	 * [load_stores Load the Stores using AJAX Request]
	 * @return [type] [description]
	 */
	public function load_stores($output_return = false, $_lang = null) {

		global $wpdb;

		$nonce = isset($_GET['nonce'])? $_GET['nonce']: null;
		
		/*
		if ( ! wp_verify_nonce( $nonce, 'asl_remote_nonce' ))
 			die ( 'CRF check error.');
 		*/
		$load_all 	 = true;
		$accordion   = (isset($_REQUEST['layout']) && $_REQUEST['layout'] == '1')?true:false;
		$category    = (isset($_REQUEST['category']))? sanitize_text_field($_REQUEST['category']):null;
		$stores      = (isset($_REQUEST['stores']))? sanitize_text_field($_REQUEST['stores']):null;
		$lang      	 = (isset($_REQUEST['asl_lang']))? sanitize_text_field($_REQUEST['asl_lang']): '';
		$meta_key    = (isset($_REQUEST['asl_meta_key']))? sanitize_text_field($_REQUEST['asl_meta_key']): '';
		$meta_val    = (isset($_REQUEST['asl_meta_val']))? sanitize_text_field($_REQUEST['asl_meta_val']): null;
		$branches    = (isset($_REQUEST['branches']))? true: false;


		//	Get the fields
		$ddl_fields  = \AgileStoreLocator\Model\Attribute::get_fields();
	
		$ddl_filters = [];

		foreach($ddl_fields as $ddl_field) {

			$ddl_filters[$ddl_field] = (isset($_REQUEST[$ddl_field]))? sanitize_text_field($_REQUEST[$ddl_field]):null;	
		}

		// ddl_fields in the query
    $ddl_fields_str = \AgileStoreLocator\Model\Attribute::sql_query_fields();
		

		$address_filter = [
			'title'     	=> (isset($_REQUEST['title']))? sanitize_text_field($_REQUEST['title']): null,
			'state'     	=> (isset($_REQUEST['state']))? sanitize_text_field($_REQUEST['state']): null,
			'postal_code'	=> (isset($_REQUEST['postal_code']))? sanitize_text_field($_REQUEST['postal_code']): null,
			'city' 				=> (isset($_REQUEST['city']))? sanitize_text_field($_REQUEST['city']): null
		];

		//	Link type we replace the website with the slug
		$slug_link   = (isset($_GET['slug_link']))?true:false;

		$ASL_PREFIX  = ASL_PREFIX;

		$bound   				= '';

		$join_sql 			= '';
		$country_field 	= '';

		//	Cache Lang
		if($_lang) {
			$lang = $_lang;
		}
		

		//Load on bound :: no Load all
		if(!$load_all && isset($_GET['nw']) && isset($_GET['se'])) {
			
			$nw     =  $_GET['nw'];
      $se     =  $_GET['se'];

      $a      = floatval($nw[0]);
      $b      = floatval($nw[1]);

      $c      = floatval($se[0]);
      $d      = floatval($se[1]);
	    

			$bound   = "AND (($a < $c AND s.lat BETWEEN $a AND $c) OR ($c < $a AND s.lat BETWEEN $c AND $a))
                  AND (($b < $d AND s.lng BETWEEN $b AND $d) OR ($d < $b AND s.lng BETWEEN $d AND $b))";
    }
    //else if($accordion) {
    else {

   		$country_field = " {$ASL_PREFIX}countries.`country`,";
   		$join_sql 		 = "LEFT JOIN {$ASL_PREFIX}countries ON s.`country` = {$ASL_PREFIX}countries.id";
    }
    

    $clause = '';

    if($category) {

			$load_categories = explode(',', $category);
			$the_categories  = array();

			foreach($load_categories as $_c) {

				//	Clean it
				if(is_numeric($_c)) {
					$the_categories[] = $_c;
				}
			}

			if(count($the_categories) > 0) {

				$the_categories  = implode(',', $the_categories);
				$category_clause = " AND id IN (".$the_categories.')';
				$clause 		     = " AND {$ASL_PREFIX}stores_categories.`category_id` IN (".$the_categories.")";
			}
		}


    // If marker param exist
		if($stores) {

			$stores = explode(',', $stores);

			//only number
			$store_ids = array();
			foreach($stores as $m) {

				if(is_numeric($m)) {
					$store_ids[] = $m;
				}
			}

			if($store_ids) {

				$store_ids = implode(',', $store_ids);
				$clause    .= " AND s.`id` IN ({$store_ids})";				
			}
		}


		//	Apply the where clause for the ddl_filter
		foreach($ddl_filters as $filter_key => $filter_value) {

			if($filter_value) {

				//  Clean the values
	      $filter_value = explode(',', $filter_value);
	      $filter_value = array_map( 'absint', $filter_value );
	      
	      //	When we have values
	      if($filter_value) {

	      	$conditions 	  = array_map(function($value) use ($filter_key) { return "FIND_IN_SET('$value', s.`$filter_key`)"; }, $filter_value);
					$clause 			 .= " AND (".implode(' OR ', $conditions).')';
	      }
			}
		}

		//	Add the branch Clauses in the query
		$branch_field = '';
		$branch_join 	= '';
	

		//	Filter by Meta
		if (preg_match('/^shipping_id_\d+$/', $meta_key) && is_numeric($meta_val)) {

			$join_sql   .= " LEFT JOIN {$ASL_PREFIX}stores_meta m ON s.id = m.store_id AND m.option_name = '$meta_key'";
			$clause  		.= "AND m.`option_value`  = $meta_val";
		}
					
		//	When we have branches enabled
		if($branches) {

			$branch_field = "GROUP_CONCAT(DISTINCT m.`store_id`) AS 'childs',";
			$branch_join  = "LEFT JOIN (SELECT option_value, store_id  FROM `{$ASL_PREFIX}stores_meta` WHERE  option_name = 'p_id') m ON s.id = m.option_value";
		}

		$query   = "SELECT s.`id`, `title`, {$branch_field} `description`, `street`,  `city`,  `state`, `postal_code`, {$country_field} `lat`,`lng`,`phone`,  `fax`,`email`,`website`,`logo_id`,{$ASL_PREFIX}storelogos.`path`,`marker_id`,`description_2`,`open_hours`, `ordr`, `custom`,`slug`,$ddl_fields_str,
					group_concat(DISTINCT category_id) as categories FROM {$ASL_PREFIX}stores as s 
					$branch_join
					LEFT JOIN {$ASL_PREFIX}storelogos ON logo_id = {$ASL_PREFIX}storelogos.id
					LEFT JOIN {$ASL_PREFIX}stores_categories ON s.`id` = {$ASL_PREFIX}stores_categories.store_id
					$join_sql
					WHERE (s.`pending` IS NULL OR s.`pending` = '') AND s.`lang` = '$lang' AND (is_disabled is NULL || is_disabled = 0) AND (`lat` != '' AND `lng` != '') {$bound} {$clause}";

		///	Address Filter Clause
		$addr_prepare_values = [];			
		
		foreach ($address_filter as $addr_attr => $addr_value) {
				
			if($addr_value) {
				$query  .= " AND `$addr_attr` = %s";

				$addr_prepare_values[] = sanitize_text_field($addr_value); 
			}
		}


		//	call the prepare for the address filter values, as they are strings
		if(count($addr_prepare_values) > 0) {

			$query = $wpdb->prepare($query, $addr_prepare_values);
		}

		//	Modify the Stores to add Where Clause
		$query  = apply_filters( 'asl_filter_stores_query', $query);

		//	add a limit of 25K
		$query .= " GROUP BY s.`id` ORDER BY `title` LIMIT 30000;";
	
		//	Modify the Stores Load Qery in the last
		$query  = apply_filters( 'asl_filter_stores_query_full', $query);
	
		$all_results = $wpdb->get_results($query);


		$debug_error = true;

		if($debug_error) {

			$err_message = isset($wpdb->last_error)? $wpdb->last_error: null;
			
			if(!$all_results && $err_message) {

				$database = $wpdb->dbname;

				//  Check if the new columns are there or not
	      $sql  = "SELECT count(*) as c FROM information_schema.COLUMNS WHERE TABLE_NAME = '{$ASL_PREFIX}stores' AND COLUMN_NAME = 'lang' AND TABLE_SCHEMA = '{$database}'";
	      $col_check_result = $wpdb->get_results($sql);
	      
	      if($col_check_result[0]->c == 0) {
	          
	          Activator::activate();
	      }

				echo json_encode([$err_message]);die;
			}
		}
		

		$days_in_words 	= array('sun'=> asl_esc_lbl('sun'), 'mon'=> asl_esc_lbl('mon'), 'tue'=> asl_esc_lbl('tue'), 'wed'=> asl_esc_lbl('wed'),'thu'=> asl_esc_lbl('thu'), 'fri'=> asl_esc_lbl('fri'), 'sat'=> asl_esc_lbl('sat'));
		$days 		   		= array('mon','tue','wed','thu','fri','sat','sun');


		//	Only fetch the config when link type is set to rewrite
		$slug_url = '';

		if($slug_link) {

			$rewrite_config = \AgileStoreLocator\Helper::get_configs(['rewrite_slug', 'rewrite_id']);

			if(isset($rewrite_config['rewrite_slug']) && $rewrite_config['rewrite_slug'] && $rewrite_config['rewrite_id']) {

				$slug_url = '/'.$rewrite_config['rewrite_slug'].'/';
			}
			//	rewrite data is incomplete
			else {

				$slug_link = null;
			}
		}

		// Get the custom fields
		$custom_fields = \AgileStoreLocator\Helper::get_custom_fields();

		// Make them text textarea
		if (!empty($custom_fields) && is_array($custom_fields)) {

			foreach ($custom_fields as $key => $field) {
				$custom_fields[$key]['is_textarea'] = isset($field['type']) && in_array($field['type'], ['textarea', 'richtext']);
			}
		}
		


		//	Loop over the rows
		foreach($all_results as $aRow) {

			//	Sanitize the Store
			$aRow = \AgileStoreLocator\Helper::sanitize_store($aRow);

			if($aRow->open_hours) {

				$days_are 	= array();
				$open_hours = json_decode($aRow->open_hours);

				foreach($days as $day) {

					if(!empty($open_hours->$day)) {

						$days_are[] = $days_in_words[$day];
					}
				}

				$aRow->days_str = implode(', ', $days_are);
			}


			//	Decode the Custom Fields
			if($custom_fields && $aRow->custom) {

				$custom_fields_data = json_decode($aRow->custom, true);

				// Loop over the custom fields
				foreach($custom_fields as $custom_key => $_field) {

					//	When we have custom field data
					if(isset($custom_fields_data[$custom_key])) {

						//	Replace the new line with <br>
						//$aRow->$custom_key = str_replace("\n", "<br>", wp_kses_post($custom_fields_data[$custom_key]));

						// Escape the custom field data
						$aRow->$custom_key = ($_field['is_textarea'])? wp_kses_post($custom_fields_data[$custom_key]): esc_attr($custom_fields_data[$custom_key]);
					}
				}
			}

			if(isset($aRow->country)) {
				$aRow->country = esc_attr__($aRow->country, 'asl_locator');
			}

			unset($aRow->custom);
	  }

	  //	apply the filter before JSON is sent
		$all_results   = apply_filters( 'asl_filter_stores_result', $all_results);

	  //	To Return the output object
	  if($output_return) {
	  	return $all_results;
	  }

		echo wp_json_encode($all_results);die;
	}



	/**
   * [fixURL Add https:// to the URL]
   * @param  [type] $url    [description]
   * @param  string $scheme [description]
   * @return [type]         [description]
   */
  private function fixURL($url, $scheme = 'http://') {

    if(!$url)
      return '';

    return parse_url($url, PHP_URL_SCHEME) === null ? $scheme . $url : $url;
  }

}
