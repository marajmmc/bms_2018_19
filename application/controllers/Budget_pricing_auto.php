<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Budget_pricing_auto extends Root_Controller
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
        $this->lang->language['LABEL_QUANTITY_TOTAL'] = 'Conf Qty';
        $this->lang->language['LABEL_COGS'] = 'Cogs';
        $this->lang->language['LABEL_GENERAL'] = 'general';
        $this->lang->language['LABEL_MARKETING'] = 'marketing';
        $this->lang->language['LABEL_FINANCE'] = 'finance';
        $this->lang->language['LABEL_INCENTIVE'] = 'incentive';
        $this->lang->language['LABEL_PROFIT'] = 'profit(Cogs)';
        $this->lang->language['LABEL_PRICE_NET'] = 'Net Price';
        $this->lang->language['LABEL_SALES_COMMISSION'] = 'Sales Commission';
        $this->lang->language['LABEL_PRICE_TRADE'] = 'Trade Price';
        $this->lang->language['LABEL_PERCENTAGE_PROFIT'] = 'Profit %(NP)';
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
            $data['quantity_total'] = 1;
            $data['cogs'] = 1;
            $data['general'] = 1;
            $data['marketing'] = 1;
            $data['finance'] = 1;
            $data['incentive'] = 1;
            $data['profit'] = 1;
            $data['price_net'] = 1;
            $data['sales_commission'] = 1;
            $data['price_trade'] = 1;
            $data['percentage_profit'] = 1;
        }
        return $data;
    }

    private function system_list()
    {
        $method = 'list';
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['system_preference_items'] = $this->get_preference_headers($method);
            $data['title'] = "Automatic pricing calculation";
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
            $data['budget_config']= Query_helper::get_info($this->config->item('table_bms_setup_budget_config'), '*', array('fiscal_year_id=' . $fiscal_year_id), 1);

            $data['system_preference_items'] = $this->get_preference_headers($method);
            $data['title'] = 'Automatic pricing calculation for (' . $data['fiscal_year']['name'] . ') Fiscal Year';
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
        $budget_config= Query_helper::get_info($this->config->item('table_bms_setup_budget_config'), '*', array('fiscal_year_id=' . $fiscal_year_id), 1);

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
            if(isset($principal_quantity[$result['variety_id']]))
            {
                $result['quantity_total'] = $principal_quantity[$result['variety_id']]['quantity_total'];
                $result['cogs'] = $principal_quantity[$result['variety_id']]['cogs'];
            }
            $info=$this->initialize_row($result,$budget_config);
            $items[]=$info;
        }
        $this->json_return($items);
    }
    private function initialize_row($info,$budget_config)
    {
        $row=array();
        $row['crop_name']=isset($info['crop_name'])?$info['crop_name']:'';
        $row['crop_type_name']=isset($info['crop_type_name'])?$info['crop_type_name']:'';
        $row['variety_name']=isset($info['variety_name'])?$info['variety_name']:'';
        $row['quantity_total']=isset($info['quantity_total'])?$info['quantity_total']:0;
        $row['cogs']=isset($info['cogs'])?$info['cogs']:0;
        $row['general']=($row['cogs']*(isset($budget_config['percentage_general'])?$budget_config['percentage_general']:0))/100;
        $row['marketing']=($row['cogs']*(isset($budget_config['percentage_marketing'])?$budget_config['percentage_marketing']:0))/100;
        $row['finance']=($row['cogs']*(isset($budget_config['percentage_finance'])?$budget_config['percentage_finance']:0))/100;
        $row['profit']=($row['cogs']*(isset($budget_config['percentage_profit'])?$budget_config['percentage_profit']:0))/100;

        $percentage_incentive=(isset($budget_config['percentage_incentive'])?$budget_config['percentage_incentive']:0);

        $row['price_net']=100*($row['cogs']+$row['general']+$row['marketing']+$row['finance']+$row['profit'])/(100-$percentage_incentive);
        $row['incentive']=$row['price_net']*$percentage_incentive/100;

        $percentage_sales_commission=(isset($budget_config['percentage_sales_commission'])?$budget_config['percentage_sales_commission']:0);
        $row['price_trade']=($row['price_net']*100)/(100-$percentage_sales_commission);
        $row['sales_commission']=$row['price_trade']-$row['price_net'];
        $row['percentage_profit']=0;
        if($row['price_net']>0)
        {
            $row['percentage_profit']=$row['profit']*100/$row['price_net'];
        }
        return $row;

    }
}
