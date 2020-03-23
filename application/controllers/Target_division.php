<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Target_division extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public $common_view_location;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class($this));
        $this->controller_url = strtolower(get_class($this));
        $this->locations = User_helper::get_locations();
        if (!($this->locations)) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->common_view_location = 'target_hq';
        $this->load->helper('target_helper');
        $this->language_labels();
    }

    private function language_labels()
    {
        // Labels
        $this->lang->language['LABEL_AMOUNT_TARGET'] = 'Target Amount (BDT)';
        $this->lang->language['LABEL_AMOUNT_TARGET_TOTAL'] = 'Total Target Amount (BDT)';
        $this->lang->language['LABEL_AMOUNT_ALLOCATED'] = 'Allocated Amount';
        $this->lang->language['LABEL_AMOUNT_REMAINING'] = 'Remaining Amount';
        $this->lang->language['LABEL_LOCATION'] = 'Location';
        $this->lang->language['LABEL_NO_OF_EDIT'] = 'No. of Edit';
        $this->lang->language['LABEL_NO_OF_DELETE'] = 'No. of Delete';
        $this->lang->language['LABEL_REASON_REMARKS'] = 'Reason/ Remarks';
        $this->lang->language['LABEL_DATE_DELETED_TIME'] = 'Deleted Time';
        // Target
        $this->lang->language['LABEL_ASSIGN_TARGET'] = 'Assign Target';
        $this->lang->language['LABEL_ASSIGNED_TARGET'] = 'Assigned Target';
        $this->lang->language['LABEL_REMAINING_TARGET'] = 'Remaining Target';
        // Messages
        $this->lang->language['MSG_FORWARDED_ALREADY'] = 'This Target has been Forwarded Already.';
        $this->lang->language['MSG_FORWARDED_DELETE'] = 'Only a Forwarded target can be Deleted.';
        $this->lang->language['MSG_TARGET_NOT_ASSIGNED'] = 'No Target has been assigned for this Location.';
        $this->lang->language['MSG_TARGET_ALLOCATION'] = 'No Target Amount has been Allocated.';
    }

    public function index($action = "list", $id = 0)
    {
        if ($action == "list") {
            $this->system_list();
        }
        elseif ($action == "get_items") {
            $this->system_get_items();
        }
        elseif ($action == "list_all") {
            $this->system_list_all();
        }
        elseif ($action == "get_items_all") {
            $this->system_get_items_all();
        }
        elseif ($action == "list_deleted") {
            $this->system_list_deleted();
        }
        elseif ($action == "get_items_deleted") {
            $this->system_get_items_deleted();
        }
        elseif ($action == "edit") {
            $this->system_edit($id);
        }
        elseif ($action == "save") {
            $this->system_save();
        }
        elseif ($action == "details") {
            $this->system_details($id);
        }
        elseif ($action == "details_deleted") {
            $this->system_details_deleted($id);
        }
        elseif ($action == "delete") {
            $this->system_delete($id);
        }
        elseif ($action == "save_delete") {
            $this->system_save_delete($id);
        }
        elseif ($action == "forward") {
            $this->system_forward($id);
        }
        elseif ($action == "save_forward") {
            $this->system_save_forward();
        }
        elseif ($action == "get_delete_history") {
            $this->system_get_delete_history();
        }
        elseif ($action == "set_preference") {
            $this->system_set_preference('list');
        }
        elseif ($action == "set_preference_all") {
            $this->system_set_preference('list_all');
        }
        elseif ($action == "set_preference_deleted") {
            $this->system_set_preference('list_deleted');
        }
        elseif ($action == "save_preference") {
            System_helper::save_preference();
        }
        else {
            $this->system_list();
        }
    }

    private function get_preference_headers($method = 'list')
    {
        $data = array();
        $data['id'] = 1;
        $data['year'] = 1;
        $data['month'] = 1;
        if (!($this->locations['division_id'] > 0)) {
            $data['location'] = 1;
        }
        if ($method == 'list') {
            $data['no_of_edit'] = 1;
            $data['amount_target'] = 1;
            $data['amount_allocated'] = 1;
            $data['amount_remaining'] = 1;
        }
        if ($method == 'list_all') {
            $data['no_of_edit'] = 1;
            $data['amount_target'] = 1;
            $data['amount_allocated'] = 1;
            $data['amount_remaining'] = 1;
            $data['status_forward'] = 1;
        }
        if ($method == 'list_deleted') {
            $data['no_of_delete'] = 1;
        }
        return $data;
    }

    private function system_set_preference($method = 'list')
    {
        $user = User_helper::get_user();
        if (isset($this->permissions['action6']) && ($this->permissions['action6'] == 1)) {
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['preference_method_name'] = $method;
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view("preference_add_edit", $data, true));
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/set_preference_' . $method);
            $this->json_return($ajax);
        }
        else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_list()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1)) {
            $user = User_helper::get_user();
            $method = 'list';
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title'] = "Monthly " . ($this->lang->line('LABEL_DIVISION_NAME')) . " Target List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/' . $method);
            $this->json_return($ajax);
        }
        else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items()
    {
        $this->common_query(); // Call Common part of below Query Stack
        // Additional Conditions -STARTS
        $this->db->where('target.status', $this->config->item('system_status_active'));
        $this->db->where('target.status_forward', $this->config->item('system_status_pending'));
        // Additional Conditions -ENDS
        $items = $this->db->get()->result_array();
        $this->db->flush_cache(); // Flush/Clear current Query Stack

        // Details Table
        $this->db->from($this->config->item('table_bms_target_zone'));
        $this->db->select('target_division_id, SUM(amount_target) AS amount_allocated');
        $this->db->where('status', $this->config->item('system_status_active'));
        $this->db->group_by('target_division_id');
        $results = $this->db->get()->result_array();

        $detail_items = array();
        foreach ($results as $result) {
            $detail_items[$result['target_division_id']] = $result;
        }

        foreach ($items as &$item) {
            if (isset($detail_items[$item['id']])) {
                $item['amount_remaining'] = System_helper::get_string_amount($item['amount_target'] - $detail_items[$item['id']]['amount_allocated']);
                $item['amount_allocated'] = System_helper::get_string_amount($detail_items[$item['id']]['amount_allocated']);
            }
            else {
                $item['amount_remaining'] = '-';
                $item['amount_allocated'] = '-';
            }
            $item['amount_target'] = System_helper::get_string_amount($item['amount_target']);
            $item['month'] = DateTime::createFromFormat('!m', $item['month'])->format('F');
        }
        $this->json_return($items);
    }

    private function system_list_all()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1)) {
            $user = User_helper::get_user();
            $method = 'list_all';
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title'] = "Monthly " . ($this->lang->line('LABEL_DIVISION_NAME')) . " Target All List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list_all", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/' . $method);
            $this->json_return($ajax);
        }
        else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items_all()
    {
        $current_records = $this->input->post('total_records');
        if (!$current_records) {
            $current_records = 0;
        }
        $pagesize = $this->input->post('pagesize');
        if (!$pagesize) {
            $pagesize = 100;
        }
        else {
            $pagesize = $pagesize * 2;
        }

        $this->common_query(); // Call Common part of below Query Stack
        // Additional Conditions -STARTS
        $this->db->where('target.status', $this->config->item('system_status_active'));
        $this->db->limit($pagesize, $current_records);
        // Additional Conditions -ENDS
        $items = $this->db->get()->result_array();
        $this->db->flush_cache(); // Flush/Clear current Query Stack

        // Details Table
        $this->db->from($this->config->item('table_bms_target_zone'));
        $this->db->select('target_division_id, SUM(amount_target) AS amount_allocated');
        $this->db->where('status', $this->config->item('system_status_active'));
        $this->db->group_by('target_division_id');
        $results = $this->db->get()->result_array();

        $detail_items = array();
        foreach ($results as $result) {
            $detail_items[$result['target_division_id']] = $result;
        }

        foreach ($items as &$item) {
            if (isset($detail_items[$item['id']])) {
                $item['amount_remaining'] = System_helper::get_string_amount($item['amount_target'] - $detail_items[$item['id']]['amount_allocated']);
                $item['amount_allocated'] = System_helper::get_string_amount($detail_items[$item['id']]['amount_allocated']);
            }
            else {
                $item['amount_remaining'] = '-';
                $item['amount_allocated'] = '-';
            }
            $item['amount_target'] = System_helper::get_string_amount($item['amount_target']);
            $item['month'] = DateTime::createFromFormat('!m', $item['month'])->format('F');
        }
        $this->json_return($items);
    }

    private function system_list_deleted()
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1)) {
            $user = User_helper::get_user();
            $method = 'list_deleted';
            $data = array();
            $data['system_preference_items'] = System_helper::get_preference($user->user_id, $this->controller_url, $method, $this->get_preference_headers($method));
            $data['title'] = "Monthly " . ($this->lang->line('LABEL_HEAD_OFFICE_NAME')) . " Target Deleted List";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list_deleted", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/' . $method);
            $this->json_return($ajax);
        }
        else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items_deleted()
    {
        $user = User_helper::get_user();

        $this->db->from($this->config->item('table_bms_target_division'));
        $this->db->select('id, year, month, SUM(revision_count_delete) AS no_of_delete');

        $this->db->where("user_deleted", $user->user_id);
        $this->db->where("revision_count_delete > ", 0);

        $this->db->group_by("year");
        $this->db->group_by("month");
        $items = $this->db->get()->result_array();

        foreach ($items as &$item) {
            $item['month'] = DateTime::createFromFormat('!m', $item['month'])->format('F');
        }
        $this->json_return($items);
    }

    private function system_edit($id)
    {
        if (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)) {
            if ($id > 0) {
                $item_id = $id;
            }
            else {
                $item_id = $this->input->post('id');
            }
            $data = array();

            $this->common_query(); // Call Common part of below Query Stack
            // Additional Conditions -STARTS
            $this->db->where('target.status', $this->config->item('system_status_active'));
            $this->db->where('target.id', $item_id);
            // Additional Conditions -ENDS
            $data['item'] = $this->db->get()->row_array();
            $this->db->flush_cache(); // Flush/Clear current Query Stack

            if (!$data['item']) {
                System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_ID_NOT_EXIST'));
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_INVALID_TRY');
                $this->json_return($ajax);
            }
            if ($data['item']['status_forward'] == $this->config->item('system_status_forwarded')) {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_FORWARDED_ALREADY');
                $this->json_return($ajax);
            }
            if (!$this->check_my_editable($data['item'])) {
                System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_LOCATION_ERROR'));
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_LOCATION_ERROR');
                $this->json_return($ajax);
            }

            $results = Query_helper::get_info($this->config->item('table_bms_target_zone'), array('zone_id', 'amount_target'), array('target_division_id =' . $item_id, "status ='".$this->config->item('system_status_active')."'"));
            $data['item']['targets'] = array();
            foreach ($results as $result) {
                $data['item']['targets'][$result['zone_id']] = $result['amount_target'];
            }
            $data['item']['target_locations'] = Query_helper::get_info($this->config->item('table_login_setup_location_zones'), array('id', 'name'), array('division_id =' . ($data['item']['division_id']), 'status ="' . $this->config->item('system_status_active') . '"'), 0, 0, array('ordering ASC'));
            $data['item']['label_location'] = $this->lang->line('LABEL_ZONE_NAME');

            $data['title'] = "Assign " . ($data['item']['label_location']) . "-wise Target (ID: " . $item_id . ")";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/edit/' . $item_id);
            $this->json_return($ajax);
        }
        else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save()
    {
        $user = User_helper::get_user();
        $time = time();

        $item_id = $this->input->post('id');
        $item = $this->input->post('item');
        $amount_target = $this->input->post('amount_target');

        //Permission Checking
        if (!(isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        $this->common_query(); // Call Common part of below Query Stack
        // Additional Conditions -STARTS
        $this->db->where('target.status', $this->config->item('system_status_active'));
        $this->db->where('target.id', $item_id);
        // Additional Conditions -ENDS
        $result = $this->db->get()->row_array();
        $this->db->flush_cache(); // Flush/Clear current Query Stack

        if (!$result) {
            System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_ID_NOT_EXIST'));
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_INVALID_TRY');
            $this->json_return($ajax);
        }
        if ($result['status_forward'] == $this->config->item('system_status_forwarded')) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_FORWARDED_ALREADY');
            $this->json_return($ajax);
        }
        if (!$this->check_my_editable($result)) {
            System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_LOCATION_ERROR'));
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_ERROR');
            $this->json_return($ajax);
        }
        //Validation Checking
        if (!$this->check_validation()) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START

        // Main Table Update
        $this->db->set('revision_count', 'revision_count+1', FALSE);
        Query_helper::update($this->config->item('table_bms_target_division'), array(), array("id = " . $item_id)); // UPDATE into Main Table

        $results_old = Query_helper::get_info($this->config->item('table_bms_target_zone'), array('id'), array('target_division_id =' . $item_id, "status ='" . $this->config->item('system_status_active') . "'"));
        if ($results_old) // EDIT
        {
            foreach ($amount_target as $location_id => $amount) {
                $items = array();
                $items['amount_target'] = $amount;
                $items['status'] = $this->config->item('system_status_active');
                $items['user_updated'] = $user->user_id;
                $items['date_updated'] = $time;
                Query_helper::update($this->config->item('table_bms_target_zone'), $items, array('target_division_id =' . $item_id, 'zone_id =' . $location_id, "status ='" . $this->config->item('system_status_active') . "'")); // UPDATE into Details Table
            }
        }
        else{
            foreach ($amount_target as $location_id => $amount) {
                $items = array(
                    'target_division_id' => $item_id,
                    'year' => $item['year'],
                    'month' => $item['month'],
                    'zone_id' => $location_id,
                    'amount_target' => $amount,
                    'revision_count' => 0,
                    'status' => $this->config->item('system_status_active'),
                    'date_created' => $time,
                    'user_created' => $user->user_id
                );
                Query_helper::add($this->config->item('table_bms_target_zone'), $items, FALSE); // INSERT into Details Table
            }
        }
        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE) {
            $ajax['status'] = true;
            $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        }
        else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    private function system_details($id)
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1)) {
            if ($id > 0) {
                $item_id = $id;
            }
            else {
                $item_id = $this->input->post('id');
            }

            $data = $this->get_item_info($item_id);

            $data['title'] = ($this->lang->line('LABEL_ZONE_NAME')) . "-wise Variety Target Details (ID: " . $item_id . ")";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->common_view_location . "/details", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/details/' . $item_id);
            $this->json_return($ajax);
        }
        else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_details_deleted($id)
    {
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1)) {
            if ($id > 0) {
                $item_id = $id;
            }
            else {
                $item_id = $this->input->post('id');
            }
            $result = Query_helper::get_info($this->config->item('table_bms_target_division'), array('year', 'month'), array('id ='.$item_id), 1);
            $params = array(
                'year' => $result['year'],
                'month' => $result['month'],
                'main_table' => $this->config->item('table_bms_target_division'),
                'details_table' => $this->config->item('table_bms_target_zone'),
                'location_table' => $this->config->item('table_login_setup_location_zones'),
                'location_id_field' => 'zone_id',
                'foreign_key' => 'target_division_id'
            );
            $data = Target_helper::get_delete_info($params);

            $data['title'] = ($this->lang->line('LABEL_HEAD_OFFICE_NAME')) . " Deleted Target Details ( " . DateTime::createFromFormat('!m', $result['month'])->format('F') . ", {$result['year']} )";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->common_view_location . "/details_deleted", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/details_deleted/' . $item_id);
            $this->json_return($ajax);
        }
        else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_delete($id)
    {
        if (isset($this->permissions['action3']) && ($this->permissions['action3'] == 1)) {
            if ($id > 0) {
                $item_id = $id;
            }
            else {
                $item_id = $this->input->post('id');
            }

            $data = $this->get_item_info($item_id);
            // Validation: Only Forwarded Targets can be deleted.
            if ($data['item_head']['status_forward'] != $this->config->item('system_status_forwarded')) {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_FORWARDED_DELETE');
                $this->json_return($ajax);
            }
            $data['id'] = $item_id;
            $data['title'] = "Delete " . ($this->lang->line('LABEL_ZONE_NAME')) . "-wise Target (ID: " . $item_id . ")";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/delete", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/delete/' . $item_id);
            $this->json_return($ajax);
        }
        else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save_delete()
    {
        $item_id = $this->input->post('id');
        $item = $this->input->post('item');

        //Permission Checking
        if (!(isset($this->permissions['action3']) && ($this->permissions['action3'] == 1))) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $this->common_query(); // Call Common part of below Query Stack
        // Additional Conditions -STARTS
        $this->db->where('target.status', $this->config->item('system_status_active'));
        $this->db->where('target.id', $item_id);
        // Additional Conditions -ENDS
        $result = $this->db->get()->row_array();
        $this->db->flush_cache(); // Flush/Clear current Query Stack

        // Validation: Only Forwarded Targets can be deleted.
        if ($result['status_forward'] != $this->config->item('system_status_forwarded')) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_FORWARDED_DELETE');
            $this->json_return($ajax);
        }
        if (!$result) {
            System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_ID_NOT_EXIST'));
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_INVALID_TRY');
            $this->json_return($ajax);
        }
        if (!$this->check_my_editable($result)) {
            System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_LOCATION_ERROR'));
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_ERROR');
            $this->json_return($ajax);
        }
        if ($item['status'] != $this->config->item('system_status_delete')) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('LABEL_STATUS_DELETE') . ' field is required.';
            $this->json_return($ajax);
        }
        if (trim($item['remarks_delete']) == '') {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('LABEL_REASON_REMARKS') . ' field is required.';
            $this->json_return($ajax);
        }
        $this->db->trans_start(); //DB Transaction Handle START

        Target_helper::delete_target_tree('division');
        $delete_status = Target_helper::$update_success_status;

        $this->db->trans_complete(); //DB Transaction Handle END

        if (($this->db->trans_status() === TRUE) && (!in_array(FALSE, $delete_status))) {
            $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        }
        else {
            $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    private function system_forward($id)
    {
        if (isset($this->permissions['action7']) && ($this->permissions['action7'] == 1)) {
            if ($id > 0) {
                $item_id = $id;
            }
            else {
                $item_id = $this->input->post('id');
            }

            $data = $this->get_item_info($item_id);
            // Validation
            if ($data['item_head']['status_forward'] == $this->config->item('system_status_forwarded')) {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_FORWARDED_ALREADY');
                $this->json_return($ajax);
            }
            if (!($data['item_head']['amount_allocated'] > 0)) {
                $ajax['status'] = false;
                $ajax['system_message'] = $this->lang->line('MSG_TARGET_ALLOCATION');
                $this->json_return($ajax);
            }
            $data['id'] = $item_id;
            $data['title'] = "Forward " . ($this->lang->line('LABEL_ZONE_NAME')) . "-wise Target (ID: " . $item_id . ")";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/forward", $data, true));
            if ($this->message) {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/forward/' . $item_id);
            $this->json_return($ajax);
        }
        else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save_forward()
    {
        $item_id = $this->input->post('id');
        $item = $this->input->post('item');
        $user = User_helper::get_user();
        $time = time();

        //Permission Checking
        if (!(isset($this->permissions['action7']) && ($this->permissions['action7'] == 1))) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }

        $this->common_query(); // Call Common part of below Query Stack
        // Additional Conditions -STARTS
        $this->db->join($this->config->item('table_bms_target_zone') . ' details', 'details.target_division_id = target.id');
        $this->db->select('SUM(details.amount_target) AS amount_allocated');

        $this->db->where('target.status', $this->config->item('system_status_active'));
        $this->db->where('target.id', $item_id);

        $this->db->group_by('details.target_division_id');
        // Additional Conditions -ENDS
        $result = $this->db->get()->row_array();
        $this->db->flush_cache(); // Flush/Clear current Query Stack

        if (!$result) {
            System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_ID_NOT_EXIST'));
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_INVALID_TRY');
            $this->json_return($ajax);
        }
        if (!$this->check_my_editable($result)) {
            System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_LOCATION_ERROR'));
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_LOCATION_ERROR');
            $this->json_return($ajax);
        }
        if ($item['status_forward'] != $this->config->item('system_status_forwarded')) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('LABEL_STATUS_FORWARD') . ' field is required.';
            $this->json_return($ajax);
        }
        if ($result['status_forward'] == $this->config->item('system_status_forwarded')) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_FORWARDED_ALREADY');
            $this->json_return($ajax);
        }
        if (!($result['amount_allocated'] > 0)) {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_TARGET_ALLOCATION');
            $this->json_return($ajax);
        }

        $this->db->trans_start(); //DB Transaction Handle START

        $item['date_forwarded'] = $time;
        $item['user_forwarded'] = $user->user_id;
        // Main Table UPDATE
        Query_helper::update($this->config->item('table_bms_target_division'), $item, array("id =" . $item_id), FALSE);

        $this->db->trans_complete(); //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE) {
            $ajax['status'] = true;
            $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        }
        else {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    private function get_item_info($item_id) // Common Item Details Info
    {
        $this->common_query(); // Call Common part of below Query Stack
        // Additional Conditions -STARTS
        $this->db->join($this->config->item('table_bms_target_zone') . ' details', "details.target_division_id = target.id AND details.status = '".$this->config->item('system_status_active')."'", 'LEFT');
        $this->db->select('SUM(details.amount_target) AS amount_allocated');

        $this->db->where('target.status', $this->config->item('system_status_active'));
        $this->db->where('target.id', $item_id);

        $this->db->group_by('details.target_division_id');
        // Additional Conditions -ENDS
        $result = $this->db->get()->row_array();
        $this->db->flush_cache(); // Flush/Clear current Query Stack

        if (!$result) {
            System_helper::invalid_try(__FUNCTION__, $item_id, $this->lang->line('MSG_ID_NOT_EXIST'));
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line('MSG_INVALID_TRY');
            $this->json_return($ajax);
        }

        //--------- System User Info ------------
        $user_ids = array();
        $user_ids[$result['user_created']] = $result['user_created'];
        if ($result['parent_user_forwarded'] > 0) {
            $user_ids[$result['parent_user_forwarded']] = $result['parent_user_forwarded'];
        }
        if ($result['user_updated'] > 0) {
            $user_ids[$result['user_updated']] = $result['user_updated'];
        }
        if ($result['user_forwarded'] > 0) {
            $user_ids[$result['user_forwarded']] = $result['user_forwarded'];
        }
        $user_info = System_helper::get_users_info($user_ids);

        //---------------- Basic Info ----------------
        $user_acting = $this->lang->line('LABEL_DIVISION_NAME') . " ";
        $parent_acting = $this->lang->line('LABEL_HEAD_OFFICE_NAME') . " ";
        $user_acting_target = $user_acting . ' target ';
        $parent_acting_target = $parent_acting . ' target ';

        $data = array();
        $data['item_head'] = $result;
        $data['item'][] = array
        (
            'label_1' => 'Target ' . $this->lang->line('LABEL_MONTH'),
            'value_1' => (DateTime::createFromFormat('!m', $result['month'])->format('F')) . ', ' . $result['year'],
            'label_2' => $parent_acting . $this->lang->line('LABEL_AMOUNT_TARGET'),
            'value_2' => System_helper::get_string_amount($result['amount_target'])
        );
        $data['item'][] = array
        (
            'label_1' => $this->lang->line('LABEL_AMOUNT_TARGET') . ' ( In-words )',
            'value_1' => Target_helper::get_string_amount_inword($result['amount_target']),
        );
        if ($result['parent_user_forwarded'] > 0) { // Parent
            $data['item'][] = array
            (
                'label_1' => $parent_acting_target . $this->lang->line('LABEL_FORWARDED_BY'),
                'value_1' => $user_info[$result['parent_user_forwarded']]['name'] . ' ( ' . $user_info[$result['parent_user_forwarded']]['employee_id'] . ' )',
                'label_2' => $parent_acting_target . $this->lang->line('LABEL_DATE_FORWARDED_TIME'),
                'value_2' => System_helper::display_date_time($result['parent_date_forwarded'])
            );
            if (trim($result['parent_remarks_forward']) != '') {
                $data['item'][] = array
                (
                    'label_1' => $parent_acting . $this->lang->line('LABEL_REMARKS'),
                    'value_1' => nl2br($result['parent_remarks_forward'])
                );
            }
        }
        if ($result['user_updated'] > 0) { // Parent
            $data['item'][] = array
            (
                'label_1' => $user_acting_target . $this->lang->line('LABEL_UPDATED_BY'),
                'value_1' => $user_info[$result['user_updated']]['name'] . ' ( ' . $user_info[$result['user_updated']]['employee_id'] . ' )',
                'label_2' => $user_acting_target . $this->lang->line('LABEL_DATE_UPDATED_TIME'),
                'value_2' => System_helper::display_date_time($result['date_updated'])
            );
        }
        if ($result['user_forwarded'] > 0) {
            $data['item'][] = array
            (
                'label_1' => $user_acting_target . $this->lang->line('LABEL_FORWARDED_BY'),
                'value_1' => $user_info[$result['user_forwarded']]['name'] . ' ( ' . $user_info[$result['user_forwarded']]['employee_id'] . ' )',
                'label_2' => $user_acting_target . $this->lang->line('LABEL_DATE_FORWARDED_TIME'),
                'value_2' => System_helper::display_date_time($result['date_forwarded'])
            );
            if (trim($result['remarks_forward']) != '') {
                $data['item'][] = array
                (
                    'label_1' => $user_acting . $this->lang->line('LABEL_REMARKS'),
                    'value_1' => nl2br($result['remarks_forward'])
                );
            }
        }

        //Details Data
        $data['details_title'] = $this->lang->line('LABEL_DIVISION_NAME') . ' Target Distribution';
        $location_id_field = 'zone_id';
        $foreign_key = 'target_division_id';

        $this->db->from($this->config->item('table_bms_target_zone') . ' details');
        $this->db->select("details.{$location_id_field}, details.amount_target");
        $this->db->join($this->config->item('table_login_setup_location_zones') . ' location', "location.id = details.{$location_id_field}", 'INNER');
        $this->db->select('location.name');
        $this->db->where("details.status", $this->config->item('system_status_active'));
        $this->db->where("details.{$foreign_key}", $item_id);
        $data['details'] = $this->db->get()->result_array();

        return $data;
    }

    private function system_get_delete_history()
    {
        $post = $this->input->post();
        $params = array(
            'year' => $post['year'],
            'month' => $post['month'],
            'main_table' => $this->config->item('table_bms_target_division'),
            'details_table' => $this->config->item('table_bms_target_zone'),
            'location_table' => $this->config->item('table_login_setup_location_zones'),
            'location_id_field' => 'zone_id',
            'foreign_key' => 'target_division_id'
        );
        $data = Target_helper::get_delete_info($params);

        $data['title'] = "Deleted Target History ( " . DateTime::createFromFormat('!m', $post['month'])->format('F') . ", {$post['year']} )";
        $data['no_back_button'] = TRUE;
        if ($data) {
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => $post['html_container_id'], "html" => $this->load->view($this->common_view_location . "/details_deleted", $data, true));
            $this->json_return($ajax);
        }
        else {
            $ajax['status'] = false;
            $ajax['system_message'] = 'No market size found.';
            $this->json_return($ajax);
        }
    }

    private function check_validation()
    {
        $amount_target = $this->input->post('amount_target');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[month]', $this->lang->line('LABEL_MONTH'), 'required|trim|is_natural_no_zero');
        $this->form_validation->set_rules('item[year]', $this->lang->line('LABEL_YEAR'), 'required|trim|is_natural_no_zero');
        if ($this->form_validation->run() == FALSE) {
            $this->message = validation_errors();
            return false;
        }
        $no_entry_found = true;
        foreach ($amount_target as $amount) {
            if (trim($amount) > 0) {
                $no_entry_found = false;
            }
        }
        if ($no_entry_found) {
            $this->message = 'Atleast One ' . $this->lang->line('LABEL_AMOUNT_TARGET') . ' need to Save.';
            return false;
        }
        return true;
    }

    private function check_my_editable($item)
    {
        if (($this->locations['division_id'] > 0) && ($this->locations['division_id'] != $item['division_id'])) {
            return false;
        }
        if (($this->locations['zone_id'] > 0) && ($this->locations['zone_id'] != $item['zone_id'])) {
            return false;
        }
        if (($this->locations['territory_id'] > 0) && ($this->locations['territory_id'] != $item['territory_id'])) {
            return false;
        }
        if (($this->locations['district_id'] > 0) && ($this->locations['district_id'] != $item['district_id'])) {
            return false;
        }
        return true;
    }

    private function common_query()
    {
        $user = User_helper::get_user();

        $this->db->start_cache();

        $this->db->from($this->config->item('table_bms_target_division') . ' target');
        $this->db->select('target.*, target.revision_count AS no_of_edit');

        $this->db->join($this->config->item('table_bms_target_hq') . ' parent', 'parent.id = target.target_hq_id AND parent.status_forward="' . $this->config->item('system_status_forwarded') . '"', 'INNER');
        $this->db->select('parent.remarks_forward AS parent_remarks_forward, parent.date_forwarded AS parent_date_forwarded, parent.user_forwarded AS parent_user_forwarded');

        $this->db->join($this->config->item('table_login_setup_location_divisions') . ' division', 'division.id = target.division_id', 'INNER');
        $this->db->select('division.name location');

        $this->db->join($this->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = target.user_created', 'INNER');
        $this->db->select('user_info.name created_by');

        if ($user->user_group != $this->config->item('USER_GROUP_SUPER')) // If not SuperAdmin, Then user can only access own Item.
        {
            if ($this->locations['division_id'] > 0) {
                $this->db->where('target.division_id', $this->locations['division_id']);
            }
        }
        $this->db->where('user_info.revision', 1);
        $this->db->order_by('target.id', 'DESC');
        $this->db->order_by('target.year');
        $this->db->order_by('target.month');

        $this->db->stop_cache();
    }
}
