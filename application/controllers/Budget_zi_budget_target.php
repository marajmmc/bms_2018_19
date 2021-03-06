<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Budget_zi_budget_target extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $common_view_location;
    public $locations;
    public $user_zones;
    public $user_zone_ids;

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
        $this->language_labels();
    }
    private function language_labels()
    {
        // Title
        $this->lang->language['LABEL_TITLE_DETAILS']='Zone budget and target details';
        // area
        $this->lang->language['LABEL_STATUS_BUDGET_FORWARD_AREA']='Zone Budget';
        // area sub
        $this->lang->language['LABEL_STATUS_TARGET_FORWARD_AREA_SUB']='Showrooms Target Assigned';
        // superior area
        $this->lang->language['LABEL_STATUS_TARGET_FORWARD_AREA']='Zone Target Assigned';
        $this->lang->language['LABEL_STATUS_TARGET_FORWARD_AREA_NEXT_YEAR']='Zone 3years Target Assigned';
        $this->lang->language['LABEL_STATUS_TARGET_FORWARD_AREA_SUB_NEXT_YEAR']='Showroom 3years Target Assigned';
        // jqx grid
        $this->lang->language['LABEL_BUDGET_SUB_KG']='Showroom Budget (Kg)';
        $this->lang->language['LABEL_BUDGET_SUB_AMOUNT']='Showroom Budget (Amount)';
        $this->lang->language['LABEL_TARGET_SUB_KG']='Showroom Target (Kg)';
        $this->lang->language['LABEL_TARGET_SUB_AMOUNT']='Showroom Target (Amount)';

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
        elseif($action=="forward_budget_zone")
        {
            $this->system_forward_budget_zone($id,$id1);
        }
        elseif($action=="get_items_forward_budget_zone")
        {
            $this->system_get_items_forward_budget_zone();
        }
        elseif($action=="save_forward_budget_zone")
        {
            $this->system_save_forward_budget_zone();
        }

        elseif($action=="list_target_outlet")
        {
            $this->system_list_target_outlet($id,$id1);
        }
        elseif($action=="get_items_target_outlet")
        {
            $this->system_get_items_target_outlet();
        }
        elseif($action=="edit_target_outlet")
        {
            $this->system_edit_target_outlet($id,$id1,$id2);
        }
        elseif($action=="get_items_edit_target_outlet")
        {
            $this->system_get_items_edit_target_outlet();
        }
        elseif($action=="save_target_outlet")
        {
            $this->system_save_target_outlet();
        }
        elseif($action=="forward_target_outlet")
        {
            $this->system_forward_target_outlet($id,$id1);
        }
        elseif($action=="get_items_forward_target_outlet")
        {
            $this->system_get_items_forward_target_outlet();
        }
        elseif($action=="save_forward_target_outlet")
        {
            $this->system_save_forward_target_outlet();
        }

        elseif($action=="list_target_outlet_next_year")
        {
            $this->system_list_target_outlet_next_year($id,$id1);
        }
        elseif($action=="get_items_target_outlet_next_year")
        {
            $this->system_get_items_target_outlet_next_year();
        }
        elseif($action=="edit_target_outlet_next_year")
        {
            $this->system_edit_target_outlet_next_year($id,$id1,$id2);
        }
        elseif($action=="get_items_edit_target_outlet_next_year")
        {
            $this->system_get_items_edit_target_outlet_next_year();
        }
        elseif($action=="save_target_outlet_next_year")
        {
            $this->system_save_target_outlet_next_year();
        }
        elseif($action=="forward_target_outlet_next_year")
        {
            $this->system_forward_target_outlet_next_year($id,$id1);
        }
        elseif($action=="get_items_forward_target_outlet_next_year")
        {
            $this->system_get_items_forward_target_outlet_next_year();
        }
        elseif($action=="save_forward_target_outlet_next_year")
        {
            $this->system_save_forward_target_outlet_next_year();
        }
        elseif($action=="details")
        {
            $this->system_details($id,$id1);
        }
        elseif($action=="get_items_details")
        {
            $this->system_get_items_details();
        }
        elseif($action=="set_preference_details")
        {
            $this->system_set_preference('search_details');
        }
        elseif($action=="save_preference")
        {
            System_helper::save_preference();
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
            $data['status_target_zi_forward']= 1;
            $data['status_target_outlet_forward']= 1;
            $data['status_target_zi_next_year_forward']= 1;
            $data['status_target_outlet_next_year_forward']= 1;
        }
        else if($method=='list_budget_zone')
        {
            $data['crop_id']= 1;
            $data['crop_name']= 1;
            $data['number_of_variety_active']= 1;
            $data['number_of_variety_budgeted']= 1;
            $data['number_of_variety_budget_due']= 1;
        }
        else if($method=='edit_budget_zone')
        {
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            $data['quantity_budget_outlet_total']= 1;
            $data['quantity_budget']= 1;
        }
        else if($method=='forward_budget_zone')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            //more datas
            $data['quantity_budget_zone']= 1;
            $data['quantity_budget_outlet_total']= 1;
        }
        else if($method=='list_target_outlet')
        {
            $data['id']= 1;
            $data['crop_id']= 1;
            $data['crop_name']= 1;
            $data['number_of_variety_active']= 1;
            $data['number_of_variety_targeted']= 1;
            $data['number_of_variety_target_due']= 1;
        }
        else if($method=='edit_target_outlet')
        {
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            $data['quantity_budget_zi']= 1;
            $data['quantity_target_zi']= 1;
            $data['quantity_target_outlet_total']= 1;
        }
        else if($method=='forward_target_outlet')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            $data['quantity_budget_zi']= 1;
            $data['quantity_target_zi']= 1;
            $data['quantity_target_outlet_total']= 1;
        }
        else if($method=='edit_target_outlet_next_year')
        {
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            //previous sales form initialize row
            $data['quantity_target_zi']= 1;
            //zi prediction from initialize row
            //outlet prediction from initialize row
            $data['quantity_prediction_outlet_total']= 1;
        }
        else if($method=='forward_target_outlet_next_year')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['quantity_prediction_1']= 1;
            $data['quantity_prediction_2']= 1;
            $data['quantity_prediction_3']= 1;
            $data['quantity_target_zi']= 1;
            $data['quantity_prediction_outlet_total']= 1;
        }
        else if($method=='search_details')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['price_unit_kg_amount']= 1;
            $data['budget_kg']= 1;
            $data['budget_amount']= 1;
            $data['target_kg']= 1;
            $data['target_amount']= 1;

            $data['budget_sub_kg']= 1;
            $data['budget_sub_amount']= 1;
            $data['target_sub_kg']= 1;
            $data['target_sub_amount']= 1;

            $data['prediction_kg']= 1;
            $data['prediction_amount']= 1;
        }
        return $data;
    }
    private function system_set_preference($method)
    {
        $user = User_helper::get_user();
        if(isset($this->permissions['action6']) && ($this->permissions['action6']==1))
        {
            $data['system_preference_items']=System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $data['preference_method_name']=$method;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("preference_add_edit",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url.'/index/set_preference_'.$method);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }

    private function system_list()
    {
        //$user = User_helper::get_user();
        $method='list';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="Yearly ZSC Budget & Target";
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

        $this->db->from($this->config->item('table_bms_di_budget_target').' di_budget_target');
        $this->db->select('di_budget_target.status_target_zi_forward, di_budget_target.status_target_zi_next_year_forward');
        $this->db->select('di_budget_target.fiscal_year_id');
        $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = di_budget_target.division_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','division.id = zone.division_id','INNER');

        $this->db->select('zone.id zone_id');
        $this->db->where_in('zone.id',$this->user_zone_ids);
        $results=$this->db->get()->result_array();
        $budget_target_di=array();
        foreach($results as $result)
        {
            $budget_target_di[$result['fiscal_year_id']][$result['zone_id']]=$result;
        }


        $this->db->from($this->config->item('table_bms_zi_budget_target').' budget_target');
        $this->db->select('budget_target.status_budget_forward, budget_target.status_target_outlet_forward, budget_target.status_target_outlet_next_year_forward');
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
                $data['status_budget_forward']=$this->config->item('system_status_pending');
                $data['status_target_zi_forward']=$this->config->item('system_status_pending');
                $data['status_target_outlet_forward']=$this->config->item('system_status_pending');
                $data['status_target_zi_next_year_forward']=$this->config->item('system_status_pending');
                $data['status_target_outlet_next_year_forward']=$this->config->item('system_status_pending');
                if(isset($budget_target[$fy['id']][$zone['zone_id']]))
                {
                    $data['status_budget_forward']=$budget_target[$fy['id']][$zone['zone_id']]['status_budget_forward'];
                    $data['status_target_outlet_forward']=$budget_target[$fy['id']][$zone['zone_id']]['status_target_outlet_forward'];
                    $data['status_target_outlet_next_year_forward']=$budget_target[$fy['id']][$zone['zone_id']]['status_target_outlet_next_year_forward'];
                }
                if(isset($budget_target_di[$fy['id']][$zone['zone_id']]))
                {
                    $data['status_target_zi_forward']=$budget_target_di[$fy['id']][$zone['zone_id']]['status_target_zi_forward'];
                    $data['status_target_zi_next_year_forward']=$budget_target_di[$fy['id']][$zone['zone_id']]['status_target_zi_next_year_forward'];
                }
                $items[]=$data;
            }
        }
        $this->json_return($items);
    }

    /*Zone Budget Edit*/
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
            $info_budget=$this->get_info_budget_target($fiscal_year_id,$zone_id);
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                if(($info_budget['status_budget_forward']==$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Budget Already Forwarded.';
                    $this->json_return($ajax);
                }
            }

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['zone']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),'*',array('id ='.$zone_id),1);
            $data['title']="ZSC Yearly Budget Crop list";
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
        $this->db->select('SUM(CASE WHEN budget_target_zone.quantity_budget>0 then 1 ELSE 0 END) number_of_variety_budgeted',false);

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
            $budgeted[$result['crop_id']]=$result['number_of_variety_budgeted'];
        }
        // number of variety
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

        //jqxgrid crop list
        foreach($crops as $crop)
        {
            $item=$crop;
            $item['number_of_variety_active']=$crop['number_of_variety_active'];
            $item['number_of_variety_budgeted']=0;
            if(isset($budgeted[$crop['crop_id']]))
            {
                $item['number_of_variety_budgeted']=$budgeted[$crop['crop_id']];
            }
            $item['number_of_variety_budget_due']=($item['number_of_variety_active']-$item['number_of_variety_budgeted']);
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
            // valid crop check
            $crop=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),'*',array('id ='.$crop_id),1);
            if(!$crop)
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong crop id.';
                $this->json_return($ajax);
            }
            //validation forward
            $info_budget=$this->get_info_budget_target($fiscal_year_id,$zone_id);
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                if(($info_budget['status_budget_forward']==$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Budget Already Forwarded.';
                    $this->json_return($ajax);
                }
            }

            $data['fiscal_years_previous_sales']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['zone']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),'*',array('id ='.$zone_id),1);
            $data['crop']=$crop;
            $data['outlets']=$this->get_outlets($zone_id);
            $data['acres']=$this->get_acres($zone_id,$crop_id);

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="ZSC Yearly Budget for (".$data['crop']['name'].')';
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

        $this->db->join($this->config->item('table_pos_si_budget_target').' budget_target','budget_target.fiscal_year_id=budget_target_outlet.fiscal_year_id AND budget_target.outlet_id=budget_target_outlet.outlet_id','INNER');
        $this->db->select('budget_target.status_budget_forward');

        $this->db->where('budget_target_outlet.fiscal_year_id',$fiscal_year_id);
        $this->db->where_in('budget_target_outlet.outlet_id',$outlet_ids);

        $results=$this->db->get()->result_array();
        //echo $this->db->last_query();
        $budgeted_outlets=array();
        foreach($results as $result)
        {
            $budgeted_outlets[$result['outlet_id']][$result['variety_id']]=$result;
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
        $results=Budget_helper::get_crop_type_varieties(array($crop_id));
        foreach($results as $result)
        {
            $info=$this->initialize_row_edit_budget_zone($fiscal_years_previous_sales,$outlets,$result);
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
                if(isset($budgeted_outlets[$outlet['outlet_id']][$result['variety_id']]))
                {
                    if($budgeted_outlets[$outlet['outlet_id']][$result['variety_id']]['status_budget_forward']==$this->config->item('system_status_pending'))
                    {
                        $info['quantity_budget_outlet_'.$outlet['outlet_id']]= 'N/F';
                    }
                    else
                    {
                        $info['quantity_budget_outlet_'.$outlet['outlet_id']]= $budgeted_outlets[$outlet['outlet_id']][$result['variety_id']]['quantity_budget'];
                        $quantity_budget_dealer_total+=$budgeted_outlets[$outlet['outlet_id']][$result['variety_id']]['quantity_budget'];
                    }
                }
            }
            $info['quantity_budget_outlet_total']= $quantity_budget_dealer_total;

            if(isset($items_old[$result['variety_id']]))
            {
                $info['quantity_budget']=$items_old[$result['variety_id']]['quantity_budget'];
            }
            $items[]=$info;
        }

        $this->json_return($items);
    }
    private function initialize_row_edit_budget_zone($fiscal_years,$outlets,$info)
    {
        $row=$this->get_preference_headers('edit_budget_zone');
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['crop_type_name']=$info['crop_type_name'];
        $row['variety_name']=$info['variety_name'];
        $row['variety_id']=$info['variety_id'];
        foreach($fiscal_years as $fy)
        {
            $row['quantity_sale_'.$fy['id']]=0;
        }
        foreach($outlets as $outlet)
        {
            $row['quantity_budget_outlet_'.$outlet['outlet_id']]= 'N/D';
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
        $info_budget=$this->get_info_budget_target($item_head['fiscal_year_id'],$item_head['zone_id']);
        if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
        {
            if(($info_budget['status_budget_forward']==$this->config->item('system_status_forwarded')))
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
                    Query_helper::update($this->config->item('table_bms_zi_budget_target_zone'),$data,array('id='.$items_old[$variety_id]['id']),false);
                }
            }
            else
            {
                $data=array();
                $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                $data['zone_id']=$item_head['zone_id'];
                $data['variety_id']=$variety_id;
                $data['quantity_budget']=0;
                if($quantity_budget>0)
                {
                    $data['quantity_budget']=$quantity_budget;
                    $data['revision_count_budget']=1;
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
    /*Zone Budget Forward*/
    private function system_forward_budget_zone($fiscal_year_id=0,$zone_id=0)
    {
        //$user = User_helper::get_user();
        $method='forward_budget_zone';
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
            $info_budget=$this->get_info_budget_target($fiscal_year_id,$zone_id);
            if(($info_budget['status_budget_forward']==$this->config->item('system_status_forwarded')))
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
            
            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = budget_target_outlet.outlet_id AND cus_info.revision = 1','INNER');
            $this->db->select('cus_info.customer_id outlet_id, cus_info.name outlet_name');
        
            $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = cus_info.district_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');
            /*$this->db->join($this->config->item('table_login_setup_location_zones').' zones','zones.id = territories.zone_id','INNER');*/

            $this->db->where('budget_target.status_budget_forward',$this->config->item('system_status_forwarded'));
            $this->db->where('budget_target_outlet.fiscal_year_id',$fiscal_year_id);
            //$this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));
            $this->db->where('territories.zone_id',$zone_id);
            $this->db->group_by('budget_target_outlet.outlet_id');
            $this->db->order_by('cus_info.ordering, cus_info.id');
            $data['outlets']=$this->db->get()->result_array();
            $data['acres']=$this->get_acres($zone_id);
            $data['fiscal_year_budget_target']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['zone']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),'*',array('id ='.$zone_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['title']="ZSC Forward/Complete budget";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['zone_id']=$zone_id;

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/forward_budget_zone",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/forward_budget_zone/'.$fiscal_year_id.'/'.$zone_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_forward_budget_zone()
    {
        $items=array();
        //$this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $zone_id=$this->input->post('zone_id');
        $fiscal_years_previous_sales=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
        $sales_previous=$this->get_sales_previous_years_zone($fiscal_years_previous_sales,$zone_id);

        //get outlet budgeted quantity & outlet ids
        $this->db->from($this->config->item('table_pos_si_budget_target_outlet').' budget_target_outlet');
        $this->db->select('budget_target_outlet.variety_id, budget_target_outlet.quantity_budget');

        $this->db->join($this->config->item('table_pos_si_budget_target').' budget_target','budget_target.fiscal_year_id=budget_target_outlet.fiscal_year_id AND budget_target.outlet_id=budget_target_outlet.outlet_id','INNER');
        $this->db->where('budget_target.status_budget_forward',$this->config->item('system_status_forwarded'));

        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = budget_target_outlet.outlet_id AND cus_info.revision = 1','INNER');
        $this->db->select('cus_info.customer_id outlet_id, cus_info.name outlet_name');
        
        $this->db->join($this->config->item('table_login_setup_location_districts').' districts','districts.id = cus_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territories','territories.id = districts.territory_id','INNER');

        $this->db->where('territories.zone_id',$zone_id);
        $this->db->order_by('cus_info.ordering, cus_info.id');
        $this->db->where('budget_target_outlet.fiscal_year_id',$fiscal_year_id);
        //$this->db->group_by('budget_target_outlet.outlet_id');
        $results=$this->db->get()->result_array();
        $budgeted_outlets=array();
        $outlet_ids[0]=0;
        foreach($results as $result)
        {
            $outlet_ids[$result['outlet_id']]=$result['outlet_id'];
            $budgeted_outlets[$result['outlet_id']][$result['variety_id']]=$result;
        }
        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_zi_budget_target_zone'),'*',array('fiscal_year_id ='.$fiscal_year_id,'zone_id ='.$zone_id));
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']]=$result;
        }

        //variety lists
        $results=Budget_helper::get_crop_type_varieties();
        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;

        $type_total=$this->initialize_row_forward_budget($fiscal_years_previous_sales,$outlet_ids,'','','Total Type','');
        $crop_total=$this->initialize_row_forward_budget($fiscal_years_previous_sales,$outlet_ids,'','Total Crop','','');
        $grand_total=$this->initialize_row_forward_budget($fiscal_years_previous_sales,$outlet_ids,'Grand Total','','','');

        foreach($results as $result)
        {
            $info=$this->initialize_row_forward_budget($fiscal_years_previous_sales,$outlet_ids,$result['crop_name'],$result['crop_type_name'],$result['variety_name']);
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
                }
            }
            if(isset($items_old[$result['variety_id']]))
            {
                $info['quantity_budget_zone']=$items_old[$result['variety_id']]['quantity_budget'];
            }
            $quantity_budget_outlet_total=0;
            foreach($outlet_ids as $outlet_id)
            {
                if(isset($budgeted_outlets[$outlet_id][$result['variety_id']]))
                {
                    $info['quantity_budget_outlet_'.$outlet_id]=$budgeted_outlets[$outlet_id][$result['variety_id']]['quantity_budget'];
                    $quantity_budget_outlet_total+=$info['quantity_budget_outlet_'.$outlet_id];
                }
            }

            $info['quantity_budget_outlet_total']=$quantity_budget_outlet_total;

            foreach($info as $key=>$r)
            {
                if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')))
                {
                    $type_total[$key]+=$info[$key];
                    $crop_total[$key]+=$info[$key];
                    $grand_total[$key]+=$info[$key];
                }
            }

            $items[]=$info;
        }

        $items[]=$type_total;
        $items[]=$crop_total;
        $items[]=$grand_total;
        
        $this->json_return($items);

    }
    private function initialize_row_forward_budget($fiscal_years,$outlet_ids,$crop_name,$crop_type_name,$variety_name)
    {
        $row=$this->get_preference_headers('forward_budget_zone');
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;
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
    private function system_save_forward_budget_zone()
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
        $info_budget=$this->get_info_budget_target($item_head['fiscal_year_id'],$item_head['zone_id']);
        if(($info_budget['status_budget_forward']==$this->config->item('system_status_forwarded')))
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
        Query_helper::update($this->config->item('table_bms_zi_budget_target'),$data,array('id='.$info_budget['id']),false);

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
    /*Assign Outlet Target Edit*/
    private function system_list_target_outlet($fiscal_year_id=0,$zone_id=0)
    {
        $method='list_target_outlet';
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
            // validation assign division
            if(!in_array($zone_id, $this->user_zone_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$zone_id,'Zone Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Zone.';
                $this->json_return($ajax);
            }

            //validation forward status
            $info_target=$this->get_info_budget_target($fiscal_year_id,$zone_id);
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                if(($info_target['status_target_outlet_forward']==$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Outlet Target Already Assigned.';
                    $this->json_return($ajax);
                }

                $info_target_di=$this->get_info_target_di($fiscal_year_id, $info_target['division_id']);
                if(($info_target_di['status_target_zi_forward']!=$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='ZSC Assign Target Not Forwarded From DI.';
                    $this->json_return($ajax);
                }
            }

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['division']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),'*',array('id ='.$info_target['division_id'],'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['zone']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),'*',array('id ='.$zone_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['title']="Assign Outlet Target :: Crop list";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['zone_id']=$zone_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_target_outlet",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list_target_outlet/'.$fiscal_year_id.'/'.$zone_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_target_outlet()
    {
        $items=array();
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $zone_id=$this->input->post('zone_id');

        //get Target Revision
        $this->db->from($this->config->item('table_pos_si_budget_target_outlet').' budget_target_outlet');
        $this->db->select('COUNT(DISTINCT budget_target_outlet.variety_id) number_of_variety_targeted',false);

        $this->db->join($this->config->item('table_login_csetup_cus_info').' customer_info','customer_info.customer_id = budget_target_outlet.outlet_id AND customer_info.revision = 1','INNER');
        $this->db->select('customer_info.customer_id,customer_info.name,customer_info.type,customer_info.name_short,customer_info.customer_code');

        $this->db->join($this->config->item('table_login_setup_location_districts').' district','district.id = customer_info.district_id','INNER');
        $this->db->select('district.id district_id,district.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories').' territory','territory.id = district.territory_id','INNER');
        $this->db->select('territory.id territory_id,territory.name territory_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = budget_target_outlet.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.crop_id');

        $this->db->where('budget_target_outlet.fiscal_year_id',$fiscal_year_id);
        $this->db->where('territory.zone_id',$zone_id);
        $this->db->where('budget_target_outlet.quantity_target > ',0);
        $this->db->group_by('crop_type.crop_id');
        $results=$this->db->get()->result_array();
        $targeted=array();
        foreach($results as $result)
        {
            $targeted[$result['crop_id']]=$result['number_of_variety_targeted'];
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
            $item['number_of_variety_targeted']=0;
            if(isset($targeted[$crop['crop_id']]))
            {
                $item['number_of_variety_targeted']=$targeted[$crop['crop_id']];
            }
            $item['number_of_variety_target_due']=($item['number_of_variety_active']-$item['number_of_variety_targeted']);
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_edit_target_outlet($fiscal_year_id=0,$zone_id,$crop_id=0)
    {
        //$user = User_helper::get_user();
        $method='edit_target_outlet';
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
            // validation assign division
            if(!in_array($zone_id, $this->user_zone_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$zone_id,'Zone Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Zone.';
                $this->json_return($ajax);
            }
            // valid crop check
            $crop=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),'*',array('id ='.$crop_id),1);
            if(!$crop)
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong crop id.';
                $this->json_return($ajax);
            }
            //validation ZSC Budget & Outlet Target forward status
            $info_target=$this->get_info_budget_target($fiscal_year_id,$zone_id);
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                if(($info_target['status_target_outlet_forward']==$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Outlet Target Already Assigned.';
                    $this->json_return($ajax);
                }

                $info_target_di=$this->get_info_target_di($fiscal_year_id, $info_target['division_id']);
                if(($info_target_di['status_target_zi_forward']!=$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='ZSC Assign Target Not Forwarded From DI.';
                    $this->json_return($ajax);
                }
            }

            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['division']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),'*',array('id ='.$info_target['division_id'],'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['zone']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),'*',array('id ='.$zone_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['crop']=$crop;
            $data['outlets']=$this->get_outlets($zone_id);
            $data['acres']=$this->get_acres($zone_id,$crop_id);

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="ZSC Yearly Target Assign To Outlet for (".$data['crop']['name'].')';
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['zone_id']=$zone_id;
            $data['options']['crop_id']=$crop_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_target_outlet",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_target_outlet/'.$fiscal_year_id.'/'.$zone_id.'/'.$crop_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_edit_target_outlet()
    {
        $items=array();
        //$this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $zone_id=$this->input->post('zone_id');
        $crop_id=$this->input->post('crop_id');

        //get zone target
        $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' budget_target_zone');
        $this->db->select('budget_target_zone.*');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=budget_target_zone.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');

        $this->db->where('crop_type.crop_id',$crop_id);
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');
        $this->db->where('budget_target_zone.fiscal_year_id',$fiscal_year_id);
        $this->db->where('budget_target_zone.zone_id',$zone_id);
        $results=$this->db->get()->result_array();
        $budget_target_info=array();
        foreach($results as $result)
        {
            $budget_target_info[$result['variety_id']]=$result;
        }

        $outlet_ids[0]=0;
        $outlets=$this->get_outlets($zone_id);
        foreach ($outlets as $outlet)
        {
            $outlet_ids[$outlet['outlet_id']]=$outlet['outlet_id'];
        }

        // get old items

        $this->db->from($this->config->item('table_pos_si_budget_target_outlet').' budget_target_outlet');
        $this->db->select('budget_target_outlet.*');
        $this->db->where('budget_target_outlet.fiscal_year_id',$fiscal_year_id);
        $this->db->where_in('budget_target_outlet.outlet_id',$outlet_ids);
        $results=$this->db->get()->result_array();
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['outlet_id']][$result['variety_id']]=$result;
        }

        //variety lists
        $results=Budget_helper::get_crop_type_varieties(array($crop_id));
        foreach($results as $result)
        {
            $info=$this->initialize_row_edit_target_outlet($outlet_ids,$result);
            if(isset($budget_target_info[$result['variety_id']]))
            {
                $info['quantity_budget_zi']=$budget_target_info[$result['variety_id']]['quantity_budget'];
                $info['quantity_target_zi']=$budget_target_info[$result['variety_id']]['quantity_target'];
            }
            $quantity_target_outlet_total=0;
            foreach($outlet_ids as $outlet_id)
            {
                if(isset($items_old[$outlet_id][$result['variety_id']]))
                {
                    $info['quantity_budget_outlet_'.$outlet_id]=$items_old[$outlet_id][$result['variety_id']]['quantity_budget'];
                    $info['quantity_target_outlet_'.$outlet_id]=$items_old[$outlet_id][$result['variety_id']]['quantity_target'];
                    $quantity_target_outlet_total+=$info['quantity_target_outlet_'.$outlet_id];
                }
            }
            $info['quantity_target_outlet_total']= $quantity_target_outlet_total;
            $items[]=$info;
        }
        $this->json_return($items);
    }
    private function initialize_row_edit_target_outlet($outlet_ids,$info)
    {
        $row=$this->get_preference_headers('edit_target_outlet');
        foreach($row  as $key=>$r)
    {
        $row[$key]=0;
    }
        $row['crop_type_name']=$info['crop_type_name'];
        $row['variety_name']=$info['variety_name'];
        $row['variety_id']=$info['variety_id'];
        foreach($outlet_ids as $outlet_id)
        {
            $row['quantity_budget_outlet_'.$outlet_id]= 0;
            $row['quantity_target_outlet_'.$outlet_id]= 0;
        }
        return $row;
    }
    private function system_save_target_outlet()
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
        // validation assign zone
        if(!in_array($item_head['zone_id'], $this->user_zone_ids))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['zone_id'],'Zone Not Assigned');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Zone.';
            $this->json_return($ajax);
        }
        //validation DI Budget & ZSC Target forward status
        $info_target=$this->get_info_budget_target($item_head['fiscal_year_id'],$item_head['zone_id']);

        if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
        {
            // outlet target forward
            if(($info_target['status_target_outlet_forward']==$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='Outlet Target Already Assigned.';
                $this->json_return($ajax);
            }
            // validation DI assign target to zi forward status
            $info_target_di=$this->get_info_target_di($item_head['fiscal_year_id'], $info_target['division_id']);
            if(($info_target_di['status_target_zi_forward']!=$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='ZSC Assign Target Not Forwarded From DI.';
                $this->json_return($ajax);
            }
        }

        // get outlet ids
        $outlet_ids[0]=0;
        $outlets=$this->get_outlets($item_head['zone_id']);
        foreach ($outlets as $outlet)
        {
            $outlet_ids[$outlet['outlet_id']]=$outlet['outlet_id'];
        }

        //old items
        $this->db->from($this->config->item('table_pos_si_budget_target_outlet').' budget_target_outlet');
        $this->db->select('budget_target_outlet.*');

        $this->db->where('budget_target_outlet.fiscal_year_id',$item_head['fiscal_year_id']);
        $this->db->where_in('budget_target_outlet.outlet_id',$outlet_ids);

        $results=$this->db->get()->result_array();
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['outlet_id']][$result['variety_id']]=$result;
        }

        $this->db->trans_start();  //DB Transaction Handle START

        foreach($items as $variety_id=>$variety_info)
        {
            foreach($variety_info as $outlet_id=>$quantity_info)
            {
                if(isset($items_old[$outlet_id][$variety_id]))
                {
                    if($items_old[$outlet_id][$variety_id]['quantity_target']!=$quantity_info['quantity_target'])
                    {
                        $data=array();
                        $data['quantity_target']=$quantity_info['quantity_target'];
                        $data['date_updated_target']=$time;
                        $data['user_updated_target']=$user->user_id;
                        $this->db->set('revision_count_target','revision_count_target+1',false);
                        Query_helper::update($this->config->item('table_pos_si_budget_target_outlet'),$data,array('id='.$items_old[$outlet_id][$variety_id]['id']),false);
                    }
                }
                else
                {
                    $data=array();
                    $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                    $data['outlet_id']=$outlet_id;
                    $data['variety_id']=$variety_id;
                    $data['quantity_target']=0;
                    if($quantity_info['quantity_target']>0)
                    {
                        $data['quantity_target']=$quantity_info['quantity_target'];
                        $data['revision_count_target']=1;
                    }
                    $data['date_updated_target']=$time;
                    $data['user_updated_target']=$user->user_id;
                    Query_helper::add($this->config->item('table_pos_si_budget_target_outlet'),$data,false);
                }
            }
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list_target_outlet($item_head['fiscal_year_id'],$item_head['zone_id']);

        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    /*Assign outlet target forward*/
    private function system_forward_target_outlet($fiscal_year_id=0,$zone_id)
    {
        $method='forward_target_outlet';
        if((isset($this->permissions['action7']) && ($this->permissions['action7']==1)))
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
            // validation assign division
            if(!in_array($zone_id, $this->user_zone_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$zone_id,'Zone Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Zone.';
                $this->json_return($ajax);
            }
            //validation ZSC Budget & Outlet Target forward status
            $info_budget_target=$this->get_info_budget_target($fiscal_year_id,$zone_id);
            if(($info_budget_target['status_target_outlet_forward']==$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='Outlet Target Already Assigned.';
                $this->json_return($ajax);
            }
            // validation DI assign target to ZSC forward status
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                $info_target_di=$this->get_info_target_di($fiscal_year_id, $info_budget_target['division_id']);
                if(($info_target_di['status_target_zi_forward']!=$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='DI Assign ZSC Target Not Forwarded.';
                    $this->json_return($ajax);
                }
            }

            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['division']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),'*',array('id ='.$info_budget_target['division_id'],'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['zone']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),'*',array('id ='.$zone_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['outlets']=$this->get_outlets($zone_id);
            $data['acres']=$this->get_acres($zone_id);

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']='ZSC Yearly Target Forward To Outlet';
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['zone_id']=$zone_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/forward_target_outlet",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/forward_target_outlet/'.$fiscal_year_id.'/'.$zone_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_forward_target_outlet()
    {
        $items=array();
        //$this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $zone_id=$this->input->post('zone_id');

        //get zone target
        $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' budget_target_zone');
        $this->db->select('budget_target_zone.*');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=budget_target_zone.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');

        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');
        $this->db->where('budget_target_zone.fiscal_year_id',$fiscal_year_id);
        $this->db->where('budget_target_zone.zone_id',$zone_id);
        $results=$this->db->get()->result_array();
        $target_zones=array();
        foreach($results as $result)
        {
            $target_zones[$result['variety_id']]=$result;
        }

        $outlet_ids[0]=0;
        $outlets=$this->get_outlets($zone_id);
        foreach ($outlets as $outlet)
        {
            $outlet_ids[$outlet['outlet_id']]=$outlet['outlet_id'];
        }

        // get old items

        $this->db->from($this->config->item('table_pos_si_budget_target_outlet').' budget_target_outlet');
        $this->db->select('budget_target_outlet.*');
        $this->db->where('budget_target_outlet.fiscal_year_id',$fiscal_year_id);
        $this->db->where_in('budget_target_outlet.outlet_id',$outlet_ids);
        $results=$this->db->get()->result_array();
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['outlet_id']][$result['variety_id']]=$result;
        }

        //variety lists
        $results=Budget_helper::get_crop_type_varieties();

        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;

        $type_total=$this->initialize_row_forward_target_outlet($outlet_ids,'','','Total Type','');
        $crop_total=$this->initialize_row_forward_target_outlet($outlet_ids,'','Total Crop','','');
        $grand_total=$this->initialize_row_forward_target_outlet($outlet_ids,'Grand Total','','','');

        foreach($results as $result)
        {
            $info=$this->initialize_row_forward_target_outlet($outlet_ids,$result['crop_name'],$result['crop_type_name'],$result['variety_name']);
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
            if(isset($target_zones[$result['variety_id']]))
            {
                $info['quantity_budget_zi']=$target_zones[$result['variety_id']]['quantity_budget'];
                $info['quantity_target_zi']=$target_zones[$result['variety_id']]['quantity_target'];
            }
            $quantity_target_outlet_total=0;
            foreach($outlet_ids as $outlet_id)
            {
                if(isset($items_old[$outlet_id][$result['variety_id']]))
                {
                    $info['quantity_budget_outlet_'.$outlet_id]=$items_old[$outlet_id][$result['variety_id']]['quantity_budget'];
                    $info['quantity_target_outlet_'.$outlet_id]=$items_old[$outlet_id][$result['variety_id']]['quantity_target'];
                    $quantity_target_outlet_total+=$info['quantity_target_outlet_'.$outlet_id];
                }
            }
            $info['quantity_target_outlet_total']= $quantity_target_outlet_total;
            foreach($info as $key=>$r)
            {
                if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')))
                {
                    $type_total[$key]+=$info[$key];
                    $crop_total[$key]+=$info[$key];
                    $grand_total[$key]+=$info[$key];
                }
            }
            $items[]=$info;
        }
        $items[]=$type_total;
        $items[]=$crop_total;
        $items[]=$grand_total;
        $this->json_return($items);
    }
    private function initialize_row_forward_target_outlet($outlet_ids,$crop_name,$crop_type_name,$variety_name)
    {
        $row=$this->get_preference_headers('forward_target_outlet');
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;
        foreach($outlet_ids as $outlet_id)
        {
            $row['quantity_budget_outlet_'.$outlet_id]= 0;
            $row['quantity_target_outlet_'.$outlet_id]= 0;
        }
        return $row;
    }
    private function system_save_forward_target_outlet()
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
        if($item_head['status_target_outlet_forward']!=$this->config->item('system_status_forwarded'))
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
        // validation assign zone
        if(!in_array($item_head['zone_id'], $this->user_zone_ids))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['zone_id'],'Zone Not Assigned');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Zone.';
            $this->json_return($ajax);
        }
        //validation DI Budget & ZSC Target forward status
        $info_target=$this->get_info_budget_target($item_head['fiscal_year_id'],$item_head['zone_id']);
        // outlet target forward
        if(($info_target['status_target_outlet_forward']==$this->config->item('system_status_forwarded')))
        {
            $ajax['status']=false;
            $ajax['system_message']='Outlet Target Already Assigned.';
            $this->json_return($ajax);
        }
        // validation DI assign target to zi forward status
        if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
        {
            $info_target_di=$this->get_info_target_di($item_head['fiscal_year_id'], $info_target['division_id']);
            if(($info_target_di['status_target_zi_forward']!=$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='DI Assign ZSC Target Not Forwarded.';
                $this->json_return($ajax);
            }
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $data=array();
        $data['status_target_outlet_forward']=$item_head['status_target_outlet_forward'];
        $data['date_target_outlet_forwarded']=$time;
        $data['user_target_outlet_forwarded']=$user->user_id;
        Query_helper::update($this->config->item('table_bms_zi_budget_target'),$data,array('id='.$info_target['id']),false);

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
    /*Assign Outlet Next 3 Target Edit*/
    private function system_list_target_outlet_next_year($fiscal_year_id=0,$zone_id=0)
    {
        $method='list_target_outlet_next_year';
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
            // validation assign division
            if(!in_array($zone_id, $this->user_zone_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$zone_id,'Zone Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Zone.';
                $this->json_return($ajax);
            }
            //validation forward status
            $info_target=$this->get_info_budget_target($fiscal_year_id,$zone_id);
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                if(($info_target['status_target_outlet_next_year_forward']==$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Outlet Next 3 Years Target Already Assigned.';
                    $this->json_return($ajax);
                }
                // validation di assign target to zi forward status
                $info_target_di=$this->get_info_target_di($fiscal_year_id, $info_target['division_id']);
                if(($info_target_di['status_target_zi_next_year_forward']!=$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='DI Assign To ZSC Next 3 Years Target Not Forwarded.';
                    $this->json_return($ajax);
                }
            }

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['division']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),'*',array('id ='.$info_target['division_id'],'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['zone']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),'*',array('id ='.$zone_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['title']="Next 3 Years Assign Outlet Target :: Crop list";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['zone_id']=$zone_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_target_outlet_next_year",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list_target_outlet_next_year/'.$fiscal_year_id.'/'.$zone_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_target_outlet_next_year()
    {
        $items=array();
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $zone_id=$this->input->post('zone_id');

        //get Target Revision
        $this->db->from($this->config->item('table_pos_si_budget_target_outlet').' budget_target_outlet');
        //$this->db->select('MAX(budget_target_outlet.revision_count_target_prediction) revision_count_target_prediction');
        $this->db->select('COUNT(DISTINCT budget_target_outlet.variety_id) number_of_variety_targeted',false);

        $this->db->join($this->config->item('table_login_csetup_cus_info').' customer_info','customer_info.customer_id = budget_target_outlet.outlet_id AND customer_info.revision = 1','INNER');
        $this->db->select('customer_info.customer_id,customer_info.name,customer_info.type,customer_info.name_short,customer_info.customer_code');

        $this->db->join($this->config->item('table_login_setup_location_districts').' district','district.id = customer_info.district_id','INNER');
        $this->db->select('district.id district_id,district.name district_name');

        $this->db->join($this->config->item('table_login_setup_location_territories').' territory','territory.id = district.territory_id','INNER');
        $this->db->select('territory.id territory_id,territory.name territory_name');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = budget_target_outlet.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.crop_id');

        $this->db->where('budget_target_outlet.fiscal_year_id',$fiscal_year_id);
        $this->db->where('territory.zone_id',$zone_id);
        $this->db->where('(budget_target_outlet.quantity_prediction_1>0 OR budget_target_outlet.quantity_prediction_2>0 OR budget_target_outlet.quantity_prediction_3>0)');
        $this->db->group_by('crop_type.crop_id');
        $results=$this->db->get()->result_array();
        $targeted=array();
        foreach($results as $result)
        {
            $targeted[$result['crop_id']]=$result['number_of_variety_targeted'];
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
            $item['number_of_variety_targeted']=0;
            if(isset($targeted[$crop['crop_id']]))
            {
                $item['number_of_variety_targeted']=$targeted[$crop['crop_id']];
            }
            $item['number_of_variety_target_due']=($item['number_of_variety_active']-$item['number_of_variety_targeted']);
            $items[]=$item;
        }
        $this->json_return($items);
    }
    private function system_edit_target_outlet_next_year($fiscal_year_id=0,$zone_id,$crop_id=0)
    {
        //$user = User_helper::get_user();
        $method='edit_target_outlet_next_year';
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
            // validation assign division
            if(!in_array($zone_id, $this->user_zone_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$zone_id,'Zone Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Zone.';
                $this->json_return($ajax);
            }
            // valid crop check
            $crop=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),'*',array('id ='.$crop_id),1);
            if(!$crop)
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong crop id.';
                $this->json_return($ajax);
            }
            //validation ZSC Budget & Outlet Target forward status
            $info_target=$this->get_info_budget_target($fiscal_year_id,$zone_id);
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                if(($info_target['status_target_outlet_next_year_forward']==$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Outlet Next 3 Years Target Already Assigned.';
                    $this->json_return($ajax);
                }

                // validation DI assign target to ZSC forward status
                $info_target_di=$this->get_info_target_di($fiscal_year_id, $info_target['division_id']);
                if(($info_target_di['status_target_zi_next_year_forward']!=$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='DI Assign To ZSC Next 3 Years Target Not Forwarded.';
                    $this->json_return($ajax);
                }
            }

            $data['fiscal_years_previous_sales']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
            $data['fiscal_years_next_budgets']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['division']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),'*',array('id ='.$info_target['division_id'],'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['zone']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),'*',array('id ='.$zone_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['crop']=$crop;
            $data['outlets']=$this->get_outlets($zone_id);
            $data['acres']=$this->get_acres($zone_id,$crop_id);

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="ZSC Next 3 Years Target Assign To Outlet for (".$data['crop']['name'].')';
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['zone_id']=$zone_id;
            $data['options']['crop_id']=$crop_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_target_outlet_next_year",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_target_outlet_next_year/'.$fiscal_year_id.'/'.$zone_id.'/'.$crop_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_edit_target_outlet_next_year()
    {
        $items=array();
        //$this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $zone_id=$this->input->post('zone_id');
        $crop_id=$this->input->post('crop_id');

        //get zone target
        $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' budget_target_zone');
        $this->db->select('budget_target_zone.*');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=budget_target_zone.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');

        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');
        $this->db->where('budget_target_zone.fiscal_year_id',$fiscal_year_id);
        $this->db->where('budget_target_zone.zone_id',$zone_id);
        $this->db->where('crop_type.crop_id',$crop_id);
        $results=$this->db->get()->result_array();
        $target_zones=array();
        foreach($results as $result)
        {
            $target_zones[$result['variety_id']]=$result;
        }

        $fiscal_years_previous_sales=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
        $sales_previous=$this->get_sales_previous_years_zone($fiscal_years_previous_sales,$zone_id);
        $fiscal_years_next_budgets=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);

        $outlet_ids[0]=0;
        $outlets=$this->get_outlets($zone_id);
        foreach ($outlets as $outlet)
        {
            $outlet_ids[$outlet['outlet_id']]=$outlet['outlet_id'];
        }

        // get old items
        $this->db->from($this->config->item('table_pos_si_budget_target_outlet').' budget_target_outlet');
        $this->db->select('budget_target_outlet.*');
        $this->db->where('budget_target_outlet.fiscal_year_id',$fiscal_year_id);
        $this->db->where_in('budget_target_outlet.outlet_id',$outlet_ids);
        $results=$this->db->get()->result_array();
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']][$result['outlet_id']]=$result;
        }

        //variety lists
        $results=Budget_helper::get_crop_type_varieties(array($crop_id));
        foreach($results as $result)
        {
            $info=$this->initialize_row_edit_target_outlet_next_year($fiscal_years_previous_sales, $fiscal_years_next_budgets,$outlets,$result);
            foreach($fiscal_years_previous_sales as $fy)
            {
                if(isset($sales_previous[$fy['id']][$result['variety_id']]))
                {
                    $info['quantity_sale_'.$fy['id']]=$sales_previous[$fy['id']][$result['variety_id']]/1000;
                }
            }
            $quantity_prediction_outlet_total=0;
            $fiscal_year_serial=0;
            foreach($fiscal_years_next_budgets as $fy)
            {
                ++$fiscal_year_serial;
                $quantity_prediction_sub_total_outlet=0;
                foreach($outlet_ids as $outlet_id)
                {
                    if(isset($items_old[$result['variety_id']][$outlet_id]))
                    {
                        //$info['quantity_target_']
                        $info['quantity_prediction_outlet_'.$fiscal_year_serial.'_'.$outlet_id]=$items_old[$result['variety_id']][$outlet_id]['quantity_prediction_'.$fiscal_year_serial];

                        $quantity_prediction_outlet_total+=$info['quantity_prediction_outlet_'.$fiscal_year_serial.'_'.$outlet_id];
                        $quantity_prediction_sub_total_outlet+=$info['quantity_prediction_outlet_'.$fiscal_year_serial.'_'.$outlet_id];
                    }
                }
                $info['quantity_prediction_sub_total_outlet_'.$fiscal_year_serial]+=$quantity_prediction_sub_total_outlet;
            }
            $info['quantity_prediction_outlet_total']= $quantity_prediction_outlet_total;

            if(isset($target_zones[$result['variety_id']]))
            {
                $info['quantity_target_zi']=$target_zones[$result['variety_id']]['quantity_target'];
                $info['quantity_prediction_1']=$target_zones[$result['variety_id']]['quantity_prediction_1'];
                $info['quantity_prediction_2']=$target_zones[$result['variety_id']]['quantity_prediction_2'];
                $info['quantity_prediction_3']=$target_zones[$result['variety_id']]['quantity_prediction_3'];
            }
            $items[]=$info;
        }
        $this->json_return($items);
    }
    private function initialize_row_edit_target_outlet_next_year($fiscal_years_previous_sales, $fiscal_years_next_budgets,$outlets,$info)
    {
        $row=$this->get_preference_headers('edit_target_outlet_next_year');
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['crop_type_name']=$info['crop_type_name'];
        $row['variety_name']=$info['variety_name'];
        $row['variety_id']=$info['variety_id'];
        foreach($fiscal_years_previous_sales as $fy)
        {
            $row['quantity_sale_'.$fy['id']]=0;
        }
        $serial=0;
        foreach($fiscal_years_next_budgets as $fy)
        {
            ++$serial;
            $row['quantity_prediction_'.$serial]=0;
            foreach($outlets as $outlet)
            {
                $row['quantity_prediction_outlet_'.$serial.'_'.$outlet['outlet_id']]= 0;
            }
            $row['quantity_prediction_sub_total_outlet_'.$serial]=0;
        }
        return $row;
    }
    private function system_save_target_outlet_next_year()
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
        // validation assign zone
        if(!in_array($item_head['zone_id'], $this->user_zone_ids))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['zone_id'],'Zone Not Assigned');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Zone.';
            $this->json_return($ajax);
        }

        $info_target=$this->get_info_budget_target($item_head['fiscal_year_id'],$item_head['zone_id']);
        if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
        {
            if(($info_target['status_target_outlet_next_year_forward']==$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='Outlet Next 3 Years Target Already Assigned.';
                $this->json_return($ajax);
            }
            // validation DI assign target to ZSC forward status
            $info_target_di=$this->get_info_target_di($item_head['fiscal_year_id'], $info_target['division_id']);
            if(($info_target_di['status_target_zi_next_year_forward']!=$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='DI Assign To ZSC Next 3 Years Target Not Forwarded.';
                $this->json_return($ajax);
            }
        }

        // get outlet ids
        $outlet_ids[0]=0;
        $outlets=$this->get_outlets($item_head['zone_id']);
        foreach ($outlets as $outlet)
        {
            $outlet_ids[$outlet['outlet_id']]=$outlet['outlet_id'];
        }

        //old items
        $this->db->from($this->config->item('table_pos_si_budget_target_outlet').' budget_target_outlet');
        $this->db->select('budget_target_outlet.*');

        $this->db->where('budget_target_outlet.fiscal_year_id',$item_head['fiscal_year_id']);
        $this->db->where_in('budget_target_outlet.outlet_id',$outlet_ids);

        $results=$this->db->get()->result_array();
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']][$result['outlet_id']]=$result;
        }

        $this->db->trans_start();  //DB Transaction Handle START

        foreach($items as $variety_id=>$variety_info)
        {
            foreach($variety_info as $outlet_id=>$quantity_info)
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
                if(isset($items_old[$variety_id][$outlet_id]))
                {
                    if(($items_old[$variety_id][$outlet_id]['quantity_prediction_1']!=$quantity_prediction_1) || ($items_old[$variety_id][$outlet_id]['quantity_prediction_2']!=$quantity_prediction_2) || ($items_old[$variety_id][$outlet_id]['quantity_prediction_2']!=$quantity_prediction_2))
                    {
                        $data=array();
                        $data['quantity_prediction_1']=$quantity_prediction_1;
                        $data['quantity_prediction_2']=$quantity_prediction_2;
                        $data['quantity_prediction_3']=$quantity_prediction_3;
                        $data['date_updated_prediction_target']=$time;
                        $data['user_updated_prediction_target']=$user->user_id;
                        $this->db->set('revision_count_target_prediction','revision_count_target_prediction+1',false);
                        Query_helper::update($this->config->item('table_pos_si_budget_target_outlet'),$data,array('id='.$items_old[$variety_id][$outlet_id]['id']),false);
                    }
                }
                else
                {
                    $data=array();
                    $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                    $data['outlet_id']=$outlet_id;
                    $data['variety_id']=$variety_id;
                    $data['quantity_prediction_1']=$quantity_prediction_1;
                    $data['quantity_prediction_2']=$quantity_prediction_2;
                    $data['quantity_prediction_3']=$quantity_prediction_3;
                    $data['revision_count_target_prediction']=1;
                    $data['date_updated_prediction_target']=$time;
                    $data['user_updated_prediction_target']=$user->user_id;
                    Query_helper::add($this->config->item('table_pos_si_budget_target_outlet'),$data,false);
                }
            }
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list_target_outlet_next_year($item_head['fiscal_year_id'],$item_head['zone_id']);

        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }
    /*Assign Outlet Next 3 Target Forward*/
    private function system_forward_target_outlet_next_year($fiscal_year_id=0,$zone_id)
    {
        //$user = User_helper::get_user();
        $method='forward_target_outlet_next_year';
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
            // validation assign division
            if(!in_array($zone_id, $this->user_zone_ids))
            {
                System_helper::invalid_try(__FUNCTION__,$zone_id,'Zone Not Assigned');
                $ajax['status']=false;
                $ajax['system_message']='Invalid Zone.';
                $this->json_return($ajax);
            }
            //validation ZSC Budget & Outlet Target forward status
            $info_target=$this->get_info_budget_target($fiscal_year_id,$zone_id);
            if(($info_target['status_target_outlet_next_year_forward']==$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='Outlet Next 3 Years Target Already Assigned.';
                $this->json_return($ajax);
            }
            // validation DI assign target to ZSC forward status
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                $info_target_di=$this->get_info_target_di($fiscal_year_id, $info_target['division_id']);
                if(($info_target_di['status_target_zi_next_year_forward']!=$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='DI Assign To ZSC Next 3 Years Target Not Forwarded.';
                    $this->json_return($ajax);
                }
            }

            $data['fiscal_years_previous_sales']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
            $data['fiscal_years_next_budgets']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['division']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),'*',array('id ='.$info_target['division_id'],'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['zone']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),'*',array('id ='.$zone_id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['outlets']=$this->get_outlets($zone_id);
            $data['acres']=$this->get_acres($zone_id);

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']='ZSC Next 3 Years Target Forward To Outlet';
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['zone_id']=$zone_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/forward_target_outlet_next_year",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/forward_target_outlet_next_year/'.$fiscal_year_id.'/'.$zone_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_forward_target_outlet_next_year()
    {
        $items=array();
        //$this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $zone_id=$this->input->post('zone_id');

        //get zone target
        $results=Query_helper::get_info($this->config->item('table_bms_zi_budget_target_zone'),'*',array('fiscal_year_id ='.$fiscal_year_id, 'zone_id ='.$zone_id));
        $target_zones=array();
        foreach($results as $result)
        {
            $target_zones[$result['variety_id']]=$result;
        }

        $fiscal_years_previous_sales=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
        $sales_previous=$this->get_sales_previous_years_zone($fiscal_years_previous_sales,$zone_id);
        $fiscal_years_next_budgets=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);

        $outlet_ids[0]=0;
        $outlets=$this->get_outlets($zone_id);
        foreach ($outlets as $outlet)
        {
            $outlet_ids[$outlet['outlet_id']]=$outlet['outlet_id'];
        }

        // get old items
        $this->db->from($this->config->item('table_pos_si_budget_target_outlet').' budget_target_outlet');
        $this->db->select('budget_target_outlet.*');
        $this->db->where('budget_target_outlet.fiscal_year_id',$fiscal_year_id);
        $this->db->where_in('budget_target_outlet.outlet_id',$outlet_ids);
        $results=$this->db->get()->result_array();
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']][$result['outlet_id']]=$result;
        }

        //variety lists
        $results=Budget_helper::get_crop_type_varieties();

        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;

        $type_total=$this->initialize_row_forward_target_outlet_next_year($fiscal_years_previous_sales, $fiscal_years_next_budgets,$outlet_ids,'','','Total Type','');
        $crop_total=$this->initialize_row_forward_target_outlet_next_year($fiscal_years_previous_sales, $fiscal_years_next_budgets,$outlet_ids,'','Total Crop','','');
        $grand_total=$this->initialize_row_forward_target_outlet_next_year($fiscal_years_previous_sales, $fiscal_years_next_budgets, $outlet_ids,'Grand Total','','','');

        foreach($results as $result)
        {
            $info=$this->initialize_row_forward_target_outlet_next_year($fiscal_years_previous_sales, $fiscal_years_next_budgets, $outlet_ids,$result['crop_name'],$result['crop_type_name'],$result['variety_name']);
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
                }
            }
            $quantity_prediction_outlet_total=0;
            $fiscal_year_serial=0;
            foreach($fiscal_years_next_budgets as $fy)
            {
                ++$fiscal_year_serial;
                $quantity_prediction_sub_total_outlet=0;
                foreach($outlet_ids as $outlet_id)
                {
                    if(isset($items_old[$result['variety_id']][$outlet_id]))
                    {
                        //$info['quantity_target_']
                        $info['quantity_prediction_outlet_'.$fy['id'].'_'.$outlet_id]=$items_old[$result['variety_id']][$outlet_id]['quantity_prediction_'.$fiscal_year_serial];
                        $quantity_prediction_outlet_total+=$info['quantity_prediction_outlet_'.$fy['id'].'_'.$outlet_id];
                        $quantity_prediction_sub_total_outlet+=$info['quantity_prediction_outlet_'.$fy['id'].'_'.$outlet_id];
                    }
                }
                $info['quantity_prediction_sub_total_outlet_'.$fy['id']]+=$quantity_prediction_sub_total_outlet;
            }
            $info['quantity_prediction_outlet_total']= $quantity_prediction_outlet_total;

            if(isset($target_zones[$result['variety_id']]))
            {
                $info['quantity_target_zi']=$target_zones[$result['variety_id']]['quantity_target'];
                $info['quantity_prediction_1']=$target_zones[$result['variety_id']]['quantity_prediction_1'];
                $info['quantity_prediction_2']=$target_zones[$result['variety_id']]['quantity_prediction_2'];
                $info['quantity_prediction_3']=$target_zones[$result['variety_id']]['quantity_prediction_3'];
            }
            foreach($info as $key=>$r)
            {
                if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='pack_size')))
                {
                    $type_total[$key]+=$info[$key];
                    $crop_total[$key]+=$info[$key];
                    $grand_total[$key]+=$info[$key];
                }
            }
            $items[]=$info;
        }
        $items[]=$type_total;
        $items[]=$crop_total;
        $items[]=$grand_total;
        $this->json_return($items);
    }
    private function initialize_row_forward_target_outlet_next_year($fiscal_years_previous_sales, $fiscal_years_next_budgets,$outlet_ids,$crop_name,$crop_type_name,$variety_name)
    {
        $row=$this->get_preference_headers('forward_target_outlet_next_year');
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;
        foreach($fiscal_years_previous_sales as $fy)
        {
            $row['quantity_sale_'.$fy['id']]=0;
        }
        foreach($fiscal_years_next_budgets as $fy)
        {
            foreach($outlet_ids as $outlet_id)
            {
                $row['quantity_prediction_outlet_'.$fy['id'].'_'.$outlet_id]= 0;
            }
            $row['quantity_prediction_sub_total_outlet_'.$fy['id']]=0;
        }
        return $row;
    }
    private function system_save_forward_target_outlet_next_year()
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
        if($item_head['status_target_outlet_next_year_forward']!=$this->config->item('system_status_forwarded'))
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
        // validation assign zone
        if(!in_array($item_head['zone_id'], $this->user_zone_ids))
        {
            System_helper::invalid_try(__FUNCTION__,$item_head['zone_id'],'Zone Not Assigned');
            $ajax['status']=false;
            $ajax['system_message']='Invalid Zone.';
            $this->json_return($ajax);
        }
        $info_budget_target=$this->get_info_budget_target($item_head['fiscal_year_id'],$item_head['zone_id']);
        if(($info_budget_target['status_target_outlet_next_year_forward']==$this->config->item('system_status_forwarded')))
        {
            $ajax['status']=false;
            $ajax['system_message']='Outlet Next 3 Years Target Already Assigned.';
            $this->json_return($ajax);
        }
        // validation DI assign target to ZSC forward status
        if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
        {
            $info_target_di=$this->get_info_target_di($item_head['fiscal_year_id'], $info_budget_target['division_id']);
            if(($info_target_di['status_target_zi_next_year_forward']!=$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='DI Assign To ZSC Next 3 Years Target Not Forwarded.';
                $this->json_return($ajax);
            }
        }

        $this->db->trans_start();  //DB Transaction Handle START

        $data=array();
        $data['status_target_outlet_next_year_forward']=$item_head['status_target_outlet_next_year_forward'];
        $data['date_target_outlet_next_year_forwarded']=$time;
        $data['user_target_outlet_next_year_forwarded']=$user->user_id;
        Query_helper::update($this->config->item('table_bms_zi_budget_target'),$data,array('id='.$info_budget_target['id']),false);

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

    private function system_details($fiscal_year_id=0,$zone_id=0)
    {
        $user = User_helper::get_user();
        $method='search_details';//this is because after save preference it will go to list view.because details view need additional parameter
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            if(!($zone_id>0))
            {
                $zone_id=$this->input->post('zone_id');
            }
            //for jqx grid
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['area_id']=$zone_id;

            $data['title']=$this->lang->line('LABEL_TITLE_DETAILS');
            $outlets=$this->get_outlets($zone_id);;
            $data['areas']=array();//here areas means sub area or dealers
            foreach($outlets as $result)
            {
                $data['areas'][]=array('value'=>$result['outlet_id'],'text'=>$result['outlet_name']);

            }
            $data['sub_column_group_name']='Showrooms';
            $data['fiscal_years_next_predictions']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);
            $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            //jqx grid section end

            //details section start
            $data['fiscal_year_budget_target']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);

            $this->db->from($this->config->item('table_login_setup_location_zones').' zone');
            $this->db->select('division.name division_name');
            $this->db->select('zone.name zone_name');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->where('zone.id',$zone_id);
            $data['info_area']=$this->db->get()->row_array();
            $data['acres']=$this->get_acres($zone_id);

            $budget_target=$this->get_info_budget_target($fiscal_year_id,$zone_id);
            $user_ids=array();
            $user_ids[$budget_target['user_created']]=$budget_target['user_created'];
            if($budget_target['user_budget_forwarded']>0)
            {
                $user_ids[$budget_target['user_budget_forwarded']]=$budget_target['user_budget_forwarded'];
            }
            if($budget_target['user_target_outlet_forwarded']>0)
            {
                $user_ids[$budget_target['user_target_outlet_forwarded']]=$budget_target['user_target_outlet_forwarded'];
            }
            if($budget_target['user_target_outlet_next_year_forwarded']>0)
            {
                $user_ids[$budget_target['user_target_outlet_next_year_forwarded']]=$budget_target['user_target_outlet_next_year_forwarded'];
            }
            $division_id=$budget_target['division_id'];
            $budget_target_superior=$this->get_info_target_di($fiscal_year_id,$division_id);
            if($budget_target_superior)
            {
                if($budget_target_superior['user_target_zi_forwarded']>0)
                {
                    $user_ids[$budget_target_superior['user_target_zi_forwarded']]=$budget_target_superior['user_target_zi_forwarded'];
                }
                if($budget_target_superior['user_target_zi_next_year_forwarded']>0)
                {
                    $user_ids[$budget_target_superior['user_target_zi_next_year_forwarded']]=$budget_target_superior['user_target_zi_next_year_forwarded'];
                }
            }
            $users=System_helper::get_users_info($user_ids);

            $data['info_basic']=array();
            //budget forward area(zone)
            $result=array();
            $result['label_1']=$this->lang->line('LABEL_STATUS_BUDGET_FORWARD_AREA').' Status';
            $result['value_1']=$budget_target['status_budget_forward'];
            $result['label_2']='';
            $result['value_2']='';
            $data['info_basic'][]=$result;
            if($budget_target['status_budget_forward']==$this->config->item('system_status_forwarded'))
            {
                $result=array();
                $result['label_1']=$this->lang->line('LABEL_STATUS_BUDGET_FORWARD_AREA').' By';
                $result['value_1']=$users[$budget_target['user_budget_forwarded']]['name'];
                $result['label_2']=$this->lang->line('LABEL_STATUS_BUDGET_FORWARD_AREA').' Time';
                $result['value_2']=System_helper::display_date_time($budget_target['date_budget_forwarded']);
                $data['info_basic'][]=$result;
            }

            //target forward area(to zone from di)
            $result=array();
            $result['label_1']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA').' Status';
            $result['value_1']=$this->config->item('system_status_pending');
            if($budget_target_superior['status_target_zi_forward']==$this->config->item('system_status_forwarded'))
            {
                $result['value_1']=$this->config->item('system_status_forwarded');
            }
            $result['label_2']='';
            $result['value_2']='';
            $data['info_basic'][]=$result;

            if($budget_target_superior['status_target_zi_forward']==$this->config->item('system_status_forwarded'))
            {
                $result=array();
                $result['label_1']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA').' By';
                $result['value_1']=$users[$budget_target_superior['user_target_zi_forwarded']]['name'];
                $result['label_2']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA').' Time';
                $result['value_2']=System_helper::display_date_time($budget_target_superior['date_target_zi_forwarded']);
                $data['info_basic'][]=$result;
            }
            //target forward sub area(to outlets from ZI)
            $result=array();
            $result['label_1']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA_SUB').' Status';
            $result['value_1']=$budget_target['status_target_outlet_forward'];
            $result['label_2']='';
            $result['value_2']='';
            $data['info_basic'][]=$result;
            if($budget_target['status_target_outlet_forward']==$this->config->item('system_status_forwarded'))
            {
                $result=array();
                $result['label_1']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA_SUB').' By';
                $result['value_1']=$users[$budget_target['user_target_outlet_forwarded']]['name'];
                $result['label_2']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA_SUB').' Time';
                $result['value_2']=System_helper::display_date_time($budget_target['date_target_outlet_forwarded']);
                $data['info_basic'][]=$result;
            }
            //target forward area 3yr(to ZI from DI)
            $result=array();
            $result['label_1']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA_NEXT_YEAR').' Status';
            $result['value_1']=$this->config->item('system_status_pending');
            if($budget_target_superior['status_target_zi_next_year_forward']==$this->config->item('system_status_forwarded'))
            {
                $result['value_1']=$this->config->item('system_status_forwarded');
            }
            $result['label_2']='';
            $result['value_2']='';
            $data['info_basic'][]=$result;

            if($budget_target_superior['status_target_zi_next_year_forward']==$this->config->item('system_status_forwarded'))
            {
                $result=array();
                $result['label_1']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA_NEXT_YEAR').' By';
                $result['value_1']=$users[$budget_target_superior['user_target_zi_next_year_forwarded']]['name'];
                $result['label_2']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA_NEXT_YEAR').' Time';
                $result['value_2']=System_helper::display_date_time($budget_target_superior['date_target_zi_next_year_forwarded']);
                $data['info_basic'][]=$result;
            }
            //target forward sub area 3yr(to outlets from ZI)
            $result=array();
            $result['label_1']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA_SUB_NEXT_YEAR').' Status';
            $result['value_1']=$budget_target['status_target_outlet_next_year_forward'];
            $result['label_2']='';
            $result['value_2']='';
            $data['info_basic'][]=$result;
            if($budget_target['status_target_outlet_next_year_forward']==$this->config->item('system_status_forwarded'))
            {
                $result=array();
                $result['label_1']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA_SUB_NEXT_YEAR').' By';
                $result['value_1']=$users[$budget_target['user_target_outlet_next_year_forwarded']]['name'];
                $result['label_2']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA_SUB_NEXT_YEAR').' Time';
                $result['value_2']=System_helper::display_date_time($budget_target['date_target_outlet_next_year_forwarded']);
                $data['info_basic'][]=$result;
            }
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->common_view_location."/details",$data,true));

            $ajax['status']=true;
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$fiscal_year_id.'/'.$zone_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_details()
    {
        $items=array();

        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $zone_id=$this->input->post('area_id');

        $outlets=$this->get_outlets($zone_id);;
        $areas=array();
        foreach($outlets as $result)
        {
            $areas[]=array('value'=>$result['outlet_id'],'text'=>$result['outlet_name']);

        }

        //get variety pricing
        $variety_pricing=array();
        $results=Query_helper::get_info($this->config->item('table_bms_setup_budget_config_variety_pricing'),array('variety_id','amount_price_net amount_price'),array('fiscal_year_id ='.$fiscal_year_id));
        foreach($results as $result)
        {
            $variety_pricing[$result['variety_id']]=$result['amount_price'];
        }

        //getting sub area budget and target
        $budget_target_sub=array();//showroom budget_target

        $this->db->from($this->config->item('table_pos_si_budget_target_outlet').' bt');
        $this->db->select('bt.outlet_id area_id');
        $this->db->select('bt.variety_id,bt.quantity_budget,bt.quantity_target');

        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = bt.outlet_id','INNER');
        $this->db->where('cus_info.revision',1);

        $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->where('t.zone_id',$zone_id);
        $this->db->where('bt.fiscal_year_id',$fiscal_year_id);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $budget_target_sub[$result['variety_id']][$result['area_id']]=$result;
        }
        //getting budget and target
        $budget_target=array();
        $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' bt');
        $this->db->where('bt.zone_id',$zone_id);
        $this->db->select('bt.*');
        $this->db->where('bt.fiscal_year_id',$fiscal_year_id);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $budget_target[$result['variety_id']]=$result;
        }
        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');

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

        $type_total=$this->initialize_row_details(array('variety_name'=>'Total Type'),$areas);
        $crop_total=$this->initialize_row_details(array('crop_type_name'=>'Total Crop'),$areas);
        $grand_total=$this->initialize_row_details(array('crop_name'=>'Grand Total'),$areas);

        foreach($results as $result)
        {
            //pricing set
            if(isset($variety_pricing[$result['variety_id']]))
            {
                $result['price_unit_kg_amount']=$variety_pricing[$result['variety_id']];
            }
            //budget target set
            if(isset($budget_target[$result['variety_id']]))
            {
                $result['quantity_budget']=$budget_target[$result['variety_id']]['quantity_budget'];
                $result['quantity_target']=$budget_target[$result['variety_id']]['quantity_target'];
                $result['quantity_prediction_1']=$budget_target[$result['variety_id']]['quantity_prediction_1'];
                $result['quantity_prediction_2']=$budget_target[$result['variety_id']]['quantity_prediction_2'];
                $result['quantity_prediction_3']=$budget_target[$result['variety_id']]['quantity_prediction_3'];
            }
            //sub budget target set
            if(isset($budget_target_sub[$result['variety_id']]))
            {
                foreach($budget_target_sub[$result['variety_id']] as $area_id=>$bud_tar)
                {
                    $result['quantity_budget_'.$area_id]=$bud_tar['quantity_budget'];
                    $result['quantity_target_'.$area_id]=$bud_tar['quantity_target'];
                }
            }
            $info=$this->initialize_row_details($result,$areas);
            if(!$first_row)
            {
                if($prev_crop_name!=$info['crop_name'])
                {
                    $type_total['crop_name']=$prev_crop_name;
                    $type_total['crop_type_name']=$prev_type_name;
                    $crop_total['crop_name']=$prev_crop_name;

                    $items[]=$type_total;
                    $items[]=$crop_total;
                    $type_total=$this->reset_row($type_total);
                    $crop_total=$this->reset_row($crop_total);
                    $prev_crop_name=$info['crop_name'];
                    $prev_type_name=$info['crop_type_name'];

                }
                elseif($prev_type_name!=$info['crop_type_name'])
                {
                    $type_total['crop_name']=$prev_crop_name;
                    $type_total['crop_type_name']=$prev_type_name;

                    $items[]=$type_total;
                    $type_total=$this->reset_row($type_total);
                    //$info['crop_name']='';
                    $prev_type_name=$info['crop_type_name'];
                }
                else
                {
                    //$info['crop_name']='';
                    //info['crop_type_name']='';
                }
            }
            else
            {
                $prev_crop_name=$info['crop_name'];
                $prev_type_name=$info['crop_type_name'];
                $first_row=false;
            }
            $items[]=$info;

            foreach($info  as $key=>$r)
            {
                if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')||($key=='price_unit_kg_amount')))
                {
                    $type_total[$key]+=$info[$key];
                    $crop_total[$key]+=$info[$key];
                    $grand_total[$key]+=$info[$key];
                }
            }

        }
        $items[]=$type_total;
        $items[]=$crop_total;
        $items[]=$grand_total;
        $this->json_return($items);
    }
    private function initialize_row_details($info,$areas)
    {
        $row=array();
        $row['crop_name']=isset($info['crop_name'])?$info['crop_name']:'';
        $row['crop_type_name']=isset($info['crop_type_name'])?$info['crop_type_name']:'';
        $row['variety_name']=isset($info['variety_name'])?$info['variety_name']:'';
        $row['price_unit_kg_amount']=isset($info['price_unit_kg_amount'])?$info['price_unit_kg_amount']:0;
        $row['budget_kg']=isset($info['quantity_budget'])?$info['quantity_budget']:0;
        $row['budget_amount']=$row['budget_kg']*$row['price_unit_kg_amount'];

        $row['target_kg']=isset($info['quantity_target'])?$info['quantity_target']:0;
        $row['target_amount']=$row['target_kg']*$row['price_unit_kg_amount'];
        foreach($areas as $area)
        {
            $row['budget_sub_'.$area['value'].'_kg']=isset($info['quantity_budget_'.$area['value']])?$info['quantity_budget_'.$area['value']]:0;
            $row['budget_sub_'.$area['value'].'_amount']=$row['budget_sub_'.$area['value'].'_kg']*$row['price_unit_kg_amount'];
            $row['target_sub_'.$area['value'].'_kg']=isset($info['quantity_target_'.$area['value']])?$info['quantity_target_'.$area['value']]:0;
            $row['target_sub_'.$area['value'].'_amount']=$row['target_sub_'.$area['value'].'_kg']*$row['price_unit_kg_amount'];;
        }

        $row['prediction_1_kg']=isset($info['quantity_prediction_1'])?$info['quantity_prediction_1']:0;
        $row['prediction_2_kg']=isset($info['quantity_prediction_2'])?$info['quantity_prediction_2']:0;
        $row['prediction_3_kg']=isset($info['quantity_prediction_3'])?$info['quantity_prediction_3']:0;

        $row['prediction_1_amount']=$row['prediction_1_kg']*$row['price_unit_kg_amount'];
        $row['prediction_2_amount']=$row['prediction_2_kg']*$row['price_unit_kg_amount'];
        $row['prediction_3_amount']=$row['prediction_3_kg']*$row['price_unit_kg_amount'];
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
    private function get_info_budget_target($fiscal_year_id,$zone_id)
    {
        $user = User_helper::get_user();
        $time=time();

        $this->db->from($this->config->item('table_pos_si_budget_target').' item');
        $this->db->select('item.*');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = item.outlet_id AND cus_info.revision = 1','INNER');
        $this->db->join($this->config->item('table_login_setup_location_districts').' district','district.id = cus_info.district_id','INNER');
        $this->db->join($this->config->item('table_login_setup_location_territories').' territory','territory.id = district.territory_id','INNER');
        $this->db->select('territory.zone_id');
        $this->db->where('item.fiscal_year_id',$fiscal_year_id);
        $this->db->where('territory.zone_id',$zone_id);
        $results=$this->db->get()->result_array();
        $budget_target_si=array();
        foreach($results as $result)
        {
            $budget_target_si[$result['outlet_id']]=$result;
        }
        $outlets=$this->get_outlets($zone_id);
        foreach($outlets as $outlet)
        {
            if(!isset($budget_target_si[$outlet['outlet_id']]))
            {
                $data=array();
                $data['fiscal_year_id'] = $fiscal_year_id;
                $data['outlet_id'] = $outlet['outlet_id'];
                $data['date_created'] = $time;
                $data['user_created'] = $user->user_id;
                Query_helper::add($this->config->item('table_pos_si_budget_target'),$data,false);
            }
        }

        //$info=Query_helper::get_info($this->config->item('table_bms_zi_budget_target'),'*',array('fiscal_year_id ='.$fiscal_year_id,'zone_id ='.$zone_id),1);
        $this->db->from($this->config->item('table_bms_zi_budget_target').' item');
        $this->db->select('item.*');
        $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = item.zone_id','INNER');
        $this->db->select('zone.division_id');
        $this->db->where('item.fiscal_year_id',$fiscal_year_id);
        $this->db->where('item.zone_id',$zone_id);
        $info=$this->db->get()->row_array();
        if(!$info)
        {
            $data=array();
            $data['fiscal_year_id'] = $fiscal_year_id;
            $data['zone_id'] = $zone_id;
            $data['date_created'] = $time;
            $data['user_created'] = $user->user_id;
            $id=Query_helper::add($this->config->item('table_bms_zi_budget_target'),$data,false);
            //$info=Query_helper::get_info($this->config->item('table_bms_zi_budget_target'),'*',array('id ='.$id),1);\
            $this->db->from($this->config->item('table_bms_zi_budget_target').' item');
            $this->db->select('item.*');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = item.zone_id','INNER');
            $this->db->select('zone.division_id');
            $this->db->where('item.id',$id);
            $info=$this->db->get()->row_array();
        }
        return $info;
    }
    private function get_info_target_di($fiscal_year_id, $division_id)
    {
        $info=Query_helper::get_info($this->config->item('table_bms_di_budget_target'),'*',array('fiscal_year_id ='.$fiscal_year_id,'division_id ='.$division_id),1);
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
        $this->db->where('cus_info.type',$this->config->item('system_customer_type_outlet_id'));  // i am not sure this where condition delete or not
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
