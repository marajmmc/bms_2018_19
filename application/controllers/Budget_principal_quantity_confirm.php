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
        //$this->language_config();
    }

    /*
    private function language_config()
    {
        $this->lang->language['LABEL_PRICE']='Price';
        // Setup > Budget Config > Indirect Cost
        $this->lang->language['LABEL_GENERAL_EXPENSE'] = 'General Expense';
        $this->lang->language['LABEL_MARKETING_EXPENSE'] = 'Marketing Expense';
        $this->lang->language['LABEL_FINANCIAL_EXPENSE'] = 'Financial Expense';
        $this->lang->language['LABEL_INCENTIVE'] = 'Incentive';
        $this->lang->language['LABEL_PROFIT'] = 'Profit';
        $this->lang->language['LABEL_SALES_COMMISSION'] = 'Sales Commission';
    }
    */

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
//            $data['fiscal_year_id'] = 1;
//            $data['fiscal_year'] = 1;
        }
        else if ($method == 'variety_list')
        {
//            $data['fiscal_year_id'] = 1;
//            $data['crop_name'] = 1;
//            $data['crop_type_name'] = 1;
//            $data['variety_name'] = 1;
//            $data['variety_id'] = 1;
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
                $data['item']['total_direct_cost_percentage'] = (float)$sum;
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

//        echo '<pre>';
//        print_r($this->input->post());
//        echo '</pre>';

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


        $item_details = array();
        $item_head['quantity_grand_total'] = 0.0;
        $item_head['cogs_grand_total'] = 0.0;

        foreach ($items as $principal_id => $item)
        {
            $each = array();
            $each = $item['monthly_principal_quantity']; // Assigns from Q_1 to Q_12. Need to be assigned at First.

            // Monthly total quantity
            $principal_quantity_total = 0.0;
            foreach ($item['monthly_principal_quantity'] as $quantity)
            {
                if ($quantity > 0)
                {
                    $principal_quantity_total += $quantity;
                }
            }

            // Details Table Data
            $each['fiscal_year_id'] = $item_head['fiscal_year_id'];
            $each['variety_id'] = $item_head['variety_id'];
            $each['principal_id'] = $principal_id;
            $each['currency_id'] = $item['currency_id'];
            $each['quantity_total'] = $principal_quantity_total; // Sub Total Quantity
            $each['unit_price'] = $item['unit_price'];
            $each['unit_price_taka'] = $item['unit_price'] * $fiscal_year_currencies[$item['currency_id']];
            $each['currency_rate'] = $fiscal_year_currencies[$item['currency_id']];

            // Principal COGS Calculation
            $A = $item['unit_price'] * $fiscal_year_currencies[$item['currency_id']];
            $B = $A * ($total_direct_cost_percentage / 100);
            $C = $A * ($total_packing_cost_percentage / 100);

            $each['cogs'] = $A + $B + $C;
            $each['total_cogs'] = $each['cogs'] * $principal_quantity_total;
            $each['date_created'] = $time;
            $each['user_created'] = $user->user_id;;

            // Main Table Data
            $item_head['quantity_grand_total'] += $principal_quantity_total; // Grand Total Quantity - Main Table
            $item_head['cogs_grand_total'] += $each['total_cogs']; // Grand Total COGS - Main Table
            $item_head['date_created'] = $time;
            $item_head['user_created'] = $user->user_id;;

            $item_details[] = $each;
        }

//        echo '<pre>';
//        print_r($item_head); // Main Table Data for Insert
//        print_r($item_details); // Details Table Data for Insert
//        echo '</pre>';

        $ajax['status'] = false;
        $ajax['system_message'] = "Save Method - NOT COMPLETED YET";
        $this->json_return($ajax);
    }

    private function get_info_budget_config($fiscal_year_id)
    {
        $info = Query_helper::get_info($this->config->item('table_bms_setup_budget_config'), '*', array('fiscal_year_id =' . $fiscal_year_id), 1);
        if (!$info)
        {
            $user = User_helper::get_user();
            $data = array();
            $data['fiscal_year_id'] = $fiscal_year_id;
            $data['date_created'] = time();
            $data['user_created'] = $user->user_id;
            $id = Query_helper::add($this->config->item('table_bms_setup_budget_config'), $data, false);
            $info = Query_helper::get_info($this->config->item('table_bms_setup_budget_config'), '*', array('id =' . $id), 1);
        }
        return $info;
    }

    /*
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('items[amount_general_percentage]', $this->lang->line('LABEL_GENERAL_EXPENSE'), 'required|numeric');
        $this->form_validation->set_rules('items[amount_marketing_percentage]', $this->lang->line('LABEL_MARKETING_EXPENSE'), 'required|numeric');
        $this->form_validation->set_rules('items[amount_finance_percentage]', $this->lang->line('LABEL_FINANCIAL_EXPENSE'), 'required|numeric');
        $this->form_validation->set_rules('items[amount_incentive_percentage]', $this->lang->line('LABEL_INCENTIVE'), 'required|numeric');
        $this->form_validation->set_rules('items[amount_profit_percentage]', $this->lang->line('LABEL_PROFIT'), 'required|numeric');
        $this->form_validation->set_rules('items[amount_sales_commission_percentage]', $this->lang->line('LABEL_SALES_COMMISSION'), 'required|numeric');
        if ($this->form_validation->run() == FALSE)
        {
            $this->message = validation_errors();
            return false;
        }
        return true;
    }
    */
}
