<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Budget_principal_quantity_confirm extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;

    public function __construct()
    {
        parent::__construct();
        $this->message = "";
        $this->permissions = User_helper::get_permission(get_class());
        $this->controller_url = strtolower(get_class());
        $this->load->helper('budget');
        $this->language_config();
    }

    private function language_config()
    {
        $this->lang->language['LABEL_UNIT_PRICE'] = 'Unit Price (per Kg)';
        $this->lang->language['LABEL_CURRENCY'] = 'Currency';
    }

    public function index($action = "list", $id = 0, $id1 = 0)
    {
        if ($action == "list")
        {
            $this->system_list();
        }
        elseif ($action == "get_items")
        {
            $this->system_get_items();
        }
        elseif ($action == "list_variety")
        {
            $this->system_list_variety($id);
        }
        elseif ($action == "get_items_list_variety")
        {
            $this->system_get_items_list_variety();
        }
        elseif ($action == "add_edit")
        {
            $this->system_add_edit($id, $id1);
        }
        elseif ($action == "save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_list();
        }
    }

    private function get_preference_headers($method)
    {
        $data = array();
        if ($method == 'list')
        {
            $data['fiscal_year_id'] = 1;
            $data['fiscal_year'] = 1;
            $data['number_of_variety_active'] = 1;
            $data['number_of_variety_done'] = 1;
            $data['number_of_variety_due']= 1;
        }
        else if ($method == 'list_variety')
        {
            $data['crop_name'] = 1;
            $data['crop_type_name'] = 1;
            $data['variety_name'] = 1;
            $data['variety_id'] = 1;
            $data['amount_unit_price_taka'] = 1;
            $data['cogs'] = 1;
            $data['cogs_total'] = 1;
            $data['quantity_total'] = 1;
        }
        return $data;
    }

    private function system_list()
    {
        $method = 'list';
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['system_preference_items'] = $this->get_preference_headers($method);
            $data['title'] = "Principal Quantity Confirmation";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items()
    {
        $items = array();

        $this->db->from($this->config->item('table_bms_principal_quantity').' principal_quantity');
        $this->db->select('principal_quantity. fiscal_year_id');
        $this->db->select('COUNT(principal_quantity.variety_id) number_of_variety_done',false);

        $this->db->group_by('principal_quantity.fiscal_year_id');
        $results=$this->db->get()->result_array();
        $principal_quantity=array();
        foreach($results as $result)
        {
            $principal_quantity[$result['fiscal_year_id']]=$result;
        }

        $varieties=Budget_helper::get_crop_type_varieties();
        $fiscal_years = Budget_helper::get_fiscal_years();
        foreach ($fiscal_years as $fy)
        {
            $data = array();
            $data['fiscal_year_id'] = $fy['id'];
            $data['fiscal_year'] = $fy['text'];
            $data['number_of_variety_active'] = sizeof($varieties);
            $data['number_of_variety_done'] = 0;
            if(isset($principal_quantity[$fy['id']]))
            {
                $data['number_of_variety_done'] = $principal_quantity[$fy['id']]['number_of_variety_done'];
            }
            $data['number_of_variety_due']= $data['number_of_variety_active']-$data['number_of_variety_done'];
            $items[] = $data;
        }
        $this->json_return($items);
    }

    private function system_list_variety($fiscal_year_id = 0)
    {
        $method = 'list_variety';
        if ((isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)) || (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
        {
            if (!($fiscal_year_id > 0))
            {
                $fiscal_year_id = $this->input->post('fiscal_year_id');
            }
            if (!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
            {
                System_helper::invalid_try(__FUNCTION__, $fiscal_year_id, 'Invalid Fiscal year');
                $ajax['status'] = false;
                $ajax['system_message'] = 'Invalid Fiscal Year';
                $this->json_return($ajax);
            }

            $data['fiscal_year'] = Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'), '*', array('id =' . $fiscal_year_id), 1);

            $data['system_preference_items'] = $this->get_preference_headers($method);
            $data['title'] = 'Variety Principal Quantity Setup for (' . $data['fiscal_year']['name'] . ') Fiscal Year';
            $data['options']['fiscal_year_id'] = $fiscal_year_id;
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/list_variety", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/list_variety/' . $fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items_list_variety()
    {
        $items = array();
        $fiscal_year_id = $this->input->post('fiscal_year_id');
        $this->db->from($this->config->item('table_bms_principal_quantity').' principal_quantity');
        $this->db->select('principal_quantity. *');
        $this->db->where('principal_quantity.fiscal_year_id',$fiscal_year_id);
        $results=$this->db->get()->result_array();
        $principal_quantity=array();
        foreach($results as $result)
        {
            $principal_quantity[$result['variety_id']]=$result;
        }
        $results = Budget_helper::get_crop_type_varieties();
        foreach ($results as $result)
        {
            $item=array();
            $item['crop_name']=$result['crop_name'];
            $item['crop_type_name']=$result['crop_type_name'];
            $item['variety_name']=$result['variety_name'];
            $item['variety_id']=$result['variety_id'];
            if(isset($principal_quantity[$result['variety_id']]))
            {
                $item['amount_unit_price_taka'] = $principal_quantity[$result['variety_id']]['amount_unit_price_taka'];
                $item['cogs'] = $principal_quantity[$result['variety_id']]['cogs'];
                $item['cogs_total'] = $principal_quantity[$result['variety_id']]['cogs_total'];
                $item['quantity_total'] = $principal_quantity[$result['variety_id']]['quantity_total'];
            }
            else
            {
                $item['amount_unit_price_taka'] = '';
                $item['cogs'] = '';
                $item['cogs_total'] = '';
                $item['quantity_total'] = '';
            }

            $items[]=$item;
        }
        $this->json_return($items);
    }

    private function system_add_edit($fiscal_year_id = 0, $variety_id = 0)
    {
        if ((isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)) || (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1)))
        {
            if (!($fiscal_year_id > 0))
            {
                $fiscal_year_id = $this->input->post('fiscal_year_id');
            }
            if (!($variety_id > 0))
            {
                $variety_id = $this->input->post('variety_id');
            }

            $data = array();

            $data['message_warning_config'] = array(); //for configuration not set
            $data['message_warning_changes'] = array(); //for edit configuration change message


            $this->db->from($this->config->item('table_login_basic_setup_fiscal_year') . ' fiscal_year');
            $this->db->select('fiscal_year.name fiscal_year_name');

            $this->db->join($this->config->item('table_bms_setup_budget_config') . ' budget_config', 'budget_config.fiscal_year_id = fiscal_year.id', 'INNER');
            $this->db->select('budget_config.*');

            $this->db->where('fiscal_year.id', $fiscal_year_id);
            $fiscal_year_info = $this->db->get()->row_array();

            $data['item'] = array();
            $data['item']['fiscal_year_id'] = $fiscal_year_id;
            $data['item']['fiscal_year_name'] = $fiscal_year_info['fiscal_year_name'];

            $results = Budget_helper::get_crop_type_varieties(array(), array(), array($variety_id));
            $data['item']['crop_name'] = $results[0]['crop_name'];
            $data['item']['crop_type_name'] = $results[0]['crop_type_name'];
            $data['item']['variety_name'] = $results[0]['variety_name'];
            $data['item']['variety_id'] = $variety_id;

            $data['item']['percentage_direct_cost'] = 0;
            if ($fiscal_year_info['revision_count_percentage_direct_cost'] > 0)
            {
                $results = json_decode($fiscal_year_info['percentage_direct_cost'], true);
                foreach ($results as $value)
                {
                    $data['item']['percentage_direct_cost'] += $value;
                }
            }
            else
            {
                $data['message_warning_config'][] = '<b>Direct Cost Percentage</b> - NOT Configured for this Fiscal Year';
            }

            $data['item']['percentage_packing_cost'] = 0;
            if ($fiscal_year_info['revision_count_percentage_packing_cost'] > 0)
            {
                $results = json_decode($fiscal_year_info['percentage_packing_cost'], true);
                foreach ($results as $value)
                {
                    $data['item']['percentage_packing_cost'] += $value;
                }
            }
            else
            {
                $data['message_warning_config'][] = '<b>Packing Cost Percentage</b> - NOT Configured for this Fiscal Year';
            }
            $currency_rates = array();
            if ($fiscal_year_info['revision_count_percentage_packing_cost'] > 0)
            {
                $currency_rates = json_decode($fiscal_year_info['amount_currency_rate'], true);
            }
            else
            {
                $data['message_warning_config'][] = '<b>Currency Rate</b> - NOT Configured for this Fiscal Year';
            }
            $data['currencies'] = array();
            $results = Query_helper::get_info($this->config->item('table_login_setup_currency'), array('id', 'name'), array('status !="' . $this->config->item('system_status_delete') . '"'));

            foreach ($results as $currency)
            {
                $data['currencies'][$currency['id']] = array(
                    'name' => $currency['name'],
                    'amount_currency_rate' => (isset($currency_rates[$currency['id']])) ? $currency_rates[$currency['id']] : 0
                );
            }
            $data['item']['quantity_total_hom_target']='';
            $result = Query_helper::get_info($this->config->item('table_bms_hom_budget_target_hom'), 'quantity_principal_quantity_confirm', array('fiscal_year_id=' . $fiscal_year_id, 'variety_id =' . $variety_id), 1);
            if($result)
            {
                $data['item']['quantity_total_hom_target'] = System_helper::get_string_kg($result['quantity_principal_quantity_confirm']); //calculate from National target task
            }

            //$data['item']['cogs'];//will auto calculate
            //$data['item']['cogs_total'];//will auto calculate

            $results = Query_helper::get_info($this->config->item('table_bms_principal_quantity_principal'), '*', array('fiscal_year_id=' . $fiscal_year_id, 'variety_id =' . $variety_id,'revision = 1'));
            $old_item_principles = array();
            $principle_ids_old = array();
            $change_dc=false;
            $change_packing=false;
            foreach ($results as $result)
            {
                $old_item_principles[$result['principal_id']] = $result;
                $principle_ids_old[$result['principal_id']] = $result['principal_id']; // Storing Principal Id's for Comparison
                if($result['percentage_direct_cost'] != $data['item']['percentage_direct_cost'])
                {
                    $msg=$result['percentage_direct_cost'].'--'.$data['item']['percentage_direct_cost'];
                    $msg.='--'.strlen($result['percentage_direct_cost']).'--'.strlen($data['item']['percentage_direct_cost']);
                    $data['message_warning_changes'][] =$msg;
                    $change_dc=true;
                }
                if($result['percentage_packing_cost'] != $data['item']['percentage_packing_cost'])
                {
                    $change_packing=true;
                }
                //currency rate validation
                //no need to check isset.
                if($result['amount_currency_rate']!=$data['currencies'][$result['currency_id']]['amount_currency_rate'])
                {
                    $data['message_warning_changes'][] = '<b>Currency('.$data['currencies'][$result['currency_id']]['name'].')Rate</b> has been Changed.';
                }
            }
            if($change_dc)
            {
                $data['message_warning_changes'][] = '<b>Direct Cost Percentage</b> has been Changed.';
            }
            if($change_packing)
            {
                $data['message_warning_changes'][] = '<b>Packing Cost Percentage</b> has been Changed. ';
            }

            // Variety Principles
            $this->db->from($this->config->item('table_login_setup_classification_variety_principals') . ' variety_principals');

            $this->db->join($this->config->item('table_login_basic_setup_principal') . ' principal', 'principal.id = variety_principals.principal_id', 'INNER');
            $this->db->select('principal.id, principal.name');

            $this->db->where('variety_principals.variety_id', $variety_id);
            $this->db->where('variety_principals.revision', 1);
            $this->db->where('principal.status', $this->config->item('system_status_active'));
            $results = $this->db->get()->result_array();
            if (sizeof($results) == 0)
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Please Assign a principle';
                $this->json_return($ajax);
            }
            $change_principal=false;
            if(sizeof($results) !=sizeof($principle_ids_old))
            {
                $change_principal=true;
                //$data['message_warning_changes'][] = '<b>Principle</b> Size not equal.';
            }
            $data['item']['principals'] = array();
            foreach ($results as $result)
            {
                $info = array();
                $info['id'] = $result['id'];
                $info['name'] = $result['name'];
                for ($i = 1; $i < 13; $i++)
                {
                    $info['quantity_' . $i] = isset($old_item_principles[$result['id']]['quantity_' . $i]) ? $old_item_principles[$result['id']]['quantity_' . $i] : 0;
                    $info['amount_unit_price_currency'] = isset($old_item_principles[$result['id']]['amount_unit_price_currency']) ? $old_item_principles[$result['id']]['amount_unit_price_currency'] : 0;
                    $info['currency_id'] = isset($old_item_principles[$result['id']]['currency_id']) ? $old_item_principles[$result['id']]['currency_id'] : 0;
                }
                $data['item']['principals'][] = $info;
                if(!(in_array($result['id'],$principle_ids_old)))
                {
                    $change_principal=true;
                    //$data['message_warning_changes'][] = '<b>Principle</b> not found';
                }
            }
            if($change_principal)
            {
                $data['message_warning_changes'][] = '<b>Principle</b> added/removed.';
            }

            $data['title'] = "Edit Principal Quantity Confirmation";
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/add_edit/' . $fiscal_year_id . '/' . $variety_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_save()
    {
        $user = User_helper::get_user();
        $time = time();
        $item_head = $this->input->post('item');
        $items = $this->input->post('items');
        if (!((isset($this->permissions['action1']) && ($this->permissions['action1'] == 1)) || (isset($this->permissions['action2']) && ($this->permissions['action2'] == 1))))
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if (!Budget_helper::check_validation_fiscal_year($item_head['fiscal_year_id']))
        {
            System_helper::invalid_try(__FUNCTION__, $item_head['fiscal_year_id'], 'Invalid Fiscal year');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Fiscal Year';
            $this->json_return($ajax);
        }
        //Validation Checking
        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }
        $fiscal_year_info = Query_helper::get_info($this->config->item('table_bms_setup_budget_config'), '*', array('fiscal_year_id=' . $item_head['fiscal_year_id']), 1);


        $percentage_direct_cost = 0;
        if ($fiscal_year_info['revision_count_percentage_direct_cost'] > 0)
        {
            $results = json_decode($fiscal_year_info['percentage_direct_cost'], true);
            foreach ($results as $value)
            {
                $percentage_direct_cost += $value;
            }
        }

        $percentage_packing_cost = 0;
        if ($fiscal_year_info['revision_count_percentage_packing_cost'] > 0)
        {
            $results = json_decode($fiscal_year_info['percentage_packing_cost'], true);
            foreach ($results as $value)
            {
                $percentage_packing_cost += $value;
            }
        }
        $currency_rates = array();
        if ($fiscal_year_info['revision_count_percentage_packing_cost'] > 0)
        {
            $currency_rates = json_decode($fiscal_year_info['amount_currency_rate'], true);
        }
        //$item_head['amount_unit_price_taka']; calculate bellow
        $item_head['amount_unit_price_taka'] = 0;
        $item_head['percentage_direct_cost'] = $percentage_direct_cost;
        $item_head['percentage_packing_cost'] = $percentage_packing_cost;
        $item_head['cogs'] = 0; //calculate bellow
        $item_head['cogs_total'] = 0; //calculate bellow//441
        $item_head['quantity_total'] = 0; //428-432
        $item_head['quantity_1'] = 0;
        $item_head['quantity_2'] = 0;
        $item_head['quantity_3'] = 0;
        $item_head['quantity_4'] = 0;
        $item_head['quantity_5'] = 0;
        $item_head['quantity_6'] = 0;
        $item_head['quantity_7'] = 0;
        $item_head['quantity_8'] = 0;
        $item_head['quantity_9'] = 0;
        $item_head['quantity_10'] = 0;
        $item_head['quantity_11'] = 0;
        $item_head['quantity_12'] = 0;
        //$item_head['date_created']; calculate bellow
        //$item_head['user_created']; calculate bellow
        //$item_head['date_updated']; calculate bellow
        //$item_head['user_updated']; calculate bellow

        $item_head_old = Query_helper::get_info($this->config->item('table_bms_principal_quantity'), '*', array('fiscal_year_id=' . $item_head['fiscal_year_id'], 'variety_id =' . $item_head['variety_id']), 1);

        $this->db->trans_start(); //DB Transaction Handle START
        //revision increase
        $revision_history_data = array();
        $revision_history_data['date_updated'] = $time;
        $revision_history_data['user_updated'] = $user->user_id;
        Query_helper::update($this->config->item('table_bms_principal_quantity_principal'), $revision_history_data, array('revision=1', 'fiscal_year_id=' . $item_head['fiscal_year_id'], 'variety_id=' . $item_head['variety_id']), false);
        $this->db->set('revision', 'revision+1', false);
        Query_helper::update($this->config->item('table_bms_principal_quantity_principal'), array(), array('fiscal_year_id =' . $item_head['fiscal_year_id'], 'variety_id =' . $item_head['variety_id']), false);
        //revision end
        foreach ($items as $principal_id => $principal_info)
        {
            $data = array();
            $data['fiscal_year_id'] = $item_head['fiscal_year_id'];
            $data['variety_id'] = $item_head['variety_id'];
            $data['principal_id'] = $principal_id;
            $data['revision'] = 1;
            $data['amount_unit_price_currency'] = $principal_info['amount_unit_price_currency'];
            $data['currency_id'] = $principal_info['currency_id'];
            $data['amount_currency_rate'] = (isset($currency_rates[$principal_info['currency_id']])) ? $currency_rates[$principal_info['currency_id']] : 0;
            $data['amount_unit_price_taka'] = $data['amount_unit_price_currency'] * $data['amount_currency_rate'];

            $data['percentage_direct_cost'] = $percentage_direct_cost;
            $data['percentage_packing_cost'] = $percentage_packing_cost;
            //$data['cogs'];//later--439
            //$data['cogs_total'];//later--440
            $data['quantity_total'] = 0;
            foreach ($principal_info['quantities'] as $month_index => $quantity)
            {
                if ($quantity > 0)
                {
                    $data['quantity_' . $month_index] = $quantity;
                    $item_head['quantity_' . $month_index] += $quantity;

                    $data['quantity_total'] += $quantity;
                    $item_head['quantity_total'] += $quantity;
                }
                else
                {
                    $data['quantity_' . $month_index] = 0;
                }
            }
            $data['cogs'] = $data['amount_unit_price_taka'] + ($data['amount_unit_price_taka'] * $percentage_direct_cost / 100) + ($data['amount_unit_price_taka'] * $percentage_packing_cost / 100);
            $data['cogs_total'] = $data['cogs'] * $data['quantity_total'];
            $item_head['cogs_total'] += $data['cogs_total'];
            $data['date_created'] = $time;
            $data['user_created'] = $user->user_id;
            Query_helper::add($this->config->item('table_bms_principal_quantity_principal'), $data, false);

        }
        if ($item_head['quantity_total'] > 0)
        {
            $item_head['cogs'] = $item_head['cogs_total'] / $item_head['quantity_total'];
            $item_head['amount_unit_price_taka'] = (($item_head['cogs']*100)/(100+$percentage_direct_cost+$percentage_packing_cost));
        }
        if ($item_head_old)
        {
            $item_head['date_updated'] = $time;
            $item_head['user_updated'] = $user->user_id;
            Query_helper::update($this->config->item('table_bms_principal_quantity'), $item_head, array('id =' . $item_head_old['id']));
        }
        else
        {
            $item_head['date_created'] = $time;
            $item_head['user_created'] = $user->user_id;
            Query_helper::add($this->config->item('table_bms_principal_quantity'), $item_head);
        }

        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
            $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list_variety($item_head['fiscal_year_id']);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    /*
    private function calculate_principle_cogs($unit_price, $rate, $direct_cost_percentage_array, $packing_cost_percentage_array)
    {
        $total_direct_cost_percentage = 0.0;
        $total_packing_cost_percentage = 0.0;

        foreach ($direct_cost_percentage_array as $value)
        {
            $total_direct_cost_percentage += $value;
        }
        foreach ($packing_cost_percentage_array as $value)
        {
            $total_packing_cost_percentage += $value;
        }

        $A = $unit_price * $rate;
        $B = $A * $total_direct_cost_percentage;
        $C = $A * $total_packing_cost_percentage;

        return ($A + $B + $C);
    }
    */

    private function check_validation()
    {
        $items = $this->input->post('items');
        foreach ($items as $principal_item)
        {
            if (!($principal_item['amount_unit_price_currency'] > 0)) // Unit Price Validation
            {
                $this->message = $this->lang->line('LABEL_UNIT_PRICE') . ' fields cannot be Empty';
                return false;
            }
            if (!($principal_item['currency_id'] > 0)) // Currency DropDown Validation
            {
                $this->message = $this->lang->line('LABEL_CURRENCY') . ' fields must be Selected';
                return false;
            }
        }
        return true;
    }
}
