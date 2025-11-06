<?php

namespace AgileStoreLocator\Admin;


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

use AgileStoreLocator\Admin\Base;

/**
 * The category manager functionality of the admin.
 *
 * @link       https://agilestorelocator.com
 * @since      1.4.3
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/Admin/Category
 */

class Category extends Base {


  /**
   * [__construct description]
   */
  public function __construct() {
    
    parent::__construct();
  }
  
  ////////////////////////////////
  /////////ALL Category Methods //
  ////////////////////////////////
  
  /**
   * [add_category Add Category Method]
   */
  public function add_category() {

    global $wpdb;

    $response = new \stdclass();
    $response->success = false;

    //  Forms Data
    $form_data = stripslashes_deep($_REQUEST['data']);

    //  The Order ID
    $order_id  = (isset($form_data['ordr']) && is_numeric($form_data['ordr']))? $form_data['ordr']: '0';

    //  Parameters to Save
    $data_params = array(
      'category_name' => $this->clean_input($form_data['category_name']),
      'ordr'          => $order_id
    );

    //  lang
    $data_params['lang']    = $this->lang;


    //  Upload the Category Icon File
    $upload_result  = $this->_file_uploader($_FILES["files"], 'svg');

    //  Validate the Upload Success
    if(isset($upload_result['success']) && $upload_result['success']) {

      $file_name    = $upload_result['file_name'];

      //  Add the newly uploaded file
      $data_params['icon'] = $file_name;
    }
    else {

      $response->msg      = ($upload_result['error'])? $upload_result['error']: esc_attr__('Error! Failed to upload the image.','asl_locator');
      return $this->send_response($response);
    }
    
    
    //  Insert the Category Record
    if($wpdb->insert(ASL_PREFIX.'categories', $data_params , array('%s','%s','%s'))) {
        
      $response->msg = esc_attr__("Category added successfully",'asl_locator');
      $response->data = $data_params;
      $response->success = true;
    }
    else {
      
      $response->msg = esc_attr__('Error occurred while saving record','asl_locator');//$form_data
    }

    return $this->send_response($response);
  }

  /**
   * [delete_category delete category/categories]
   * @return [type] [description]
   */
  public function delete_category() {

    global $wpdb;

    $response  = new \stdclass();
    $response->success = false;

    $multiple = isset($_REQUEST['multiple'])? $_REQUEST['multiple']: null;
    $delete_sql;$cResults;

    if($multiple) {

      $item_ids      = implode(",", array_map( 'intval', $_POST['item_ids'] ));
      $delete_sql    = "DELETE FROM ".ASL_PREFIX."categories WHERE id IN (".$item_ids.")";
      $cResults      = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."categories WHERE id IN (".$item_ids.")");
    }
    else {

      $category_id   = intval($_REQUEST['category_id']);
      $delete_sql    = "DELETE FROM ".ASL_PREFIX."categories WHERE id = ".$category_id;
      $cResults      = $wpdb->get_results("SELECT * FROM ".ASL_PREFIX."categories WHERE id = ".$category_id );
    }


    if(count($cResults) != 0) {
      
      if($wpdb->query($delete_sql))
      {
          $response->success = true;
          foreach($cResults as $c) {

            $inputFileName = ASL_UPLOAD_DIR.'icon/'.sanitize_file_name($c->icon);
          
            if(file_exists($inputFileName) && $c->icon != 'default.png') {  
                  
              unlink($inputFileName);
            }
          }             
      }
      else
      {
        $response->error = esc_attr__('Error occurred while deleting record','asl_locator');//$form_data
        $response->msg   = $wpdb->show_errors();
      }
    }
    else
    {
      $response->error = esc_attr__('Error occurred while deleting record','asl_locator');
    }

    if($response->success)
      $response->msg = ($multiple)?__('Categories deleted successfully.','asl_locator'): esc_attr__('Category deleted successfully.','asl_locator');
    
