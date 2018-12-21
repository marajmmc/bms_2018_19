<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Budget_pricing_finalize extends Root_Controller
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
        $this->lang->language['LABEL_PRICE_TRADE_AUTO'] = 'Trade Price (automatic)';
        $this->lang->language['LABEL_PRICE_TRADE'] = 'Trade Price';
        $this->lang->language['LABEL_PERCENTAGE_SALES_COMMISSION'] = 'Sales Commission %';
        $this->lang->language['LABEL_SALES_COMMISSION'] = 'Sales Commission';
        $this->lang->language['LABEL_PRICE_NET'] = 'Net Price';
        $this->lang->language['LABEL_INCENTIVE'] = 'incentive';
        $this->lang->language['LABEL_COGS'] = 'Cogs';
        $this->lang->language['LABEL_GENERAL'] = 'general';
        $this->lang->language['LABEL_MARKETING'] = 'marketing';
        $this->lang->language['LABEL_FINANCE'] = 'finance';
        $this->lang->language['LABEL_PROFIT'] = 'profit';
        $this->lang->language['LABEL_PERCENTAGE_PROFIT_NP'] = 'Profit %(NP)';
        $this->lang->language['LABEL_PERCENTAGE_PROFIT_COGS'] = 'Profit %(COGS)';
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
        elseif ($action == "edit")
        {
            $this->system_edit($id);
        }
        elseif ($action == "get_items_edit")
        {
            $this->system_get_items_edit();
        }
        elseif($action=="save")
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
        else if ($method == 'edit')
        {
            $data['crop_name'] = 1;
            $data['crop_type_name'] = 1;
            //$data['variety_id'] = 1;send and received manually because of preference
            $data['variety_name'] = 1;
            $data['price_trade_auto'] = 1;
            $data['price_trade'] = 1;
            $data['percentage_sales_commission'] = 1;
            $data['sales_commission'] = 1;
            $data['price_net'] = 1;
            $data['incentive'] = 1;
            $data['cogs'] = 1;
            $data['general'] = 1;
            $data['marketing'] = 1;
            $data['finance'] = 1;
            $data['profit'] = 1;
            $data['percentage_profit_np'] = 1;
            $data['percentage_profit_cogs'] = 1;
        }
        return $data;
    }

    private function system_list()
    {
        $method = 'list';
        if (isset($this->permissions['action0']) && ($this->permissions['action0'] == 1))
        {
            $data['system_preference_items'] = $this->get_preference_headers($method);
            $data['title'] = "Final pricing calculation";
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

        $this->db->from($this->config->item('table_bms_budget_variety_pricing_finalize').' vp');
        $this->db->select('vp. fiscal_year_id');
        $this->db->select('COUNT(vp.variety_id) number_of_variety_done',false);
        $this->db->where('vp.amount_price_trade >',0);
        $this->db->group_by('vp.fiscal_year_id');
        $results=$this->db->get()->result_array();
        $variety_pricing_finalized=array();
        foreach($results as $result)
        {
            $variety_pricing_finalized[$result['fiscal_year_id']]=$result;
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
            if(isset($variety_pricing_finalized[$fy['id']]))
            {
                $data['number_of_variety_done'] = $variety_pricing_finalized[$fy['id']]['number_of_variety_done'];
            }
            $data['number_of_variety_due']= $data['number_of_variety_active']-$data['number_of_variety_done'];
            $items[] = $data;
        }
        $this->json_return($items);
    }

    private function system_edit($fiscal_year_id = 0)
    {
        $method = 'edit';
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
            $data['title'] = 'Final pricing calculation for (' . $data['fiscal_year']['name'] . ') Fiscal Year';
            $data['options']['fiscal_year_id'] = $fiscal_year_id;
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/edit", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/edit/' . $fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_get_items_edit()
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

        $results=Query_helper::get_info($this->config->item('table_bms_budget_variety_pricing_finalize'),'*',array('fiscal_year_id ='.$fiscal_year_id));
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']]=$result;
        }

        $results = Budget_helper::get_crop_type_varieties();
        foreach ($results as $result)
        {
            if(isset($principal_quantity[$result['variety_id']]))
            {
                $result['quantity_total'] = $principal_quantity[$result['variety_id']]['quantity_total'];
                $result['cogs'] = $principal_quantity[$result['variety_id']]['cogs'];
            }
            if(isset($items_old[$result['variety_id']]))
            {
                $result['amount_price_trade'] = $items_old[$result['variety_id']]['amount_price_trade'];
                $result['percentage_sales_commission'] = $items_old[$result['variety_id']]['percentage_sales_commission'];
            }
            $info=$this->initialize_row($result,$budget_config);
            $items[]=$info;
        }
        $this->json_return($items);
    }
    private function initialize_row($info,$budget_config)
    {
        $row=array();
        //automatic calculation
        $row['crop_name']=isset($info['crop_name'])?$info['crop_name']:'';
        $row['crop_type_name']=isset($info['crop_type_name'])?$info['crop_type_name']:'';
        $row['variety_name']=isset($info['variety_name'])?$info['variety_name']:'';
        $row['variety_id']=$info['variety_id'];


        $row['cogs']=isset($info['cogs'])?$info['cogs']:0;//auto and final
        $percentage_general=(isset($budget_config['percentage_general'])?$budget_config['percentage_general']:0);
        $row['general']=($row['cogs']*$percentage_general)/100;//auto and final

        $percentage_marketing=(isset($budget_config['percentage_marketing'])?$budget_config['percentage_marketing']:0);
        $row['marketing']=($row['cogs']*$percentage_marketing)/100;//auto and final

        $percentage_finance=(isset($budget_config['percentage_finance'])?$budget_config['percentage_finance']:0);
        $row['finance']=($row['cogs']*$percentage_finance)/100;//auto and final

        $profit_auto=($row['cogs']*(isset($budget_config['percentage_profit'])?$budget_config['percentage_profit']:0))/100;
        $percentage_incentive=(isset($budget_config['percentage_incentive'])?$budget_config['percentage_incentive']:0);
        $price_net_auto=100*($row['cogs']+$row['general']+$row['marketing']+$row['finance']+$profit_auto)/(100-$percentage_incentive);

        $incentive_auto=$price_net_auto*$percentage_incentive/100;
        $percentage_sales_commission=(isset($budget_config['percentage_sales_commission'])?$budget_config['percentage_sales_commission']:0);

        $row['price_trade_auto']=($price_net_auto*100)/(100-$percentage_sales_commission);
        $row['price_trade']=round(isset($info['amount_price_trade'])?$info['amount_price_trade']:$row['price_trade_auto'],2);//final==saved or auto one

        $row['percentage_sales_commission']=isset($info['percentage_sales_commission'])?$info['percentage_sales_commission']:$percentage_sales_commission;//auto and final
        $row['sales_commission']=$row['price_trade']*$row['percentage_sales_commission']/100;
        $row['price_net']=$row['price_trade']-$row['sales_commission'];
        $row['incentive']=$row['price_net']*$percentage_incentive/100;

        $row['profit']=$row['price_net']-$row['cogs']-$row['general']-$row['marketing']-$row['finance']-$row['incentive'];
        $row['percentage_profit_np']=0;
        if($row['price_net']>0)
        {
            $row['percentage_profit_np']=$row['profit']*100/$row['price_net'];
        }
        $row['percentage_profit_cogs']=0;
        if($row['cogs']>0)
        {
            $row['percentage_profit_cogs']=$row['profit']*100/$row['cogs'];
        }

        return $row;

    }
    private function system_save()
    {
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        $items=$this->input->post('items');
        if(!((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1))))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        $results=Query_helper::get_info($this->config->item('table_bms_budget_variety_pricing_finalize'),'*',array('fiscal_year_id ='.$item_head['fiscal_year_id']));
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']]=$result;
        }
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($items as $variety_id=>$quantity_info)
        {
            if(isset($items_old[$variety_id]))
            {
                if(($items_old[$variety_id]['amount_price_trade'] != $quantity_info['amount_price_trade'])||($items_old[$variety_id]['percentage_sales_commission'] != $quantity_info['percentage_sales_commission']))
                {
                    $data=array();
                    $this->db->set('revision_count','revision_count+1',false);
                    $data['amount_price_trade']=$quantity_info['amount_price_trade'];
                    $data['percentage_sales_commission']=$quantity_info['percentage_sales_commission'];
                    $data['amount_price_net']=$data['amount_price_trade']-($quantity_info['percentage_sales_commission']*$quantity_info['amount_price_trade']/100);
                    $data['date_updated']=$time;
                    $data['user_updated']=$user->user_id;
                    Query_helper::update($this->config->item('table_bms_budget_variety_pricing_finalize'),$data,array('id='.$items_old[$variety_id]['id']),false);
                }
            }
            else
            {
                $data=array();
                $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                $data['variety_id']=$variety_id;
                $data['amount_price_trade']=$quantity_info['amount_price_trade'];
                $data['percentage_sales_commission']=$quantity_info['percentage_sales_commission'];
                $data['amount_price_net']=$data['amount_price_trade']-($quantity_info['percentage_sales_commission']*$quantity_info['amount_price_trade']/100);
                $data['revision_count']=1;
                $data['date_created'] = $time;
                $data['user_created'] = $user->user_id;
                Query_helper::add($this->config->item('table_bms_budget_variety_pricing_finalize'),$data,false);
            }
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list();

        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
}
