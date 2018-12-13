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
        elseif ($action == "variety_list")
        {
            $this->system_variety_list($id);
        }
        elseif ($action == "get_items_variety_list")
        {
            $this->system_get_items_variety_list();
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
        else if ($method == 'variety_list')
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

    private function system_variety_list($fiscal_year_id = 0)
    {
        $method = 'variety_list';
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
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/variety_list", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/variety_list/' . $fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items_variety_list()
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

            $this->db->from($this->config->item('table_login_basic_setup_fiscal_year') . ' fiscal_year');
            $this->db->select('fiscal_year.name AS fiscal_year_name');

            $this->db->join($this->config->item('table_bms_setup_budget_config') . ' budget_config', 'budget_config.fiscal_year_id = fiscal_year.id', 'LEFT');
            $this->db->select('budget_config.*');

            $this->db->where('fiscal_year.id', $fiscal_year_id);
            $data['fiscal_year_data'] = $this->db->get()->row_array();

            // --------Fiscal year Config Checking-------
            $data['config_warning'] = array('status' => false, 'messages' => array());
            if (trim($data['fiscal_year_data']['amount_currency_rate']) == '') // Currency Rate - is NOT Configured
            {
                $data['config_warning']['status'] = true;
                $data['config_warning']['messages'][] = '<b>Currency Rate</b> - NOT Configured for this Fiscal Year';
            }
            if (trim($data['fiscal_year_data']['amount_direct_cost_percentage']) == '') // DC Percentage - is NOT Configured
            {
                $data['config_warning']['status'] = true;
                $data['config_warning']['messages'][] = '<b>Direct Cost Percentage</b> - NOT Configured for this Fiscal Year';
            }
            if (trim($data['fiscal_year_data']['amount_packing_cost_percentage']) == '') // Packing Percentage - is NOT Configured
            {
                $data['config_warning']['status'] = true;
                $data['config_warning']['messages'][] = '<b>Packing Cost Percentage</b> - NOT Configured for this Fiscal Year';
            }
            //-------------------------------------------


            $items = Budget_helper::get_crop_type_varieties(array(), array(), array($variety_id));
            $data['item'] = $items[0];

            $data['item']['total_direct_cost_percentage'] = 0;
            if ($data['fiscal_year_data']['amount_direct_cost_percentage'])
            {
                $sum = 0;
                $direct_cost_items = json_decode($data['fiscal_year_data']['amount_direct_cost_percentage'], true);
                foreach ($direct_cost_items as $value)
                {
                    $sum += $value;
                }
                $data['item']['total_direct_cost_percentage'] = $sum;
            }

            $data['item']['total_packing_cost_percentage'] = 0;
            if ($data['fiscal_year_data']['amount_packing_cost_percentage'])
            {
                $sum = 0;
                $packing_cost_items = json_decode($data['fiscal_year_data']['amount_packing_cost_percentage'], true);
                foreach ($packing_cost_items as $value)
                {
                    $sum += $value;
                }
                $data['item']['total_packing_cost_percentage'] = $sum;
            }

            $currency_items = json_decode($data['fiscal_year_data']['amount_currency_rate'], true); // From 'bms_setup_budget_config' table

            $data['currencies'] = array();
            $currencies = Query_helper::get_info($this->config->item('table_login_setup_currency'), '*', array('status !="' . $this->config->item('system_status_delete') . '"'));
            foreach ($currencies as $currency)
            {
                $data['currencies'][$currency['id']] = array(
                    'name' => $currency['name'],
                    'symbol' => $currency['symbol'],
                    'currency_rate' => (isset($currency_items[$currency['id']])) ? $currency_items[$currency['id']] : 0,
                    'description' => $currency['description']
                );
            }

            // Variety Principles
            $this->db->from($this->config->item('table_login_setup_classification_variety_principals') . ' variety_principals');

            $this->db->join($this->config->item('table_login_basic_setup_principal') . ' principal', 'principal.id = variety_principals.principal_id', 'LEFT');
            $this->db->select('principal.id, principal.name');

            $this->db->where('variety_principals.variety_id', $variety_id);
            $this->db->where('variety_principals.revision', 1);
            $this->db->where('principal.status', $this->config->item('system_status_active'));
            $data['item']['principals'] = $this->db->get()->result_array();


            // --------Currency Rate, DC percentage, PC percentage, No. of Principle Checking-------
            $data['changes_warning'] = array('status' => false, 'messages' => array());

            // ADD, EDIT Data Prepare
            $result = Query_helper::get_info($this->config->item('table_bms_principal_quantity'), '*', array('fiscal_year_id =' . $fiscal_year_id, 'variety_id =' . $variety_id));
            if ($result) // EDIT
            {
                $principal_data = Query_helper::get_info($this->config->item('table_bms_principal_quantity_principal'), '*', array('fiscal_year_id =' . $fiscal_year_id, 'variety_id =' . $variety_id, 'revision =1'));

//                echo '<pre>';
//                print_r($principal_data);
//                print_r($data['item']);
//                var_dump($principal_data);
//                var_dump($data['item']);
//                echo '</pre>'; die();

                foreach ($principal_data as $row)
                {
                    // --------Currency Rate, DC percentage, PC percentage, No. of Principle Checking-------
                    /*if ($data['currencies'][$row['currency_id']]['currency_rate'] != $row['currency_rate'])
                    {
                        $data['changes_warning']['status'] = true;
                        $data['changes_warning']['messages'][0] = '<b>Currency Rate</b> has been Changed.';
                    }*/

                    if ($data['item']['total_direct_cost_percentage'] != $row['direct_cost_percentage_total'])
                    {
                        $data['changes_warning']['status'] = true;
                        $data['changes_warning']['messages'][1] =    $data['item']['total_direct_cost_percentage'] ." -- ".  $row['direct_cost_percentage_total'];         //'<b>Direct Cost Percentage</b> has been Changed.';
                    }

                    /*if ($data['item']['total_packing_cost_percentage'] != $row['packing_cost_percentage_total'])
                    {
                        $data['changes_warning']['status'] = true;
                        $data['changes_warning']['messages'][2] = '<b>Packing Cost Percentage</b> has been Changed.';
                    }*/
                    // ------------------------------------------------------------------------------------


                }

            }
            else // ADD
            {
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

        $fiscal_year_config = Query_helper::get_info($this->config->item('table_bms_setup_budget_config'), 'amount_currency_rate, amount_direct_cost_percentage, amount_packing_cost_percentage', array('fiscal_year_id=' . $item_head['fiscal_year_id']), 1);
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
        $fiscal_year_packing_cost_percentage = json_decode($fiscal_year_config['amount_packing_cost_percentage'], true);
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
