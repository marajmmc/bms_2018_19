<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Budget_mgt_budget_target extends Root_Controller
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
        $this->lang->load('budget');

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
        elseif($action=="edit_mgt_budget_quantity_confirm")
        {
            $this->system_edit_mgt_budget_quantity_confirm($id);
        }
        elseif($action=="get_items_edit_mgt_budget_quantity_confirm")
        {
            $this->system_get_items_edit_mgt_budget_quantity_confirm();
        }
        elseif($action=="save_mgt_budget_quantity_confirm")
        {
            $this->system_save_mgt_budget_quantity_confirm();
        }
        elseif($action=="edit_mgt_target_hom")
        {
            $this->system_edit_mgt_target_hom($id);
        }
        elseif($action=="get_items_edit_mgt_target_hom")
        {
            $this->system_get_items_edit_mgt_target_hom();
        }
        elseif($action=="save_mgt_target_hom")
        {
            $this->system_save_mgt_target_hom();
        }
        elseif($action=="target_forward")
        {
            $this->system_target_forward($id);
        }
        elseif($action=="save_target_forward")
        {
            $this->system_save_target_forward();
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
            $data['revision_count_mgt_budget_quantity_confirm']= 1;
            $data['status_mgt_target_forward']= 1;
        }
        else if($method=='edit_mgt_budget_quantity_confirm')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            //more data
            $data['stock_current_hq']= 1;
            $data['quantity_budget_hom']= 1;
            $data['quantity_budget_needed']= 1;
            $data['quantity_budget_quantity_confirm']= 1;
        }
        else if($method=='edit_mgt_target_hom')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            //more data
            $data['quantity_budget_hom']= 1;
            $data['stock_current_hq']= 1;
            $data['quantity_budget_quantity_confirm']= 1;
            $data['quantity_target_available']= 1;
            $data['quantity_target_hom']= 1;
        }
        else if($method=='target_forward')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            //more data
            $data['stock_current_hq']= 1;
            $data['quantity_budget_hom']= 1;
            $data['quantity_budget_needed']= 1;
            $data['quantity_budget_quantity_confirm']= 1;
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
            $data['title']="Yearly MGT Budget Quantity Confirmed & Target";
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
        $fiscal_years=Budget_helper::get_fiscal_years();
        $this->db->from($this->config->item('table_bms_hom_budget_target').' budget_target');
        $this->db->select('budget_target.fiscal_year_id');
        $this->db->select('budget_target.revision_count_mgt_budget_quantity_confirm');
        $this->db->select('budget_target.status_mgt_target_forward');
        $results=$this->db->get()->result_array();
        $budget_target=array();
        foreach($results as $result)
        {
            $budget_target[$result['fiscal_year_id']]=$result;
        }
        $items=array();
        foreach($fiscal_years as $fy)
        {
            $data=array();
            $data['fiscal_year_id']=$fy['id'];
            $data['fiscal_year']=$fy['text'];
            if(isset($budget_target[$fy['id']]))
            {
                $data['status_mgt_target_forward']=$budget_target[$fy['id']]['status_mgt_target_forward'];
            }
            else
            {
                $data['status_mgt_target_forward']=$this->config->item('system_status_confirm');
            }
            $data['revision_count_mgt_budget_quantity_confirm']=$budget_target[$fy['id']]['revision_count_mgt_budget_quantity_confirm'];
            $items[]=$data;
        }
        $this->json_return($items);
    }
    private function system_edit_mgt_budget_quantity_confirm($fiscal_year_id=0)
    {
        $method='edit_mgt_budget_quantity_confirm';
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            //validation fiscal year
            if(!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
            {
                System_helper::invalid_try(__FUNCTION__,$fiscal_year_id,'Invalid Fiscal year');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Fiscal Year';
                $this->json_return($ajax);
            }
            //validation forward
            $get_info_budget_target=$this->get_info_budget_target($fiscal_year_id);
            if(($get_info_budget_target['status_mgt_target_forward']==$this->config->item('system_status_forwarded')))
            {
                if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Target Already Forwarded.';
                    $this->json_return($ajax);
                }
            }
            $data['fiscal_years_previous_sales']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['acres']=$this->get_acres();

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="MGT Yearly Budget Quantity Confirm For All Variety";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_mgt_budget_quantity_confirm",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_mgt_budget_quantity_confirm/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_edit_mgt_budget_quantity_confirm()
    {
        $items=array();
        //$this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');

        $fiscal_years_previous_sales=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
        $sales_previous=$this->get_sales_previous_years_hq($fiscal_years_previous_sales);

        //HQ Current Stock
        $this->db->from($this->config->item('table_sms_stock_summary_variety').' stock_summary_variety');
        $this->db->select('SUM((pack.name*stock_summary_variety.current_stock)/1000) current_stock, stock_summary_variety.variety_id');
        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id=stock_summary_variety.pack_size_id','LEFT');
        //$this->db->where('stock_summary_variety.pack_size_id > 0');
        $this->db->group_by('stock_summary_variety.variety_id');
        $results=$this->db->get()->result_array();
        $stocks=array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']]=$result;
        }
        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_hom_budget_target_hom'),'*',array('fiscal_year_id ='.$fiscal_year_id));
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']]=$result;
        }

        //variety lists
        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','INNER');
        $this->db->select('crop.name crop_name');
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $item=$result;
            foreach($fiscal_years_previous_sales as $fy)
            {
                if(isset($sales_previous[$fy['id']][$result['variety_id']]))
                {
                    $item['quantity_sale_'.$fy['id']]=$sales_previous[$fy['id']][$result['variety_id']]/1000;
                }
                else
                {
                    $item['quantity_sale_'.$fy['id']]=0;
                }
            }
            if(isset($items_old[$result['variety_id']]))
            {
                if($items_old[$result['variety_id']]['quantity_budget']>0)
                {
                    $item['quantity_budget_hom']=$items_old[$result['variety_id']]['quantity_budget'];
                }
                else
                {
                    $item['quantity_budget_hom']='';
                }
                if($items_old[$result['variety_id']]['quantity_budget_quantity_confirm']>0)
                {
                    $item['quantity_budget_quantity_confirm']=$items_old[$result['variety_id']]['quantity_budget_quantity_confirm'];
                }
                else
                {
                    $item['quantity_budget_quantity_confirm']='';
                }
            }
            else
            {
                $item['quantity_budget_hom']='';
                $item['quantity_budget_quantity_confirm']='';
            }
            if(isset($stocks[$result['variety_id']]))
            {
                if($stocks[$result['variety_id']]['current_stock']>0)
                {
                    $item['stock_current_hq']=$stocks[$result['variety_id']]['current_stock'];
                }
                else
                {
                    $item['stock_current_hq']='';
                }
            }
            else
            {
                $item['stock_current_hq']='';
            }
            $quantity_budget_needed=($item['stock_current_hq']-$item['quantity_budget_hom']);
            if($quantity_budget_needed>0)
            {
                $item['quantity_budget_needed']=$quantity_budget_needed;
            }
            else
            {
                $item['quantity_budget_needed']='';
            }
            $items[]=$item;
        }

        $this->json_return($items);
    }
    private function system_save_mgt_budget_quantity_confirm()
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
        //validation fiscal year
        if(!Budget_helper::check_validation_fiscal_year($item_head['fiscal_year_id']))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['fiscal_year_id'],'Invalid Fiscal year');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Fiscal Year';
            $this->json_return($ajax);
        }
        //validation target forward
        $info_budget_target=$this->get_info_budget_target($item_head['fiscal_year_id']);
        if(($info_budget_target['status_mgt_target_forward']==$this->config->item('system_status_forwarded')))
        {
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']='Budget Already Forwarded.';
                $this->json_return($ajax);
            }
        }
        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_hom_budget_target_hom'),'*',array('fiscal_year_id ='.$item_head['fiscal_year_id']));
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']]=$result;
        }
        $this->db->trans_start();  //DB Transaction Handle START
        $revision_count_mgt_budget_quantity_confirm_status=false;
        foreach($items as $variety_id=>$quantity_budget_quantity_confirm)
        {
            if(isset($items_old[$variety_id]))
            {
                if($items_old[$variety_id]['quantity_budget_quantity_confirm']!=$quantity_budget_quantity_confirm && $quantity_budget_quantity_confirm)
                {
                    $this->db->set('revision_count_budget_quantity_confirm','revision_count_budget_quantity_confirm+1',false);
                    $data['quantity_budget_quantity_confirm']=$quantity_budget_quantity_confirm;
                    /*$data['date_updated_budget']=$time;
                    $data['user_updated_budget']=$user->user_id;*/
                    Query_helper::update($this->config->item('table_bms_hom_budget_target_hom'),$data,array('id='.$items_old[$variety_id]['id']));
                    $revision_count_mgt_budget_quantity_confirm_status=true;
                }
            }
            else
            {
                $data=array();
                $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                $data['variety_id']=$variety_id;
                if($quantity_budget_quantity_confirm>0)
                {
                    $data['quantity_budget_quantity_confirm']=$quantity_budget_quantity_confirm;
                    $data['revision_count_budget_quantity_confirm']=1;
                    $revision_count_mgt_budget_quantity_confirm_status=true;
                }
                else
                {
                    $data['quantity_budget_quantity_confirm']=0;
                }
                /*$data['date_updated_budget'] = $time;
                $data['user_updated_budget'] = $user->user_id;*/
                Query_helper::add($this->config->item('table_bms_hom_budget_target_hom'),$data,false);
            }
            if($revision_count_mgt_budget_quantity_confirm_status)
            {
                $data=array();
                $data['date_updated_mgt_budget_quantity_confirm'] = $time;
                $data['user_updated_mgt_budget_quantity_confirm'] = $user->user_id;
                // problem:: revision count wrong insert ?
                //$this->db->set('revision_count_mgt_budget_quantity_confirm','revision_count_mgt_budget_quantity_confirm+1',false);
                Query_helper::update($this->config->item('table_bms_hom_budget_target'),$data,array('fiscal_year_id ='.$item_head['fiscal_year_id']));
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
    private function system_edit_mgt_target_hom($fiscal_year_id=0)
    {
        $method='edit_mgt_target_hom';
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            //validation fiscal year
            if(!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
            {
                System_helper::invalid_try(__FUNCTION__,$fiscal_year_id,'Invalid Fiscal year');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Fiscal Year';
                $this->json_return($ajax);
            }
            //validation forward
            $get_info_budget_target=$this->get_info_budget_target($fiscal_year_id);
            if(($get_info_budget_target['status_mgt_target_forward']==$this->config->item('system_status_forwarded')))
            {
                if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Target Already Forwarded.';
                    $this->json_return($ajax);
                }
            }
            $data['fiscal_years_previous_sales']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['acres']=$this->get_acres();

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="MGT Set Yearly Target For HOM (All Variety)";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_mgt_target_hom",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_mgt_target_hom/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_edit_mgt_target_hom()
    {
        $items=array();
        //$this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');

        $fiscal_years_previous_sales=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
        $sales_previous=$this->get_sales_previous_years_hq($fiscal_years_previous_sales);

        //HQ Current Stock
        $this->db->from($this->config->item('table_sms_stock_summary_variety').' stock_summary_variety');
        $this->db->select('SUM((pack.name*stock_summary_variety.current_stock)/1000) current_stock, stock_summary_variety.variety_id');
        $this->db->join($this->config->item('table_login_setup_classification_pack_size').' pack','pack.id=stock_summary_variety.pack_size_id','LEFT');
        //$this->db->where('stock_summary_variety.pack_size_id > 0');
        $this->db->group_by('stock_summary_variety.variety_id');
        $results=$this->db->get()->result_array();
        $stocks=array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']]=$result;
        }
        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_hom_budget_target_hom'),'*',array('fiscal_year_id ='.$fiscal_year_id));
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']]=$result;
        }

        //variety lists
        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','INNER');
        $this->db->select('crop.name crop_name');
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $item=$result;
            foreach($fiscal_years_previous_sales as $fy)
            {
                if(isset($sales_previous[$fy['id']][$result['variety_id']]))
                {
                    $item['quantity_sale_'.$fy['id']]=$sales_previous[$fy['id']][$result['variety_id']]/1000;
                }
                else
                {
                    $item['quantity_sale_'.$fy['id']]=0;
                }
            }
            if(isset($items_old[$result['variety_id']]))
            {
                if($items_old[$result['variety_id']]['quantity_budget']>0)
                {
                    $item['quantity_budget_hom']=$items_old[$result['variety_id']]['quantity_budget'];
                }
                else
                {
                    $item['quantity_budget_hom']='';
                }
                if($items_old[$result['variety_id']]['quantity_budget_quantity_confirm']>0)
                {
                    $item['quantity_budget_quantity_confirm']=$items_old[$result['variety_id']]['quantity_budget_quantity_confirm'];
                }
                else
                {
                    $item['quantity_budget_quantity_confirm']='';
                }
            }
            else
            {
                $item['quantity_budget_hom']='';
                $item['quantity_budget_quantity_confirm']='';
            }
            if(isset($stocks[$result['variety_id']]))
            {
                if($stocks[$result['variety_id']]['current_stock']>0)
                {
                    $item['stock_current_hq']=$stocks[$result['variety_id']]['current_stock'];
                }
                else
                {
                    $item['stock_current_hq']='';
                }
            }
            else
            {
                $item['stock_current_hq']='';
            }
            $quantity_budget_needed=($item['stock_current_hq']-$item['quantity_budget_hom']);
            if($quantity_budget_needed>0)
            {
                $item['quantity_budget_needed']=$quantity_budget_needed;
            }
            else
            {
                $item['quantity_budget_needed']='';
            }
            $item['quantity_target_hom']='';
            $items[]=$item;
        }

        $this->json_return($items);
    }
    private function system_save_mgt_target_hom()
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
        //validation fiscal year
        if(!Budget_helper::check_validation_fiscal_year($item_head['fiscal_year_id']))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['fiscal_year_id'],'Invalid Fiscal year');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Fiscal Year';
            $this->json_return($ajax);
        }
        //validation target forward
        $info_budget_target=$this->get_info_budget_target($item_head['fiscal_year_id']);
        if(($info_budget_target['status_mgt_target_forward']==$this->config->item('system_status_forwarded')))
        {
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']='Budget Already Forwarded.';
                $this->json_return($ajax);
            }
        }
        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_hom_budget_target_hom'),'*',array('fiscal_year_id ='.$item_head['fiscal_year_id']));
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']]=$result;
        }
        $this->db->trans_start();  //DB Transaction Handle START
        $revision_count_mgt_budget_quantity_confirm_status=false;
        foreach($items as $variety_id=>$quantity_budget_quantity_confirm)
        {
            if(isset($items_old[$variety_id]))
            {
                if($items_old[$variety_id]['quantity_budget_quantity_confirm']!=$quantity_budget_quantity_confirm && $quantity_budget_quantity_confirm)
                {
                    $this->db->set('revision_count_budget_quantity_confirm','revision_count_budget_quantity_confirm+1',false);
                    $data['quantity_budget_quantity_confirm']=$quantity_budget_quantity_confirm;
                    /*$data['date_updated_budget']=$time;
                    $data['user_updated_budget']=$user->user_id;*/
                    Query_helper::update($this->config->item('table_bms_hom_budget_target_hom'),$data,array('id='.$items_old[$variety_id]['id']));
                    $revision_count_mgt_budget_quantity_confirm_status=true;
                }
            }
            else
            {
                $data=array();
                $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                $data['variety_id']=$variety_id;
                if($quantity_budget_quantity_confirm>0)
                {
                    $data['quantity_budget_quantity_confirm']=$quantity_budget_quantity_confirm;
                    $data['revision_count_budget_quantity_confirm']=1;
                    $revision_count_mgt_budget_quantity_confirm_status=true;
                }
                else
                {
                    $data['quantity_budget_quantity_confirm']=0;
                }
                /*$data['date_updated_budget'] = $time;
                $data['user_updated_budget'] = $user->user_id;*/
                Query_helper::add($this->config->item('table_bms_hom_budget_target_hom'),$data,false);
            }
            if($revision_count_mgt_budget_quantity_confirm_status)
            {
                $data=array();
                $data['date_updated_mgt_budget_quantity_confirm'] = $time;
                $data['user_updated_mgt_budget_quantity_confirm'] = $user->user_id;
                // problem:: revision count wrong insert ?
                //$this->db->set('revision_count_mgt_budget_quantity_confirm','revision_count_mgt_budget_quantity_confirm+1',false);
                Query_helper::update($this->config->item('table_bms_hom_budget_target'),$data,array('fiscal_year_id ='.$item_head['fiscal_year_id']));
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
    private function system_target_forward($fiscal_year_id=0)
    {
        $method='target_forward';
        if(isset($this->permissions['action7'])&&($this->permissions['action7']==1))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            //validation fiscal year
            if(!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
            {
                System_helper::invalid_try(__FUNCTION__,$fiscal_year_id,'Invalid Fiscal year');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Fiscal Year';
                $this->json_return($ajax);
            }
            //validation forward
            $info_budget_target=$this->get_info_budget_target($fiscal_year_id);
            if(($info_budget_target['status_mgt_target_forward']==$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='Target Already Forwarded.';
                $this->json_return($ajax);
            }
            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['fiscal_years_previous_sales']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
            $data['acres']=$this->get_acres();
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['title']="MGT Set Target For HOM";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/budget_forward",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/budget_forward/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    public function target_forward_items()
    {
        $items=array();
        $this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $fiscal_years_previous_sales=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
        $sales_previous=$this->get_sales_previous_years_division($fiscal_years_previous_sales);

        // get division budgeted quantity & division ids
        $results=Query_helper::get_info($this->config->item('table_bms_di_budget_target_division'),'*',array('fiscal_year_id ='.$fiscal_year_id));
        
        $division_ids[0]=0;
        $budget_divisions=array();
        foreach($results as $result)
        {
            $division_ids[$result['division_id']]=$result['division_id'];
            //$budget_divisions[$result['variety_id']]=$result;
            $budget_divisions[$result['division_id']][$result['variety_id']]=$result;
        }

        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_hom_budget_target_hom'),'*',array('fiscal_year_id ='.$fiscal_year_id));
        $old_items=array();
        foreach($results as $result)
        {
            $old_items[$result['variety_id']]=$result;
        }

        //variety lists
        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id = crop_type.crop_id','INNER');
        $this->db->select('crop.name crop_name');
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');
        $results=$this->db->get()->result_array();

        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;

        $type_total=$this->initialize_row($fiscal_years_previous_sales,$division_ids,'','','Total Type','');
        $crop_total=$this->initialize_row($fiscal_years_previous_sales,$division_ids,'','Total Crop','','');
        $grand_total=$this->initialize_row($fiscal_years_previous_sales,$division_ids,'Grand Total','','','');


        foreach($results as $result)
        {
            $info=$this->initialize_row($fiscal_years_previous_sales,$division_ids,$result['crop_name'],$result['crop_type_name'],$result['variety_name']);


            if(!$first_row)
            {
                if($prev_crop_name!=$result['crop_name'])
                {
                    $type_total['crop_name']=$prev_crop_name;
                    $type_total['crop_type_name']=$prev_type_name;
                    $crop_total['crop_name']=$prev_crop_name;
                    $items[]=$type_total;
                    $items[]=$crop_total;

                    $type_total=$this->reset_row($type_total);
                    $crop_total=$this->reset_row($crop_total);

                    $prev_crop_name=$result['crop_name'];
                    $prev_type_name=$result['crop_type_name'];

                }
                elseif($prev_type_name!=$result['crop_type_name'])
                {
                    $type_total['crop_name']=$prev_crop_name;
                    $type_total['crop_type_name']=$prev_type_name;
                    $items[]=$type_total;
                    $type_total=$this->reset_row($type_total);
                    //$info['crop_name']='';
                    $prev_type_name=$result['crop_type_name'];
                }
                else
                {
                    //$info['crop_name']='';
                    //$info['crop_type_name']='';
                }
            }
            else
            {
                $prev_crop_name=$result['crop_name'];
                $prev_type_name=$result['crop_type_name'];
                $first_row=false;
            }
            foreach($fiscal_years_previous_sales as $fy)
            {
                if(isset($sales_previous[$fy['id']][$result['variety_id']]))
                {
                    $info['quantity_sale_'.$fy['id']]=$sales_previous[$fy['id']][$result['variety_id']]/1000;
                    $type_total['quantity_sale_'.$fy['id']]+=$info['quantity_sale_'.$fy['id']];
                    $crop_total['quantity_sale_'.$fy['id']]+=$info['quantity_sale_'.$fy['id']];
                    $grand_total['quantity_sale_'.$fy['id']]+=$info['quantity_sale_'.$fy['id']];
                }
            }
            if(isset($old_items[$result['variety_id']]))
            {
                $info['quantity_budget']=$old_items[$result['variety_id']]['quantity_budget'];
                $info['quantity_budget_1']=$old_items[$result['variety_id']]['quantity_budget_1'];
                $info['quantity_budget_2']=$old_items[$result['variety_id']]['quantity_budget_2'];
                $info['quantity_budget_3']=$old_items[$result['variety_id']]['quantity_budget_3'];

                $type_total['quantity_budget']+=$info['quantity_budget'];
                $type_total['quantity_budget_1']+=$info['quantity_budget_1'];
                $type_total['quantity_budget_2']+=$info['quantity_budget_2'];
                $type_total['quantity_budget_3']+=$info['quantity_budget_3'];

                $crop_total['quantity_budget']+=$info['quantity_budget'];
                $crop_total['quantity_budget_1']+=$info['quantity_budget_1'];
                $crop_total['quantity_budget_2']+=$info['quantity_budget_2'];
                $crop_total['quantity_budget_3']+=$info['quantity_budget_3'];

                $grand_total['quantity_budget']+=$info['quantity_budget'];
                $grand_total['quantity_budget_1']+=$info['quantity_budget_1'];
                $grand_total['quantity_budget_2']+=$info['quantity_budget_2'];
                $grand_total['quantity_budget_3']+=$info['quantity_budget_3'];
            }
            $quantity_budget_division_total=0;
            foreach($division_ids as $division_id)
            {
                if(isset($budget_divisions[$division_id][$result['variety_id']]))
                {
                    $info['quantity_budget_division_'.$division_id]=$budget_divisions[$division_id][$result['variety_id']]['quantity_budget'];
                    $quantity_budget_division_total+=$info['quantity_budget_division_'.$division_id];
                    $type_total['quantity_budget_division_'.$division_id]+=$info['quantity_budget_division_'.$division_id];
                    $crop_total['quantity_budget_division_'.$division_id]+=$info['quantity_budget_division_'.$division_id];
                    $grand_total['quantity_budget_division_'.$division_id]+=$info['quantity_budget_division_'.$division_id];

                }
            }
            $info['quantity_budget_division_total']=$quantity_budget_division_total;
            $type_total['quantity_budget_division_total']+=$quantity_budget_division_total;
            $crop_total['quantity_budget_division_total']+=$quantity_budget_division_total;
            $grand_total['quantity_budget_division_total']+=$quantity_budget_division_total;
            $items[]=$info;
        }

        $items[]=$type_total;
        $items[]=$crop_total;
        $items[]=$grand_total;
        $this->json_return($items);
    }
    private function initialize_row($fiscal_years,$zone_ids,$crop_name,$crop_type_name,$variety_name)
    {
        $row=array();
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;

        $row['quantity_budget']=0;
        $row['quantity_budget_1']=0;
        $row['quantity_budget_2']=0;
        $row['quantity_budget_3']=0;
        $row['quantity_budget_division_total']=0;

        foreach($fiscal_years as $fy)
        {
            $row['quantity_sale_'.$fy['id']]=0;
        }
        foreach($zone_ids as $zone_id)
        {
            $row['quantity_budget_division_'.$zone_id]= 0;
        }

        return $row;
    }
    private function reset_row($info)
    {
        foreach($info as $key=>$r)
        {
            if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')))
            {
                $info[$key]=0;
            }
        }
        return $info;
    }
    private function system_save_forward_budget()
    {
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        if(!((isset($this->permissions['action7']) && ($this->permissions['action7']==1))))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if($item_head['status_budget_forward']!=$this->config->item('system_status_forwarded'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Select Forward Option.';
            $this->json_return($ajax);
        }
        //validation fiscal year
        if(!Budget_helper::check_validation_fiscal_year($item_head['fiscal_year_id']))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['fiscal_year_id'],'Invalid Fiscal year');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Fiscal Year';
            $this->json_return($ajax);
        }
        //validation forward
        $info_budget_target=$this->get_info_budget_target($item_head['fiscal_year_id']);
        if(($info_budget_target['status_budget_forward']==$this->config->item('system_status_forwarded')))
        {
            $ajax['status']=false;
            $ajax['system_message']='Budget Already Forwarded.';
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START
        $data=array();
        $data['status_budget_forward']=$item_head['status_budget_forward'];
        $data['date_budget_forwarded']=$time;
        $data['user_budget_forwarded']=$user->user_id;
        Query_helper::update($this->config->item('table_bms_hom_budget_target'),$data,array('id='.$info_budget_target['id']));

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
    private function get_info_budget_target($fiscal_year_id)
    {

        $info=Query_helper::get_info($this->config->item('table_bms_hom_budget_target'),'*',array('fiscal_year_id ='.$fiscal_year_id),1);
        /*if(!$info)
        {
            $user = User_helper::get_user();
            $data=array();
            $data['fiscal_year_id'] = $fiscal_year_id;
            $data['date_created'] = time();
            $data['user_created'] = $user->user_id;
            $id=Query_helper::add($this->config->item('table_bms_hom_budget_target'),$data);
            $info=Query_helper::get_info($this->config->item('table_bms_hom_budget_target'),'*',array('id ='.$id),1);
        }*/
        return $info;
    }
    private function get_sales_previous_years_hq($fiscal_years)
    {
        $sales=array();
        foreach($fiscal_years as $fy)
        {
            $this->db->from($this->config->item('table_pos_sale_details').' details');
            $this->db->select('details.variety_id');
            $this->db->select('SUM(details.pack_size*details.quantity) quantity_sale');
            
            $this->db->join($this->config->item('table_pos_sale').' sale','sale.id = details.sale_id','INNER');

            $this->db->where('sale.date_sale >=',$fy['date_start']);
            $this->db->where('sale.date_sale <=',$fy['date_end']);
            $this->db->where('sale.status',$this->config->item('system_status_active'));
            $this->db->group_by('details.variety_id');
            $results=$this->db->get()->result_array();
            foreach($results as $result)
            {
                $sales[$fy['id']][$result['variety_id']]=$result['quantity_sale'];
            }
        }
        return $sales;
    }
    private function get_acres($crop_id=0)
    {
        
        $this->db->from($this->config->item('table_login_setup_classification_type_acres').' acres');
        $this->db->select('SUM(acres.quantity_acres) quantity',false);
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=acres.type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name,crop_type.quantity_kg_acre');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        $this->db->order_by('crop.ordering');
        $this->db->order_by('crop.id');
        $this->db->order_by('crop_type.ordering');
        $this->db->order_by('crop_type.id');
        if($crop_id>0)
        {
            $this->db->where('crop.id',$crop_id);
        }
        $this->db->group_by('crop_type.id');
        $results=$this->db->get()->result_array();
        return $results;
    }


}
