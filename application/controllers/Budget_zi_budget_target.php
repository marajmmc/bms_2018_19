<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Budget_zi_budget_target extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public $user_zones;
    public $user_zone_ids;

    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission(get_class());
        $this->controller_url=strtolower(get_class());
        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->json_return($ajax);
        }
        $this->user_zone_ids=array();
        $this->user_zones=User_helper::get_assigned_zones($this->locations['division_id'],$this->locations['zone_id']);
        if(sizeof($this->user_zones)>0)
        {
            foreach($this->user_zones as $row)
            {
                $this->user_zone_ids[]=$row['zone_id'];
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_ZONE_NOT_ASSIGNED');
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
        elseif($action=="list_budget_zone")
        {
            $this->system_list_budget_zone($id,$id1);
        }
        elseif($action=="get_items_budget_zone")
        {
            $this->system_get_items_budget_zone();
        }
        elseif($action=="edit_budget_zone")
        {
            $this->system_edit_budget_zone($id,$id1,$id2);
        }
        elseif($action=="get_items_edit_budget_zone")
        {
            $this->system_get_items_edit_budget_zone();
        }
        elseif($action=="save_budget_zone")
        {
            $this->system_save_budget_zone();
        }
        elseif($action=="budget_forward")
        {
            $this->system_budget_forward($id,$id1);
        }
        elseif($action=="save_forward_budget")
        {
            $this->system_save_forward_budget();
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
            $data['zone_id']= 1;
            $data['zone_name']= 1;
            $data['status_budget_forward']= 1;
        }
        else if($method=='list_budget_zone')
        {
            $data['crop_id']= 1;
            $data['crop_name']= 1;
            $data['revision_count_budget']= 1;
        }
        else if($method=='edit_budget_zone')
        {
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            //more data
            $data['quantity_budget_outlet_total']= 1;
            $data['quantity_budget']= 1;
        }
        else if($method=='budget_forward')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            //more datas
            $data['quantity_budget_zone']= 1;
            $data['quantity_budget_outlet_total']= 1;
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
            $data['title']="Yearly ZI Budget";
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
        $this->db->from($this->config->item('table_bms_zi_budget_target').' budget_target');
        $this->db->select('budget_target.status_budget_forward');
        $this->db->select('budget_target.fiscal_year_id');
        $this->db->select('budget_target.zone_id');
        $this->db->where_in('budget_target.zone_id',$this->user_zone_ids);
        $results=$this->db->get()->result_array();
        $budget_target=array();
        foreach($results as $result)
        {
            $budget_target[$result['fiscal_year_id']][$result['zone_id']]=$result;
        }
        $items=array();
        foreach($fiscal_years as $fy)
        {
            foreach($this->user_zones as $zone)
            {
                $data=array();
                $data['fiscal_year_id']=$fy['id'];
                $data['fiscal_year']=$fy['text'];
                $data['zone_id']=$zone['zone_id'];
                $data['zone_name']=$zone['zone_name'];
                if(isset($budget_target[$fy['id']][$zone['zone_id']]))
                {
                    $data['status_budget_forward']=$budget_target[$fy['id']][$zone['zone_id']]['status_budget_forward'];
                }
                else
                {
                    $data['status_budget_forward']=$this->config->item('system_status_pending');
                }
                $items[]=$data;
            }
        }
        $this->json_return($items);
    }
    private function system_list_budget_zone($fiscal_year_id=0,$zone_id=0)
    {
        //$user = User_helper::get_user();
        $method='list_budget_zone';
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            if(!($zone_id>0))
            {
                $zone_id=$this->input->post('zone_id');
            }
            //validation fiscal year
            if(!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
            {
                System_helper::invalid_try(__FUNCTION__,$fiscal_year_id,'Invalid Fiscal year');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Fiscal Year';
                $this->json_return($ajax);
            }
            //validation assigned zone
            if(!in_array($zone_id, $this->user_zone_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$zone_id,'Zone Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Zone.';
                $this->json_return($ajax);
            }
            //validation forward
            $info_budget_target=$this->get_info_budget_target($fiscal_year_id,$zone_id);
            if(($info_budget_target['status_budget_forward']==$this->config->item('system_status_forwarded')))
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
            $data['zone']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),'*',array('id ='.$zone_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['title']="ZI Yearly Budget Crop list";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['zone_id']=$zone_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_budget_zone",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list_budget_zone/'.$fiscal_year_id.'/'.$zone_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_budget_zone()
    {
        $items=array();
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $zone_id=$this->input->post('zone_id');

        //get budget revision
        $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' budget_target_zone');
        $this->db->select('MAX(budget_target_zone.revision_count_budget) revision_count_budget');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = budget_target_zone.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.crop_id');
        $this->db->where('budget_target_zone.fiscal_year_id',$fiscal_year_id);
        $this->db->where('budget_target_zone.zone_id',$zone_id);
        $this->db->group_by('crop_type.crop_id');
        $results=$this->db->get()->result_array();
        $budgeted=array();
        foreach($results as $result)
        {
            $budgeted[$result['crop_id']]=$result['revision_count_budget'];
        }
        //crop list
        $results=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),array('id crop_id','name crop_name'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC','id ASC'));
        foreach($results as $crop)
        {
            $item=$crop;
            if(isset($budgeted[$crop['crop_id']]))
            {
                $item['revision_count_budget']=$budgeted[$crop['crop_id']];
            }
            else
            {
                $item['revision_count_budget']=0;
            }
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_edit_budget_zone($fiscal_year_id=0,$zone_id=0,$crop_id=0)
    {
        //$user = User_helper::get_user();
        $method='edit_budget_zone';
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            if(!($zone_id>0))
            {
                $zone_id=$this->input->post('zone_id');
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
            //validation assigned zone
            if(!in_array($zone_id, $this->user_zone_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$zone_id,'Zone Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Zone.';
                $this->json_return($ajax);
            }
            //validation forward
            $info_budget_target=$this->get_info_budget_target($fiscal_year_id,$zone_id);
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
            $data['zone']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),'*',array('id ='.$zone_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['crop']=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),'*',array('id ='.$crop_id),1);
            $data['outlets']=$this->get_outlets($zone_id);
            $data['acres']=$this->get_acres($zone_id,$crop_id);

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="ZI Yearly Budget for (".$data['crop']['name'].')';
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['zone_id']=$zone_id;
            $data['options']['crop_id']=$crop_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_budget_zone",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_budget_zone/'.$fiscal_year_id.'/'.$zone_id.'/'.$crop_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_edit_budget_zone()
    {
        $items=array();
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $zone_id=$this->input->post('zone_id');
        $crop_id=$this->input->post('crop_id');

        $outlets=$this->get_outlets($zone_id);
        $outlet_ids[0]=0;
        foreach($outlets as $outlet)
        {
            $outlet_ids[$outlet['outlet_id']]=$outlet['outlet_id'];
        }

        //Outlet budget & get outlet wise forward status
        $this->db->from($this->config->item('table_pos_si_budget_target_outlet').' budget_target_outlet');
        $this->db->select('budget_target_outlet.*');
        $this->db->where('budget_target_outlet.fiscal_year_id',$fiscal_year_id);
        $this->db->where_in('budget_target_outlet.outlet_id',$outlet_ids);
        $this->db->join($this->config->item('table_pos_si_budget_target').' budget_target','budget_target.fiscal_year_id=budget_target_outlet.fiscal_year_id AND budget_target.outlet_id=budget_target_outlet.outlet_id','INNER');
        $this->db->select('budget_target.status_budget_forward');
        $results=$this->db->get()->result_array();
        $budget_outlets=array();
        foreach($results as $result)
        {
            $budget_outlets[$result['outlet_id']][$result['variety_id']]=$result;
        }

        $fiscal_years_previous_sales=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
        $sales_previous=$this->get_sales_previous_years_zone($fiscal_years_previous_sales,$zone_id);

        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_zi_budget_target_zone'),'*',array('fiscal_year_id ='.$fiscal_year_id,'zone_id ='.$zone_id));
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
            $info=$this->initialize_row_edit_budget_zone($fiscal_years_previous_sales,$outlet_ids,$result);
            foreach($fiscal_years_previous_sales as $fy)
            {
                if(isset($sales_previous[$fy['id']][$result['variety_id']]))
                {
                    $info['quantity_sale_'.$fy['id']]=$sales_previous[$fy['id']][$result['variety_id']]/1000;
                }
            }
            $quantity_budget_dealer_total=0;
            foreach($outlets as $outlet)
            {
                if(isset($budget_outlets[$outlet['outlet_id']][$result['variety_id']]))
                {
                    if($budget_outlets[$outlet['outlet_id']][$result['variety_id']]['status_budget_forward']==$this->config->item('system_status_pending'))
                    {
                        $info['quantity_budget_outlet_'.$outlet['outlet_id']]= 'N/F';
                    }
                    else
                    {
                        $info['quantity_budget_outlet_'.$outlet['outlet_id']]= $budget_outlets[$outlet['outlet_id']][$result['variety_id']]['quantity_budget'];
                        $quantity_budget_dealer_total+=$budget_outlets[$outlet['outlet_id']][$result['variety_id']]['quantity_budget'];
                    }
                }
            }
            $info['quantity_budget_outlet_total']= $quantity_budget_dealer_total;

            if(isset($items_old[$result['variety_id']]))
            {
                if($items_old[$result['variety_id']]['quantity_budget']>0)
                {
                    $info['quantity_budget']=$items_old[$result['variety_id']]['quantity_budget'];
                }
            }
            $items[]=$info;
        }

        $this->json_return($items);
    }
    private function initialize_row_edit_budget_zone($fiscal_years,$outlet_ids,$info)
    {
        $row=array();
        $row['crop_type_name']=$info['crop_type_name'];
        $row['variety_name']=$info['variety_name'];
        $row['variety_id']=$info['variety_id'];
        $row['quantity_budget']=0;
        $row['quantity_budget_outlet_total']=0;
        foreach($fiscal_years as $fy)
        {
            $row['quantity_sale_'.$fy['id']]=0;
        }
        foreach($outlet_ids as $outlet_id)
        {
            $row['quantity_budget_outlet_'.$outlet_id]= 'N/D';
        }
        return $row;
    }
    private function system_save_budget_zone()
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
        //validation assigned zone
        if(!in_array($item_head['zone_id'], $this->user_zone_ids))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['zone_id'],'Zone Not Assigned');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Zone.';
            $this->json_return($ajax);
        }
        //validation forward
        $info_budget_target=$this->get_info_budget_target($item_head['fiscal_year_id'],$item_head['zone_id']);
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
        $results=Query_helper::get_info($this->config->item('table_bms_zi_budget_target_zone'),'*',array('fiscal_year_id ='.$item_head['fiscal_year_id'],'zone_id ='.$item_head['zone_id']));
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
                    Query_helper::update($this->config->item('table_bms_zi_budget_target_zone'),$data,array('id='.$items_old[$variety_id]['id']));
                }
            }
            else
            {
                $data=array();
                $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                $data['zone_id']=$item_head['zone_id'];
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
                Query_helper::add($this->config->item('table_bms_zi_budget_target_zone'),$data,false);
            }
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list_budget_zone($item_head['fiscal_year_id'],$item_head['zone_id']);

        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    private function system_budget_forward($fiscal_year_id=0,$zone_id=0)
    {
        //$user = User_helper::get_user();
        $method='budget_forward';
        if(isset($this->permissions['action7'])&&($this->permissions['action7']==1))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            if(!($zone_id>0))
            {
                $zone_id=$this->input->post('zone_id');
            }
            //validation fiscal year
            if(!Budget_helper::check_validation_fiscal_year($fiscal_year_id))
            {
                System_helper::invalid_try(__FUNCTION__,$fiscal_year_id,'Invalid Fiscal year');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Fiscal Year';
                $this->json_return($ajax);
            }
            //validation assigned zone
            if(!in_array($zone_id, $this->user_zone_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$zone_id,'Zone Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Zone.';
                $this->json_return($ajax);
            }
            //validation forward
            $info_budget_target=$this->get_info_budget_target($fiscal_year_id,$zone_id);
            if(($info_budget_target['status_budget_forward']==$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='Budget Already Forwarded.';
                $this->json_return($ajax);
            }
            
            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['fiscal_years_previous_sales']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));

            // get outlet list
            $this->db->from($this->config->item('table_pos_si_budget_target_outlet').' budget_target_outlet');
        
            $this->db->join($this->config->item('table_pos_si_budget_target').' budget_target','budget_target.fiscal_year_id=budget_target_outlet.fiscal_year_id AND budget_target.outlet_id=budget_target_outlet.outlet_id','INNER');
            
            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = budget_target_outlet.outlet_id','INNER');
            $this->db->select('cus_info.customer_id outlet_id, cus_info.name outlet_name');
        
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = cus_info.district_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            /*$this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');*/

            $this->db->where('budget_target.status_budget_forward',$this->config->item('system_status_forwarded'));
            $this->db->where('budget_target_outlet.fiscal_year_id',$fiscal_year_id);
            $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
            $this->db->where('cus_info.revision',1);
            $this->db->where('territories.zone_id',$zone_id);
            $this->db->group_by('budget_target_outlet.outlet_id');
            $this->db->order_by('cus_info.ordering, cus_info.id');
            $data['outlets']=$this->db->get()->result_array();
            $data['acres']=$this->get_acres($zone_id);
            $data['fiscal_year_budget_target']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['zone']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),'*',array('id ='.$zone_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['title']="ZI Forward/Complete budget";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['zone_id']=$zone_id;

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/budget_forward",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/budget_forward/'.$fiscal_year_id.'/'.$zone_id);
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
        $zone_id=$this->input->post('zone_id');
        $fiscal_years_previous_sales=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
        $sales_previous=$this->get_sales_previous_years_zone($fiscal_years_previous_sales,$zone_id);

        //get outlet budgeted quantity & outlet ids
        $this->db->from($this->config->item('table_pos_si_budget_target_outlet').' budget_target_outlet');
        $this->db->select('budget_target_outlet.variety_id, budget_target_outlet.quantity_budget');
        

        $this->db->join($this->config->item('table_pos_si_budget_target').' budget_target','budget_target.fiscal_year_id=budget_target_outlet.fiscal_year_id AND budget_target.outlet_id=budget_target_outlet.outlet_id','INNER');
        $this->db->where('budget_target.status_budget_forward',$this->config->item('system_status_forwarded'));

        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = budget_target_outlet.outlet_id','INNER');
        $this->db->select('cus_info.customer_id outlet_id, cus_info.name outlet_name');
        
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = cus_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        /*$this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');*/

        $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
        $this->db->where('cus_info.revision',1);
        $this->db->where('territories.zone_id',$zone_id);
        $this->db->order_by('cus_info.ordering, cus_info.id');
        $this->db->where('budget_target_outlet.fiscal_year_id',$fiscal_year_id);
        $this->db->group_by('budget_target_outlet.outlet_id');
        $results=$this->db->get()->result_array();
        $budget_outlets=array();
        $outlet_ids[0]=0;
        foreach($results as $result)
        {
            $outlet_ids[$result['outlet_id']]=$result['outlet_id'];
            $budget_outlets[$result['outlet_id']][$result['variety_id']]=$result;
        }

        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_zi_budget_target_zone'),'*',array('fiscal_year_id ='.$fiscal_year_id,'zone_id ='.$zone_id));
        $budget_zones=array();
        foreach($results as $result)
        {
            $budget_zones[$result['variety_id']]=$result;
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

        $type_total=$this->initialize_row($fiscal_years_previous_sales,$outlet_ids,'','','Total Type','');
        $crop_total=$this->initialize_row($fiscal_years_previous_sales,$outlet_ids,'','Total Crop','','');
        $grand_total=$this->initialize_row($fiscal_years_previous_sales,$outlet_ids,'Grand Total','','','');


        foreach($results as $result)
        {
            $info=$this->initialize_row($fiscal_years_previous_sales,$outlet_ids,$result['crop_name'],$result['crop_type_name'],$result['variety_name']);


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
            if(isset($budget_zones[$result['variety_id']]))
            {
                $info['quantity_budget_zone']=$budget_zones[$result['variety_id']]['quantity_budget'];
                $type_total['quantity_budget_zone']+=$info['quantity_budget_zone'];
                $crop_total['quantity_budget_zone']+=$info['quantity_budget_zone'];
                $grand_total['quantity_budget_zone']+=$info['quantity_budget_zone'];
            }
            $quantity_budget_outlet_total=0;
            foreach($outlet_ids as $outlet_id)
            {
                if(isset($budget_outlets[$outlet_id][$result['variety_id']]))
                {
                    $info['quantity_budget_outlet_'.$outlet_id]=$budget_outlets[$outlet_id][$result['variety_id']]['quantity_budget'];
                    $quantity_budget_outlet_total+=$info['quantity_budget_outlet_'.$outlet_id];
                    $type_total['quantity_budget_outlet_'.$outlet_id]+=$info['quantity_budget_outlet_'.$outlet_id];
                    $crop_total['quantity_budget_outlet_'.$outlet_id]+=$info['quantity_budget_outlet_'.$outlet_id];
                    $grand_total['quantity_budget_outlet_'.$outlet_id]+=$info['quantity_budget_outlet_'.$outlet_id];

                }
            }
            $info['quantity_budget_outlet_total']=$quantity_budget_outlet_total;
            $type_total['quantity_budget_outlet_total']+=$quantity_budget_outlet_total;
            $crop_total['quantity_budget_outlet_total']+=$quantity_budget_outlet_total;
            $grand_total['quantity_budget_outlet_total']+=$quantity_budget_outlet_total;
            $items[]=$info;
        }

        $items[]=$type_total;
        $items[]=$crop_total;
        $items[]=$grand_total;
        
        $this->json_return($items);

    }
    private function initialize_row($fiscal_years,$outlet_ids,$crop_name,$crop_type_name,$variety_name)
    {
        $row=array();
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;

        $row['quantity_budget_zone']=0;
        $row['quantity_budget_outlet_total']=0;

        foreach($fiscal_years as $fy)
        {
            $row['quantity_sale_'.$fy['id']]=0;
        }
        foreach($outlet_ids as $outlet_id)
        {
            $row['quantity_budget_outlet_'.$outlet_id]= 0;
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
        //validation assigned zone
        if(!in_array($item_head['zone_id'], $this->user_zone_ids))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['zone_id'],'Zone Not Assigned');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Zone.';
            $this->json_return($ajax);
        }
        //validation forward
        $info_budget_target=$this->get_info_budget_target($item_head['fiscal_year_id'],$item_head['zone_id']);
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
        Query_helper::update($this->config->item('table_bms_zi_budget_target'),$data,array('id='.$info_budget_target['id']));

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
    private function get_info_budget_target($fiscal_year_id,$zone_id)
    {

        $info=Query_helper::get_info($this->config->item('table_bms_zi_budget_target'),'*',array('fiscal_year_id ='.$fiscal_year_id,'zone_id ='.$zone_id),1);
        if(!$info)
        {
            $user = User_helper::get_user();
            $data=array();
            $data['fiscal_year_id'] = $fiscal_year_id;
            $data['zone_id'] = $zone_id;
            $data['date_created'] = time();
            $data['user_created'] = $user->user_id;
            $id=Query_helper::add($this->config->item('table_bms_zi_budget_target'),$data);
            $info=Query_helper::get_info($this->config->item('table_bms_zi_budget_target'),'*',array('id ='.$id),1);
        }
        return $info;
    }
    private function get_outlets($zone_id)
    {
        $this->db->from($this->config->item('table_login_csetup_customer').' customer');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
        $this->db->select('cus_info.customer_id outlet_id, cus_info.name outlet_name');

        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = cus_info.district_id','INNER');

        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');

        /*$this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');*/

        if(!(isset($this->permissions['action3'])&&($this->permissions['action3']==1)))
        {
            $this->db->where('customer.status',$this->config->item('system_status_active'));
        }

        $this->db->where('territories.zone_id',$zone_id);
        $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
        $this->db->where('cus_info.revision',1);
        $this->db->order_by('cus_info.ordering, cus_info.id');
        return $this->db->get()->result_array();

    }
    private function get_sales_previous_years_zone($fiscal_years,$zone_id)
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

            /*$this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');*/

            $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
            $this->db->where('cus_info.revision',1);
            $this->db->where('sale.date_sale >=',$fy['date_start']);
            $this->db->where('sale.date_sale <=',$fy['date_end']);
            $this->db->where('sale.status',$this->config->item('system_status_active'));
            $this->db->where('territories.zone_id',$zone_id);
            $this->db->group_by('details.variety_id');
            $results=$this->db->get()->result_array();
            foreach($results as $result)
            {
                $sales[$fy['id']][$result['variety_id']]=$result['quantity_sale'];
            }
        }

        return $sales;

    }
    private function get_acres($zone_id,$crop_id=0)
    {
        $this->db->from($this->config->item('table_login_setup_location_upazillas').' upazillas');
        $this->db->select('upazillas.id upazilla_id');
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = upazillas.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
        /*$this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = zones.division_id','INNER');*/
        $this->db->where('territories.zone_id',$zone_id);
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
        return $results;
    }


}
