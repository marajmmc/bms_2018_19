<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Budget_di_budget_target extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $common_view_location;
    public $locations;
    public $user_divisions;
    public $user_division_ids;

    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission(get_class());
        $this->controller_url=strtolower(get_class());
        $this->common_view_location='budget_zi_budget_target';
        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->user_division_ids=array();
        $this->user_divisions=User_helper::get_assigned_divisions($this->locations['division_id']);
        
        if(sizeof($this->user_divisions)>0)
        {
            foreach($this->user_divisions as $row)
            {
                $this->user_division_ids[]=$row['division_id'];
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_DIVISION_NOT_ASSIGNED');
            $this->json_return($ajax);
        }
        $this->load->helper('budget');
        $this->lang->load('budget');

    }
    public function index($action="list", $id=0,$id1=0,$id2=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }

        elseif($action=="list_budget_division")
        {
            $this->system_list_budget_division($id,$id1);
        }
        elseif($action=="get_items_budget_division")
        {
            $this->system_get_items_budget_division();
        }
        elseif($action=="edit_budget_division")
        {
            $this->system_edit_budget_division($id,$id1,$id2);
        }
        elseif($action=="get_items_edit_budget_division")
        {
            $this->system_get_items_edit_budget_division();
        }
        elseif($action=="save_budget_division")
        {
            $this->system_save_budget_division();
        }
        elseif($action=="budget_forward")
        {
            $this->system_budget_forward($id,$id1);
        }
        elseif($action=="save_forward_budget")
        {
            $this->system_save_forward_budget();
        }

        elseif($action=="list_target_zi")
        {
            $this->system_list_target_zi($id,$id1);
        }
        elseif($action=="get_items_target_zi")
        {
            $this->system_get_items_target_zi();
        }
        elseif($action=="assign_target_zi")
        {
            $this->system_assign_target_zi($id,$id1,$id2);
        }
        elseif($action=="get_items_assign_target_zi")
        {
            $this->system_get_items_assign_target_zi();
        }
        elseif($action=="save_target_zi")
        {
            $this->system_save_target_zi();
        }
        elseif($action=="assign_target_zi_forward")
        {
            $this->system_assign_target_zi_forward($id,$id1);
        }
        elseif($action=="get_items_forward_assign_target_zi")
        {
            $this->system_get_items_forward_assign_target_zi();
        }
        elseif($action=="save_target_zi_forward")
        {
            $this->system_save_target_zi_forward();
        }

        elseif($action=="list_target_zi_next_year")
        {
            $this->system_list_target_zi_next_year($id,$id1);
        }
        elseif($action=="get_items_target_zi_next_year")
        {
            $this->system_get_items_target_zi_next_year();
        }
        elseif($action=="assign_target_zi_next_year")
        {
            $this->system_assign_target_zi_next_year($id,$id1,$id2);
        }
        elseif($action=="get_items_assign_target_zi_next_year")
        {
            $this->system_get_items_assign_target_zi_next_year();
        }
        elseif($action=="save_target_zi_next_year")
        {
            $this->system_save_target_zi_next_year();
        }
        elseif($action=="assign_target_zi_forward_next_year")
        {
            $this->system_assign_target_zi_forward_next_year($id,$id1);
        }
        elseif($action=="get_items_forward_assign_target_zi_next_year")
        {
            $this->system_get_items_forward_assign_target_zi_next_year();
        }
        elseif($action=="save_target_zi_forward_next_year")
        {
            $this->system_save_target_zi_forward_next_year();
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
            $data['division_id']= 1;
            $data['division_name']= 1;
            $data['status_budget_forward']= 1;
            $data['status_target_zi_forward']= 1;
            $data['status_target_zi_next_year_forward']= 1;
        }
        else if($method=='list_budget_division')
        {
            $data['crop_id']= 1;
            $data['crop_name']= 1;
            $data['number_of_variety_active']= 1;
            $data['number_of_variety_budgeted']= 1;
            $data['number_of_variety_budget_due']= 1;
        }
        else if($method=='edit_budget_division')
        {
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            //more data
            $data['quantity_budget_zone_total']= 1;
            $data['quantity_budget']= 1;
        }
        else if($method=='edit_budget_division')
        {
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            //more data
            $data['quantity_budget_zone_total']= 1;
            $data['quantity_budget']= 1;
        }
        else if($method=='budget_forward')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            //more datas
            $data['quantity_budget_division']= 1;
            $data['quantity_budget_zone_total']= 1;
        }
        else if($method=='list_target_zi')
        {
            $data['crop_id']= 1;
            $data['crop_name']= 1;
            $data['revision_count_target']= 1;
        }
        else if($method=='assign_target_zi')
        {
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            //more data
            $data['quantity_target_di']= 1;
            $data['quantity_target_zi_total']= 1;
        }
        else if($method=='assign_target_zi_forward')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            //more data
            $data['quantity_target_di']= 1;
            $data['quantity_target_zi_total']= 1;
        }
        else if($method=='assign_target_zi_next_year')
        {
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            //more data
            $data['quantity_target_di']= 1;
            $data['quantity_target_zi_total']= 1;
        }
        else if($method=='assign_target_zi_next_year_forward')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            //more data
            $data['quantity_target_di']= 1;
            $data['quantity_target_zi_total']= 1;
            $data['quantity_prediction_total_zi']= 1;
        }

        return $data;
    }

    // Budget & Target main list
    private function system_list()
    {
        //$user = User_helper::get_user();
        $method='list';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="Yearly DI Budget";
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
        $this->db->from($this->config->item('table_bms_di_budget_target').' budget_target');
        $this->db->select('budget_target.status_budget_forward, budget_target.status_target_zi_forward, budget_target.status_target_zi_next_year_forward');
        $this->db->select('budget_target.fiscal_year_id');
        $this->db->select('budget_target.division_id');
        $this->db->where_in('budget_target.division_id',$this->user_division_ids);
        $results=$this->db->get()->result_array();
        $budget_target=array();
        foreach($results as $result)
        {
            $budget_target[$result['fiscal_year_id']][$result['division_id']]=$result;
        }
        $items=array();
        foreach($fiscal_years as $fy)
        {
            foreach($this->user_divisions as $division)
            {
                $data=array();
                $data['fiscal_year_id']=$fy['id'];
                $data['fiscal_year']=$fy['text'];
                $data['division_id']=$division['division_id'];
                $data['division_name']=$division['division_name'];
                $data['status_budget_forward']=$this->config->item('system_status_pending');
                $data['status_target_zi_forward']=$this->config->item('system_status_pending');
                $data['status_target_zi_next_year_forward']=$this->config->item('system_status_pending');
                if(isset($budget_target[$fy['id']][$division['division_id']]))
                {
                    $data['status_budget_forward']=$budget_target[$fy['id']][$division['division_id']]['status_budget_forward'];
                    $data['status_target_zi_forward']=$budget_target[$fy['id']][$division['division_id']]['status_target_zi_forward'];
                    $data['status_target_zi_next_year_forward']=$budget_target[$fy['id']][$division['division_id']]['status_target_zi_next_year_forward'];
                }

                $items[]=$data;
            }
        }
        $this->json_return($items);
    }

    // Budget Edit
    private function system_list_budget_division($fiscal_year_id=0,$division_id=0)
    {
        //$user = User_helper::get_user();
        $method='list_budget_division';
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            if(!($division_id>0))
            {
                $division_id=$this->input->post('division_id');
            }
            //validation fiscal year
            if(!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
            {
                System_helper::invalid_try(__FUNCTION__,$fiscal_year_id,'Invalid Fiscal year');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Fiscal Year';
                $this->json_return($ajax);
            }
            //validation assigned division
            if(!in_array($division_id, $this->user_division_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$division_id,'Division Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Division.';
                $this->json_return($ajax);
            }
            //validation forward
            $info_budget=$this->get_info_budget_target($fiscal_year_id,$division_id);
            if(($info_budget['status_budget_forward']==$this->config->item('system_status_forwarded')))
            {
                if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Budget Already Forwarded.';
                    $this->json_return($ajax);
                }
            }

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['division']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),'*',array('id ='.$division_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['title']="DI Yearly Budget Crop list";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['division_id']=$division_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_budget_division",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list_budget_division/'.$fiscal_year_id.'/'.$division_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_budget_division()
    {
        $items=array();
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $division_id=$this->input->post('division_id');

        //get budget revision
        $this->db->from($this->config->item('table_bms_di_budget_target_division').' budget_target_division');
        $this->db->select('SUM(CASE WHEN budget_target_division.quantity_budget>0 then 1 ELSE 0 END) number_of_variety_budgeted',false);

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = budget_target_division.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.crop_id');
        $this->db->where('budget_target_division.fiscal_year_id',$fiscal_year_id);
        $this->db->where('budget_target_division.division_id',$division_id);
        $this->db->group_by('crop_type.crop_id');
        $results=$this->db->get()->result_array();
        $budgeted=array();
        foreach($results as $result)
        {
            $budgeted[$result['crop_id']]=$result['number_of_variety_budgeted'];
        }

        $varieties=Budget_helper::get_crop_type_varieties();
        $crops=array();
        foreach($varieties as $variety)
        {
            $crops[$variety['crop_id']]['crop_id']=$variety['crop_id'];
            $crops[$variety['crop_id']]['crop_name']=$variety['crop_name'];
            if(isset($crops[$variety['crop_id']]['number_of_variety_active']))
            {
                $crops[$variety['crop_id']]['number_of_variety_active']+=1;
            }
            else
            {
                $crops[$variety['crop_id']]['number_of_variety_active']=1;
            }
        }
        //crop list
        //$results=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id crop_id','name crop_name'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC','id ASC'));
        foreach($crops as $crop)
        {
            $item=$crop;
            $item['number_of_variety_active']=$crop['number_of_variety_active'];
            $item['number_of_variety_budgeted']=0;
            if(isset($budgeted[$crop['crop_id']]))
            {
                $item['number_of_variety_budgeted']=$budgeted[$crop['crop_id']];
            }
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_edit_budget_division($fiscal_year_id=0,$division_id=0,$crop_id=0)
    {
        //$user = User_helper::get_user();
        $method='edit_budget_division';
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            if(!($division_id>0))
            {
                $division_id=$this->input->post('division_id');
            }
            if(!($crop_id>0))
            {
                $crop_id=$this->input->post('crop_id');
            }
            //validation fiscal year
            if(!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
            {
                System_helper::invalid_try(__FUNCTION__,$fiscal_year_id,'Invalid Fiscal year');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Fiscal Year';
                $this->json_return($ajax);
            }
            //validation assigned division
            if(!in_array($division_id, $this->user_division_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$division_id,'Division Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Division.';
                $this->json_return($ajax);
            }
            //validation forward
            $info_budget_target=$this->get_info_budget_target($fiscal_year_id,$division_id);
            if(($info_budget_target['status_budget_forward']==$this->config->item('system_status_forwarded')))
            {
                if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Budget Already Forwarded.';
                    $this->json_return($ajax);
                }
            }
            $data['fiscal_years_previous_sales']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['division']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),'*',array('id ='.$division_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['crop']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),'*',array('id ='.$crop_id),1);
            $data['zones']=User_helper::get_assigned_zones($division_id);
            $data['acres']=$this->get_acres($division_id,$crop_id);

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="DI Yearly Budget for (".$data['crop']['name'].')';
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['division_id']=$division_id;
            $data['options']['crop_id']=$crop_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_budget_division",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_budget_division/'.$fiscal_year_id.'/'.$division_id.'/'.$crop_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_edit_budget_division()
    {
        $items=array();
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $division_id=$this->input->post('division_id');
        $crop_id=$this->input->post('crop_id');

        //Zone budget & get zone wise forward status
        $zone_ids[0]=0;
        $zones=User_helper::get_assigned_zones($division_id);
        foreach ($zones as $zone) 
        {
            $zone_ids[$zone['zone_id']]=$zone['zone_id'];
        }

        $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' budget_target_zone');
        $this->db->select('budget_target_zone.*');
        $this->db->where('budget_target_zone.fiscal_year_id',$fiscal_year_id);
        $this->db->where_in('budget_target_zone.zone_id',$zone_ids);
        $this->db->join($this->config->item('table_bms_zi_budget_target').' budget_target','budget_target.fiscal_year_id=budget_target_zone.fiscal_year_id AND budget_target.zone_id=budget_target_zone.zone_id','INNER');
        $this->db->select('budget_target.status_budget_forward');
        $results=$this->db->get()->result_array();
        $budget_zones=array();
        foreach($results as $result)
        {
            $budget_zones[$result['zone_id']][$result['variety_id']]=$result;
        }

        $fiscal_years_previous_sales=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
        $sales_previous=$this->get_sales_previous_years_division($fiscal_years_previous_sales,$division_id);

        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_di_budget_target_division'),'*',array('fiscal_year_id ='.$fiscal_year_id,'division_id ='.$division_id));
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

        $this->db->where('crop_type.crop_id',$crop_id);
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');

        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $info=$this->initialize_row_edit_budget_division($fiscal_years_previous_sales,$zone_ids,$result);
            foreach($fiscal_years_previous_sales as $fy)
            {
                if(isset($sales_previous[$fy['id']][$result['variety_id']]))
                {
                    $info['quantity_sale_'.$fy['id']]=$sales_previous[$fy['id']][$result['variety_id']]/1000;
                }
            }

            $quantity_budget_zone_total=0;
            foreach($zones as $zone)
            {
                if(isset($budget_zones[$zone['zone_id']][$result['variety_id']]))
                {
                    if($budget_zones[$zone['zone_id']][$result['variety_id']]['status_budget_forward']==$this->config->item('system_status_pending'))
                    {
                        $info['quantity_budget_zone_'.$zone['zone_id']]= 'N/F';
                    }
                    else
                    {
                        $info['quantity_budget_zone_'.$zone['zone_id']]= $budget_zones[$zone['zone_id']][$result['variety_id']]['quantity_budget'];
                        $quantity_budget_zone_total+=$budget_zones[$zone['zone_id']][$result['variety_id']]['quantity_budget'];
                    }
                }
            }
            $info['quantity_budget_zone_total']= $quantity_budget_zone_total;

            if(isset($items_old[$result['variety_id']]))
            {
                $info['quantity_budget']=$items_old[$result['variety_id']]['quantity_budget'];
            }

            $items[]=$info;
        }

        $this->json_return($items);
    }
    private function initialize_row_edit_budget_division($fiscal_years,$zone_ids,$info)
    {
        $row=array();
        $row['crop_type_name']=$info['crop_type_name'];
        $row['variety_name']=$info['variety_name'];
        $row['variety_id']=$info['variety_id'];

        $row['quantity_budget']=0;
        $row['quantity_budget_division']=0;
        $row['quantity_budget_zone_total']=0;

        foreach($fiscal_years as $fy)
        {
            $row['quantity_sale_'.$fy['id']]=0;
        }
        foreach($zone_ids as $zone_id)
        {
            $row['quantity_budget_zone_'.$zone_id]= 'N/D';
        }

        return $row;
    }
    private function system_save_budget_division()
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
        //validation assigned division
        if(!in_array($item_head['division_id'], $this->user_division_ids))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['division_id'],'Division Not Assigned');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Division.';
            $this->json_return($ajax);
        }
        //validation forward
        $info_budget_target=$this->get_info_budget_target($item_head['fiscal_year_id'],$item_head['division_id']);
        if(($info_budget_target['status_budget_forward']==$this->config->item('system_status_forwarded')))
        {
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']='Budget Already Forwarded.';
                $this->json_return($ajax);
            }
        }
        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_di_budget_target_division'),'*',array('fiscal_year_id ='.$item_head['fiscal_year_id'],'division_id ='.$item_head['division_id']));
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']]=$result;
        }
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($items as $variety_id=>$quantity_budget)
        {
            if(isset($items_old[$variety_id]))
            {
                if($items_old[$variety_id]['quantity_budget']!=$quantity_budget)
                {
                    $data['quantity_budget']=$quantity_budget;
                    $data['date_updated_budget']=$time;
                    $data['user_updated_budget']=$user->user_id;
                    $this->db->set('revision_count_budget','revision_count_budget+1',false);
                    Query_helper::update($this->config->item('table_bms_di_budget_target_division'),$data,array('id='.$items_old[$variety_id]['id']),false);
                }
            }
            else
            {
                $data=array();
                $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                $data['division_id']=$item_head['division_id'];
                $data['variety_id']=$variety_id;
                if($quantity_budget>0)
                {
                    $data['quantity_budget']=$quantity_budget;
                    $data['revision_count_budget']=1;
                }
                else
                {
                    $data['quantity_budget']=0;
                }
                $data['date_updated_budget'] = $time;
                $data['user_updated_budget'] = $user->user_id;
                Query_helper::add($this->config->item('table_bms_di_budget_target_division'),$data,false);
            }
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list_budget_division($item_head['fiscal_year_id'],$item_head['division_id']);

        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    // Budget Forward
    private function system_budget_forward($fiscal_year_id=0,$division_id=0)
    {
        //$user = User_helper::get_user();
        $method='budget_forward';
        if(isset($this->permissions['action7'])&&($this->permissions['action7']==1))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            if(!($division_id>0))
            {
                $division_id=$this->input->post('division_id');
            }
            //validation fiscal year
            if(!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
            {
                System_helper::invalid_try(__FUNCTION__,$fiscal_year_id,'Invalid Fiscal year');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Fiscal Year';
                $this->json_return($ajax);
            }
            //validation assigned division
            if(!in_array($division_id, $this->user_division_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$division_id,'Division Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Division.';
                $this->json_return($ajax);
            }
            //validation forward
            $info_budget_target=$this->get_info_budget_target($fiscal_year_id,$division_id);
            if(($info_budget_target['status_budget_forward']==$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='Budget Already Forwarded.';
                $this->json_return($ajax);
            }

            /*$results=Query_helper::get_info($this->config->item('table_bms_di_budget_target_division'),'*',array('fiscal_year_id ='.$fiscal_year_id,'division_id ='.$division_id));
            $budgeted_divisions[0]=0;
            foreach($results as $result)
            {
                $budgeted_divisions[$result['division_id']]=$result['division_id'];
            }*/

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['fiscal_years_previous_sales']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));

            // get zone list
            $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' budget_target_zone');
            $this->db->join($this->config->item('table_bms_zi_budget_target').' budget_target','budget_target.fiscal_year_id=budget_target_zone.fiscal_year_id AND budget_target.zone_id=budget_target_zone.zone_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = budget_target_zone.zone_id','INNER');
            $this->db->select('zones.id zone_id, zones.name zone_name');
            $this->db->where('budget_target.status_budget_forward',$this->config->item('system_status_forwarded'));
            $this->db->where('budget_target_zone.fiscal_year_id',$fiscal_year_id);
            $this->db->where('zones.division_id',$division_id);
            $this->db->group_by('budget_target_zone.zone_id');
            $this->db->order_by('zones.ordering, zones.id');
            $data['zones']=$this->db->get()->result_array();

            $data['acres']=$this->get_acres($division_id);

            $data['fiscal_year_budget_target']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['division']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),'*',array('id ='.$division_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['title']="DI Forward/Complete budget";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['division_id']=$division_id;

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/budget_forward",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/budget_forward/'.$fiscal_year_id.'/'.$division_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    public function budget_forward_items()
    {
        $items=array();

        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $division_id=$this->input->post('division_id');
        $fiscal_years_previous_sales=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
        $sales_previous=$this->get_sales_previous_years_division($fiscal_years_previous_sales,$division_id);


        //get zone budgeted quantity & zone ids
        $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' budget_target_zone');
        //$this->db->select('budget_target_zone.*, SUM(budget_target_zone.quantity_budget) quantity_budget');
        $this->db->join($this->config->item('table_bms_zi_budget_target').' budget_target','budget_target.fiscal_year_id=budget_target_zone.fiscal_year_id AND budget_target.zone_id=budget_target_zone.zone_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = budget_target_zone.zone_id','INNER');

        $this->db->where('budget_target.status_budget_forward',$this->config->item('system_status_forwarded'));
        $this->db->where('budget_target_zone.fiscal_year_id',$fiscal_year_id);
        $this->db->where('zones.division_id',$division_id);
        $this->db->group_by('budget_target_zone.variety_id, budget_target_zone.zone_id');
        $this->db->order_by('zones.ordering, zones.id');
        //$data['zones']=$this->db->get()->result_array();
        $results=$this->db->get()->result_array();
        $budget_zones=array();
        $zone_ids[0]=0;
        foreach($results as $result)
        {
            $zone_ids[$result['zone_id']]=$result['zone_id'];
            $budget_zones[$result['zone_id']][$result['variety_id']]=$result;
        }

        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_di_budget_target_division'),'*',array('fiscal_year_id ='.$fiscal_year_id,'division_id ='.$division_id));
        $budget_divisions=array();
        foreach($results as $result)
        {
            $budget_divisions[$result['variety_id']]=$result;
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

        $type_total=$this->initialize_row_budget_forward($fiscal_years_previous_sales,$zone_ids,'','','Total Type','');
        $crop_total=$this->initialize_row_budget_forward($fiscal_years_previous_sales,$zone_ids,'','Total Crop','','');
        $grand_total=$this->initialize_row_budget_forward($fiscal_years_previous_sales,$zone_ids,'Grand Total','','','');


        foreach($results as $result)
        {
            $info=$this->initialize_row_budget_forward($fiscal_years_previous_sales,$zone_ids,$result['crop_name'],$result['crop_type_name'],$result['variety_name']);
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
            if(isset($budget_divisions[$result['variety_id']]))
            {
                $info['quantity_budget_division']=$budget_divisions[$result['variety_id']]['quantity_budget'];
                $type_total['quantity_budget_division']+=$info['quantity_budget_division'];
                $crop_total['quantity_budget_division']+=$info['quantity_budget_division'];
                $grand_total['quantity_budget_division']+=$info['quantity_budget_division'];
            }
            $quantity_budget_zone_total=0;
            foreach($zone_ids as $zone_id)
            {
                if(isset($budget_zones[$zone_id][$result['variety_id']]))
                {
                    $info['quantity_budget_zone_'.$zone_id]=$budget_zones[$zone_id][$result['variety_id']]['quantity_budget'];
                    $quantity_budget_zone_total+=$info['quantity_budget_zone_'.$zone_id];
                    $type_total['quantity_budget_zone_'.$zone_id]+=$info['quantity_budget_zone_'.$zone_id];
                    $crop_total['quantity_budget_zone_'.$zone_id]+=$info['quantity_budget_zone_'.$zone_id];
                    $grand_total['quantity_budget_zone_'.$zone_id]+=$info['quantity_budget_zone_'.$zone_id];

                }
            }
            $info['quantity_budget_zone_total']=$quantity_budget_zone_total;
            $type_total['quantity_budget_zone_total']+=$quantity_budget_zone_total;
            $crop_total['quantity_budget_zone_total']+=$quantity_budget_zone_total;
            $grand_total['quantity_budget_zone_total']+=$quantity_budget_zone_total;
            $items[]=$info;
        }

        $items[]=$type_total;
        $items[]=$crop_total;
        $items[]=$grand_total;
        $this->json_return($items);

    }
    private function initialize_row_budget_forward($fiscal_years,$zone_ids,$crop_name,$crop_type_name,$variety_name)
    {
        $row=array();
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;

        $row['quantity_budget_division']=0;
        $row['quantity_budget_zone_total']=0;

        foreach($fiscal_years as $fy)
        {
            $row['quantity_sale_'.$fy['id']]=0;
        }
        foreach($zone_ids as $zone_id)
        {
            $row['quantity_budget_zone_'.$zone_id]= 0;
        }

        return $row;
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
        //validation assigned division
        if(!in_array($item_head['division_id'], $this->user_division_ids))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['division_id'],'division Not Assigned');
            $ajax['status']=false;
            $ajax['system_message']='Invalid division.';
            $this->json_return($ajax);
        }
        //validation forward
        $info_budget_target=$this->get_info_budget_target($item_head['fiscal_year_id'],$item_head['division_id']);
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
        Query_helper::update($this->config->item('table_bms_di_budget_target'),$data,array('id='.$info_budget_target['id']),false);

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

    // for ZI Assign target
    private function system_list_target_zi($fiscal_year_id=0,$division_id=0)
    {
        //$user = User_helper::get_user();
        $method='list_target_zi';
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            if(!($division_id>0))
            {
                $division_id=$this->input->post('division_id');
            }
            //validation fiscal year
            if(!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
            {
                System_helper::invalid_try(__FUNCTION__,$fiscal_year_id,'Invalid Fiscal year');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Fiscal Year';
                $this->json_return($ajax);
            }
            // validation assign division
            if(!in_array($division_id, $this->user_division_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$division_id,'Division Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Division.';
                $this->json_return($ajax);
            }
            //validation forward status
            $info_budget_target=$this->get_info_budget_target($fiscal_year_id,$division_id);
            if(($info_budget_target['status_target_zi_forward']==$this->config->item('system_status_forwarded')))
            {
                if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='ZI Target Already Assigned.';
                    $this->json_return($ajax);
                }
            }
            // validation hom assign target to di forward status
            $info_target_hom=$this->get_info_target_hom($fiscal_year_id);
            if(($info_target_hom['status_target_forward']!=$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='HOM Assign DI Target Not Forwarded.';
                $this->json_return($ajax);
            }

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['division']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),'*',array('id ='.$division_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['title']="Assign ZI Target :: Crop list";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['division_id']=$division_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_target_zi",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list_target_zi/'.$fiscal_year_id.'/'.$division_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_target_zi()
    {
        $items=array();
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $division_id=$this->input->post('division_id');

        //get Target Revision
        $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' budget_target_zone');
        $this->db->select('MAX(budget_target_zone.revision_count_target) revision_count_target');

        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = budget_target_zone.zone_id','INNER');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = budget_target_zone.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.crop_id');

        $this->db->where('budget_target_zone.fiscal_year_id',$fiscal_year_id);
        $this->db->where('zone.division_id',$division_id);
        $this->db->group_by('crop_type.crop_id');
        $results=$this->db->get()->result_array();
        $targeted=array();
        foreach($results as $result)
        {
            $targeted[$result['crop_id']]=$result['revision_count_target'];
        }
        //crop list
        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id crop_id','name crop_name'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC','id ASC'));
        foreach($results as $crop)
        {
            $item=$crop;
            $item['revision_count_target']=0;
            if(isset($targeted[$crop['crop_id']]))
            {
                $item['revision_count_target']=$targeted[$crop['crop_id']];
            }
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_assign_target_zi($fiscal_year_id=0,$division_id,$crop_id=0)
    {
        //$user = User_helper::get_user();
        $method='assign_target_zi';
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            if(!($division_id>0))
            {
                $division_id=$this->input->post('division_id');
            }
            if(!($crop_id>0))
            {
                $crop_id=$this->input->post('crop_id');
            }
            //validation fiscal year
            if(!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
            {
                System_helper::invalid_try(__FUNCTION__,$fiscal_year_id,'Invalid Fiscal year');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Fiscal Year';
                $this->json_return($ajax);
            }
            // validation assign division
            if(!in_array($division_id, $this->user_division_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$division_id,'Division Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Division.';
                $this->json_return($ajax);
            }
            //validation DI Budget & ZI Target forward status
            $info_budget_target=$this->get_info_budget_target($fiscal_year_id,$division_id);
            if(($info_budget_target['status_target_zi_forward']==$this->config->item('system_status_forwarded')))
            {
                if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='ZI Target Already Assigned.';
                    $this->json_return($ajax);
                }
            }
            // validation hom assign target to di forward status
            $info_target_hom=$this->get_info_target_hom($fiscal_year_id);
            if(($info_target_hom['status_target_forward']!=$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='HOM Assign DI Target Not Forwarded.';
                $this->json_return($ajax);
            }

            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['division']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),'*',array('id ='.$division_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['crop']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),'*',array('id ='.$crop_id),1);
            $data['zones']=User_helper::get_assigned_zones($division_id);
            $data['acres']=$this->get_acres($division_id,$crop_id);

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="DI Yearly Target Assign To ZI for (".$data['crop']['name'].')';
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['division_id']=$division_id;
            $data['options']['crop_id']=$crop_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/assign_target_zi",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/assign_target_zi/'.$fiscal_year_id.'/'.$division_id.'/'.$crop_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_assign_target_zi()
    {
        $items=array();
        //$this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $division_id=$this->input->post('division_id');
        $crop_id=$this->input->post('crop_id');

        //get division target
        $this->db->from($this->config->item('table_bms_di_budget_target_division').' budget_target_division');
        $this->db->select('budget_target_division.*');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=budget_target_division.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');

        $this->db->where('crop_type.crop_id',$crop_id);
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');
        $this->db->where('budget_target_division.fiscal_year_id',$fiscal_year_id);
        $this->db->where('budget_target_division.division_id',$division_id);
        $results=$this->db->get()->result_array();
        $target_divisions=array();
        foreach($results as $result)
        {
            $target_divisions[$result['variety_id']]=$result;
        }

        $zone_ids[0]=0;
        $zones=User_helper::get_assigned_zones($division_id);
        foreach ($zones as $zone)
        {
            $zone_ids[$zone['zone_id']]=$zone['zone_id'];
        }

        $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' budget_target_zone');
        $this->db->select('budget_target_zone.*');
        $this->db->where('budget_target_zone.fiscal_year_id',$fiscal_year_id);
        $this->db->where_in('budget_target_zone.zone_id',$zone_ids);
        $results=$this->db->get()->result_array();
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['zone_id']][$result['variety_id']]=$result;
        }

        //variety lists
        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->where('crop_type.crop_id',$crop_id);
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');

        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $info=$this->initialize_row_assign_target_zi($zone_ids,$result);
            if(isset($target_divisions[$result['variety_id']]))
            {
                $info['quantity_target_di']=$target_divisions[$result['variety_id']]['quantity_target'];
            }
            $quantity_target_zi_total=0;
            foreach($zone_ids as $zone_id)
            {
                if(isset($items_old[$zone_id][$result['variety_id']]))
                {
                    $info['quantity_target_zi_'.$zone_id]=$items_old[$zone_id][$result['variety_id']]['quantity_target'];
                    $quantity_target_zi_total+=$info['quantity_target_zi_'.$zone_id];
                }
            }
            $info['quantity_target_zi_total']= $quantity_target_zi_total;
            $items[]=$info;
        }
        $this->json_return($items);
    }
    private function initialize_row_assign_target_zi($zone_ids,$info)
    {
        $row=array();
        $row['crop_type_name']=$info['crop_type_name'];
        $row['variety_name']=$info['variety_name'];
        $row['variety_id']=$info['variety_id'];
        $row['quantity_target_di']=0;
        foreach($zone_ids as $zone_id)
        {
            $row['quantity_target_zi_'.$zone_id]= 0;
        }
        $row['quantity_target_zi_total']=0;
        return $row;
    }
    private function system_save_target_zi()
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
        // validation assign division
        if(!in_array($item_head['division_id'], $this->user_division_ids))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['division_id'],'Division Not Assigned');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Division.';
            $this->json_return($ajax);
        }
        //validation DI Budget & ZI Target forward status
        $info_budget_target=$this->get_info_budget_target($item_head['fiscal_year_id'],$item_head['division_id']);
        if(($info_budget_target['status_target_zi_forward']==$this->config->item('system_status_forwarded')))
        {
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']='ZI Target Already Assigned.';
                $this->json_return($ajax);
            }
        }
        // validation hom assign target to di forward status
        $info_target_hom=$this->get_info_target_hom($item_head['fiscal_year_id']);
        if(($info_target_hom['status_target_forward']!=$this->config->item('system_status_forwarded')))
        {
            $ajax['status']=false;
            $ajax['system_message']='HOM Assign DI Target Not Forwarded.';
            $this->json_return($ajax);
        }

        //old items
        $zone_ids[0]=0;
        $zones=User_helper::get_assigned_zones($item_head['division_id']);
        foreach ($zones as $zone)
        {
            $zone_ids[$zone['zone_id']]=$zone['zone_id'];
        }

        $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' budget_target_zone');
        $this->db->select('budget_target_zone.*');
        $this->db->where('budget_target_zone.fiscal_year_id',$item_head['fiscal_year_id']);
        $this->db->where_in('budget_target_zone.zone_id',$zone_ids);
        $results=$this->db->get()->result_array();
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['zone_id']][$result['variety_id']]=$result;
        }

        $this->db->trans_start();  //DB Transaction Handle START

        foreach($items as $variety_id=>$variety_info)
        {
            foreach($variety_info as $zone_id=>$quantity_info)
            {
                if(isset($items_old[$zone_id][$variety_id]))
                {
                    if($items_old[$zone_id][$variety_id]['quantity_target']!=$quantity_info['quantity_target'])
                    {
                        $data=array();
                        $data['quantity_target']=$quantity_info['quantity_target'];
                        $data['date_updated_target']=$time;
                        $data['user_updated_target']=$user->user_id;
                        $this->db->set('revision_count_target','revision_count_target+1',false);
                        Query_helper::update($this->config->item('table_bms_zi_budget_target_zone'),$data,array('id='.$items_old[$zone_id][$variety_id]['id']),false);
                    }
                }
                else
                {
                    $data=array();
                    $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                    $data['zone_id']=$zone_id;
                    $data['variety_id']=$variety_id;
                    if($quantity_info['quantity_target']>0)
                    {
                        $data['quantity_target']=$quantity_info['quantity_target'];
                        $data['revision_count_target']=1;
                    }
                    else
                    {
                        $data['quantity_target']=0;
                    }
                    $data['date_updated_target']=$time;
                    $data['user_updated_target']=$user->user_id;
                    Query_helper::add($this->config->item('table_bms_zi_budget_target_zone'),$data,false);
                }
            }
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list_target_zi($item_head['fiscal_year_id'],$item_head['division_id']);

        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    // for ZI assign target forward
    private function system_assign_target_zi_forward($fiscal_year_id=0,$division_id)
    {
        //$user = User_helper::get_user();
        $method='assign_target_zi_forward';
        if((isset($this->permissions['action7']) && ($this->permissions['action7']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            if(!($division_id>0))
            {
                $division_id=$this->input->post('division_id');
            }

            //validation fiscal year
            if(!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
            {
                System_helper::invalid_try(__FUNCTION__,$fiscal_year_id,'Invalid Fiscal year');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Fiscal Year';
                $this->json_return($ajax);
            }
            // validation assign division
            if(!in_array($division_id, $this->user_division_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$division_id,'Division Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Division.';
                $this->json_return($ajax);
            }
            //validation DI Budget & ZI Target forward status
            $info_budget_target=$this->get_info_budget_target($fiscal_year_id,$division_id);
            if(($info_budget_target['status_target_zi_forward']==$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='ZI Target Already Assigned.';
                $this->json_return($ajax);
            }
            // validation hom assign target to di forward status
            $info_target_hom=$this->get_info_target_hom($fiscal_year_id);
            if(($info_target_hom['status_target_forward']!=$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='HOM Assign DI Target Not Forwarded.';
                $this->json_return($ajax);
            }

            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['division']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),'*',array('id ='.$division_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['zones']=User_helper::get_assigned_zones($division_id);
            $data['acres']=$this->get_acres($division_id);

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']='DI Yearly Assign Target Forward To ZI';
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['division_id']=$division_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/assign_target_zi_forward",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/assign_target_zi_forward/'.$fiscal_year_id.'/'.$division_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_forward_assign_target_zi()
    {
        $items=array();
        //$this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $division_id=$this->input->post('division_id');

        //get division target
        $results=Query_helper::get_info($this->config->item('table_bms_di_budget_target_division'),'*',array('fiscal_year_id ='.$fiscal_year_id, 'division_id ='.$division_id));
        $target_divisions=array();
        foreach($results as $result)
        {
            $target_divisions[$result['variety_id']]=$result;
        }

        $zone_ids[0]=0;
        $zones=User_helper::get_assigned_zones($division_id);
        foreach ($zones as $zone)
        {
            $zone_ids[$zone['zone_id']]=$zone['zone_id'];
        }

        $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' budget_target_zone');
        $this->db->select('budget_target_zone.*');
        $this->db->where('budget_target_zone.fiscal_year_id',$fiscal_year_id);
        $this->db->where_in('budget_target_zone.zone_id',$zone_ids);
        $results=$this->db->get()->result_array();
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['zone_id']][$result['variety_id']]=$result;
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

        $type_total=$this->initialize_row_forward_assign_target_zi($zone_ids,'','','Total Type','');
        $crop_total=$this->initialize_row_forward_assign_target_zi($zone_ids,'','Total Crop','','');
        $grand_total=$this->initialize_row_forward_assign_target_zi($zone_ids,'Grand Total','','','');

        foreach($results as $result)
        {
            $info=$this->initialize_row_forward_assign_target_zi($zone_ids,$result['crop_name'],$result['crop_type_name'],$result['variety_name']);
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

            if(isset($target_divisions[$result['variety_id']]))
            {
                $info['quantity_target_di']=$target_divisions[$result['variety_id']]['quantity_target'];
                $type_total['quantity_target_di']+=$info['quantity_target_di'];
                $crop_total['quantity_target_di']+=$info['quantity_target_di'];
                $grand_total['quantity_target_di']+=$info['quantity_target_di'];
            }
            $quantity_target_zi_total=0;
            foreach($zone_ids as $zone_id)
            {
                if(isset($items_old[$zone_id][$result['variety_id']]))
                {
                    $info['quantity_target_zi_'.$zone_id]=$items_old[$zone_id][$result['variety_id']]['quantity_target'];
                    $type_total['quantity_target_zi_'.$zone_id]+=$info['quantity_target_zi_'.$zone_id];
                    $crop_total['quantity_target_zi_'.$zone_id]+=$info['quantity_target_zi_'.$zone_id];
                    $grand_total['quantity_target_zi_'.$zone_id]+=$info['quantity_target_zi_'.$zone_id];

                    $quantity_target_zi_total+=$info['quantity_target_zi_'.$zone_id];
                }
            }
            $info['quantity_target_zi_total']= $quantity_target_zi_total;
            $type_total['quantity_target_zi_total']+=$info['quantity_target_zi_total'];
            $crop_total['quantity_target_zi_total']+=$info['quantity_target_zi_total'];
            $grand_total['quantity_target_zi_total']+=$info['quantity_target_zi_total'];
            $items[]=$info;
        }
        $items[]=$type_total;
        $items[]=$crop_total;
        $items[]=$grand_total;
        $this->json_return($items);
    }
    private function initialize_row_forward_assign_target_zi($zone_ids,$crop_name,$crop_type_name,$variety_name)
    {
        $row=array();
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;
        $row['quantity_target_di']=0;
        foreach($zone_ids as $zone_id)
        {
            $row['quantity_target_zi_'.$zone_id]= 0;
        }
        $row['quantity_target_zi_total']=0;
        return $row;
    }
    private function system_save_target_zi_forward()
    {
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');

        if((!isset($this->permissions['action7']) && ($this->permissions['action7']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if($item_head['status_target_zi_forward']!=$this->config->item('system_status_forwarded'))
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
        // validation assign division
        if(!in_array($item_head['division_id'], $this->user_division_ids))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['division_id'],'Division Not Assigned');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Division.';
            $this->json_return($ajax);
        }
        //validation DI Budget & ZI Target forward status
        $info_budget_target=$this->get_info_budget_target($item_head['fiscal_year_id'],$item_head['division_id']);
        if(($info_budget_target['status_target_zi_forward']==$this->config->item('system_status_forwarded')))
        {
            $ajax['status']=false;
            $ajax['system_message']='ZI Target Already Assigned.';
            $this->json_return($ajax);
        }
        // validation hom assign target to di forward status
        $info_target_hom=$this->get_info_target_hom($item_head['fiscal_year_id']);
        if(($info_target_hom['status_target_forward']!=$this->config->item('system_status_forwarded')))
        {
            $ajax['status']=false;
            $ajax['system_message']='HOM Assign DI Target Not Forwarded.';
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $data=array();
        $data['status_target_zi_forward']=$item_head['status_target_zi_forward'];
        $data['date_target_zi_forwarded']=$time;
        $data['user_target_zi_forwarded']=$user->user_id;
        Query_helper::update($this->config->item('table_bms_di_budget_target'),$data,array('id='.$info_budget_target['id']),false);

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

    // ZI Next 3 years target
    private function system_list_target_zi_next_year($fiscal_year_id=0,$division_id=0)
    {
        //$user = User_helper::get_user();
        $method='list_target_zi_next_year';
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            if(!($division_id>0))
            {
                $division_id=$this->input->post('division_id');
            }
            //validation fiscal year
            if(!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
            {
                System_helper::invalid_try(__FUNCTION__,$fiscal_year_id,'Invalid Fiscal year');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Fiscal Year';
                $this->json_return($ajax);
            }
            // validation assign division
            if(!in_array($division_id, $this->user_division_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$division_id,'Division Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Division.';
                $this->json_return($ajax);
            }
            //validation forward status
            $info_budget_target=$this->get_info_budget_target($fiscal_year_id,$division_id);
            if(($info_budget_target['status_target_zi_next_year_forward']==$this->config->item('system_status_forwarded')))
            {
                if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='ZI Next Years Target Already Assigned.';
                    $this->json_return($ajax);
                }
            }
            // validation hom assign target to di forward status
            $info_target_hom=$this->get_info_target_hom($fiscal_year_id);
            if(($info_target_hom['status_target_forward']!=$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='HOM Assign DI Target Not Forwarded.';
                $this->json_return($ajax);
            }

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['division']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),'*',array('id ='.$division_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['title']="Next 3 Years ZI Assign Target :: Crop list";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['division_id']=$division_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_target_zi_next_year",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list_target_zi_next_year/'.$fiscal_year_id.'/'.$division_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_target_zi_next_year()
    {
        $items=array();
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $division_id=$this->input->post('division_id');

        //get Target Revision
        $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' budget_target_zone');
        $this->db->select('MAX(budget_target_zone.revision_count_target_prediction) revision_count_target_prediction');

        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = budget_target_zone.zone_id','INNER');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = budget_target_zone.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.crop_id');

        $this->db->where('budget_target_zone.fiscal_year_id',$fiscal_year_id);
        $this->db->where('zone.division_id',$division_id);
        $this->db->group_by('crop_type.crop_id');
        $results=$this->db->get()->result_array();
        $targeted=array();
        foreach($results as $result)
        {
            $targeted[$result['crop_id']]=$result['revision_count_target_prediction'];
        }
        //crop list
        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id crop_id','name crop_name'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC','id ASC'));
        foreach($results as $crop)
        {
            $item=$crop;
            $item['revision_count_target_prediction']=0;
            if(isset($targeted[$crop['crop_id']]))
            {
                $item['revision_count_target_prediction']=$targeted[$crop['crop_id']];
            }
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_assign_target_zi_next_year($fiscal_year_id=0,$division_id,$crop_id=0)
    {
        //$user = User_helper::get_user();
        $method='assign_target_zi_next_year';
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            if(!($division_id>0))
            {
                $division_id=$this->input->post('division_id');
            }
            if(!($crop_id>0))
            {
                $crop_id=$this->input->post('crop_id');
            }
            //validation fiscal year
            if(!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
            {
                System_helper::invalid_try(__FUNCTION__,$fiscal_year_id,'Invalid Fiscal year');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Fiscal Year';
                $this->json_return($ajax);
            }
            // validation assign division
            if(!in_array($division_id, $this->user_division_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$division_id,'Division Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Division.';
                $this->json_return($ajax);
            }
            //validation DI Budget & ZI Target forward status
            $info_budget_target=$this->get_info_budget_target($fiscal_year_id,$division_id);
            if(($info_budget_target['status_target_zi_next_year_forward']==$this->config->item('system_status_forwarded')))
            {
                if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='ZI Next 3 Years Target Already Forwarded.';
                    $this->json_return($ajax);
                }
            }
            // validation hom assign target to di forward status
            $info_target_hom=$this->get_info_target_hom($fiscal_year_id);
            if(($info_target_hom['status_target_forward']!=$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='HOM Assign DI Target Not Forwarded.';
                $this->json_return($ajax);
            }

            $data['fiscal_years_previous_sales']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
            $data['fiscal_years_next_budgets']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['division']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),'*',array('id ='.$division_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['crop']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),'*',array('id ='.$crop_id),1);
            $data['zones']=User_helper::get_assigned_zones($division_id);
            $data['acres']=$this->get_acres($division_id,$crop_id);

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="DI Next 3 Years Target Assign To ZI for (".$data['crop']['name'].')';
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['division_id']=$division_id;
            $data['options']['crop_id']=$crop_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/assign_target_zi_next_year",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/assign_target_zi_next_year/'.$fiscal_year_id.'/'.$division_id.'/'.$crop_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_assign_target_zi_next_year()
    {
        $items=array();
        //$this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $division_id=$this->input->post('division_id');
        $crop_id=$this->input->post('crop_id');

        //get division target
        $this->db->from($this->config->item('table_bms_di_budget_target_division').' budget_target_division');
        $this->db->select('budget_target_division.*');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=budget_target_division.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');

        $this->db->where('crop_type.crop_id',$crop_id);
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');
        $this->db->where('budget_target_division.fiscal_year_id',$fiscal_year_id);
        $this->db->where('budget_target_division.division_id',$division_id);
        $results=$this->db->get()->result_array();
        $target_divisions=array();
        foreach($results as $result)
        {
            $target_divisions[$result['variety_id']]=$result;
        }

        $zone_ids[0]=0;
        $zones=User_helper::get_assigned_zones($division_id);
        foreach ($zones as $zone)
        {
            $zone_ids[$zone['zone_id']]=$zone['zone_id'];
        }

        $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' budget_target_zone');
        $this->db->select('budget_target_zone.*');
        $this->db->where('budget_target_zone.fiscal_year_id',$fiscal_year_id);
        $this->db->where_in('budget_target_zone.zone_id',$zone_ids);
        $results=$this->db->get()->result_array();
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']][$result['zone_id']]=$result;
        }

        $fiscal_years_previous_sales=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
        $sales_previous=$this->get_sales_previous_years_division($fiscal_years_previous_sales,$division_id);
        $fiscal_years_next_budgets=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);

        //variety lists
        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->where('crop_type.crop_id',$crop_id);
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');

        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $info=$this->initialize_row_assign_target_zi_next_year($fiscal_years_next_budgets, $zone_ids,$result);
            foreach($fiscal_years_previous_sales as $fy)
            {
                if(isset($sales_previous[$fy['id']][$result['variety_id']]))
                {
                    $info['quantity_sale_'.$fy['id']]=$sales_previous[$fy['id']][$result['variety_id']]/1000;
                }
            }
            $quantity_prediction_total_zi=0;
            $fiscal_year_serial=0;
            foreach($fiscal_years_next_budgets as $fy)
            {
                ++$fiscal_year_serial;
                $quantity_prediction_sub_total_zi=0;
                foreach($zone_ids as $zone_id)
                {
                    if(isset($items_old[$result['variety_id']][$zone_id]))
                    {
                        //$info['quantity_target_']
                        $info['quantity_prediction_zi_'.$fy['id'].'_'.$zone_id]=$items_old[$result['variety_id']][$zone_id]['quantity_prediction_'.$fiscal_year_serial];

                        $quantity_prediction_total_zi+=$info['quantity_prediction_zi_'.$fy['id'].'_'.$zone_id];
                        $quantity_prediction_sub_total_zi+=$info['quantity_prediction_zi_'.$fy['id'].'_'.$zone_id];
                    }
                }
                $info['quantity_prediction_sub_total_zi_'.$fy['id']]+=$quantity_prediction_sub_total_zi;
            }
            $info['quantity_prediction_total_zi']= $quantity_prediction_total_zi;

            if(isset($target_divisions[$result['variety_id']]))
            {
                $info['quantity_target_di']=$target_divisions[$result['variety_id']]['quantity_target'];
                $info['quantity_prediction_1']=$target_divisions[$result['variety_id']]['quantity_prediction_1'];
                $info['quantity_prediction_2']=$target_divisions[$result['variety_id']]['quantity_prediction_2'];
                $info['quantity_prediction_3']=$target_divisions[$result['variety_id']]['quantity_prediction_3'];
            }
            $items[]=$info;
        }
        $this->json_return($items);
    }
    private function initialize_row_assign_target_zi_next_year($fiscal_years,$zone_ids,$info)
    {
        $row=array();
        $row['crop_type_name']=$info['crop_type_name'];
        $row['variety_name']=$info['variety_name'];
        $row['variety_id']=$info['variety_id'];

        $row['quantity_target_di']=0;
        // prediction 1,2,3 for di
        $row['quantity_prediction_1']=0;
        $row['quantity_prediction_2']=0;
        $row['quantity_prediction_3']=0;
        //
        foreach($fiscal_years as $fy)
        {
            foreach($zone_ids as $zone_id)
            {
                $row['quantity_prediction_zi_'.$fy['id'].'_'.$zone_id]= 0;
            }
            $row['quantity_prediction_sub_total_zi_'.$fy['id']]=0;
        }
        $row['quantity_prediction_total_zi']=0;
        return $row;
    }
    private function system_save_target_zi_next_year()
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
        // validation assign division
        if(!in_array($item_head['division_id'], $this->user_division_ids))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['division_id'],'Division Not Assigned');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Division.';
            $this->json_return($ajax);
        }
        //validation DI Budget & ZI Target forward status
        $info_budget_target=$this->get_info_budget_target($item_head['fiscal_year_id'],$item_head['division_id']);
        if(($info_budget_target['status_target_zi_next_year_forward']==$this->config->item('system_status_forwarded')))
        {
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']='ZI Next 3 Years Target Already Assigned.';
                $this->json_return($ajax);
            }
        }
        // validation hom assign target to di forward status
        $info_target_hom=$this->get_info_target_hom($item_head['fiscal_year_id']);
        if(($info_target_hom['status_target_forward']!=$this->config->item('system_status_forwarded')))
        {
            $ajax['status']=false;
            $ajax['system_message']='HOM Assign DI Target Not Forwarded.';
            $this->json_return($ajax);
        }

        //old items
        $zone_ids[0]=0;
        $zones=User_helper::get_assigned_zones($item_head['division_id']);
        foreach ($zones as $zone)
        {
            $zone_ids[$zone['zone_id']]=$zone['zone_id'];
        }

        $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' budget_target_zone');
        $this->db->select('budget_target_zone.*');
        $this->db->where('budget_target_zone.fiscal_year_id',$item_head['fiscal_year_id']);
        $this->db->where_in('budget_target_zone.zone_id',$zone_ids);
        $results=$this->db->get()->result_array();
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']][$result['zone_id']]=$result;
        }

        $this->db->trans_start();  //DB Transaction Handle START

        foreach($items as $variety_id=>$variety_info)
        {
            foreach($variety_info as $zone_id=>$quantity_info)
            {
                $quantity_prediction_1=0;
                $quantity_prediction_2=0;
                $quantity_prediction_3=0;
                if(isset($quantity_info['quantity_prediction_1']))
                {
                    $quantity_prediction_1=$quantity_info['quantity_prediction_1'];
                }
                if(isset($quantity_info['quantity_prediction_2']))
                {
                    $quantity_prediction_2=$quantity_info['quantity_prediction_2'];
                }
                if(isset($quantity_info['quantity_prediction_3']))
                {
                    $quantity_prediction_3=$quantity_info['quantity_prediction_3'];
                }
                if(isset($items_old[$variety_id][$zone_id]))
                {
                    if(($items_old[$variety_id][$zone_id]['quantity_prediction_1']!=$quantity_prediction_1) || ($items_old[$variety_id][$zone_id]['quantity_prediction_2']!=$quantity_prediction_2) || ($items_old[$variety_id][$zone_id]['quantity_prediction_2']!=$quantity_prediction_2))
                    {
                        $data=array();
                        $data['quantity_prediction_1']=$quantity_prediction_1;
                        $data['quantity_prediction_2']=$quantity_prediction_2;
                        $data['quantity_prediction_3']=$quantity_prediction_3;
                        $data['date_updated_prediction_target']=$time;
                        $data['user_updated_prediction_target']=$user->user_id;
                        $this->db->set('revision_count_target_prediction','revision_count_target_prediction+1',false);
                        Query_helper::update($this->config->item('table_bms_zi_budget_target_zone'),$data,array('id='.$items_old[$variety_id][$zone_id]['id']),false);
                    }
                }
                else
                {
                    $data=array();
                    $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                    $data['zone_id']=$zone_id;
                    $data['variety_id']=$variety_id;
                    $data['quantity_prediction_1']=$quantity_prediction_1;
                    $data['quantity_prediction_2']=$quantity_prediction_2;
                    $data['quantity_prediction_3']=$quantity_prediction_3;
                    $data['revision_count_target_prediction']=1;
                    $data['date_updated_prediction_target']=$time;
                    $data['user_updated_prediction_target']=$user->user_id;
                    Query_helper::add($this->config->item('table_bms_zi_budget_target_zone'),$data,false);
                }
            }
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list_target_zi_next_year($item_head['fiscal_year_id'],$item_head['division_id']);

        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    // ZI Next 3 years target forward
    private function system_assign_target_zi_forward_next_year($fiscal_year_id=0,$division_id)
    {
        //$user = User_helper::get_user();
        $method='assign_target_zi_next_year_forward';
        if((isset($this->permissions['action7']) && ($this->permissions['action7']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            if(!($division_id>0))
            {
                $division_id=$this->input->post('division_id');
            }
            //validation fiscal year
            if(!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
            {
                System_helper::invalid_try(__FUNCTION__,$fiscal_year_id,'Invalid Fiscal year');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Fiscal Year';
                $this->json_return($ajax);
            }
            // validation assign division
            if(!in_array($division_id, $this->user_division_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$division_id,'Division Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Division.';
                $this->json_return($ajax);
            }
            //validation DI Budget & ZI Target forward status
            $info_budget_target=$this->get_info_budget_target($fiscal_year_id,$division_id);
            if(($info_budget_target['status_target_zi_next_year_forward']==$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='ZI Next 3 Years Target Already Forwarded.';
                $this->json_return($ajax);
            }
            // validation hom assign target to di forward status
            $info_target_hom=$this->get_info_target_hom($fiscal_year_id);
            if(($info_target_hom['status_target_forward']!=$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='HOM Assign DI Target Not Forwarded.';
                $this->json_return($ajax);
            }

            $data['fiscal_years_previous_sales']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
            $data['fiscal_years_next_budgets']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['division']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),'*',array('id ='.$division_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['zones']=User_helper::get_assigned_zones($division_id);
            $data['acres']=$this->get_acres($division_id);

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']='DI Next 3 Years Assign Target Forward To ZI';
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['division_id']=$division_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/assign_target_zi_forward_next_year",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/assign_target_zi_forward_next_year/'.$fiscal_year_id.'/'.$division_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_forward_assign_target_zi_next_year()
    {
        $items=array();
        //$this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $division_id=$this->input->post('division_id');

        //get division target
        $results=Query_helper::get_info($this->config->item('table_bms_di_budget_target_division'),'*',array('fiscal_year_id ='.$fiscal_year_id, 'division_id ='.$division_id));
        $target_divisions=array();
        foreach($results as $result)
        {
            $target_divisions[$result['variety_id']]=$result;
        }

        $zone_ids[0]=0;
        $zones=User_helper::get_assigned_zones($division_id);
        foreach ($zones as $zone)
        {
            $zone_ids[$zone['zone_id']]=$zone['zone_id'];
        }

        $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' budget_target_zone');
        $this->db->select('budget_target_zone.*');
        $this->db->where('budget_target_zone.fiscal_year_id',$fiscal_year_id);
        $this->db->where_in('budget_target_zone.zone_id',$zone_ids);
        $results=$this->db->get()->result_array();
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']][$result['zone_id']]=$result;
        }

        $fiscal_years_previous_sales=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
        $sales_previous=$this->get_sales_previous_years_division($fiscal_years_previous_sales,$division_id);
        $fiscal_years_next_budgets=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);

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

        $type_total=$this->initialize_row_assign_target_zi_forward_next_year_forward($fiscal_years_previous_sales, $fiscal_years_next_budgets,$zone_ids,'','','Total Type','');
        $crop_total=$this->initialize_row_assign_target_zi_forward_next_year_forward($fiscal_years_previous_sales, $fiscal_years_next_budgets,$zone_ids,'','Total Crop','','');
        $grand_total=$this->initialize_row_assign_target_zi_forward_next_year_forward($fiscal_years_previous_sales, $fiscal_years_next_budgets, $zone_ids,'Grand Total','','','');

        foreach($results as $result)
        {
            $info=$this->initialize_row_assign_target_zi_forward_next_year_forward($fiscal_years_previous_sales, $fiscal_years_next_budgets, $zone_ids,$result['crop_name'],$result['crop_type_name'],$result['variety_name']);
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
            $quantity_prediction_total_zi=0;
            $fiscal_year_serial=0;
            foreach($fiscal_years_next_budgets as $fy)
            {
                ++$fiscal_year_serial;
                $quantity_prediction_sub_total_zi=0;
                foreach($zone_ids as $zone_id)
                {
                    if(isset($items_old[$result['variety_id']][$zone_id]))
                    {
                        //$info['quantity_target_']
                        $info['quantity_prediction_zi_'.$fy['id'].'_'.$zone_id]=$items_old[$result['variety_id']][$zone_id]['quantity_prediction_'.$fiscal_year_serial];
                        $type_total['quantity_prediction_zi_'.$fy['id'].'_'.$zone_id]+=$info['quantity_prediction_zi_'.$fy['id'].'_'.$zone_id];
                        $crop_total['quantity_prediction_zi_'.$fy['id'].'_'.$zone_id]+=$info['quantity_prediction_zi_'.$fy['id'].'_'.$zone_id];
                        $grand_total['quantity_prediction_zi_'.$fy['id'].'_'.$zone_id]+=$info['quantity_prediction_zi_'.$fy['id'].'_'.$zone_id];

                        $quantity_prediction_total_zi+=$info['quantity_prediction_zi_'.$fy['id'].'_'.$zone_id];
                        $quantity_prediction_sub_total_zi+=$info['quantity_prediction_zi_'.$fy['id'].'_'.$zone_id];
                    }
                }
                $info['quantity_prediction_sub_total_zi_'.$fy['id']]+=$quantity_prediction_sub_total_zi;
                $type_total['quantity_prediction_sub_total_zi_'.$fy['id']]+=$quantity_prediction_sub_total_zi;
                $crop_total['quantity_prediction_sub_total_zi_'.$fy['id']]+=$quantity_prediction_sub_total_zi;
                $grand_total['quantity_prediction_sub_total_zi_'.$fy['id']]+=$quantity_prediction_sub_total_zi;

            }
            $info['quantity_prediction_total_zi']= $quantity_prediction_total_zi;
            $type_total['quantity_prediction_total_zi']+=$info['quantity_prediction_total_zi'];
            $crop_total['quantity_prediction_total_zi']+=$info['quantity_prediction_total_zi'];
            $grand_total['quantity_prediction_total_zi']+=$info['quantity_prediction_total_zi'];

            if(isset($target_divisions[$result['variety_id']]))
            {
                $info['quantity_target_di']=$target_divisions[$result['variety_id']]['quantity_target'];
                $type_total['quantity_target_di']+=$info['quantity_target_di'];
                $crop_total['quantity_target_di']+=$info['quantity_target_di'];
                $grand_total['quantity_target_di']+=$info['quantity_target_di'];

                $info['quantity_prediction_1']=$target_divisions[$result['variety_id']]['quantity_prediction_1'];
                $type_total['quantity_prediction_1']+=$info['quantity_prediction_1'];
                $crop_total['quantity_prediction_1']+=$info['quantity_prediction_1'];
                $grand_total['quantity_prediction_1']+=$info['quantity_prediction_1'];

                $info['quantity_prediction_2']=$target_divisions[$result['variety_id']]['quantity_prediction_2'];
                $type_total['quantity_prediction_2']+=$info['quantity_prediction_2'];
                $crop_total['quantity_prediction_2']+=$info['quantity_prediction_2'];
                $grand_total['quantity_prediction_2']+=$info['quantity_prediction_2'];

                $info['quantity_prediction_3']=$target_divisions[$result['variety_id']]['quantity_prediction_3'];
                $type_total['quantity_prediction_3']+=$info['quantity_prediction_3'];
                $crop_total['quantity_prediction_3']+=$info['quantity_prediction_3'];
                $grand_total['quantity_prediction_3']+=$info['quantity_prediction_3'];
            }

            $items[]=$info;
        }
        $items[]=$type_total;
        $items[]=$crop_total;
        $items[]=$grand_total;
        $this->json_return($items);
    }
    private function initialize_row_assign_target_zi_forward_next_year_forward($fiscal_years_previous_sales,$fiscal_years_next_budgets,$zone_ids,$crop_name,$crop_type_name,$variety_name)
    {
        $row=array();
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;

        $row['quantity_target_di']=0;
        // prediction 1,2,3 for di
        $row['quantity_prediction_1']=0;
        $row['quantity_prediction_2']=0;
        $row['quantity_prediction_3']=0;
        foreach($fiscal_years_previous_sales as $fy)
        {
            $row['quantity_sale_'.$fy['id']]=0;
        }
        foreach($fiscal_years_next_budgets as $fy)
        {
            foreach($zone_ids as $zone_id)
            {
                $row['quantity_prediction_zi_'.$fy['id'].'_'.$zone_id]= 0;
            }
            $row['quantity_prediction_sub_total_zi_'.$fy['id']]=0;
        }
        $row['quantity_prediction_total_zi']=0;
        return $row;
    }
    private function system_save_target_zi_forward_next_year()
    {
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');

        if(!(isset($this->permissions['action7']) && ($this->permissions['action7']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
        if($item_head['status_target_zi_next_year_forward']!=$this->config->item('system_status_forwarded'))
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
        // validation assign division
        if(!in_array($item_head['division_id'], $this->user_division_ids))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['division_id'],'Division Not Assigned');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Division.';
            $this->json_return($ajax);
        }
        //validation DI Budget & ZI Target forward status
        $info_budget_target=$this->get_info_budget_target($item_head['fiscal_year_id'],$item_head['division_id']);
        if(($info_budget_target['status_target_zi_next_year_forward']==$this->config->item('system_status_forwarded')))
        {
            $ajax['status']=false;
            $ajax['system_message']='ZI Next 3 Years Target Already Assigned.';
            $this->json_return($ajax);
        }
        // validation hom assign target to di forward status
        $info_target_hom=$this->get_info_target_hom($item_head['fiscal_year_id']);
        if(($info_target_hom['status_target_forward']!=$this->config->item('system_status_forwarded')))
        {
            $ajax['status']=false;
            $ajax['system_message']='HOM Assign DI Target Not Forwarded.';
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $data=array();
        $data['status_target_zi_next_year_forward']=$item_head['status_target_zi_next_year_forward'];
        $data['date_target_zi_next_year_forwarded']=$time;
        $data['user_target_zi_next_year_forwarded']=$user->user_id;
        Query_helper::update($this->config->item('table_bms_di_budget_target'),$data,array('id='.$info_budget_target['id']),false);

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

    private function get_info_budget_target($fiscal_year_id,$division_id)
    {

        $info=Query_helper::get_info($this->config->item('table_bms_di_budget_target'),'*',array('fiscal_year_id ='.$fiscal_year_id,'division_id ='.$division_id),1);
        if(!$info)
        {
            $user = User_helper::get_user();
            $data=array();
            $data['fiscal_year_id'] = $fiscal_year_id;
            $data['division_id'] = $division_id;
            $data['date_created'] = time();
            $data['user_created'] = $user->user_id;
            $id=Query_helper::add($this->config->item('table_bms_di_budget_target'),$data,false);
            $info=Query_helper::get_info($this->config->item('table_bms_di_budget_target'),'*',array('id ='.$id),1);
        }
        return $info;
    }
    private function get_info_target_hom($fiscal_year_id)
    {
        $info=Query_helper::get_info($this->config->item('table_bms_hom_budget_target'),'*',array('fiscal_year_id ='.$fiscal_year_id),1);
        return $info;
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
    private function get_sales_previous_years_division($fiscal_years,$division_id)
    {
        $sales=array();
        foreach($fiscal_years as $fy)
        {
            $this->db->from($this->config->item('table_pos_sale_details').' details');
            $this->db->select('details.variety_id');
            $this->db->select('SUM(details.pack_size*details.quantity) quantity_sale');
            
            $this->db->join($this->config->item('table_pos_sale').' sale','sale.id = details.sale_id','INNER');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = sale.outlet_id','INNER');
            $this->db->select('cus_info.customer_id outlet_id, cus_info.name outlet_name');
            
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = cus_info.district_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');

            $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            /*$this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');*/

            $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
            $this->db->where('cus_info.revision',1);
            $this->db->where('sale.date_sale >=',$fy['date_start']);
            $this->db->where('sale.date_sale <=',$fy['date_end']);
            $this->db->where('sale.status',$this->config->item('system_status_active'));
            $this->db->where('zones.division_id',$division_id);
            $this->db->group_by('details.variety_id');
            $results=$this->db->get()->result_array();
            foreach($results as $result)
            {
                $sales[$fy['id']][$result['variety_id']]=$result['quantity_sale'];
            }
        }
        return $sales;
    }
    private function get_acres($division_id,$crop_id=0)
    {
        $this->db->from($this->config->item('table_login_setup_location_upazillas').' upazillas');
        $this->db->select('upazillas.id upazilla_id');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = upazillas.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        /*$this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');*/
        $this->db->where('zones.division_id',$division_id);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $upazilla_ids[$result['upazilla_id']]=$result['upazilla_id'];
        }

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
        $this->db->where_in('acres.upazilla_id',$upazilla_ids);
        $this->db->group_by('crop_type.id');
        $results=$this->db->get()->result_array();
        $items=array();
        foreach($results as $result)
        {
            $items[$result['crop_id']][$result['crop_type_id']]['crop_name']=$result['crop_name'];
            $items[$result['crop_id']][$result['crop_type_id']]['crop_type_name']=$result['crop_type_name'];
            $items[$result['crop_id']][$result['crop_type_id']]['quantity']=$result['quantity'];
            $items[$result['crop_id']][$result['crop_type_id']]['quantity_kg_acre']=$result['quantity_kg_acre'];
        }
        return $items;
    }


}
