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
            /*$data['fiscal_year_id'] = 1;
            $data['fiscal_year'] = 1;*/
        }
        else if ($method == 'list_variety')
        {
            /*$data['fiscal_year_id'] = 1;
            $data['crop_name'] = 1;
            $data['crop_type_name'] = 1;
            $data['variety_name'] = 1;
            $data['variety_id'] = 1;*/
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
        $fiscal_years = Budget_helper::get_fiscal_years();
        foreach ($fiscal_years as $fy)
        {
            $data = array();
            $data['fiscal_year_id'] = $fy['id'];
            $data['fiscal_year'] = $fy['text'];
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
        $fiscal_year_id = $this->input->post('fiscal_year_id');
        if (!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
        {
            System_helper::invalid_try(__FUNCTION__, $fiscal_year_id, 'Invalid Fiscal year');
            $ajax['status'] = false;
            $ajax['system_message'] = 'Invalid Fiscal Year';
            $this->json_return($ajax);
        }

        $items = Budget_helper::get_crop_type_varieties();
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

            $data['message_warning_config']=array();//for configuration not set
            $data['message_warning_changes']=array();//for edit configuration change message




            $this->db->from($this->config->item('table_login_basic_setup_fiscal_year') . ' fiscal_year');
            $this->db->select('fiscal_year.name fiscal_year_name');

            $this->db->join($this->config->item('table_bms_setup_budget_config') . ' budget_config', 'budget_config.fiscal_year_id = fiscal_year.id', 'LEFT');
            $this->db->select('budget_config.*');

            $this->db->where('fiscal_year.id', $fiscal_year_id);
            $fiscal_year_info = $this->db->get()->row_array();

            $data['item']=array();
            $data['item']['fiscal_year_id'] = $fiscal_year_id;
            $data['item']['fiscal_year_name'] = $fiscal_year_info['fiscal_year_name'];

            $results = Budget_helper::get_crop_type_varieties(array(), array(), array($variety_id));
            $data['item']['crop_name'] = $results[0]['crop_name'];
            $data['item']['crop_type_name'] = $results[0]['crop_type_name'];
            $data['item']['variety_name'] = $results[0]['variety_name'];
            $data['item']['variety_id'] = $variety_id;

            $data['item']['percentage_direct_cost'] = 0;
            if ($fiscal_year_info['revision_count_percentage_direct_cost']>0)
            {
                $results = json_decode($fiscal_year_info['percentage_direct_cost'], true);
                foreach ($results as $value)
                {
                    $data['item']['percentage_direct_cost'] += $value;
                }
            }
            else
            {
                $data['message_warning_config'][]='<b>Direct Cost Percentage</b> - NOT Configured for this Fiscal Year';
            }

            $data['item']['percentage_packing_cost'] = 0;
            if ($fiscal_year_info['revision_count_percentage_packing_cost']>0)
            {
                $results = json_decode($fiscal_year_info['percentage_packing_cost'], true);
                foreach ($results as $value)
                {
                    $data['item']['percentage_packing_cost'] += $value;
                }
            }
            else
            {
                $data['message_warning_config'][]='<b>Packing Cost Percentage</b> - NOT Configured for this Fiscal Year';
            }
            $currency_rates=array();
            if ($fiscal_year_info['revision_count_percentage_packing_cost']>0)
            {
                $currency_rates = json_decode($fiscal_year_info['amount_currency_rate'], true);
            }
            else
            {
                $data['message_warning_config'][]='<b>Currency Rate</b> - NOT Configured for this Fiscal Year';
            }
            $data['currencies'] =array();
            $results=Query_helper::get_info($this->config->item('table_login_setup_currency'), array('id','name'), array('status !="' . $this->config->item('system_status_delete') . '"'));

            foreach($results as $currency)
            {
                $data['currencies'][$currency['id']] = array(
                    'name' => $currency['name'],
                    'amount_currency_rate' => (isset($currency_rates[$currency['id']])) ? $currency_rates[$currency['id']] : 0
                );
            }
            $data['item']['quantity_total_hom_target']='Need to calculate';//calculate from National target task
            //$data['item']['cogs'];//will auto calculate
            //$data['item']['cogs_total'];//will auto calculate

            //get main value
            $old_item=array();
            //check validation
            $old_item_principles=array();

            //get values of previous principles value
            //check validations
            //$data['message_warning_changes'][]='<b>Currency Rate</b> has been Changed.';
            //$data['message_warning_changes'][]='<b>Direct Cost Percentage</b> has been Changed.';
            //$data['message_warning_changes'][]='<b>Packing Cost Percentage</b> has been Changed.';
            //$data['message_warning_changes'][]='<b>Principle</b> has been added/removed.';

            // Variety Principles
            $this->db->from($this->config->item('table_login_setup_classification_variety_principals') . ' variety_principals');

            $this->db->join($this->config->item('table_login_basic_setup_principal') . ' principal', 'principal.id = variety_principals.principal_id', 'INNER');
            $this->db->select('principal.id, principal.name');

            $this->db->where('variety_principals.variety_id', $variety_id);
            $this->db->where('variety_principals.revision', 1);
            $this->db->where('principal.status', $this->config->item('system_status_active'));
            $results=$this->db->get()->result_array();
            if(sizeof($results)==0)
            {
                $ajax['status'] = false;
                $ajax['system_message'] = 'Please Assign a principle';
                $this->json_return($ajax);
            }
            $data['item']['principals'] =array();
            foreach($results as $result)
            {
                $info=array();
                $info['id']=$result['id'];
                $info['name']=$result['name'];
                for($i=1;$i<13;$i++)
                {
                    $info['quantity_'.$i]=isset($old_item_principles[$result['id']]['quantity_'.$i])?$old_item_principles[$result['id']]['quantity_'.$i]:1;
                    $info['amount_unit_price_currency']=isset($old_item_principles[$result['id']]['amount_unit_price_currency'])?$old_item_principles[$result['id']]['amount_unit_price_currency']:1;
                    $info['currency_id']=isset($old_item_principles[$result['id']]['currency_id'])?$old_item_principles[$result['id']]['currency_id']:0;
                }
                $data['item']['principals'][]=$info;

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
echo '<pre>';
print_r($this->input->post());
echo '</pre>';
        die();
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

        $fiscal_year_config = Query_helper::get_info($this->config->item('table_bms_setup_budget_config'), 'amount_currency_rate, percentage_direct_cost, percentage_packing_cost', array('fiscal_year_id=' . $item_head['fiscal_year_id']), 1);
        $fiscal_year_currencies = json_decode($fiscal_year_config['amount_currency_rate'], true);

        // Total direct_cost_percentage
        $total_direct_cost_percentage = 0.0;
        $fiscal_year_direct_cost_percentage = json_decode($fiscal_year_config['amount_direct_cost_percentage'], true);
        foreach ($fiscal_year_direct_cost_percentage as $value)
        {
            $total_direct_cost_percentage += $value;
        }

        // Total packing_cost_percentage
        $total_packing_cost_percentage = 0.0;
        $fiscal_year_packing_cost_percentage = json_decode($fiscal_year_config['percentage_packing_cost'], true);
        foreach ($fiscal_year_packing_cost_percentage as $value)
        {
            $total_packing_cost_percentage += $value;
        }

        $item_head['direct_cost_percentage_total'] = $total_direct_cost_percentage;
        $item_head['packing_cost_percentage_total'] = $total_packing_cost_percentage;

        $item_head['quantity_grand_total'] = 0.0;
        $item_head['unit_price'] = 0.0;
        $item_head['cogs_grand_total'] = 0.0;
        $item_head['cogs_average'] = 0.0;

        $item_details = array();

        foreach ($items as $principal_id => $principal_item)
        {
            $row = array();

            // Details Table Data
            $row['fiscal_year_id'] = $item_head['fiscal_year_id'];
            $row['variety_id'] = $item_head['variety_id'];
            $row['direct_cost_percentage_total'] = $total_direct_cost_percentage;
            $row['packing_cost_percentage_total'] = $total_packing_cost_percentage;
            $row['currency_id'] = $principal_item['currency_id'];
            $row['principal_id'] = $principal_id;

            // Monthly total quantity
            $principal_quantity_total = 0.0;
            foreach ($principal_item['quantities'] as $month_number => $quantity)
            {
                if ($quantity > 0)
                {
                    $row['quantity_' . $month_number] = $quantity;
                    $principal_quantity_total += $quantity;
                }
            }

            $row['quantity_total'] = $principal_quantity_total; // Sub Total Quantity
            $row['unit_price_currency'] = $principal_item['unit_price'];
            $row['currency_rate'] = $fiscal_year_currencies[$principal_item['currency_id']];
            $row['unit_price_taka'] = $principal_item['unit_price'] * $fiscal_year_currencies[$principal_item['currency_id']];

            // Principal COGS Calculation
            $A = $principal_item['unit_price'] * $fiscal_year_currencies[$principal_item['currency_id']];
            $B = $A * ($total_direct_cost_percentage / 100);
            $C = $A * ($total_packing_cost_percentage / 100);

            $row['cogs'] = ($A + $B + $C);
            $row['cogs_total'] = ($A + $B + $C) * $principal_quantity_total;
            $row['revision'] = 1;
            $row['date_created'] = $time;
            $row['user_created'] = $user->user_id;

            $item_details[] = $row; //Assign into Final Array

            // Main Table Data
            $item_head['quantity_grand_total'] += $principal_quantity_total; // Grand Total Quantity -> Main Table
            $item_head['cogs_grand_total'] += $row['cogs_total']; // Grand Total COGS -> Main Table
            $item_head['unit_price'] += $A;
        }

        // Main Table Data
        $item_head['unit_price'] = $item_head['unit_price'] / sizeof($items); // Main Table -> unit_price_average = ( $A / No. of Principals )
        $item_head['cogs_average'] = ($item_head['cogs_grand_total'] / $item_head['quantity_grand_total']);
        $item_head['date_created'] = $time;
        $item_head['user_created'] = $user->user_id;

        /*echo '<pre>';
        print_r($item_head); // Main Table Data for Insert
        print_r($item_details); // Details Table Data for Insert
        echo '</pre>'; die();*/

        $this->db->trans_start(); //DB Transaction Handle START

        $data = Query_helper::get_info($this->config->item('table_bms_principal_quantity'), '*', array('fiscal_year_id =' . $item_head['fiscal_year_id'], 'variety_id =' . $item_head['variety_id']));
        if ($data) // EDIT Main Table
        {

        }
        else // ADD Main Table
        {
            Query_helper::add($this->config->item('table_bms_principal_quantity'), $item_head, false);
        }

        // Principal Details revision - UPDATE
        $this->db->set('revision', 'revision+1', false);
        Query_helper::update($this->config->item('table_bms_principal_quantity_principal'), array(), array('fiscal_year_id =' . $item_head['fiscal_year_id'], 'variety_id =' . $item_head['variety_id']), false);
        // Principal Details - INSERT
        foreach ($item_details as $principal_data)
        {
            Query_helper::add($this->config->item('table_bms_principal_quantity_principal'), $principal_data, false);
        }

        $this->db->trans_complete(); //DB Transaction Handle END

        if ($this->db->trans_status() === TRUE)
        {
            $this->message = $this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

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

    private function check_validation()
    {
        $items = $this->input->post('items');
        foreach ($items as $principal_item)
        {
            if (!($principal_item['unit_price']) > 0) // Unit Price Validation
            {
                $this->message = $this->lang->line('LABEL_UNIT_PRICE') . ' fields cannot be Empty';
                return false;
            }
            if (!($principal_item['currency_id']) > 0) // Currency DropDown Validation
            {
                $this->message = $this->lang->line('LABEL_CURRENCY') . ' fields must be Selected';
                return false;
            }
        }
        return true;
    }
}
