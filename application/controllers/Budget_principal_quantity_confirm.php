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
        elseif ($action == "add_edit_principal_quantity")
        {
            $this->system_add_edit_principal_quantity($id, $id1);
        }
        /* elseif($action=="save_currency_rate")
        {
            $this->system_save_currency_rate();
        }
        elseif($action=="add_edit_direct_cost")
        {
            $this->system_add_edit_direct_cost($id);
        }
        elseif($action=="save_direct_cost")
        {
            $this->system_save_direct_cost();
        }
        elseif($action=="add_edit_packing_cost")
        {
            $this->system_add_edit_packing_cost($id);
        }
        elseif($action=="save_packing_cost")
        {
            $this->system_save_packing_cost();
        }
        elseif($action=="add_edit_indirect_cost")
        {
            $this->system_add_edit_indirect_cost($id);
        }
        elseif($action=="save_indirect_cost")
        {
            $this->system_save_indirect_cost();
        } */
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

    private function system_add_edit_principal_quantity($fiscal_year_id = 0, $variety_id = 0)
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
                $data['item']['total_packing_cost_percentage'] = (float)$sum;
            }

            $currency_items = json_decode($data['fiscal_year_data']['amount_currency_rate'], true); // From 'bms_setup_budget_config' table

            $data['currencies'] = array();
            $currencies = Query_helper::get_info($this->config->item('table_login_setup_currency'), '*', array('status !="' . $this->config->item('system_status_delete') . '"'));
            foreach ($currencies as $currency)
            {
                if (isset($currency_items[$currency['id']]))
                {
                    $data['currencies'][$currency['id']] = array(
                        'name' => $currency['name'],
                        'symbol' => $currency['symbol'],
                        'currency_rate' => (float)$currency_items[$currency['id']],
                        'description' => $currency['description']
                    );
                }
            }

            // Variety Principles
            $this->db->from($this->config->item('table_login_setup_classification_variety_principals') . ' variety_principals');

            $this->db->join($this->config->item('table_login_basic_setup_principal') . ' principal', 'principal.id = variety_principals.principal_id', 'LEFT');
            $this->db->select('principal.id, principal.name');

            $this->db->where('variety_principals.variety_id', $variety_id);
            $this->db->where('variety_principals.revision', 1);
            $this->db->where('principal.status', $this->config->item('system_status_active'));
            $data['item']['principals'] = $this->db->get()->result_array();


