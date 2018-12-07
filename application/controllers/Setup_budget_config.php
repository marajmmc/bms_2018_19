<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_budget_config extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;

    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission(get_class());
        $this->controller_url=strtolower(get_class());
        $this->load->helper('budget');
        $this->language_config();
    }
    private function language_config()
    {
        $this->lang->language['LABEL_PRICE']='Price';
    }
    public function index($action="list", $id=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        elseif($action=="add_edit_pricing")
        {
            $this->system_add_edit_pricing($id);
        }
        elseif($action=="get_items_add_edit_pricing")
        {
            $this->system_get_items_add_edit_pricing();
        }
        elseif($action=="save_pricing_packing")
        {
            $this->system_save_pricing_packing();
        }
        elseif($action=="add_edit_currency_rate")
        {
            $this->system_add_edit_currency_rate($id);
        }
        elseif($action=="save_currency_rate")
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
        else
        {
            $this->system_list();
        }
    }
    private function get_preference_headers($method)
    {
        $data=array();
        if($method=='list')
        {
            $data['fiscal_year_id']= 1;
            $data['fiscal_year']= 1;
            $data['revision_pricing_count']= 1;
            $data['revision_currency_rate_count']= 1;
            $data['revision_direct_cost_percentage_count']= 1;
        }
        else if($method=='add_edit_pricing')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            $data['amount_price']= 1;
        }
        return $data;
    }

    private function system_list()
    {
        //$user = User_helper::get_user();
        $method='list';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="Budget Configurations";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items()
    {
        $results=Query_helper::get_info($this->config->item('table_bms_setup_budget_config'),'*',array());
        $budget_configs=array();
        foreach($results as $result)
        {
            $budget_configs[$result['fiscal_year_id']]=$result;
        }

        $items=array();
        $fiscal_years=Budget_helper::get_fiscal_years();
        foreach($fiscal_years as $fy)
        {
            $data=array();
            $data['fiscal_year_id']=$fy['id'];
            $data['fiscal_year']=$fy['text'];
            $data['revision_pricing_count']=0;
            $data['revision_currency_rate_count']=0;
            $data['revision_direct_cost_percentage_count']=0;
            if(isset($budget_configs[$fy['id']]))
            {
                $data['revision_pricing_count']=$budget_configs[$fy['id']]['revision_pricing_count'];
                $data['revision_currency_rate_count']=$budget_configs[$fy['id']]['revision_currency_rate_count'];
                $data['revision_direct_cost_percentage_count']=$budget_configs[$fy['id']]['revision_direct_cost_percentage_count'];
            }
            $items[]=$data;
        }
        $this->json_return($items);
    }
    private function system_add_edit_pricing($fiscal_year_id=0)
    {
        $method='add_edit_pricing_packing';
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            if(!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
            {
                System_helper::invalid_try(__FUNCTION__,$fiscal_year_id,'Invalid Fiscal year');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Fiscal Year';
                $this->json_return($ajax);
            }
            $info_budget_config=$this->get_info_budget_config($fiscal_year_id);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="Variety Pricing and Packing cost Setup for (".$data['fiscal_year']['name'].') Fiscal Year';
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit_pricing",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add_edit_pricing/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_add_edit_pricing()
    {
        $items=array();
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        if(!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
        {
            System_helper::invalid_try(__FUNCTION__,$fiscal_year_id,'Invalid Fiscal year');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Fiscal Year';
            $this->json_return($ajax);
        }
        // main data checking
        $info_budget_config=$this->get_info_budget_config($fiscal_year_id);
        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_setup_budget_config_variety_pricing'),'*',array('fiscal_year_id ='.$fiscal_year_id));
        if(!$results)
        {
            $results=Query_helper::get_info($this->config->item('table_bms_setup_budget_config_variety_pricing'),'*',array('fiscal_year_id ='.($fiscal_year_id-1)),0,1,array('fiscal_year_id DESC'));
        }
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']]=$result;
        }
        $results = Budget_helper::get_crop_type_varieties();
        foreach($results as $result)
        {
            $info=$this->initialize_row_add_edit_pricing_packing($result);
            if(isset($items_old[$result['variety_id']]))
            {
                $info['amount_price']=$items_old[$result['variety_id']]['amount_price'];
            }
            $items[]=$info;
        }

        $this->json_return($items);
    }
    private function initialize_row_add_edit_pricing_packing($info)
    {
        $method='add_edit_pricing';
        $row=$this->get_preference_headers($method);
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['crop_name']=$info['crop_name'];
        $row['crop_type_name']=$info['crop_type_name'];
        $row['variety_name']=$info['variety_name'];
        $row['variety_id']=$info['variety_id'];
        return $row;
    }
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
                if(($items_old[$variety_id]['amount_price']!=$price['amount_price']))
                {
                    $data=array();
                    $data['amount_price']=$price['amount_price'];
                    Query_helper::update($this->config->item('table_bms_setup_budget_config_variety_pricing'),$data,array('id='.$items_old[$variety_id]['id']),false);
                }
            }
            else
            {
                $data=array();
                $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                $data['variety_id']=$variety_id;
                $data['amount_price']=$price['amount_price'];
                Query_helper::add($this->config->item('table_bms_setup_budget_config_variety_pricing'),$data,false);
            }
        }
        $data=array();
        $data['date_pricing_updated'] = $time;
        $data['user_pricing_updated'] = $user->user_id;
        $this->db->set('revision_pricing_count','revision_pricing_count+1',false);
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
        $this->db->set('revision_currency_rate_count','revision_currency_rate_count+1',false);
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
        $this->db->set('revision_direct_cost_percentage_count','revision_direct_cost_percentage_count+1',false);
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


    private function get_info_budget_config($fiscal_year_id)
    {
        $info=Query_helper::get_info($this->config->item('table_bms_setup_budget_config'),'*',array('fiscal_year_id ='.$fiscal_year_id),1);
        if(!$info)
        {
            $user = User_helper::get_user();
            $data=array();
            $data['fiscal_year_id'] = $fiscal_year_id;
            $data['date_created'] = time();
            $data['user_created'] = $user->user_id;
            $id=Query_helper::add($this->config->item('table_bms_setup_budget_config'),$data,false);
            $info=Query_helper::get_info($this->config->item('table_bms_setup_budget_config'),'*',array('id ='.$id),1);
        }
        return $info;
    }
}
