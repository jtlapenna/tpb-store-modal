<?php

namespace AgileStoreLocator\Admin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use AgileStoreLocator\Admin\Base;

/**
 * The logo manager functionality of the plugin.
 *
 * @link       https://agilestorelocator.com
 * @since      4.7.32
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/Admin/Logo
 */

class Logo extends Base
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * [upload_logo Upload the Logo]
     * @return [type] [description]
     */
    public function upload_logo()
    {
        $response          = new \stdclass();
        $response->success = false;

        //  Validate if the Name isn't missing
        if (empty($_POST['data']['logo_name']) || !$_POST['data']['logo_name']) {
            $response->msg = __('Error! logo name is required.', 'asl_locator');
            return $this->send_response($response);
        }

        if (!empty($_POST['data']['img_id'])) {
            $img_path  = get_attached_file($_POST['data']['img_id'], 'medium');
            $content   = file_get_contents($img_path);
            $pathinfo  = pathinfo($img_path);
            $put       = file_put_contents(ASL_UPLOAD_DIR . 'Logo/' . $pathinfo['filename'] . '.' . $pathinfo['extension'], $content);
        }

        //  Logo Name
        $logo_name   = isset($_POST['data']['logo_name']) ? sanitize_text_field($_POST['data']['logo_name']) : ('Logo ' . time());

        //   Parameters to Save
        $data_params = ['name' => $logo_name];

        //  Validate the Upload Success
        if (isset($put)) {
            $file_name    = $pathinfo['filename'] . '.' . $pathinfo['extension'];

            //  Add the newly uploaded file
            $data_params['path'] = $file_name;
        } else {
            $response->msg      = ($upload_result['error']) ? $upload_result['error'] : __('Error! Failed to upload the image.', 'asl_locator');
            return $this->send_response($response);
        }

        global $wpdb;

        //  Insert the Logo
        $wpdb->insert(ASL_PREFIX . 'storelogos', $data_params);

        $response->list = $wpdb->get_results('SELECT * FROM ' . ASL_PREFIX . 'storelogos ORDER BY id DESC');
        $response->msg  = __('Logo is uploaded successfully.', 'asl_locator');

        // Get the Logo ID
        if (isset($wpdb->insert_id) && $wpdb->insert_id) {
            $response->logo_id  = $wpdb->insert_id;
        }

        $response->success = true;

        return $this->send_response($response);
    }

    /**
     * [delete_logo Delete a Logo]
     * @return [type] [description]
     */
    public function delete_logo()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $multiple = isset($_REQUEST['multiple']) ? $_REQUEST['multiple'] : null;
        $delete_sql;
        $mResults;

        if ($multiple) {
            $item_ids      = implode(',', array_map('intval', $_POST['item_ids']));
            $delete_sql    = 'DELETE FROM ' . ASL_PREFIX . 'storelogos WHERE id IN (' . $item_ids . ')';
            $mResults      = $wpdb->get_results('SELECT * FROM ' . ASL_PREFIX . 'storelogos WHERE id IN (' . $item_ids . ')');
        } else {
            $item_id       = intval($_REQUEST['logo_id']);
            $delete_sql    = 'DELETE FROM ' . ASL_PREFIX . 'storelogos WHERE id = ' . $item_id;
            $mResults      = $wpdb->get_results('SELECT * FROM ' . ASL_PREFIX . 'storelogos WHERE id = ' . $item_id);
        }

        if (count($mResults) != 0) {
            if ($wpdb->query($delete_sql)) {
                $response->success = true;

                foreach ($mResults as $m) {
                    $inputFileName = ASL_UPLOAD_DIR . 'Logo/' . sanitize_file_name($m->path);

                    if (file_exists($inputFileName) && $m->path != 'default.png') {
                        unlink($inputFileName);
                    }
                }
            } else {
                $response->error = esc_attr__('Error occurred while deleting record', 'asl_locator');
                $response->msg   = $wpdb->show_errors();
            }
        } else {
            $response->error = esc_attr__('Error occurred while deleting record', 'asl_locator');
        }

        if ($response->success) {
            $response->msg = ($multiple) ? __('Logos deleted Successfully.', 'asl_locator') : esc_attr__('Logo deleted Successfully.', 'asl_locator');
        }

        return $this->send_response($response);
    }

    /**
     * [update_logo update logo with icon]
     * @return [type] [description]
     */
    public function update_logo()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $data = $_REQUEST['data'];

        //  Logo Update Parameter
        $data_params = ['name' => trim($data['logo_name'])];

        // with icon
        if ($data['action'] == 'notsame') {
            if (!empty($_POST['data']['img_id'])) {
                $img_path = get_attached_file($_POST['data']['img_id'], 'medium');
                $content  = file_get_contents($img_path);
                $pathinfo = pathinfo($img_path);
                $put      = file_put_contents(ASL_UPLOAD_DIR . 'Logo/' . $pathinfo['filename'] . '.' . $pathinfo['extension'], $content);
            }

            //  Validate the Upload Success
            if (isset($put)) {
                $file_name    = $pathinfo['filename'] . '.' . $pathinfo['extension'];

                $response->file_name = $file_name;

                //  Add the newly uploaded file
                $data_params['path'] = $file_name;

                //  Delete the old icon if exist
                $old_icon     = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . ASL_PREFIX . 'storelogos WHERE id = %d', $data['logo_id']));

                //  Delete the old file, if exist
                if (file_exists(ASL_UPLOAD_DIR . 'Logo/' . $old_icon[0]->path)) {
                    unlink(ASL_UPLOAD_DIR . 'Logo/' . sanitize_file_name($old_icon[0]->path));
                }
            } else {
                $response->msg      = ($upload_result['error']) ? $upload_result['error'] : __('Error! Failed to upload the image.', 'asl_locator');
                return $this->send_response($response);
            }
        }

        //  Execute Update Query
        $wpdb->update(ASL_PREFIX . 'storelogos', $data_params, ['id' => $data['logo_id']]);

        $response->msg      = __('Logo updated successfully.', 'asl_locator');
        $response->success  = true;

        return $this->send_response($response);
    }

    /**
     * [get_logo_by_id get logo by id]
     * @return [type] [description]
     */
    public function get_logo_by_id()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $store_id = isset($_REQUEST['logo_id']) ? intval($_REQUEST['logo_id']) : 0;

        $response->list = $wpdb->get_results('SELECT * FROM ' . ASL_PREFIX . 'storelogos WHERE id = ' . $store_id);

        if (count($response->list) != 0) {
            $response->success = true;
        } else {
            $response->error = esc_attr__('Error occurred while geting record', 'asl_locator');
        }
        return $this->send_response($response);
    }

    /**
     * [get_logos GET the Logos]
     * @return [type] [description]
     */
    public function get_logos()
    {
        global $wpdb;

        // Pagination variables
        $start  = isset($_REQUEST['iDisplayStart']) ? intval($_REQUEST['iDisplayStart']) : 0;
        $length = isset($_REQUEST['iDisplayLength']) && $_REQUEST['iDisplayLength'] != '-1'
                  ? intval($_REQUEST['iDisplayLength']) : 10;
        $sEcho  = isset($_REQUEST['sEcho']) ? intval($_REQUEST['sEcho']) : 1;

        // Define and whitelist the allowed columns
        $acolumns        = ['id', 'id', 'name', 'path'];
        $allowed_columns = ['id', 'name', 'path'];

        // Filtering logic with SQL injection protection
        $clause     = [];
        $sql_params = [];

        if (isset($_REQUEST['filter']) && is_array($_REQUEST['filter'])) {
            foreach ($_REQUEST['filter'] as $key => $value) {
                if (!$key || !$value || $key === 'undefined' || $value === 'undefined') {
                    continue;
                }

                $key   = sanitize_text_field($key);
                $value = sanitize_text_field($value);

                // Only allow filters on safe columns
                if (in_array($key, $allowed_columns, true)) {
                    $clause[]     = "`$key` LIKE %s";
                    $sql_params[] = '%' . $wpdb->esc_like($value) . '%';
                }
            }
        }

        $sWhere = $clause ? 'WHERE ' . implode(' AND ', $clause) : '';
        $sLimit = "LIMIT $start, $length";

        // Sorting logic with column whitelist
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

        // Define fields and table
        $fields = implode(',', $acolumns);
        $table  = ASL_PREFIX . 'storelogos';

        $sql      = "SELECT $fields FROM $table";
        $sqlCount = "SELECT COUNT(*) as count FROM $table";

        // Build full query for data
        $data_query   = "$sql $sWhere $sOrder $sLimit";
        $data_output  = $wpdb->get_results($wpdb->prepare($data_query, ...$sql_params));
        $error_status = $wpdb->last_error;

        // Build query for count
        $count_query    = "$sqlCount $sWhere";
        $r              = $wpdb->get_results($wpdb->prepare($count_query, ...$sql_params));
        $iFilteredTotal = isset($r[0]->count) ? intval($r[0]->count) : 0;

        $output = [
            'sEcho'                => $sEcho,
            'iTotalRecords'        => $iFilteredTotal,
            'iTotalDisplayRecords' => $iFilteredTotal,
            'aaData'               => []
        ];

        // Format each row for DataTable
        foreach ($data_output as $row) {
            $row->path  = '<img src="' . ASL_UPLOAD_URL . 'Logo/' . esc_attr($row->path) . '" style="max-width:100px"/>';
            $row->check = '<div class="custom-control custom-checkbox">
            <input type="checkbox" data-id="' . esc_attr($row->id) . '" class="custom-control-input" id="asl-chk-' . esc_attr($row->id) . '">
            <label class="custom-control-label" for="asl-chk-' . esc_attr($row->id) . '"></label>
        </div>';
            $row->action = '<div class="edit-options">
            <a data-id="' . esc_attr($row->id) . '" title="Edit" class="glyphicon-edit edit_logo"><svg width="14" height="14"><use xlink:href="#i-edit"></use></svg></a>
            <a title="Delete" data-id="' . esc_attr($row->id) . '" class="glyphicon-trash delete_logo"><svg width="14" height="14"><use xlink:href="#i-trash"></use></svg></a>
        </div>';

            $output['aaData'][] = $row;
        }

        return $this->send_response($output);
    }
}
