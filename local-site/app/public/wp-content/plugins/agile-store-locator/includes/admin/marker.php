<?php

namespace AgileStoreLocator\Admin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use AgileStoreLocator\Admin\Base;

/**
 * The marker manager functionality of the plugin.
 *
 * @link       https://agilestorelocator.com
 * @since      4.7.32
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/Admin/Marker
 */

class Marker extends Base
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        parent::__construct();
    }



    /**
     * [delete_marker delete marker/markers]
     * @return [type] [description]
     */
    public function delete_marker()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $multiple = isset($_REQUEST['multiple']) ? $_REQUEST['multiple'] : null;
        $delete_sql;
        $mResults;

        if ($multiple) {
            $item_ids      = implode(',', array_map('intval', $_POST['item_ids']));
            $delete_sql    = 'DELETE FROM ' . ASL_PREFIX . 'markers WHERE id IN (' . $item_ids . ')';
            $mResults      = $wpdb->get_results('SELECT * FROM ' . ASL_PREFIX . 'markers WHERE id IN (' . $item_ids . ')');
        } else {
            $item_id       = intval($_REQUEST['marker_id']);
            $delete_sql    = 'DELETE FROM ' . ASL_PREFIX . 'markers WHERE id = ' . $item_id;
            $mResults      = $wpdb->get_results('SELECT * FROM ' . ASL_PREFIX . 'markers WHERE id = ' . $item_id);
        }

        if (count($mResults) != 0) {
            if ($wpdb->query($delete_sql)) {
                $response->success = true;

                foreach ($mResults as $m) {
                    $inputFileName = ASL_UPLOAD_DIR . 'icon/' . sanitize_file_name($m->icon);

                    if (file_exists($inputFileName) && $m->icon != 'default.png' && $m->icon != 'active.png') {
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
            $response->msg = ($multiple) ? __('Markers deleted successfully.', 'asl_locator') : esc_attr__('Marker deleted successfully.', 'asl_locator');
        }

        return $this->send_response($response);
    }


    /**
     * [get_markers GET the Markers List]
     * @return [type] [description]
     */
    public function get_markers()
    {
        global $wpdb;

        // Pagination variables
        $start  = isset($_REQUEST['iDisplayStart']) ? intval($_REQUEST['iDisplayStart']) : 0;
        $length = isset($_REQUEST['iDisplayLength']) && $_REQUEST['iDisplayLength'] != '-1'
                  ? intval($_REQUEST['iDisplayLength']) : 10;
        $sEcho  = isset($_REQUEST['sEcho']) ? intval($_REQUEST['sEcho']) : 1;

        // Define and whitelist the allowed columns
        $acolumns        = ['id', 'id', 'marker_name', 'icon'];
        $allowed_columns = ['id', 'marker_name', 'icon'];

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

                // Only allow filters on whitelisted columns
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
        $table  = ASL_PREFIX . 'markers';

        $sql      = "SELECT $fields FROM $table";
        $sqlCount = "SELECT COUNT(*) as count FROM $table";

        // Prepare and execute data query
        $data_query   = "$sql $sWhere $sOrder $sLimit";
        $data_output  = $wpdb->get_results($wpdb->prepare($data_query, ...$sql_params));
        $error_status = $wpdb->last_error;

        // Prepare and execute count query
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
            $row->icon = '<img src="' . ASL_UPLOAD_URL . 'icon/' . esc_attr($row->icon) . '" alt="" style="width:20px"/>';

            $row->check = '<div class="custom-control custom-checkbox">
            <input type="checkbox" data-id="' . esc_attr($row->id) . '" class="custom-control-input" id="asl-chk-' . esc_attr($row->id) . '">
            <label class="custom-control-label" for="asl-chk-' . esc_attr($row->id) . '"></label>
        </div>';

            $row->action = '<div class="edit-options">
            <a data-id="' . esc_attr($row->id) . '" title="Edit" class="glyphicon-edit edit_marker"><svg width="14" height="14"><use xlink:href="#i-edit"></use></svg></a>
            <a title="Delete" data-id="' . esc_attr($row->id) . '" class="glyphicon-trash delete_marker"><svg width="14" height="14"><use xlink:href="#i-trash"></use></svg></a>
        </div>';

            $output['aaData'][] = $row;
        }

        return $this->send_response($output);
    }

    /**
     * [get_marker_by_id get marker by id]
     * @return [type] [description]
     */
    public function get_marker_by_id()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $store_id = isset($_REQUEST['marker_id']) ? intval($_REQUEST['marker_id']) : 0;

        $response->list = $wpdb->get_results('SELECT * FROM ' . ASL_PREFIX . 'markers WHERE id = ' . $store_id);

        if (count($response->list) != 0) {
            $response->success = true;
        } else {
            $response->error = esc_attr__('Error occurred while geting record', 'asl_locator');
        }
        return $this->send_response($response);
    }
}
