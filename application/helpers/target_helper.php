<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Target_helper
{
    public static $update_success_status;
    
    public function __construct()
    {
        Target_helper::$update_success_status = array();
    }

    public static function check_update_status()
    {
        $CI = & get_instance();
        if($CI->db->affected_rows() > 0){
            Target_helper::$update_success_status[] = TRUE;
        }else{
            Target_helper::$update_success_status[] = FALSE;
        }
    }

    public static function delete_target_tree($task){
        $CI = & get_instance();
        $user = User_helper::get_user();
        $time = time();

        $item_id = $CI->input->post('id');
        $item = $CI->input->post('item');

        $pending_array = array(
            'status_forward' => $CI->config->item('system_status_pending'),
            'remarks_forward' => '',
            'date_forwarded' => null,
            'user_forwarded' => null,
            'remarks_delete' => $item['remarks_delete'],
            'date_deleted' => $time,
            'user_deleted' => $user->user_id
        );
        $delete_array = array(
            'status' => $item['status'],
            'remarks_delete_parent' => $item['remarks_delete'],
            'date_deleted_parent' => $time,
            'user_deleted_parent' => $user->user_id
        );

        if($task=='hq')
        {
                                   Target_helper::delete_target_hq($pending_array, $item_id);                  //HQ target: Revert Back to PENDING
            $target_division_ids = Target_helper::delete_target_division($delete_array, 0, $item_id);          //Division target: DELETE
                $target_zone_ids = Target_helper::delete_target_zone($delete_array, 0, $target_division_ids);  //Zone target: DELETE
                                   Target_helper::delete_target_territory($delete_array, $target_zone_ids);    //Territory target: DELETE
        }
        elseif($task=='division')
        {
                                   Target_helper::delete_target_division($pending_array, $item_id);          //Division target: Revert Back to PENDING
                $target_zone_ids = Target_helper::delete_target_zone($delete_array, 0, $item_id);            //Zone target: DELETE
                                   Target_helper::delete_target_territory($delete_array, $target_zone_ids);  //Territory target: DELETE
        }
        elseif($task=='zone')
        {
                                   Target_helper::delete_target_zone($pending_array, $item_id);      //Zone target: Revert Back to PENDING
                                   Target_helper::delete_target_territory($delete_array, $item_id);  //Territory target: DELETE
        }
    }

    public static function delete_target_hq($item, $item_id) //------------------------------------ HQ Target Delete
    {
        $CI = & get_instance();
        $table = $CI->config->item('table_bms_target_hq');
        $item['amount_target_total'] = 0;
        $item['revision_count'] = 0;

        $CI->db->set('revision_count_delete', 'revision_count_delete+1', FALSE);
        Query_helper::update($table, $item, array("id =" . $item_id, "status ='" . $CI->config->item('system_status_active') . "'"), FALSE);
        Target_helper::check_update_status();
    }

    public static function delete_target_division($item, $item_id = 0, $parent_id = 0) //----------------- Division Target Delete
    {
        $CI = & get_instance();
        $table = $CI->config->item('table_bms_target_division');
        $parent_id_field = 'target_hq_id';

        if ($item_id > 0)
        {
            $item['revision_count'] = 0;
            $CI->db->set('revision_count_delete', 'revision_count_delete+1', FALSE);
            Query_helper::update($table, $item, array("id =" . $item_id, "status ='" . $CI->config->item('system_status_active') . "'"), FALSE);
            Target_helper::check_update_status();
        }
        elseif ($parent_id != 0)
        {
            if (is_array($parent_id)) {
                $parent_id = implode(', ', $parent_id);
            }
            $result = Query_helper::get_info($table, 'GROUP_CONCAT(id) as ids', array("{$parent_id_field} IN ( {$parent_id} )", "status ='" . $CI->config->item('system_status_active') . "'"), 1);
            if($result['ids']){
                Query_helper::update($table, $item, array("id IN (" . $result['ids'] . ")", "status ='" . $CI->config->item('system_status_active') . "'"), FALSE);
                Target_helper::check_update_status();
                return $result['ids'];
            }
        }
        return 0;
    }

    public static function delete_target_zone($item, $item_id = 0, $parent_id = 0) //----------------- Zone Target Delete
    {
        $CI = & get_instance();
        $table = $CI->config->item('table_bms_target_zone');
        $parent_id_field = 'target_division_id';

        if ($item_id > 0)
        {
            $item['revision_count'] = 0;
            $CI->db->set('revision_count_delete', 'revision_count_delete+1', FALSE);
            Query_helper::update($table, $item, array("id =" . $item_id, "status ='" . $CI->config->item('system_status_active') . "'"), FALSE);
            Target_helper::check_update_status();
        }
        elseif ($parent_id != 0)
        {
            if (is_array($parent_id)) {
                $parent_id = implode(', ', $parent_id);
            }
            $result = Query_helper::get_info($table, 'GROUP_CONCAT(id) as ids', array("{$parent_id_field} IN ( {$parent_id} )", "status ='" . $CI->config->item('system_status_active') . "'"), 1);
            if($result['ids']){
                Query_helper::update($table, $item, array("id IN (" . $result['ids'] . ")", "status ='" . $CI->config->item('system_status_active') . "'"), FALSE);
                Target_helper::check_update_status();
                return $result['ids'];
            }
        }
        return 0;
    }

    public static function delete_target_territory($item, $parent_id = 0) //---------------- Territory Target Delete
    {
        $CI = & get_instance();
        $table = $CI->config->item('table_bms_target_territory');
        $parent_id_field = 'target_zone_id';

        if ($parent_id != 0)
        {
            if (is_array($parent_id)) {
                $parent_id = implode(', ', $parent_id);
            }
            $result = Query_helper::get_info($table, 'GROUP_CONCAT(id) as ids', array("{$parent_id_field} IN ( {$parent_id} )", "status ='" . $CI->config->item('system_status_active') . "'"), 1);
            if($result['ids']){
                Query_helper::update($table, $item, array("id IN (" . $result['ids'] . ")", "status ='" . $CI->config->item('system_status_active') . "'"), FALSE);
                Target_helper::check_update_status();
            }
        }
    }

    public static function get_delete_info($params)
    {
        $CI = & get_instance();
        $user = User_helper::get_user();

        // Main Table variables
        $main_table = $params['main_table'];
        // Details Table variables
        $details_table = $params['details_table'];
        $location_table = $params['location_table'];
        $location_id_field = $params['location_id_field'];
        $foreign_key = $params['foreign_key'];

        // Main Table Query
        $CI->db->from($main_table);
        $CI->db->select('*');
        $CI->db->where("user_deleted", $user->user_id);
        $CI->db->where("revision_count_delete > ", 0);
        if(isset($params['year']) && ($params['year'] > 0)){
            $CI->db->where("year", $params['year']);
        }
        if(isset($params['month']) && ($params['month'] > 0)){
            $CI->db->where("month", $params['month']);
        }
        $results = $CI->db->get()->result_array();

        $ids = array(0);
        foreach($results as $result){
            $ids[] = $result['id']; // Collecting ids for Query in Details Table
        }

        // Validation Check??

        // Details Table Query
        $CI->db->from($details_table . ' details');
        $CI->db->select("details.id, details.{$foreign_key}, details.amount_target, details.date_created, details.user_created, details.remarks_delete_parent, details.user_deleted_parent, details.date_deleted_parent");

        $CI->db->join($location_table . ' location', "location.id = details.{$location_id_field}");
        $CI->db->select('location.name');

        $CI->db->where('details.status', $CI->config->item('system_status_delete'));
        $CI->db->where('user_deleted_parent', $user->user_id);
        $CI->db->where_in("details.{$foreign_key}", $ids);
        $detail_results = $CI->db->get()->result_array();

        $user_ids = array();
        foreach($detail_results as $detail_result){
            $user_ids[$detail_result['user_created']] = $detail_result['user_created'];
            $user_ids[$detail_result['user_deleted_parent']] = $detail_result['user_deleted_parent'];
        }
        $user_info = System_helper::get_users_info($user_ids);

        $details = array();
        foreach($detail_results as $detail_result){

            $details[$detail_result['date_deleted_parent']]['title_date'] = "( <b>Deleted On:</b> ".(System_helper::display_date_time($detail_result['date_deleted_parent']))." )";
            $details[$detail_result['date_deleted_parent']]['items'][] = array(
                'name' => $detail_result['name'],
                'amount_target' => $detail_result['amount_target'],
            );
            //User CREATED
            $index=0;
            $details[$detail_result['date_deleted_parent']]['user_history'][$index++] = array(
                'label' => $CI->lang->line('LABEL_CREATED_BY'),
                'value' => $user_info[$detail_result['user_created']]['name']
            );
            $details[$detail_result['date_deleted_parent']]['user_history'][$index++] = array(
                'label' => $CI->lang->line('LABEL_DATE_CREATED_TIME'),
                'value' => System_helper::display_date_time($detail_result['date_created'])
            );
            //User DELETED
            $details[$detail_result['date_deleted_parent']]['user_history'][$index++] = array(
                'label' => $CI->lang->line('LABEL_DELETED_BY'),
                'value' => $user_info[$detail_result['user_deleted_parent']]['name']
            );
            $details[$detail_result['date_deleted_parent']]['user_history'][$index++] = array(
                'label' => $CI->lang->line('LABEL_DATE_DELETED_TIME'),
                'value' => System_helper::display_date_time($detail_result['date_deleted_parent'])
            );
            $details[$detail_result['date_deleted_parent']]['user_history'][$index++] = array(
                'label' => $CI->lang->line('LABEL_REASON_REMARKS'),
                'value' => nl2br($detail_result['remarks_delete_parent'])
            );

        }
        return array('details' => $details);
    }

    /*------------------Convert Numeric Amount INTO In-Word------------------*/
    public static function get_string_amount_inword($number)
    {
        $number = (float)$number;
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(
            0 => '', 1 => 'One', 2 => 'Two',
            3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
            7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
            10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
            13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
            16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
            40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
            70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
        );
        $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
        if ($number == 0) {
            $str[] = 'Zero';
        } else {
            while ($i < $digits_length) {
                $divider = ($i == 2) ? 10 : 100;
                $number = floor($no % $divider);
                $no = floor($no / $divider);
                $i += $divider == 10 ? 1 : 2;
                if ($number) {
                    $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                    $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                    $str [] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
                } else $str[] = null;
            }
        }
        $Taka = implode('', array_reverse($str));

        $words[0] = 'Zero';
        $Paisa = ($decimal) ? ", " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paisa' : '';

        return ($Taka ? $Taka . 'Taka' : '') . $Paisa;
    }
}
