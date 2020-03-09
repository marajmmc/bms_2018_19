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
        $item['date_deleted'] = $time;
        $item['user_deleted'] = $user->user_id;

        /*------UPDATE Data for Reverting 'Forward' -> 'Pending' Status------
        ----------------(Only for 'DSM' & 'AMS' target delete)-------------*/
        $data = array(
            'status_forward' => $CI->config->item('system_status_pending'),
            'remarks_forward' => '',
            'date_rollback' => $time,
            'user_rollback' => $user->user_id
        );

        if($task=='hosm'){
                       Target_helper::delete_target_hosm($item, $item_id);    //Delete HOSM target
            $dsm_ids = Target_helper::delete_target_dsm($item, 0, $item_id);  //Delete DSM target
            $ams_ids = Target_helper::delete_target_ams($item, 0, $dsm_ids);  //Delete AMS target
                       Target_helper::delete_target_tsme($item, 0, $ams_ids); //Delete TSME target
        }
        elseif($task=='dsm')
        {
                       Target_helper::delete_target_dsm($data, $item_id);     //Delete DSM target
            $ams_ids = Target_helper::delete_target_ams($item, 0, $item_id);  //Delete AMS target
                       Target_helper::delete_target_tsme($item, 0, $ams_ids); //Delete TSME target
        }
        elseif($task=='ams')
        {
                       Target_helper::delete_target_ams($data, $item_id);     //Delete AMS target
                       Target_helper::delete_target_tsme($item, 0, $item_id); //Delete TSME target
        }
    }

    public static function delete_target_hosm($item, $item_id) //------------------------------------ HOSM Target Delete
    {
        $CI = & get_instance();
        $table = $CI->config->item('table_bms_target_hosm');

        Query_helper::update($table, $item, array("id =" . $item_id, "status ='" . $CI->config->item('system_status_active') . "'"), FALSE);
        Target_helper::check_update_status();
    }

    public static function delete_target_dsm($item, $item_id = 0, $parent_id = 0) //----------------- DSM Target Delete
    {
        $CI = & get_instance();
        $table = $CI->config->item('table_bms_target_dsm');
        $parent_id_field = 'hosm_id';

        if ($item_id > 0)
        {
            if (is_array($item_id)) {
                $item_id = implode(', ', $item_id);
            }
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

    public static function delete_target_ams($item, $item_id = 0, $parent_id = 0) //----------------- AMS Target Delete
    {
        $CI = & get_instance();
        $table = $CI->config->item('table_bms_target_ams');
        $parent_id_field = 'dsm_id';

        if ($item_id > 0)
        {
            if (is_array($item_id)) {
                $item_id = implode(', ', $item_id);
            }
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

    public static function delete_target_tsme($item, $item_id = 0, $parent_id = 0) //---------------- TSME Target Delete
    {
        $CI = & get_instance();
        $table = $CI->config->item('table_bms_target_tsme');
        $parent_id_field = 'ams_id';

        if ($item_id > 0)
        {
            if (is_array($item_id)) {
                $item_id = implode(', ', $item_id);
            }
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
            }
        }
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

    /*
    // -------------------------------------------------------
    // Effective to Delete Whole Chain with only 1 Query.
    // -------------------------------------------------------
    public static function delete_tree($task = '')
    {
        $CI = & get_instance();
        $time = time();
        $user = User_helper::get_user();

        $item_id = $CI->input->post('id');
        $item = $CI->input->post('item');
        $item['date_deleted'] = $time;
        $item['user_deleted'] = $user->user_id;

        $user_levels = array(
            'tsme' => $CI->config->item('table_bms_target_tsme') . ' tsme',
            'ams' => $CI->config->item('table_bms_target_ams') . ' ams',
            'dsm' => $CI->config->item('table_bms_target_dsm') . ' dsm',
            'hosm' => $CI->config->item('table_bms_target_hosm') . ' hosm'
        );
        $table_name = $CI->config->item('table_bms_target_hosm') . ' hosm';

        $sql  = "UPDATE {$table_name} ";
        $sql .= " LEFT JOIN {$user_levels['dsm']} ON dsm.hosm_id = hosm.id ";
        $sql .= " LEFT JOIN {$user_levels['ams']} ON ams.dsm_id = dsm.id ";
        $sql .= " LEFT JOIN {$user_levels['tsme']} ON tsme.ams_id = ams.id ";
        $sql .= " SET ";

        $set_values = array();
        foreach ($user_levels as $key => $user_level) {
            foreach ($item as $field => $value) {
                $set_values[] = "{$key}.{$field} = '{$value}'";
            }
            if ($key == $task) {
                $sql .= implode(', ', $set_values);
                $sql .= " WHERE {$task}.id = '{$item_id}' ";
                break;
            }
        }
        $sql .= " AND hosm.status = '{$CI->config->item('system_status_active')}' ";
        $sql .= " AND dsm.status = '{$CI->config->item('system_status_active')}' ";
        $sql .= " AND ams.status = '{$CI->config->item('system_status_active')}' ";
        $sql .= " AND tsme.status = '{$CI->config->item('system_status_active')}' ";

        $CI->db->query($sql);
        $CI->db->trans_complete(); //DB Transaction Handle END

        if ($CI->db->trans_status() === TRUE) {
            return true;
        } else {
            return false;
        }
    }*/
}
