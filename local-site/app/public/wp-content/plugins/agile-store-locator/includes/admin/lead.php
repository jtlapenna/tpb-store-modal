<?php

namespace AgileStoreLocator\Admin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use AgileStoreLocator\Admin\Base;

/**
 * The lead manager functionality of the plugin.
 *
 * @link       https://agilestorelocator.com
 * @since      4.8
 *
 * @package    AgileStoreLocator
 * @subpackage AgileStoreLocator/Admin/Lead
 */

class Lead extends Base
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * [delete_lead delete lead/leads]
     * @return [type] [description]
     */
    public function delete_lead()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $multiple = isset($_REQUEST['multiple']) ? $_REQUEST['multiple'] : null;
        $delete_sql;
        $mResults;

        if ($multiple) {
            $item_ids      = implode(',', array_map('intval', $_POST['item_ids']));
            $delete_sql    = 'DELETE FROM ' . ASL_PREFIX . 'leads WHERE id IN (' . $item_ids . ')';
            $mResults      = $wpdb->get_results('SELECT * FROM ' . ASL_PREFIX . 'leads WHERE id IN (' . $item_ids . ')');
        } else {
            $item_id       = intval($_REQUEST['lead_id']);
            $delete_sql    = 'DELETE FROM ' . ASL_PREFIX . 'leads WHERE id = ' . $item_id;
            $mResults      = $wpdb->get_results('SELECT * FROM ' . ASL_PREFIX . 'leads WHERE id = ' . $item_id);
        }

        if (count($mResults) != 0) {
            if ($wpdb->query($delete_sql)) {
                $response->success = true;
            } else {
                $response->error = esc_attr__('Error occurred while deleting record', 'asl_locator');
                $response->msg   = $wpdb->show_errors();
            }
        } else {
            $response->error = esc_attr__('Error occurred while deleting record', 'asl_locator');
        }

        if ($response->success) {
            $response->msg = ($multiple) ? __('Leads deleted successfully.', 'asl_locator') : esc_attr__('Lead deleted successfully.', 'asl_locator');
        }

        return $this->send_response($response);
    }

    /**
     * [update_lead update lead with icon]
     * @return [type] [description]
     */
    public function update_lead()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $data      = $_REQUEST['data'];

        //  Lead Update Parameter
        $data_params = ['lead_name' => trim(sanitize_text_field($data['lead_name']))];

        //  Execute the Update Query
        $wpdb->update(ASL_PREFIX . 'leads', $data_params, ['id' => sanitize_text_field($data['lead_id'])]);

        $response->msg      = esc_attr__('Lead Updated Successfully.', 'asl_locator');
        $response->success  = true;

        return $this->send_response($response);
    }

    /**
     * [export_dealers Export the CSV file the dealers]
     * @return [type] [description]
     */
    public function export_dealers()
    {
        global $wpdb;

        $duration = isset($_REQUEST['sl-duration']) ? $_REQUEST['sl-duration'] : null;

        list($start_date, $end_date)  = explode('-', $duration);

        //  Trim dates
        $start_date = date('Y-m-d', strtotime((trim($start_date)))) . ' 00:00:00';
        $end_date   = date('Y-m-d', strtotime((trim($end_date)))) . ' 23:59:00';

        //  leads Data
        //$leads = $wpdb->get_results($wpdb->prepare("SELECT `l`.`id`, `l`.`name`, `l`.`phone`, `l`.`postal_code`, `l`.`email`, `l`.`message`, s.`title`, s.`street`, s.`city` FROM ".ASL_PREFIX."leads as l LEFT JOIN ".ASL_PREFIX."stores as s ON s.id = l.store_id WHERE l.created_on >= %s AND l.created_on <= %s", $start_date, $end_date ));
        $leads = $wpdb->get_results($wpdb->prepare("SELECT COUNT(*) AS 'total',  s.`title` as 'Store Name', s.`street` as 'Store Street', s.`city` as 'Store City' FROM " . ASL_PREFIX . 'leads as l LEFT JOIN ' . ASL_PREFIX . 'stores as s ON s.id = l.store_id WHERE l.created_on between %s AND %s AND `l`.`store_id` > 0 GROUP BY l.store_id', $start_date, $end_date));

        $csv = new \AgileStoreLocator\Admin\CSV\Reader();

        //  Rows to be exported
        $all_rows = [];

        //  Just send the headers for empty
        if (!$leads) {
            $leads = [['Total' => '', 'Store Name' => '', 'Street'=> '', 'City' => '']];
        }

        //  Loop over the stores data
        foreach ($leads as $value) {
            //unset($value->id);

            //  Push into rows collection
            $all_rows[] = $value;
        }

        $csv->setRows($all_rows);

        $csv->write(\AgileStoreLocator\Admin\CSV\Reader::DOWNLOAD, 'dealers-export.csv');
        ;
        die;
    }

    /**
     * [export_leads Export the leads filter by duration]
     * @return [type] [description]
     */
    public function export_leads()
    {
        global $wpdb;

        $duration = isset($_REQUEST['sl-duration']) ? $_REQUEST['sl-duration'] : null;

        list($start_date, $end_date)  = explode('-', $duration);

        //  Trim dates
        $start_date = date('Y-m-d', strtotime((trim($start_date)))) . ' 00:00:00';
        $end_date   = date('Y-m-d', strtotime((trim($end_date)))) . ' 23:59:00';

        //  leads Data
        //$leads = $wpdb->get_results($wpdb->prepare("SELECT `l`.`id`, `l`.`name`, `l`.`phone`, `l`.`postal_code`, `l`.`email`, `l`.`message`, s.`title`, s.`street`, s.`city` FROM ".ASL_PREFIX."leads as l LEFT JOIN ".ASL_PREFIX."stores as s ON s.id = l.store_id WHERE l.created_on >= %s AND l.created_on <= %s", $start_date, $end_date ));
        $leads = $wpdb->get_results($wpdb->prepare("SELECT `l`.`id`, `l`.`name` as 'Lead Name', `l`.`phone` as 'Lead Phone', `l`.`postal_code` as 'Lead Postal Code', `l`.`email` as 'Lead Email', `l`.`message` as 'Message', `l`.`created_on` as 'Dated',  s.`title` as 'Store Name', s.`street` as 'Store Street', s.`city` as 'Store City' FROM " . ASL_PREFIX . 'leads as l LEFT JOIN ' . ASL_PREFIX . 'stores as s ON s.id = l.store_id WHERE l.created_on between %s AND %s', $start_date, $end_date));

        //  Just send the headers for empty
        if (!$leads) {
            $leads = [['id' =>  '', 'Name' => '', 'Phone' => '', 'Postal Code' => '', 'Email' => '', 'Message' =>'']];
        }

        $csv = new \AgileStoreLocator\Admin\CSV\Reader();

        //  Rows to be exported
        $all_rows = [];

        //  Loop over the stores data
        foreach ($leads as $value) {
            //unset($value->id);

            //  Push into rows collection
            $all_rows[] = $value;
        }

        $csv->setRows($all_rows);

        $csv->write(\AgileStoreLocator\Admin\CSV\Reader::DOWNLOAD, 'leads-export.csv');
        
        die;
    }

    /**
     * [get_leads GET the Leads List]
     * @return [type] [description]
     */
    public function get_leads()
    {
        global $wpdb;

        // Pagination and basic params
        $start  = isset($_REQUEST['iDisplayStart']) ? intval($_REQUEST['iDisplayStart']) : 0;
        $length = isset($_REQUEST['iDisplayLength']) && $_REQUEST['iDisplayLength'] != '-1'
                  ? intval($_REQUEST['iDisplayLength']) : 10;
        $sEcho  = isset($_REQUEST['sEcho']) ? intval($_REQUEST['sEcho']) : 1;

        // Whitelisted columns for filtering and ordering
        $acolumns        = ['l.id', 'l.id', 'l.name', 'l.phone', 'l.email', 'l.postal_code', 's.title', 'l.created_on'];
        $allowed_columns = ['id', 'name', 'phone', 'email', 'postal_code', 'created_on'];

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

                // Allow filtering only on safe columns (no table prefix here)
                if (in_array($key, $allowed_columns, true)) {
                    $clause[]     = "`l`.`$key` LIKE %s";
                    $sql_params[] = '%' . $wpdb->esc_like($value) . '%';
                }
            }
        }

        $sWhere = $clause ? 'WHERE ' . implode(' AND ', $clause) : '';
        $sLimit = "LIMIT $start, $length";

        // Sorting logic
        $sOrder = '';
        if (isset($_REQUEST['iSortCol_0']) && isset($_REQUEST['iSortingCols'])) {
            for ($i = 0; $i < intval($_REQUEST['iSortingCols']); $i++) {
                $col_index = intval($_REQUEST['iSortCol_' . $i]);
                $sort_dir  = (isset($_REQUEST['sSortDir_' . $i]) && strtolower($_REQUEST['sSortDir_' . $i]) === 'asc') ? 'ASC' : 'DESC';

                if (isset($acolumns[$col_index])) {
                    $column_raw = $acolumns[$col_index];

                    // Ensure the base column is allowed for ordering
                    $base_column = str_replace(['l.', 's.'], '', $column_raw);
                    if (in_array($base_column, $allowed_columns, true) || $base_column === 'title') {
                        $sOrder = "ORDER BY $column_raw $sort_dir";
                        break;
                    }
                }
            }
        }

        // Query components
        $fields = '`l`.`id`, `l`.`name`, `l`.`phone`, `l`.`postal_code`, `l`.`email`, `l`.`message`, `l`.`created_on`, `s`.`title`';
        $table  = ASL_PREFIX . 'leads AS l LEFT JOIN ' . ASL_PREFIX . 'stores AS s ON s.id = l.store_id';

        $sql      = "SELECT $fields FROM $table";
        $sqlCount = "SELECT COUNT(*) as count FROM $table";

        // Final queries
        $data_query  = "$sql $sWhere $sOrder $sLimit";
        $count_query = "$sqlCount $sWhere";

        $data_output    = $wpdb->get_results($wpdb->prepare($data_query, ...$sql_params));
        $r              = $wpdb->get_results($wpdb->prepare($count_query, ...$sql_params));
        $iFilteredTotal = isset($r[0]->count) ? intval($r[0]->count) : 0;

        // Prepare response
        $output = [
            'sEcho'                => $sEcho,
            'iTotalRecords'        => $iFilteredTotal,
            'iTotalDisplayRecords' => $iFilteredTotal,
            'aaData'               => []
        ];

        // Format output rows
        foreach ($data_output as $row) {
            $row->dated = strtotime($row->created_on);

            $row->check = '<div class="custom-control custom-checkbox">
            <input type="checkbox" data-id="' . esc_attr($row->id) . '" class="custom-control-input" id="asl-chk-' . esc_attr($row->id) . '">
            <label class="custom-control-label" for="asl-chk-' . esc_attr($row->id) . '"></label>
            </div>';

            $row->action = '<div class="edit-options">
            <a data-id="' . esc_attr($row->id) . '" title="Edit" class="edit_lead text-primary">View</a>
        </div>';

            $output['aaData'][] = $row;
        }

        return $this->send_response($output);
    }

    /**
     * [get_lead_by_id get lead by id]
     * @return [type] [description]
     */
    public function get_lead_by_id()
    {
        global $wpdb;

        $response          = new \stdclass();
        $response->success = false;

        $store_id = isset($_REQUEST['lead_id']) ? intval($_REQUEST['lead_id']) : 0;

        $response->list = $wpdb->get_results('SELECT * FROM ' . ASL_PREFIX . 'leads WHERE id = ' . $store_id);

        if (count($response->list) != 0) {
            $response->success = true;
        } else {
            $response->error = esc_attr__('Error occurred while geting record', 'asl_locator');
        }
        return $this->send_response($response);
    }
}
