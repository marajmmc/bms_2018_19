<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Target_helper
{
    public static function get_location_name($task='', $location_id = 0)
    {
        $CI = & get_instance();
        if($task=='division')
        {
            $CI->db->from($CI->config->item('table_login_setup_location_divisions'));
            $CI->db->select('name');
            $CI->db->where('id', $location_id);
            $result = $CI->db->get()->row_array();
            return $result['name'];
        }
        else if($task=='zone')
        {
            $CI->db->from($CI->config->item('table_login_setup_location_zones'));
            $CI->db->select('name');
            $CI->db->where('id', $location_id);
            $result = $CI->db->get()->row_array();
            return $CI->lang->line('LABEL_ZONE_NAME').' - '.$result['name'];
        }
        else if($task=='territory')
        {
            $CI->db->from($CI->config->item('table_login_setup_location_territories'));
            $CI->db->select('name');
            $CI->db->where('id', $location_id);
            $result = $CI->db->get()->row_array();
            return $CI->lang->line('LABEL_TERRITORY_NAME').' - '.$result['name'];
        }
        else
        {
            return $CI->lang->line('LABEL_HEAD_OFFICE_NAME');
        }

    }

    public static function delete_target_tree($task)
    {
        $CI = & get_instance();
        $user = User_helper::get_user();
        $time = time();
        $item_id = $CI->input->post('id');
        $item = $CI->input->post('item');
        $get_location_relation_ids=Target_helper::get_location_relation_ids($task,$item_id);

        $delete_array = array
        (
            'status' => $CI->config->item('system_status_active'),
            'status_forward' => $CI->config->item('system_status_pending'),
            'remarks_delete' => $item['remarks_delete'],
            'date_deleted' => $time,
            'user_deleted' => $user->user_id
        );
        $delete_parent_array = array
        (
            'status' => $item['status'],
            'remarks_delete_parent' => $item['remarks_delete'],
            'date_deleted_parent' => $time,
            'user_deleted_parent' => $user->user_id
        );
        if($task=='hq')
        {
            $delete_array['amount_target_total'] = 0;
            $CI->db->set('revision_count_delete', 'revision_count_delete+1', FALSE);
            Query_helper::update($CI->config->item('table_bms_target_hq'), $delete_array, array("id =".$item_id), FALSE);
            Query_helper::update($CI->config->item('table_bms_target_division'), $delete_parent_array, array("target_hq_id =".$item_id, "status = '".$CI->config->item('system_status_active')."'"), FALSE);
            if(sizeof($get_location_relation_ids['zone'])>0)
            {
                $parent_id = implode(', ', $get_location_relation_ids['zone']);
                Query_helper::update($CI->config->item('table_bms_target_zone'), $delete_parent_array, array("id IN (" . $parent_id . ")"), FALSE);
                Query_helper::update($CI->config->item('table_bms_target_territory'), $delete_parent_array, array("target_zone_id IN (" . $parent_id . ")", "status = '".$CI->config->item('system_status_active')."'"), FALSE);
            }
        }
        elseif($task=='division')
        {
            $CI->db->set('revision_count_delete', 'revision_count_delete+1', FALSE);
            Query_helper::update($CI->config->item('table_bms_target_division'), $delete_array, array("id =".$item_id), FALSE);

            if(sizeof($get_location_relation_ids['zone'])>0)
            {
                $parent_id = implode(', ', $get_location_relation_ids['zone']);
                Query_helper::update($CI->config->item('table_bms_target_zone'), $delete_parent_array, array("id IN (" . $parent_id . ")"), FALSE);
                Query_helper::update($CI->config->item('table_bms_target_territory'), $delete_parent_array, array("target_zone_id IN (" . $parent_id . ")", "status = '".$CI->config->item('system_status_active')."'"), FALSE);
            }
        }
        elseif($task=='zone')
        {
            $CI->db->set('revision_count_delete', 'revision_count_delete+1', FALSE);
            Query_helper::update($CI->config->item('table_bms_target_zone'), $delete_array, array("id =".$item_id), FALSE);
            Query_helper::update($CI->config->item('table_bms_target_territory'), $delete_parent_array, array("target_zone_id =".$item_id, "status = '".$CI->config->item('system_status_active')."'"), FALSE);
        }
    }
    public static function get_location_relation_ids($task, $item_id)
    {
        $CI = & get_instance();
        $user = User_helper::get_user();
        $item_ids['hq']=array();
        $item_ids['division']=array();
        $item_ids['zone']=array();
        if($task=='hq')
        {
            $results = Query_helper::get_info($CI->config->item('table_bms_target_division'), array('*'), array('target_hq_id =' . $item_id, "status = '".$CI->config->item('system_status_active')."'"));
            foreach($results as $result)
            {
                $item_ids['division'][$result['id']]=$result['id'];
            }
            if(sizeof($item_ids['division'])>0)
            {
                $parent_id = implode(', ', $item_ids['division']);
                $results = Query_helper::get_info($CI->config->item('table_bms_target_zone'), array('*'), array('target_division_id IN (' . $parent_id .')', "status = '".$CI->config->item('system_status_active')."'"));
                foreach($results as $result)
                {
                    $item_ids['zone'][$result['id']]=$result['id'];
                }
            }

        }
        elseif($task=='division')
        {
            $results = Query_helper::get_info($CI->config->item('table_bms_target_zone'), array('*'), array('target_division_id =' . $item_id, "status = '".$CI->config->item('system_status_active')."'"));
            foreach($results as $result)
            {
                $item_ids['zone'][$result['id']]=$result['id'];
            }
        }
        elseif($task=='zone')
        {

        }
        return $item_ids;
    }

    public static function get_delete_info($params)
    {
        $CI = & get_instance();
        $user = User_helper::get_user();

        // Details Table variables
        $details_table = $params['details_table'];
        $location_table = $params['location_table'];
        $location_id_field = $params['location_id_field'];
        $foreign_key = $params['foreign_key'];

        // Validation Check??

        // Details Table Query
        $CI->db->from($details_table . ' details');
        $CI->db->select("details.id, details.{$foreign_key}, details.amount_target, details.date_created, details.user_created, details.remarks_delete_parent, details.user_deleted_parent, details.date_deleted_parent");

        $CI->db->join($location_table . ' location', "location.id = details.{$location_id_field}");
        $CI->db->select('location.name');

        $CI->db->where('details.status', $CI->config->item('system_status_delete'));
        if ($user->user_group != $CI->config->item('USER_GROUP_SUPER')) // If not SuperAdmin, Then user can only access own Deleted Item.
        {
            $CI->db->where('user_deleted_parent', $user->user_id);
        }
        if(isset($params['year']) && ($params['year'] > 0)){
            $CI->db->where("details.year", $params['year']);
        }
        if(isset($params['month']) && ($params['month'] > 0)){
            $CI->db->where("details.month", $params['month']);
        }
        $detail_results = $CI->db->get()->result_array();
        //echo $CI->db->last_query();

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