    return $this->send_response($response);
  }


  /**
   * [update_category update category with icon]
   * @return [type] [description]
   */
  public function update_category() {

    global $wpdb;

    $response  = new \stdclass();
    $response->success = false;

    $data        = stripslashes_deep($_REQUEST['data']);
    
    //  The Order ID
    $order_id    = (isset($data['ordr']) && is_numeric($data['ordr']))? $data['ordr']: '0';
      
    //  Parameters to Save
    $data_params = array('category_name' => $this->clean_input($data['category_name']), 'ordr' => $order_id);



    // Have Icon to Update?
    if($data['action'] == "notsame") {

      //  Upload the Icon File
      $upload_result  = $this->_file_uploader($_FILES["files"], 'svg');

      //  Validate the Upload Success
      if(isset($upload_result['success']) && $upload_result['success']) {

        $file_name    = $upload_result['file_name'];

        //  Add the newly uploaded file
        $data_params['icon'] = $file_name;

        //  Delete the old icon if exist
        $old_icon     = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".ASL_PREFIX."categories WHERE id = %d", $data['category_id']));

        //  Delete the old file, if exist
        if (file_exists(ASL_UPLOAD_DIR.'svg/'.$old_icon[0]->icon)) { 
          unlink(ASL_UPLOAD_DIR.'svg/'.sanitize_file_name($old_icon[0]->icon));
        }
      }
      else {

        $response->msg      = ($upload_result['error'])? $upload_result['error']: esc_attr__('Error! Failed to upload the image.','asl_locator');
        return $this->send_response($response);
      }

    }
    
      
    $wpdb->update(ASL_PREFIX."categories", $data_params, array('id' => $data['category_id']));
    $response->msg      = esc_attr__('Category updated successfully.','asl_locator');
    $response->success  = true;
        
    return $this->send_response($response);
  }


  /**
   * [get_category_by_id get category by id]
   * @return [type] [description]
   */
  public function get_category_by_id() {

    global $wpdb;

    $response  = new \stdclass();
    $response->success = false;

    $store_id = isset($_REQUEST['category_id'])? intval($_REQUEST['category_id']) : 0;
    
    $response->list = $wpdb->get_results( "SELECT * FROM ".ASL_PREFIX."categories WHERE id = ".$store_id);

    if(count($response->list)!=0) {

      $response->success = true;

    }
    else{
      $response->error = esc_attr__('Error occurred while geting record','asl_locator');//$form_data

    }
    return $this->send_response($response);
  }


  /**
     * [get_categories GET the Categories]
     * @return [type] [description]
     */
    public function get_categories()
    {
        global $wpdb;

        // Initialize paging variables
        $start  = isset($_REQUEST['iDisplayStart']) ? intval($_REQUEST['iDisplayStart']) : 0;
        $length = isset($_REQUEST['iDisplayLength']) ? intval($_REQUEST['iDisplayLength']) : 10;
        $sEcho  = isset($_REQUEST['sEcho']) ? intval($_REQUEST['sEcho']) : 1;

        $params = $_REQUEST ?? null;

        // Allowed column names for sorting and filtering
        $acolumns        = ['id', 'id', 'category_name', 'ordr', 'icon', 'created_on', 'parent_id'];
        $allowed_columns = ['id', 'category_name', 'ordr', 'icon', 'created_on', 'parent_id'];

        $clause     = [];
        $sql_params = [];

        // Filtering
        if (isset($_REQUEST['filter']) && is_array($_REQUEST['filter'])) {
            foreach ($_REQUEST['filter'] as $key => $value) {
                if (!$key || !$value || $key === 'undefined' || $value === 'undefined') {
                    continue;
                }

                $key   = sanitize_text_field($key);
                $value = sanitize_text_field($value);

                // Only allow filtering on whitelisted columns
                if (in_array($key, $allowed_columns, true)) {
                    $clause[]     = "`$key` LIKE %s";
                    $sql_params[] = '%' . $wpdb->esc_like($value) . '%';
                }
            }
        }

        // Always filter by language
        $clause[]     = '`lang` = %s';
        $sql_params[] = $this->lang;

        $sWhere = $clause ? 'WHERE ' . implode(' AND ', $clause) : '';

        // Pagination clause
        $sLimit = "LIMIT $start, $length";

        // Ordering
        $sOrder = '';
        if (isset($_REQUEST['iSortCol_0']) && isset($_REQUEST['iSortingCols'])) {
            for ($i = 0; $i < intval($_REQUEST['iSortingCols']); $i++) {
                $col_index = intval($_REQUEST['iSortCol_' . $i]);
                $sort_dir  = (isset($_REQUEST['sSortDir_' . $i]) && strtolower($_REQUEST['sSortDir_' . $i]) === 'asc') ? 'ASC' : 'DESC';

                if (isset($acolumns[$col_index]) && in_array($acolumns[$col_index], $allowed_columns, true)) {
                    $sOrder = "ORDER BY `{$acolumns[$col_index]}` $sort_dir";
                    break;
                }
            }
        }

        $fields = implode(', ', $acolumns);
        $table  = ASL_PREFIX . 'categories';

        $sql       = "SELECT $fields FROM $table";
        $sqlCount  = "SELECT COUNT(*) as count FROM $table";

        // Get top-level categories for later use (parent_id = 0)
        $parent_categories = $wpdb->get_results("SELECT `id`, `category_name` FROM $table WHERE `parent_id` = 0");
        if (!count($parent_categories) && strpos($wpdb->last_error, 'parent_id') !== false) {
            \AgileStoreLocator\Activator::add_cat_parent_id();
        }

        // Prepare and execute data query
        $data_query   = "$sql $sWhere $sOrder $sLimit";
        $data_output  = $wpdb->get_results($wpdb->prepare($data_query, ...$sql_params));
        $error_status = $wpdb->last_error;

        // Prepare and execute count query
        $count_query    = "$sqlCount $sWhere";
        $r              = $wpdb->get_results($wpdb->prepare($count_query, ...$sql_params));
        $iFilteredTotal = $r[0]->count ?? 0;

        // Final response output
        $output = [
            'sEcho'                => $sEcho,
            'error'                => $error_status,
            'iTotalRecords'        => $iFilteredTotal,
            'iTotalDisplayRecords' => $iFilteredTotal,
            'parent_categories'    => $parent_categories,
            'aaData'               => []
        ];

        foreach ($data_output as $row) {
            $row->parent_name = '';

            // Match parent name from parent_categories
            if ($row->parent_id) {
                foreach ($parent_categories as $parent) {
                    if ($parent->id == $row->parent_id) {
                        $row->parent_name = esc_attr($parent->category_name);
                        break;
                    }
                }
            }

            // Add icon image HTML
            $row->icon = "<img src='" . ASL_UPLOAD_URL . 'svg/' . esc_attr($row->icon) . "' alt='' style='width:20px'/>";

            // Add action buttons
            $row->action = '<div class="edit-options">
            <a data-id="' . esc_attr($row->id) . '" title="Edit" class="edit_category"><svg width="14" height="14"><use xlink:href="#i-edit"></use></svg></a>
            <a title="Delete" data-id="' . esc_attr($row->id) . '" class="delete_category g-trash"><svg width="14" height="14"><use xlink:href="#i-trash"></use></svg></a>
        </div>';

            // Add checkbox
            $row->check = '<div class="custom-control custom-checkbox">
            <input type="checkbox" data-id="' . esc_attr($row->id) . '" class="custom-control-input" id="asl-chk-' . esc_attr($row->id) . '">
            <label class="custom-control-label" for="asl-chk-' . esc_attr($row->id) . '"></label>
        </div>';

            // Escape category name
            $row->category_name = esc_attr($row->category_name);

            $output['aaData'][] = $row;
        }

        return $this->send_response($output);
    }
  
}