<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Budget_national_budget_target extends Root_Controller
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
        elseif($action=="edit_target_hom")
        {
            $this->system_edit_target_hom($id);
        }
        elseif($action=="get_items_edit_target_hom")
        {
            $this->system_get_items_edit_target_hom();
        }
        elseif($action=="save_target_hom")
        {
            $this->system_save_target_hom();
        }
        elseif($action=="forward_target_hom")
        {
            $this->system_forward_target_hom($id);
        }
        elseif($action=="save_forward_target_hom")
        {
            $this->system_save_forward_target_hom();
        }
        elseif($action=="edit_target_hom_next_year")
        {
            $this->system_edit_target_hom_next_year($id);
        }
        elseif($action=="get_items_edit_target_hom_next_year")
        {
            $this->system_get_items_edit_target_hom_next_year();
        }
        elseif($action=="save_target_hom_next_year")
        {
            $this->system_save_target_hom_next_year();
        }
        elseif($action=="forward_target_hom_next_year")
        {
            $this->system_forward_target_hom_next_year($id);
        }
        elseif($action=="save_forward_target_hom_next_year")
        {
            $this->system_save_forward_target_hom_next_year();
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
            $data['revision_count_principal_quantity_confirm']= 1;
            $data['revision_count_quantity_target_hom']= 1;
            $data['status_forward_hom_target']= 1;
            $data['status_forward_hom_target_next_year']= 1;
        }
        else if($method=='edit_target_hom' || $method=='forward_target_hom')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            //more data
            $data['stock_current_hq']= 1;
            $data['quantity_budget_hom']= 1;
            $data['quantity_budget_needed']= 1;
            $data['quantity_principal_quantity_confirm']= 1;
            $data['quantity_target_available']= 1;
            $data['quantity_target_hom']= 1;
        }
        else if($method=='edit_target_hom_next_year' || $method=='forward_target_hom_next_year')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            //more data
            
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
            $data['title']="Yearly National Budget :: Principal Qty Confirm & Target List";
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
        $items=array();
        $this->db->from($this->config->item('table_bms_hom_budget_target').' item');
        $this->db->select('item.fiscal_year_id');
        $this->db->select('item.status_forward_hom_target');
        $this->db->select('item.status_forward_hom_target_next_year');

        $this->db->join($this->config->item('table_bms_hom_budget_target_hom').' details','details.fiscal_year_id=item.fiscal_year_id','INNER');
        $this->db->select('MAX(details.revision_count_principal_quantity_confirm) revision_count_principal_quantity_confirm');
        $this->db->select('MAX(details.revision_count_quantity_target_hom) revision_count_quantity_target_hom');
        
        $this->db->join($this->config->item('table_login_basic_setup_fiscal_year').' fy','fy.id=item.fiscal_year_id','INNER');
        $this->db->select('fy.name fiscal_year');
        
        $this->db->where('item.status_budget_forward',$this->config->item('system_status_forwarded'));
        $this->db->group_by('item.fiscal_year_id');
        $items=$this->db->get()->result_array();
        $this->json_return($items);
    }
    private function system_edit_target_hom($fiscal_year_id=0)
    {
        $method='edit_target_hom';
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
            // Checking HOM budget forward.
            if(($get_info_budget_target['status_budget_forward']!=$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='HOM Budget Not Forwarded.';
                $this->json_return($ajax);
            }
            // Checking Target forward.
            if(($get_info_budget_target['status_forward_hom_target']==$this->config->item('system_status_forwarded')))
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
            $data['title']="Yearly National Budget :: Principal Qty Confirm & Target Edit (For All Variety)";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_target_hom",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_target_hom/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_edit_target_hom()
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
                $item['quantity_sale_'.$fy['id']]=0;
                if(isset($sales_previous[$fy['id']][$result['variety_id']]))
                {
                    $item['quantity_sale_'.$fy['id']]=$sales_previous[$fy['id']][$result['variety_id']]/1000;
                }
            }

            $item['quantity_budget_hom']='';
            $item['quantity_principal_quantity_confirm']='';
            $item['quantity_target_hom']='';
            if(isset($items_old[$result['variety_id']]))
            {
                $item['quantity_budget_hom']='';
                if($items_old[$result['variety_id']]['quantity_budget']>0)
                {
                    $item['quantity_budget_hom']=$items_old[$result['variety_id']]['quantity_budget'];
                }

                $item['quantity_principal_quantity_confirm']='';
                if($items_old[$result['variety_id']]['quantity_principal_quantity_confirm']>0)
                {
                    $item['quantity_principal_quantity_confirm']=$items_old[$result['variety_id']]['quantity_principal_quantity_confirm'];
                }

                $item['quantity_target_hom']='';
                if($items_old[$result['variety_id']]['quantity_target_hom']>0)
                {
                    $item['quantity_target_hom']=$items_old[$result['variety_id']]['quantity_target_hom'];
                }
            }
            
            $item['stock_current_hq']='';
            if(isset($stocks[$result['variety_id']]))
            {    
                if($stocks[$result['variety_id']]['current_stock']>0)
                {
                    $item['stock_current_hq']=$stocks[$result['variety_id']]['current_stock'];
                }
            }
            
            $item['quantity_budget_needed']='';
            $quantity_budget_needed=($item['quantity_budget_hom']-$item['stock_current_hq']);
            if($quantity_budget_needed>0)
            {
                $item['quantity_budget_needed']=$quantity_budget_needed;
            }
            $item['quantity_target_available']=($item['stock_current_hq']+$item['quantity_principal_quantity_confirm']);
            $items[]=$item;
        }

        $this->json_return($items);
    }
    private function system_save_target_hom()
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
        //validation forward
        $get_info_budget_target=$this->get_info_budget_target($item_head['fiscal_year_id']);
        // Checking HOM budget forward.
        if(($get_info_budget_target['status_budget_forward']!=$this->config->item('system_status_forwarded')))
        {
            $ajax['status']=false;
            $ajax['system_message']='HOM Budget Not Forwarded.';
            $this->json_return($ajax);
        }
        // Checking Target forward.
        if(($get_info_budget_target['status_forward_hom_target']==$this->config->item('system_status_forwarded')))
        {
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']='Target Already Forwarded.';
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
        foreach($items as $variety_id=>$item)
        {
            if(isset($items_old[$variety_id]))
            {
                if($items_old[$variety_id]['quantity_principal_quantity_confirm']!=$item['quantity_principal_quantity_confirm'] && $item['quantity_principal_quantity_confirm'])
                {
                    $data['quantity_principal_quantity_confirm']=$item['quantity_principal_quantity_confirm'];
                    $data['date_updated_principal_quantity_confirm'] = $time;
                    $data['user_updated_principal_quantity_confirm'] = $user->user_id;
                    $this->db->set('revision_count_principal_quantity_confirm','revision_count_principal_quantity_confirm+1',false);
                    Query_helper::update($this->config->item('table_bms_hom_budget_target_hom'),$data,array('id='.$items_old[$variety_id]['id']));
                }
                if($items_old[$variety_id]['quantity_target_hom']!=$item['quantity_target_hom'] && $item['quantity_target_hom'])
                {
                    $data['quantity_target_hom']=$item['quantity_target_hom'];
                    $data['date_updated_hom_target'] = $time;
                    $data['user_updated_hom_target'] = $user->user_id;
                    $this->db->set('revision_count_quantity_target_hom','revision_count_quantity_target_hom+1',false);
                    Query_helper::update($this->config->item('table_bms_hom_budget_target_hom'),$data,array('id='.$items_old[$variety_id]['id']));
                }
            }
            else
            {
                $data=array();
                $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                $data['variety_id']=$variety_id;
                if($item['quantity_principal_quantity_confirm']>0)
                {
                    $data['quantity_principal_quantity_confirm']=$item['quantity_principal_quantity_confirm'];
                    $data['revision_count_principal_quantity_confirm']=1;
                }
                else
                {
                    $data['quantity_principal_quantity_confirm']=0;
                }
                $data['date_updated_principal_quantity_confirm'] = $time;
                $data['user_updated_principal_quantity_confirm'] = $user->user_id;
                if($item['quantity_target_hom']>0)
                {
                    $data['quantity_target_hom']=$item['quantity_target_hom'];
                    $data['revision_count_quantity_target_hom']=1;
                }
                else
                {
                    $data['quantity_target_hom']=0;
                }
                $data['date_updated_hom_target'] = $time;
                $data['user_updated_hom_target'] = $user->user_id;
                Query_helper::add($this->config->item('table_bms_hom_budget_target_hom'),$data,false);
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
    private function system_forward_target_hom($fiscal_year_id=0)
    {
        $method='forward_target_hom';
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
            // Checking HOM budget forward.
            if(($info_budget_target['status_budget_forward']!=$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='HOM Budget Not Forwarded.';
                $this->json_return($ajax);
            }
            // Checking Target forward.
            if(($info_budget_target['status_forward_hom_target']==$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='Target Already Forwarded.';
                $this->json_return($ajax);
            }
            
            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['fiscal_years_previous_sales']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
            $data['acres']=$this->get_acres();
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['title']="Yearly National Target Forward For HOM";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/forward_target_hom",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/forward_target_hom/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    public function get_items_forward_target_hom()
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

        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;

        $type_total=$this->initialize_row($fiscal_years_previous_sales,'','','Total Type','');
        $crop_total=$this->initialize_row($fiscal_years_previous_sales,'','Total Crop','','');
        $grand_total=$this->initialize_row($fiscal_years_previous_sales,'Grand Total','','','');


        foreach($results as $result)
        {
            $info=$this->initialize_row($fiscal_years_previous_sales,$result['crop_name'],$result['crop_type_name'],$result['variety_name']);


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
            if(isset($items_old[$result['variety_id']]))
            {
                if($items_old[$result['variety_id']]['quantity_budget']>0)
                {
                    $info['quantity_budget_hom']=$items_old[$result['variety_id']]['quantity_budget'];
                    $type_total['quantity_budget_hom']+=$info['quantity_budget_hom'];
                    $crop_total['quantity_budget_hom']+=$info['quantity_budget_hom'];
                    $grand_total['quantity_budget_hom']+=$info['quantity_budget_hom'];
                }

                if($items_old[$result['variety_id']]['quantity_principal_quantity_confirm']>0)
                {
                    $info['quantity_principal_quantity_confirm']=$items_old[$result['variety_id']]['quantity_principal_quantity_confirm'];
                    $type_total['quantity_principal_quantity_confirm']+=$info['quantity_principal_quantity_confirm'];
                    $crop_total['quantity_principal_quantity_confirm']+=$info['quantity_principal_quantity_confirm'];
                    $grand_total['quantity_principal_quantity_confirm']+=$info['quantity_principal_quantity_confirm'];
                }

                if($items_old[$result['variety_id']]['quantity_target_hom']>0)
                {
                    $info['quantity_target_hom']=$items_old[$result['variety_id']]['quantity_target_hom'];
                    $type_total['quantity_target_hom']+=$info['quantity_target_hom'];
                    $crop_total['quantity_target_hom']+=$info['quantity_target_hom'];
                    $grand_total['quantity_target_hom']+=$info['quantity_target_hom'];
                }
            }

            if(isset($stocks[$result['variety_id']]))
            {    
                if($stocks[$result['variety_id']]['current_stock']>0)
                {
                    $info['stock_current_hq']=$stocks[$result['variety_id']]['current_stock'];
                    $type_total['stock_current_hq']+=$info['stock_current_hq'];
                    $crop_total['stock_current_hq']+=$info['stock_current_hq'];
                    $grand_total['stock_current_hq']+=$info['stock_current_hq'];
                }
            }
            $quantity_budget_needed=($info['quantity_budget_hom']-$info['stock_current_hq']);
            if($quantity_budget_needed>0)
            {
                $info['quantity_budget_needed']=$quantity_budget_needed;
                $type_total['quantity_budget_needed']+=$info['quantity_budget_needed'];
                $crop_total['quantity_budget_needed']+=$info['quantity_budget_needed'];
                $grand_total['quantity_budget_needed']+=$info['quantity_budget_needed'];
            }
            $quantity_target_available=($info['stock_current_hq']+$info['quantity_principal_quantity_confirm']);
            $info['quantity_target_available']=$quantity_target_available;
            $type_total['quantity_target_available']+=$info['quantity_target_available'];
            $crop_total['quantity_target_available']+=$info['quantity_target_available'];
            $grand_total['quantity_target_available']+=$info['quantity_target_available'];

            $items[]=$info;
        }

        $items[]=$type_total;
        $items[]=$crop_total;
        $items[]=$grand_total;
        $this->json_return($items);
    }
    private function initialize_row($fiscal_years,$crop_name,$crop_type_name,$variety_name)
    {
        $row=array();
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;
        foreach($fiscal_years as $fy)
        {
            $row['quantity_sale_'.$fy['id']]=0;
        }
        $row['quantity_budget_hom']=0;
        $row['quantity_principal_quantity_confirm']=0;
        $row['quantity_target_hom']=0;
        $row['stock_current_hq']=0;
        $row['quantity_budget_needed']=0;
        $row['quantity_target_available']=0;
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
    private function system_save_forward_target_hom()
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
        if($item_head['status_forward_hom_target']!=$this->config->item('system_status_forwarded'))
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
        $get_info_budget_target=$this->get_info_budget_target($item_head['fiscal_year_id']);
        // Checking HOM budget forward.
        if(($get_info_budget_target['status_budget_forward']!=$this->config->item('system_status_forwarded')))
        {
            $ajax['status']=false;
            $ajax['system_message']='HOM Budget Not Forwarded.';
            $this->json_return($ajax);
        }
        // Checking Target forward.
        if(($get_info_budget_target['status_forward_hom_target']==$this->config->item('system_status_forwarded')))
        {
            $ajax['status']=false;
            $ajax['system_message']='Target Already Forwarded.';
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START
        $data=array();
        $data['status_forward_hom_target']=$item_head['status_forward_hom_target'];
        $data['date_forwarded_hom_target']=$time;
        $data['user_forwarded_hom_target']=$user->user_id;
        Query_helper::update($this->config->item('table_bms_hom_budget_target'),$data,array('id='.$get_info_budget_target['id']));

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
    private function system_edit_target_hom_next_year($fiscal_year_id=0)
    {
        $method='edit_target_hom_next_year';
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
            // Checking HOM budget forward.
            if(($get_info_budget_target['status_budget_forward']!=$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='HOM Budget Not Forwarded.';
                $this->json_return($ajax);
            }
            // Checking Target forward.
            if(($get_info_budget_target['status_forward_hom_target_next_year']==$this->config->item('system_status_forwarded')))
            {
                if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='HOM Next Year Target Already Forwarded.';
                    $this->json_return($ajax);
                }
            }
            
            $data['fiscal_years_next_budgets']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['acres']=$this->get_acres();

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="HOM Target Edit Next 3 Years (For All Variety)";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_target_hom_next_year",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_target_hom_next_year/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_edit_target_hom_next_year()
    {
        $items=array();
        //$this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        // get next 3 years budget
        $fiscal_years_next_budgets=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);
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
            $item['quantity_budget']='';

            $serial=0;
            foreach ($fiscal_years_next_budgets as $budget)
            {
                ++$serial;
                $item['quantity_prediction_'.$serial]='';
                $item['quantity_target_hom_'.$serial]='';
            }
            if(isset($items_old[$result['variety_id']]))
            {
                if($items_old[$result['variety_id']]['quantity_budget']>0)
                {
                    $item['quantity_budget']=$items_old[$result['variety_id']]['quantity_budget'];
                }

                $serial=0;
                foreach ($fiscal_years_next_budgets as $budget)
                {
                    ++$serial;
                    if($items_old[$result['variety_id']]['quantity_prediction_'.$serial]>0)
                    {
                        $item['quantity_prediction_'.$serial]=$items_old[$result['variety_id']]['quantity_prediction_'.$serial];
                    }
                    if($items_old[$result['variety_id']]['quantity_target_hom_'.$serial]>0)
                    {
                        $item['quantity_target_hom_'.$serial]=$items_old[$result['variety_id']]['quantity_target_hom_'.$serial];
                    }
                }
            }
            
            $items[]=$item;
        }

        $this->json_return($items);
    }
    private function system_save_target_hom_next_year()
    {
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        $items_quantity_target=$this->input->post('items_quantity_target');

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
        //validation forward
        $get_info_budget_target=$this->get_info_budget_target($item_head['fiscal_year_id']);
            // Checking HOM budget forward.
        if(($get_info_budget_target['status_budget_forward']!=$this->config->item('system_status_forwarded')))
        {
            $ajax['status']=false;
            $ajax['system_message']='HOM Budget Not Forwarded.';
            $this->json_return($ajax);
        }
        // Checking Target forward.
        if(($get_info_budget_target['status_forward_hom_target_next_year']==$this->config->item('system_status_forwarded')))
        {
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']='HOM Next Year Target Already Forwarded.';
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

        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.name crop_type_name');

        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $variety_id=$result['variety_id'];
            $quantity_target_hom_1=0;
            $quantity_target_hom_2=0;
            $quantity_target_hom_3=0;
            if(isset($items_quantity_target[1][$variety_id]))
            {
                $quantity_target_hom_1=$items_quantity_target[1][$variety_id];
            }
            if(isset($items_quantity_target[2][$variety_id]))
            {
                $quantity_target_hom_2=$items_quantity_target[2][$variety_id];
            }
            if(isset($items_quantity_target[3][$variety_id]))
            {
                $quantity_target_hom_3=$items_quantity_target[3][$variety_id];
            }
            if(isset($items_old[$variety_id]))
            {
                if(($items_old[$variety_id]['quantity_target_hom_1'] != $quantity_target_hom_1) || ($items_old[$variety_id]['quantity_target_hom_2'] != $quantity_target_hom_2) || ($items_old[$variety_id]['quantity_target_hom_3'] != $quantity_target_hom_3))
                {
                    $data['quantity_target_hom_1']=$quantity_target_hom_1;
                    $data['quantity_target_hom_2']=$quantity_target_hom_2;
                    $data['quantity_target_hom_3']=$quantity_target_hom_3;
                    $data['date_updated_hom_next_year_target']=$time;
                    $data['user_updated_hom_next_year_target']=$user->user_id;
                    Query_helper::update($this->config->item('table_bms_hom_budget_target_hom'),$data,array('id='.$items_old[$variety_id]['id']));
                }
            }
            else
            {
                $data=array();
                $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                $data['variety_id']=$variety_id;
                $data['quantity_target_hom_1']=$quantity_target_hom_1;
                $data['quantity_target_hom_2']=$quantity_target_hom_2;
                $data['quantity_target_hom_3']=$quantity_target_hom_3;
                $data['date_updated_hom_next_year_target']=$time;
                $data['user_updated_hom_next_year_target']=$user->user_id;  
                Query_helper::add($this->config->item('table_bms_hom_budget_target_hom'),$data,false);
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
    private function system_forward_target_hom_next_year($fiscal_year_id=0)
    {
        $method='forward_target_hom_next_year';
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
            // Checking HOM budget forward.
            if(($info_budget_target['status_budget_forward']!=$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='HOM Budget Not Forwarded.';
                $this->json_return($ajax);
            }
            // Checking Target forward.
            if(($info_budget_target['status_forward_hom_target_next_year']==$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='HOM Next 3 Years Target Already Forwarded.';
                $this->json_return($ajax);
            }
            
            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['fiscal_years_next_budgets']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);
            $data['acres']=$this->get_acres();
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['title']="HOM Target Forward Next 3 Years (For All Variety)";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/forward_target_hom_next_year",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/forward_target_hom_next_year/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    public function get_items_forward_target_hom_next_year()
    {
        $items=array();
        //$this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        // get next 3 years budget
        $fiscal_years_next_budgets=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);

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

        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;

        $type_total=$this->initialize_row_next_year($fiscal_years_next_budgets,'','','Total Type','');
        $crop_total=$this->initialize_row_next_year($fiscal_years_next_budgets,'','Total Crop','','');
        $grand_total=$this->initialize_row_next_year($fiscal_years_next_budgets,'Grand Total','','','');

        foreach($results as $result)
        {
            $info=$this->initialize_row_next_year($fiscal_years_next_budgets,$result['crop_name'],$result['crop_type_name'],$result['variety_name']);

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
            
            if(isset($items_old[$result['variety_id']]))
            {
                $serial=0;
                foreach ($fiscal_years_next_budgets as $budget)
                {
                    ++$serial;
                    if($items_old[$result['variety_id']]['quantity_prediction_'.$serial]>0)
                    {
                        $info['quantity_prediction_'.$serial]=$items_old[$result['variety_id']]['quantity_prediction_'.$serial];
                        $type_total['quantity_prediction_'.$serial]+=$info['quantity_prediction_'.$serial];
                        $crop_total['quantity_prediction_'.$serial]+=$info['quantity_prediction_'.$serial];
                        $grand_total['quantity_prediction_'.$serial]+=$info['quantity_prediction_'.$serial];
                    }
                    if($items_old[$result['variety_id']]['quantity_target_hom_'.$serial]>0)
                    {
                        $info['quantity_target_hom_'.$serial]=$items_old[$result['variety_id']]['quantity_target_hom_'.$serial];
                        $type_total['quantity_target_hom_'.$serial]+=$info['quantity_target_hom_'.$serial];
                        $crop_total['quantity_target_hom_'.$serial]+=$info['quantity_target_hom_'.$serial];
                        $grand_total['quantity_target_hom_'.$serial]+=$info['quantity_target_hom_'.$serial];
                    }
                }
            }
            $items[]=$info;
        }

        $items[]=$type_total;
        $items[]=$crop_total;
        $items[]=$grand_total;
        $this->json_return($items);
    }
    private function initialize_row_next_year($fiscal_years,$crop_name,$crop_type_name,$variety_name)
    {
        $row=array();
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;
        $serial=0;
        foreach($fiscal_years as $fy)
        {
            ++$serial;
            $row['quantity_prediction_'.$serial]=0;
            $row['quantity_target_hom_'.$serial]=0;
        }
        return $row;
    }
    private function system_save_forward_target_hom_next_year()
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
        if($item_head['status_forward_hom_target_next_year']!=$this->config->item('system_status_forwarded'))
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
        $get_info_budget_target=$this->get_info_budget_target($item_head['fiscal_year_id']);
        // Checking HOM budget forward.
        if(($get_info_budget_target['status_budget_forward']!=$this->config->item('system_status_forwarded')))
        {
            $ajax['status']=false;
            $ajax['system_message']='HOM Budget Not Forwarded.';
            $this->json_return($ajax);
        }
        // Checking Target forward.
        if(($get_info_budget_target['status_forward_hom_target_next_year']==$this->config->item('system_status_forwarded')))
        {
            $ajax['status']=false;
            $ajax['system_message']='HOM Next 3 Years Target Already Forwarded.';
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START
        $data=array();
        $data['status_forward_hom_target_next_year']=$item_head['status_forward_hom_target_next_year'];
        $data['date_forwarded_hom_target_next_year']=$time;
        $data['user_forwarded_hom_target_next_year']=$user->user_id;
        Query_helper::update($this->config->item('table_bms_hom_budget_target'),$data,array('id='.$get_info_budget_target['id']));

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