//            echo '<pre>';
//            print_r($data['currencies']);
//            print_r($currency_items);
//            print_r($currencies);
//            print_r($data['item']['principals']);
//            echo '</pre>';

            $data['title'] = "Edit Principal Quantity Confirmation"; // of  for (".$data['fiscal_year']['name'].') Fiscal Year';
            $ajax['status'] = true;
            $ajax['system_content'][] = array("id" => "#system_content", "html" => $this->load->view($this->controller_url . "/add_edit_principal_quantity", $data, true));
            if ($this->message)
            {
                $ajax['system_message'] = $this->message;
            }
            $ajax['system_page_url'] = site_url($this->controller_url . '/index/add_edit_principal_quantity/' . $fiscal_year_id . '/' . $variety_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

//    private function initialize_row_add_edit_pricing_packing($info)
//    {
//        $method = 'add_edit_pricing';
//        $row = $this->get_preference_headers($method);
//        foreach ($row as $key => $r)
//        {
//            $row[$key] = 0;
//        }
//        $row['crop_name'] = $info['crop_name'];
//        $row['crop_type_name'] = $info['crop_type_name'];
//        $row['variety_name'] = $info['variety_name'];
//        $row['variety_id'] = $info['variety_id'];
//        return $row;
//    }

    /*
    private function system_save_pricing_packing()
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
        if(!Budget_helper::check_validation_fiscal_year($item_head['fiscal_year_id']))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['fiscal_year_id'],'Invalid Fiscal year');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Fiscal Year';
            $this->json_return($ajax);
        }
        $info_budget_config=$this->get_info_budget_config($item_head['fiscal_year_id']);
        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_setup_budget_config_variety_pricing'),'*',array('fiscal_year_id ='.$item_head['fiscal_year_id']));
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']]=$result;
        }
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($items as $variety_id=>$price)
        {
            if(isset($items_old[$variety_id]))
            {
                if( ($items_old[$variety_id]['amount_price_net']!=$price['amount_price_net']) || ($items_old[$variety_id]['amount_price_trade']!=$price['amount_price_trade']) )
                {
                    $data=array();
                    $data['amount_price_net']=$price['amount_price_net'];
                    $data['amount_price_trade']=$price['amount_price_trade'];
                    Query_helper::update($this->config->item('table_bms_setup_budget_config_variety_pricing'),$data,array('id='.$items_old[$variety_id]['id']),false);
                }
            }
            else
            {
                $data=array();
                $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                $data['variety_id']=$variety_id;
                $data['amount_price_net']=$price['amount_price_net'];
                $data['amount_price_trade']=$price['amount_price_trade'];
                Query_helper::add($this->config->item('table_bms_setup_budget_config_variety_pricing'),$data,false);
            }
        }
        $data=array();
        $data['date_pricing_updated'] = $time;
        $data['user_pricing_updated'] = $user->user_id;
        $this->db->set('revision_count_pricing','revision_count_pricing+1',false);
        Query_helper::update($this->config->item('table_bms_setup_budget_config'),$data,array('fiscal_year_id='.$item_head['fiscal_year_id']),false);

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

    private function system_add_edit_currency_rate($fiscal_year_id=0)
    {
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            $data['item']=$this->get_info_budget_config($fiscal_year_id);
            if(!$data['item']['amount_currency_rate'])
            {
                $before_year_info=Query_helper::get_info($this->config->item('table_bms_setup_budget_config'),'*',array('fiscal_year_id ='.($fiscal_year_id-1)),1);

                if($before_year_info)
                {
                    $data['item']['amount_currency_rate']=$before_year_info['amount_currency_rate'];
                }
            }
            $data['currencies']=Query_helper::get_info($this->config->item('table_login_setup_currency'),'*',array('status !="'.$this->config->item('system_status_delete').'"'));
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['title']="Currency Rate Setup For (".$data['fiscal_year']['name'].') Fiscal Year';
            //$data['item']['fiscal_year_id']=$fiscal_year_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit_currency_rate",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add_edit_currency_rate/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_currency_rate()
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
        if(!Budget_helper::check_validation_fiscal_year($item_head['fiscal_year_id']))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['fiscal_year_id'],'Invalid Fiscal year');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Fiscal Year';
            $this->json_return($ajax);
        }
        $info=$this->get_info_budget_config($item_head['fiscal_year_id']);

        $this->db->trans_start();  //DB Transaction Handle START

        $data=array();
        $data['amount_currency_rate'] = json_encode($items);
        $data['user_currency_rate'] = $time;
        $data['date_currency_rate'] = $user->user_id;
        $this->db->set('revision_count_currency_rate','revision_count_currency_rate+1',false);
        Query_helper::update($this->config->item('table_bms_setup_budget_config'),$data,array('fiscal_year_id='.$item_head['fiscal_year_id']),false);

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

    private function system_add_edit_direct_cost($fiscal_year_id=0)
    {
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            $data['item']=$this->get_info_budget_config($fiscal_year_id);
            if(!$data['item']['amount_direct_cost_percentage'])
            {
                $before_year_info=Query_helper::get_info($this->config->item('table_bms_setup_budget_config'),'*',array('fiscal_year_id ='.($fiscal_year_id-1)),1);

                if($before_year_info)
                {
                    $data['item']['amount_direct_cost_percentage']=$before_year_info['amount_direct_cost_percentage'];
                }

            }
            $data['direct_cost_items']=Query_helper::get_info($this->config->item('table_login_setup_direct_cost_items'),'*',array('status !="'.$this->config->item('system_status_delete').'"'));
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['title']="Direct Cost Percentage Setup For (".$data['fiscal_year']['name'].') Fiscal Year';
            //$data['item']['fiscal_year_id']=$fiscal_year_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit_direct_cost",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add_edit_direct_cost/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_direct_cost()
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
        if(!Budget_helper::check_validation_fiscal_year($item_head['fiscal_year_id']))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['fiscal_year_id'],'Invalid Fiscal year');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Fiscal Year';
            $this->json_return($ajax);
        }
        $info=$this->get_info_budget_config($item_head['fiscal_year_id']);

        $this->db->trans_start();  //DB Transaction Handle START

        $data=array();
        $data['amount_direct_cost_percentage'] = json_encode($items);
        $data['date_direct_cost_percentage'] = $time;
        $data['user_direct_cost_percentage'] = $user->user_id;
        $this->db->set('revision_count_direct_cost_percentage','revision_count_direct_cost_percentage+1',false);
        Query_helper::update($this->config->item('table_bms_setup_budget_config'),$data,array('fiscal_year_id='.$item_head['fiscal_year_id']),false);

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

    private function system_add_edit_packing_cost($fiscal_year_id=0)
    {
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            $data = array();
            $data['item']=$this->get_info_budget_config($fiscal_year_id);

            if(!$data['item']['amount_packing_cost_percentage'])
            {
                $before_year_info=Query_helper::get_info($this->config->item('table_bms_setup_budget_config'),'*',array('fiscal_year_id ='.($fiscal_year_id-1)),1);

                if($before_year_info)
                {
                    $data['item']['amount_packing_cost_percentage']=$before_year_info['amount_packing_cost_percentage'];
                }
            }
            $data['packing_cost_items']=Query_helper::get_info($this->config->item('table_login_setup_packing_cost_items'),'*',array('status !="'.$this->config->item('system_status_delete').'"'));
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['title']='Packing Cost Percentage Setup for ( '.$data['fiscal_year']['name'].' ) Fiscal Year';
            //$data['item']['fiscal_year_id']=$fiscal_year_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit_packing_cost",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add_edit_packing_cost/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_packing_cost()
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
        if(!Budget_helper::check_validation_fiscal_year($item_head['fiscal_year_id']))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['fiscal_year_id'],'Invalid Fiscal year');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Fiscal Year';
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $data=array();
        $data['amount_packing_cost_percentage'] = json_encode($items);
        $data['date_packing_cost_percentage'] = $time;
        $data['user_packing_cost_percentage'] = $user->user_id;
        $this->db->set('revision_count_packing_cost_percentage','revision_count_packing_cost_percentage+1',false);
        Query_helper::update($this->config->item('table_bms_setup_budget_config'),$data,array('fiscal_year_id='.$item_head['fiscal_year_id']),false);

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

    private function system_add_edit_indirect_cost($fiscal_year_id=0)
    {
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            $data = array();
            $data['item']=$this->get_info_budget_config($fiscal_year_id);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);

            $data['title']='Indirect Cost Setup for ( '.$data['fiscal_year']['name'].' ) Fiscal Year';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit_indirect_cost",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add_edit_indirect_cost/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_save_indirect_cost()
    {
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        $data=$this->input->post('items');
        if(!((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1))))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if(!Budget_helper::check_validation_fiscal_year($item_head['fiscal_year_id']))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['fiscal_year_id'],'Invalid Fiscal year');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Fiscal Year';
            $this->json_return($ajax);
        }
        //Validation Checking
        if (!$this->check_validation())
        {
            $ajax['status'] = false;
            $ajax['system_message'] = $this->message;
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $data['date_indirect_cost_percentage'] = $time;
        $data['user_indirect_cost_percentage'] = $user->user_id;
        $this->db->set('revision_count_indirect_cost_percentage','revision_count_indirect_cost_percentage+1',false);
        Query_helper::update($this->config->item('table_bms_setup_budget_config'),$data,array('fiscal_year_id='.$item_head['fiscal_year_id']),false);

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
    */

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
