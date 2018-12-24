<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Budget_hom_budget_target extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $common_view_location;
    
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission(get_class());
        $this->controller_url=strtolower(get_class());
        $this->common_view_location='budget_zi_budget_target';
        $this->load->helper('budget');
        $this->lang->load('budget');
        $this->language_labels();
    }
    private function language_labels()
    {
        // area
        $this->lang->language['LABEL_STATUS_BUDGET_FORWARD_AREA']='HOM Budget and Prediction';
        // area sub
        $this->lang->language['LABEL_STATUS_TARGET_FORWARD_AREA_SUB']='Division Target Assigned';
        // superior area
        $this->lang->language['LABEL_STATUS_TARGET_FORWARD_AREA']='HOM Target Assigned';
        $this->lang->language['LABEL_STATUS_TARGET_FORWARD_AREA_NEXT_YEAR']='HOM 3years Target Assigned';
        $this->lang->language['LABEL_STATUS_TARGET_FORWARD_AREA_SUB_NEXT_YEAR']='Division 3years Target Assigned';
        // jqx grid
        $this->lang->language['LABEL_BUDGET_SUB_KG']='Division Budget (Kg)';
        $this->lang->language['LABEL_BUDGET_SUB_AMOUNT']='Division Budget (Amount)';
        $this->lang->language['LABEL_TARGET_SUB_KG']='Division Target (Kg)';
        $this->lang->language['LABEL_TARGET_SUB_AMOUNT']='Division Target (Amount)';

    }
    public function index($action="list", $id=0,$id1=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }

        elseif($action=="list_budget_hom")
        {
            $this->system_list_budget_hom($id,$id1);
        }
        elseif($action=="get_items_budget_hom")
        {
            $this->system_get_items_budget_hom();
        }
        elseif($action=="edit_budget_hom")
        {
            $this->system_edit_budget_hom($id,$id1);
        }
        elseif($action=="get_items_edit_budget_hom")
        {
            $this->system_get_items_edit_budget_hom();
        }
        elseif($action=="save_budget_hom")
        {
            $this->system_save_budget_hom();
        }
        elseif($action=="forward_budget_hom")
        {
            $this->system_forward_budget_hom($id,$id1);
        }
        elseif($action=="get_items_forward_budget_hom")
        {
            $this->system_get_items_forward_budget_hom();
        }
        elseif($action=="save_forward_budget_hom")
        {
            $this->system_save_forward_budget_hom();
        }

        elseif($action=="list_target_di")
        {
            $this->system_list_target_di($id,$id1);
        }
        elseif($action=="get_items_target_di")
        {
            $this->system_get_items_target_di();
        }
        elseif($action=="edit_target_di")
        {
            $this->system_edit_target_di($id,$id1);
        }
        elseif($action=="get_items_edit_target_di")
        {
            $this->system_get_items_edit_target_di();
        }
        elseif($action=="save_target_di")
        {
            $this->system_save_target_di();
        }

        elseif($action=="forward_target_di")
        {
            $this->system_forward_target_di($id,$id1);
        }
        elseif($action=="get_items_forward_target_di")
        {
            $this->system_get_items_forward_target_di();
        }
        elseif($action=="save_forward_target_di")
        {
            $this->system_save_forward_target_di();
        }

        elseif($action=="list_target_di_next_year")
        {
            $this->system_list_target_di_next_year($id,$id1);
        }
        elseif($action=="get_items_target_di_next_year")
        {
            $this->system_get_items_target_di_next_year();
        }
        elseif($action=="edit_target_di_next_year")
        {
            $this->system_edit_target_di_next_year($id,$id1);
        }
        elseif($action=="get_items_edit_target_di_next_year")
        {
            $this->system_get_items_edit_target_di_next_year();
        }
        elseif($action=="save_target_di_next_year")
        {
            $this->system_save_target_di_next_year();
        }

        elseif($action=="forward_target_di_next_year")
        {
            $this->system_forward_target_di_next_year($id,$id1);
        }
        elseif($action=="get_items_forward_target_di_next_year")
        {
            $this->system_get_items_forward_target_di_next_year();
        }
        elseif($action=="save_forward_target_di_next_year")
        {
            $this->system_save_forward_target_di_next_year();
        }

        elseif($action=="details")
        {
            $this->system_details($id);
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
            $data['status_budget_forward']= 1;
            $data['status_target_forward']= 1;
            $data['status_target_di_forward']= 1;
            $data['status_target_di_next_year_forward']= 1;
        }
        else if($method=='list_budget_hom')
        {
            $data['crop_id']= 1;
            $data['crop_name']= 1;
            $data['number_of_variety_active']= 1;
            $data['number_of_variety_budgeted']= 1;
            $data['number_of_variety_budget_due']= 1;
        }
        else if($method=='edit_budget_hom')
        {
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            $data['quantity_budget']= 1;
            $data['quantity_budget_division_total']= 1;
        }
        else if($method=='forward_budget_hom')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            $data['quantity_budget']= 1;
            $data['quantity_budget_division_total']= 1;
        }
        else if($method=='list_target_di')
        {
            $data['crop_id']= 1;
            $data['crop_name']= 1;
            $data['number_of_variety_active']= 1;
            $data['number_of_variety_targeted']= 1;
            $data['number_of_variety_target_due']= 1;
        }
        else if($method=='edit_target_di')
        {
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            $data['quantity_budget']= 1;
            $data['quantity_target']= 1;
            $data['quantity_target_division_total']= 1;
        }
        else if($method=='forward_target_di')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            $data['quantity_budget']= 1;
            $data['quantity_target']= 1;
            $data['quantity_target_division_total']= 1;
        }
        else if($method=='edit_target_di_next_year')
        {
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            $data['quantity_target']= 1;
            $data['quantity_prediction_1']= 1;
            $data['quantity_prediction_2']= 1;
            $data['quantity_prediction_3']= 1;
            $data['quantity_prediction_division_total']= 1;
        }
        else if($method=='forward_target_di_next_year')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            $data['quantity_target']= 1;
            $data['quantity_prediction_1']= 1;
            $data['quantity_prediction_2']= 1;
            $data['quantity_prediction_3']= 1;
            $data['quantity_prediction_division_total']= 1;
            //$data['quantity_prediction_division_total']=1; // check this item using jqx grid
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
            $data['title']="Yearly HOM Budget & Target List";
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
        $this->db->select('budget_target.status_budget_forward, budget_target.status_target_forward, budget_target.status_target_di_forward, budget_target.status_target_di_next_year_forward');
        $this->db->select('budget_target.fiscal_year_id');
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
            $data['status_budget_forward']=$this->config->item('system_status_pending');
            $data['status_target_forward']=$this->config->item('system_status_pending');
            $data['status_target_di_forward']=$this->config->item('system_status_pending');
            $data['status_target_di_next_year_forward']=$this->config->item('system_status_pending');
            if(isset($budget_target[$fy['id']]))
            {
                $data['status_budget_forward']=$budget_target[$fy['id']]['status_budget_forward'];
                $data['status_target_forward']=$budget_target[$fy['id']]['status_target_forward'];
                $data['status_target_di_forward']=$budget_target[$fy['id']]['status_target_di_forward'];
                $data['status_target_di_next_year_forward']=$budget_target[$fy['id']]['status_target_di_next_year_forward'];
            }
            $items[]=$data;
        }
        $this->json_return($items);
    }

    /* for budget*/
    private function system_list_budget_hom($fiscal_year_id=0)
    {
        //$user = User_helper::get_user();
        $method='list_budget_hom';
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
            $info_budget=$this->get_info_budget_target($fiscal_year_id);
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
            $data['title']="HOM Yearly Budget Crop list";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_budget_hom",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list_budget_hom/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_budget_hom()
    {
        $items=array();
        $fiscal_year_id=$this->input->post('fiscal_year_id');

        //get budget revision
        $this->db->from($this->config->item('table_bms_hom_budget_target_hom').' budget_target_hom');
        $this->db->select('SUM(CASE WHEN budget_target_hom.quantity_budget>0 then 1 ELSE 0 END) number_of_variety_budgeted',false);

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = budget_target_hom.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->select('crop_type.crop_id');
        $this->db->where('budget_target_hom.fiscal_year_id',$fiscal_year_id);
        $this->db->group_by('crop_type.crop_id');
        $results=$this->db->get()->result_array();
        $budgeted=array();
        foreach($results as $result)
        {
            $budgeted[$result['crop_id']]=$result['number_of_variety_budgeted'];
        }
        //crop list
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
    private function system_edit_budget_hom($fiscal_year_id=0,$crop_id=0)
    {
        //$user = User_helper::get_user();
        $method='edit_budget_hom';
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
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
            // valid crop check
            $crop=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),'*',array('id ='.$crop_id),1);
            if(!$crop)
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong crop id.';
                $this->json_return($ajax);
            }
            //validation forward
            $info_budget=$this->get_info_budget_target($fiscal_year_id);
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
            $data['fiscal_years_next_budgets']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['crop']=$crop;
            $data['divisions']=User_helper::get_assigned_divisions();
            $data['acres']=$this->get_acres($crop_id);

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="HOM Yearly Budget for (".$data['crop']['name'].')';
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['crop_id']=$crop_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_budget_hom",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_budget_hom/'.$fiscal_year_id.'/'.$crop_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_edit_budget_hom()
    {
        $items=array();
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $crop_id=$this->input->post('crop_id');

        //Division budget & get division wise forward status
        $division_ids[0]=0;
        $divisions=User_helper::get_assigned_divisions();
        foreach ($divisions as $division) 
        {
            $division_ids[$division['division_id']]=$division['division_id'];
        }

        $this->db->from($this->config->item('table_bms_di_budget_target_division').' budget_target_division');
        $this->db->select('budget_target_division.*');
        $this->db->where('budget_target_division.fiscal_year_id',$fiscal_year_id);
        $this->db->where_in('budget_target_division.division_id',$division_ids);
        $this->db->join($this->config->item('table_bms_di_budget_target').' budget_target','budget_target.fiscal_year_id=budget_target_division.fiscal_year_id AND budget_target.division_id=budget_target_division.division_id','INNER');
        $this->db->select('budget_target.status_budget_forward');
        $results=$this->db->get()->result_array();
        $budget_divisions=array();
        foreach($results as $result)
        {
            $budget_divisions[$result['division_id']][$result['variety_id']]=$result;
        }

        $fiscal_years_previous_sales=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
        $sales_previous=$this->get_sales_previous_years_hom($fiscal_years_previous_sales);

        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_hom_budget_target_hom'),'*',array('fiscal_year_id ='.$fiscal_year_id));
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']]=$result;
        }

        //variety lists
        $results=Budget_helper::get_crop_type_varieties(array($crop_id));
        foreach($results as $result)
        {
            $info=$this->initialize_row_edit_budget_hom($fiscal_years_previous_sales,$divisions,$result);
            foreach($fiscal_years_previous_sales as $fy)
            {
                if(isset($sales_previous[$fy['id']][$result['variety_id']]))
                {
                    $info['quantity_sale_'.$fy['id']]=$sales_previous[$fy['id']][$result['variety_id']]/1000;
                }
            }

            $quantity_budget_division_total=0;
            foreach($divisions as $division)
            {
                if(isset($budget_divisions[$division['division_id']][$result['variety_id']]))
                {
                    if($budget_divisions[$division['division_id']][$result['variety_id']]['status_budget_forward']==$this->config->item('system_status_pending'))
                    {
                        $info['quantity_budget_division_'.$division['division_id']]= 'N/F';
                    }
                    else
                    {
                        $info['quantity_budget_division_'.$division['division_id']]= $budget_divisions[$division['division_id']][$result['variety_id']]['quantity_budget'];
                        $quantity_budget_division_total+=$budget_divisions[$division['division_id']][$result['variety_id']]['quantity_budget'];
                    }
                }
            }
            $info['quantity_budget_division_total']= $quantity_budget_division_total;

            if(isset($items_old[$result['variety_id']]))
            {
                $info['quantity_budget']=$items_old[$result['variety_id']]['quantity_budget'];
            }
            $items[]=$info;
        }

        $this->json_return($items);
    }
    private function initialize_row_edit_budget_hom($fiscal_years,$divisions,$info)
    {
        $row=$this->get_preference_headers('edit_budget_hom');
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
        foreach($divisions as $division)
        {
            $row['quantity_budget_division_'.$division['division_id']]= 'N/D';
        }

        return $row;
    }
    private function system_save_budget_hom()
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
        $info_budget=$this->get_info_budget_target($item_head['fiscal_year_id']);
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
        $results=Query_helper::get_info($this->config->item('table_bms_hom_budget_target_hom'),'*',array('fiscal_year_id ='.$item_head['fiscal_year_id']));
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']]=$result;
        }

        $this->db->trans_start();  //DB Transaction Handle START
        foreach($items as $variety_id=>$quantity_info)
        {
            $quantity_budget=$quantity_info['quantity_budget'];
            if(isset($items_old[$variety_id]))
            {
                if(($items_old[$variety_id]['quantity_budget'] != $quantity_budget))
                {
                    $this->db->set('revision_count_budget','revision_count_budget+1',false);
                    $data['quantity_budget']=$quantity_budget;
                    $data['date_updated_budget']=$time;
                    $data['user_updated_budget']=$user->user_id;
                    Query_helper::update($this->config->item('table_bms_hom_budget_target_hom'),$data,array('id='.$items_old[$variety_id]['id']),false);
                }
            }
            else
            {
                $data=array();
                $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                $data['variety_id']=$variety_id;
                $data['quantity_budget']=0;
                if($quantity_budget>0)
                {
                    $data['quantity_budget']=$quantity_budget;
                    $data['revision_count_budget']=1;
                }
                $data['date_updated_budget'] = $time;
                $data['user_updated_budget'] = $user->user_id;
                Query_helper::add($this->config->item('table_bms_hom_budget_target_hom'),$data,false);
            }
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list_budget_hom($item_head['fiscal_year_id']);

        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    /* for budget forward */
    private function system_forward_budget_hom($fiscal_year_id=0)
    {
        //$user = User_helper::get_user();
        $method='forward_budget_hom';
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
            $info_budget=$this->get_info_budget_target($fiscal_year_id);
            if(($info_budget['status_budget_forward']==$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='Budget Already Forwarded.';
                $this->json_return($ajax);
            }

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['fiscal_years_previous_sales']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
            $data['fiscal_years_next_budgets']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);
            // get zone list
            $this->db->from($this->config->item('table_bms_di_budget_target_division').' budget_target_division');
            $this->db->join($this->config->item('table_bms_di_budget_target').' budget_target','budget_target.fiscal_year_id=budget_target_division.fiscal_year_id AND budget_target.division_id=budget_target_division.division_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = budget_target_division.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            //$this->db->where('budget_target.status_budget_forward',$this->config->item('system_status_forwarded'));
            $this->db->where('budget_target_division.fiscal_year_id',$fiscal_year_id);
            $this->db->group_by('budget_target_division.division_id');
            $this->db->order_by('divisions.ordering, divisions.id');
            $data['divisions']=$this->db->get()->result_array();

            $data['acres']=$this->get_acres();

            $data['fiscal_year_budget_target']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['title']="HOM Forward/Complete budget";
            $data['options']['fiscal_year_id']=$fiscal_year_id;

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/forward_budget_hom",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/forward_budget_hom/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_forward_budget_hom()
    {
        $items=array();
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $fiscal_years_previous_sales=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
        $sales_previous=$this->get_sales_previous_years_hom($fiscal_years_previous_sales);

        // get division budgeted quantity & division ids
        $results=Query_helper::get_info($this->config->item('table_bms_di_budget_target_division'),'*',array('fiscal_year_id ='.$fiscal_year_id));
        $division_ids[0]=0;
        $budget_divisions=array();
        foreach($results as $result)
        {
            $division_ids[$result['division_id']]=$result['division_id'];
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
        $results=Budget_helper::get_crop_type_varieties();

        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;

        $type_total=$this->initialize_row_forward_budget_hom($fiscal_years_previous_sales,$division_ids,'','','Total Type','');
        $crop_total=$this->initialize_row_forward_budget_hom($fiscal_years_previous_sales,$division_ids,'','Total Crop','','');
        $grand_total=$this->initialize_row_forward_budget_hom($fiscal_years_previous_sales,$division_ids,'Grand Total','','','');

        foreach($results as $result)
        {
            $info=$this->initialize_row_forward_budget_hom($fiscal_years_previous_sales,$division_ids,$result['crop_name'],$result['crop_type_name'],$result['variety_name']);
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
            if(isset($old_items[$result['variety_id']]))
            {
                $info['quantity_budget']=$old_items[$result['variety_id']]['quantity_budget'];
            }
            $quantity_budget_division_total=0;
            foreach($division_ids as $division_id)
            {
                if(isset($budget_divisions[$division_id][$result['variety_id']]))
                {
                    $info['quantity_budget_division_'.$division_id]=$budget_divisions[$division_id][$result['variety_id']]['quantity_budget'];
                    $quantity_budget_division_total+=$info['quantity_budget_division_'.$division_id];
                }
            }
            $info['quantity_budget_division_total']=$quantity_budget_division_total;
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
    private function initialize_row_forward_budget_hom($fiscal_years,$division_ids,$crop_name,$crop_type_name,$variety_name)
    {
        $row=$this->get_preference_headers('forward_budget_hom');
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
        foreach($division_ids as $division_id)
        {
            $row['quantity_budget_division_'.$division_id]= 0;
        }

        return $row;
    }
    private function system_save_forward_budget_hom()
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
        $info_budget=$this->get_info_budget_target($item_head['fiscal_year_id']);
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
        Query_helper::update($this->config->item('table_bms_hom_budget_target'),$data,array('id='.$info_budget['id']),false);

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

    /* for DI Assign target*/
    private function system_list_target_di($fiscal_year_id=0)
    {
        //$user = User_helper::get_user();
        $method='list_target_di';
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
            $info_target=$this->get_info_budget_target($fiscal_year_id);
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                if(($info_target['status_target_forward']!=$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='HOM Target Not Forwarded.';
                    $this->json_return($ajax);
                }
                if(($info_target['status_target_di_forward']==$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='DI Target Already Assigned.';
                    $this->json_return($ajax);
                }
            }

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['title']="Assign DI Target :: Crop list";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_target_di",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list_target_di/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_target_di()
    {
        $items=array();
        $fiscal_year_id=$this->input->post('fiscal_year_id');

        //get Target Revision
        $this->db->from($this->config->item('table_bms_di_budget_target_division').' budget_target_division');
        $this->db->select('COUNT(DISTINCT budget_target_division.variety_id) number_of_variety_targeted',false);

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = budget_target_division.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');

        $this->db->select('crop_type.crop_id');
        $this->db->where('budget_target_division.fiscal_year_id',$fiscal_year_id);
        $this->db->where('budget_target_division.quantity_target > ',0);
        $this->db->group_by('crop_type.crop_id');
        $results=$this->db->get()->result_array();
        $targeted=array();
        foreach($results as $result)
        {
            $targeted[$result['crop_id']]=$result['number_of_variety_targeted'];
        }
        //crop list
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
    private function system_edit_target_di($fiscal_year_id=0,$crop_id=0)
    {
        //$user = User_helper::get_user();
        $method='edit_target_di';
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
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
            // valid crop check
            $crop=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),'*',array('id ='.$crop_id),1);
            if(!$crop)
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong crop id.';
                $this->json_return($ajax);
            }
            //validation forward
            $info_target=$this->get_info_budget_target($fiscal_year_id);
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                if(($info_target['status_target_forward']!=$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='HOM Target Not Forwarded.';
                    $this->json_return($ajax);
                }
                if(($info_target['status_target_di_forward']==$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='DI Target Already Assigned.';
                    $this->json_return($ajax);
                }
            }

            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['crop']=$crop;
            $data['divisions']=User_helper::get_assigned_divisions();
            $data['acres']=$this->get_acres($crop_id);

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="HOM Yearly Target Assign To DI for (".$data['crop']['name'].')';
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['crop_id']=$crop_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_target_di",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_target_di/'.$fiscal_year_id.'/'.$crop_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_edit_target_di()
    {
        $items=array();
        //$this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $crop_id=$this->input->post('crop_id');

        //Division budget & get division wise forward status
        $division_ids[0]=0;
        $divisions=User_helper::get_assigned_divisions();
        foreach ($divisions as $division)
        {
            $division_ids[$division['division_id']]=$division['division_id'];
        }

        $this->db->from($this->config->item('table_bms_di_budget_target_division').' budget_target_division');
        $this->db->select('budget_target_division.*');

        $this->db->join($this->config->item('table_bms_di_budget_target').' budget_target','budget_target.fiscal_year_id=budget_target_division.fiscal_year_id AND budget_target.division_id=budget_target_division.division_id','INNER');
        $this->db->select('budget_target.status_budget_forward');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=budget_target_division.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->where('crop_type.crop_id',$crop_id);
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');

        $this->db->where('budget_target_division.fiscal_year_id',$fiscal_year_id);
        $this->db->where_in('budget_target_division.division_id',$division_ids);
        $this->db->where('crop_type.crop_id',$crop_id);
        $results=$this->db->get()->result_array();
        $target_divisions=array();
        foreach($results as $result)
        {
            $target_divisions[$result['division_id']][$result['variety_id']]=$result;
        }

        //old items
        $this->db->from($this->config->item('table_bms_hom_budget_target_hom').' budget_target_hom');
        $this->db->select('budget_target_hom.*');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=budget_target_hom.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->where('crop_type.crop_id',$crop_id);
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');
        $this->db->where('budget_target_hom.fiscal_year_id',$fiscal_year_id);
        $this->db->where('crop_type.crop_id',$crop_id);
        $results=$this->db->get()->result_array();
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']]=$result;
        }

        //variety lists
        $results=Budget_helper::get_crop_type_varieties(array($crop_id));
        foreach($results as $result)
        {
            $info=$this->initialize_row_edit_target_di($divisions,$result);
            $quantity_budget_division_total=0;
            foreach($division_ids as $division_id)
            {
                if(isset($target_divisions[$division_id][$result['variety_id']]))
                {
                    $info['quantity_budget_division_'.$division_id]=$target_divisions[$division_id][$result['variety_id']]['quantity_budget'];
                    $info['quantity_target_division_'.$division_id]=$target_divisions[$division_id][$result['variety_id']]['quantity_target'];
                    $quantity_budget_division_total+=$info['quantity_target_division_'.$division_id];
                }
            }
            $info['quantity_target_division_total']= $quantity_budget_division_total;
            if(isset($items_old[$result['variety_id']]))
            {
                $info['quantity_budget']=$items_old[$result['variety_id']]['quantity_budget'];
                $info['quantity_target']=$items_old[$result['variety_id']]['quantity_target'];
            }
            $items[]=$info;
        }
        $this->json_return($items);
    }
    private function initialize_row_edit_target_di($divisions,$info)
    {
        $row=$this->get_preference_headers('edit_target_di');
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['crop_type_name']=$info['crop_type_name'];
        $row['variety_name']=$info['variety_name'];
        $row['variety_id']=$info['variety_id'];
        foreach($divisions as $division)
        {
            $row['quantity_budget_division_'.$division['division_id']]= 0;
            $row['quantity_target_division_'.$division['division_id']]= 0;
        }
        return $row;
    }
    private function system_save_target_di()
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
        $info_target=$this->get_info_budget_target($item_head['fiscal_year_id']);
        if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
        {
            if(($info_target['status_target_forward']!=$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='HOM Target Not Forwarded.';
                $this->json_return($ajax);
            }
            if(($info_target['status_target_di_forward']==$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='DI Target Already Assigned.';
                $this->json_return($ajax);
            }
        }

        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_di_budget_target_division'),'*',array('fiscal_year_id ='.$item_head['fiscal_year_id']));
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['division_id']][$result['variety_id']]=$result;
        }

        $this->db->trans_start();  //DB Transaction Handle START

        foreach($items as $variety_id=>$variety_info)
        {
            foreach($variety_info as $division_id=>$quantity_info)
            {
                if(isset($items_old[$division_id][$variety_id]))
                {
                    if($items_old[$division_id][$variety_id]['quantity_target']!=$quantity_info['quantity_target'])
                    {
                        $data=array();
                        $data['quantity_target']=$quantity_info['quantity_target'];
                        $data['date_updated_target']=$time;
                        $data['user_updated_target']=$user->user_id;
                        $this->db->set('revision_count_target','revision_count_target+1',false);
                        Query_helper::update($this->config->item('table_bms_di_budget_target_division'),$data,array('id='.$items_old[$division_id][$variety_id]['id']),false);
                    }
                }
                else
                {
                    $data=array();
                    $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                    $data['division_id']=$division_id;
                    $data['variety_id']=$variety_id;
                    $data['quantity_target']=0;
                    if($quantity_info['quantity_target']>0)
                    {
                        $data['quantity_target']=$quantity_info['quantity_target'];
                        $data['revision_count_target']=1;
                    }
                    $data['date_updated_target']=$time;
                    $data['user_updated_target']=$user->user_id;
                    Query_helper::add($this->config->item('table_bms_di_budget_target_division'),$data,false);
                }
            }
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list_target_di($item_head['fiscal_year_id']);

        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    private function system_forward_target_di($fiscal_year_id=0)
    {
        //$user = User_helper::get_user();
        $method='forward_target_di';
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
            $info_target=$this->get_info_budget_target($fiscal_year_id);
            if(($info_target['status_target_di_forward']==$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='DI Target Already Assigned.';
                $this->json_return($ajax);
            }
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                if(($info_target['status_target_forward']!=$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='HOM Target Not Forwarded.';
                    $this->json_return($ajax);
                }
            }

            $data['system_preference_items']= $this->get_preference_headers($method);
            // get zone list
            $this->db->from($this->config->item('table_bms_di_budget_target_division').' budget_target_division');
            $this->db->join($this->config->item('table_bms_di_budget_target').' budget_target','budget_target.fiscal_year_id=budget_target_division.fiscal_year_id AND budget_target.division_id=budget_target_division.division_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = budget_target_division.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            //$this->db->where('budget_target.status_budget_forward',$this->config->item('system_status_forwarded'));
            $this->db->where('budget_target_division.fiscal_year_id',$fiscal_year_id);
            $this->db->group_by('budget_target_division.division_id');
            $this->db->order_by('divisions.ordering, divisions.id');
            $data['divisions']=$this->db->get()->result_array();

            $data['acres']=$this->get_acres();

            $data['fiscal_year_budget_target']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['title']="HOM Yearly Assign Target Forward/Complete";
            $data['options']['fiscal_year_id']=$fiscal_year_id;

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/forward_target_di",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/forward_target_di/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_forward_target_di()
    {
        $items=array();
        //$this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');

        // get division budgeted quantity & division ids
        $results=Query_helper::get_info($this->config->item('table_bms_di_budget_target_division'),'*',array('fiscal_year_id ='.$fiscal_year_id));
        $division_ids[0]=0;
        $old_items=array();
        foreach($results as $result)
        {
            $division_ids[$result['division_id']]=$result['division_id'];
            $old_items[$result['variety_id']][$result['division_id']]=$result;
        }

        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_hom_budget_target_hom'),'*',array('fiscal_year_id ='.$fiscal_year_id));
        $target_hom=array();
        foreach($results as $result)
        {
            $target_hom[$result['variety_id']]=$result;
        }

        //variety lists
        $results=Budget_helper::get_crop_type_varieties();

        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;

        $type_total=$this->initialize_row_forward_target_di($division_ids,'','','Total Type','');
        $crop_total=$this->initialize_row_forward_target_di($division_ids,'','Total Crop','','');
        $grand_total=$this->initialize_row_forward_target_di($division_ids,'Grand Total','','','');

        foreach($results as $result)
        {
            $info=$this->initialize_row_forward_target_di($division_ids,$result['crop_name'],$result['crop_type_name'],$result['variety_name']);
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

            if(isset($target_hom[$result['variety_id']]))
            {
                $info['quantity_budget']=$target_hom[$result['variety_id']]['quantity_budget'];
                $info['quantity_target']=$target_hom[$result['variety_id']]['quantity_target'];
            }
            $quantity_target_division_total=0;
            foreach($division_ids as $division_id)
            {
                if(isset($old_items[$result['variety_id']][$division_id]))
                {
                    $info['quantity_budget_division_'.$division_id]=$old_items[$result['variety_id']][$division_id]['quantity_budget'];
                    $info['quantity_target_division_'.$division_id]=$old_items[$result['variety_id']][$division_id]['quantity_target'];
                    $quantity_target_division_total+=$info['quantity_target_division_'.$division_id];
                }
            }
            $info['quantity_target_division_total']=$quantity_target_division_total;

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
    private function initialize_row_forward_target_di($division_ids,$crop_name,$crop_type_name,$variety_name)
    {
        $row=$this->get_preference_headers('forward_target_di');
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;
        foreach($division_ids as $division_id)
        {
            $row['quantity_budget_division_'.$division_id]= 0;
            $row['quantity_target_division_'.$division_id]= 0;
        }

        return $row;
    }
    private function system_save_forward_target_di()
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
        if($item_head['status_target_di_forward']!=$this->config->item('system_status_forwarded'))
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
        $info_target=$this->get_info_budget_target($item_head['fiscal_year_id']);
        if(($info_target['status_target_di_forward']==$this->config->item('system_status_forwarded')))
        {
            $ajax['status']=false;
            $ajax['system_message']='DI Target Already Assigned.';
            $this->json_return($ajax);
        }
        if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
        {
            if(($info_target['status_target_forward']!=$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='HOM Target Not Forwarded.';
                $this->json_return($ajax);
            }
        }

        $this->db->trans_start();  //DB Transaction Handle START
        $data=array();
        $data['status_target_di_forward']=$item_head['status_target_di_forward'];
        $data['date_target_di_forwarded']=$time;
        $data['user_target_di_forwarded']=$user->user_id;
        Query_helper::update($this->config->item('table_bms_hom_budget_target'),$data,array('id='.$info_target['id']),false);

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

    /* for DI Assign Next 3 Years target*/
    private function system_list_target_di_next_year($fiscal_year_id=0)
    {
        //$user = User_helper::get_user();
        $method='list_target_di_next_year';
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
            $info_target=$this->get_info_budget_target($fiscal_year_id);
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                if(($info_target['status_target_di_next_year_forward']==$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='DI Next 3 Years Target Already Assigned.';
                    $this->json_return($ajax);
                }
            }

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['title']="Next 3 Year Assign DI Target :: Crop list";
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list_target_di_next_year",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/list_target_di_next_year/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_target_di_next_year()
    {
        $items=array();
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        //get Target Revision
        $this->db->from($this->config->item('table_bms_di_budget_target_division').' budget_target_division');
        $this->db->select('COUNT(DISTINCT budget_target_division.variety_id) number_of_variety_targeted',false);

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id = budget_target_division.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');

        $this->db->select('crop_type.crop_id');
        $this->db->where('budget_target_division.fiscal_year_id',$fiscal_year_id);
        $this->db->where('(budget_target_division.quantity_prediction_1>0 OR budget_target_division.quantity_prediction_2>0 OR budget_target_division.quantity_prediction_3>0)');
        $this->db->group_by('crop_type.crop_id');
        $results=$this->db->get()->result_array();
        $targeted=array();
        foreach($results as $result)
        {
            $targeted[$result['crop_id']]=$result['number_of_variety_targeted'];
        }
        //crop list
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
    private function system_edit_target_di_next_year($fiscal_year_id=0,$crop_id=0)
    {
        //$user = User_helper::get_user();
        $method='edit_target_di_next_year';
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
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
            // valid crop check
            $crop=Query_helper::get_info($this->config->item('table_login_setup_classification_crops'),'*',array('id ='.$crop_id),1);
            if(!$crop)
            {
                $ajax['status']=false;
                $ajax['system_message']='Wrong crop id.';
                $this->json_return($ajax);
            }
            //validation forward
            $info_target=$this->get_info_budget_target($fiscal_year_id);
            if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
            {
                if(($info_target['status_target_di_next_year_forward']==$this->config->item('system_status_forwarded')))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Next 3 Years DI Target Already Assigned.';
                    $this->json_return($ajax);
                }
            }

            $data['fiscal_years_previous_sales']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
            $data['fiscal_years_next_budgets']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['crop']=$crop;
            $data['divisions']=User_helper::get_assigned_divisions();
            $data['acres']=$this->get_acres($crop_id);

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="HOM Next 3 Years Target Assign To DI for (".$data['crop']['name'].')';
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['crop_id']=$crop_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit_target_di_next_year",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_target_di_next_year/'.$fiscal_year_id.'/'.$crop_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_edit_target_di_next_year()
    {
        $items=array();
        //$this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $crop_id=$this->input->post('crop_id');
        //Division budget & get division wise forward status
        $division_ids[0]=0;
        $divisions=User_helper::get_assigned_divisions();
        foreach ($divisions as $division)
        {
            $division_ids[$division['division_id']]=$division['division_id'];
        }

        $this->db->from($this->config->item('table_bms_hom_budget_target_hom').' budget_target_hom');
        $this->db->select('budget_target_hom.*');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=budget_target_hom.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->where('crop_type.crop_id',$crop_id);
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');
        $this->db->where('budget_target_hom.fiscal_year_id',$fiscal_year_id);
        $this->db->where('crop_type.crop_id',$crop_id);
        $results=$this->db->get()->result_array();
        $target_hom=array();
        foreach($results as $result)
        {
            $target_hom[$result['variety_id']]=$result;
        }

        $fiscal_years_previous_sales=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
        $sales_previous=$this->get_sales_previous_years_hom($fiscal_years_previous_sales);
        $fiscal_years_next_budgets=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);

        //old items
        $this->db->from($this->config->item('table_bms_di_budget_target_division').' budget_target_division');
        $this->db->select('budget_target_division.*');

        $this->db->join($this->config->item('table_bms_di_budget_target').' budget_target','budget_target.fiscal_year_id=budget_target_division.fiscal_year_id AND budget_target.division_id=budget_target_division.division_id','INNER');
        $this->db->select('budget_target.status_budget_forward');

        $this->db->join($this->config->item('table_login_setup_classification_varieties').' v','v.id=budget_target_division.variety_id','INNER');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id = v.crop_type_id','INNER');
        $this->db->where('crop_type.crop_id',$crop_id);
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');

        $this->db->where('budget_target_division.fiscal_year_id',$fiscal_year_id);
        $this->db->where_in('budget_target_division.division_id',$division_ids);
        $this->db->where('crop_type.crop_id',$crop_id);
        $results=$this->db->get()->result_array();
        $items_old=array();
        foreach($results as $result)
        {
            $items_old[$result['variety_id']][$result['division_id']]=$result;
        }

        //variety lists
        $results=Budget_helper::get_crop_type_varieties(array($crop_id));
        foreach($results as $result)
        {
            $info=$this->initialize_row_edit_target_di_next_year($fiscal_years_next_budgets, $divisions,$result);
            foreach($fiscal_years_previous_sales as $fy)
            {
                if(isset($sales_previous[$fy['id']][$result['variety_id']]))
                {
                    $info['quantity_sale_'.$fy['id']]=$sales_previous[$fy['id']][$result['variety_id']]/1000;
                }
            }

            $quantity_prediction_division_total=0;
            $fiscal_year_serial=0;
            foreach($fiscal_years_next_budgets as $fy)
            {
                ++$fiscal_year_serial;
                $quantity_prediction_sub_total_division_=0;
                foreach($division_ids as $division_id)
                {
                    if(isset($items_old[$result['variety_id']][$division_id]))
                    {
                        //$info['quantity_target_']
                        $info['quantity_prediction_division_'.$fiscal_year_serial.'_'.$division_id]=$items_old[$result['variety_id']][$division_id]['quantity_prediction_'.$fiscal_year_serial];

                        $quantity_prediction_division_total+=$info['quantity_prediction_division_'.$fiscal_year_serial.'_'.$division_id];
                        $quantity_prediction_sub_total_division_+=$info['quantity_prediction_division_'.$fiscal_year_serial.'_'.$division_id];
                    }
                }
                $info['quantity_prediction_sub_total_division_'.$fiscal_year_serial]+=$quantity_prediction_sub_total_division_;
            }

            $info['quantity_prediction_division_total']= $quantity_prediction_division_total;

            if(isset($target_hom[$result['variety_id']]))
            {
                $info['quantity_target']=$target_hom[$result['variety_id']]['quantity_target'];
                $info['quantity_prediction_1']=$target_hom[$result['variety_id']]['quantity_prediction_1'];
                $info['quantity_prediction_2']=$target_hom[$result['variety_id']]['quantity_prediction_2'];
                $info['quantity_prediction_3']=$target_hom[$result['variety_id']]['quantity_prediction_3'];
            }
            $items[]=$info;
        }
        $this->json_return($items);
    }
    private function initialize_row_edit_target_di_next_year($fiscal_years,$divisions,$info)
    {
        $row=$this->get_preference_headers('edit_target_di_next_year');
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['crop_type_name']=$info['crop_type_name'];
        $row['variety_name']=$info['variety_name'];
        $row['variety_id']=$info['variety_id'];
        $serial=0;
        foreach($fiscal_years as $fy)
        {
            ++$serial;
            foreach($divisions as $division)
            {
                $row['quantity_prediction_division_'.$serial.'_'.$division['division_id']]=0;
            }
            $row['quantity_prediction_sub_total_division_'.$serial]=0;
        }
        return $row;
    }
    private function system_save_target_di_next_year()
    {
        $user = User_helper::get_user();
        $time=time();
        $item_head=$this->input->post('item');
        $items=$this->input->post('items');
        $prediction_hom=$items['prediction_hom'];
        $prediction_divisions=$items['prediction_divisions'];

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
        $info_target=$this->get_info_budget_target($item_head['fiscal_year_id']);
        if(!(isset($this->permissions['action3']) && ($this->permissions['action3']==1)))
        {
            if(($info_target['status_target_di_next_year_forward']==$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='Next 3 Years DI Target Already Assigned.';
                $this->json_return($ajax);
            }
        }

        //old items hom
        $results=Query_helper::get_info($this->config->item('table_bms_hom_budget_target_hom'),'*',array('fiscal_year_id ='.$item_head['fiscal_year_id']));
        $items_old_hom=array();
        foreach($results as $result)
        {
            $items_old_hom[$result['variety_id']]=$result;
        }

        //old items division
        $results=Query_helper::get_info($this->config->item('table_bms_di_budget_target_division'),'*',array('fiscal_year_id ='.$item_head['fiscal_year_id']));
        $items_old_division=array();
        foreach($results as $result)
        {
            $items_old_division[$result['variety_id']][$result['division_id']]=$result;
        }

        $this->db->trans_start();  //DB Transaction Handle START
        /* for hom prediction*/
        foreach($prediction_hom as $variety_id=>$quantity_info)
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
            if(isset($items_old_hom[$variety_id]))
            {
                if(($items_old_hom[$variety_id]['quantity_prediction_1']!=$quantity_prediction_1) || ($items_old_hom[$variety_id]['quantity_prediction_2']!=$quantity_prediction_2) || ($items_old_hom[$variety_id]['quantity_prediction_3']!=$quantity_prediction_3))
                {
                    $data['quantity_prediction_1']=$quantity_prediction_1;
                    $data['quantity_prediction_2']=$quantity_prediction_2;
                    $data['quantity_prediction_3']=$quantity_prediction_3;
                    $data['date_updated_prediction_target']=$time;
                    $data['user_updated_prediction_target']=$user->user_id;
                    $this->db->set('revision_count_target_prediction','revision_count_target_prediction+1',false);
                    Query_helper::update($this->config->item('table_bms_hom_budget_target_hom'),$data,array('id='.$items_old_hom[$variety_id]['id']),false);
                }
            }
            else
            {
                $data=array();
                $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                $data['variety_id']=$variety_id;
                $data['quantity_prediction_1']=$quantity_prediction_1;
                $data['quantity_prediction_2']=$quantity_prediction_2;
                $data['quantity_prediction_3']=$quantity_prediction_3;
                $data['revision_count_target_prediction']=1;
                $data['date_updated_prediction_target']=$time;
                $data['user_updated_prediction_target']=$user->user_id;
                Query_helper::add($this->config->item('table_bms_hom_budget_target_hom'),$data,false);
            }
        }
        /*for division prediction*/
        foreach($prediction_divisions as $variety_id=>$variety_info)
        {
            foreach($variety_info as $division_id=>$quantity_info)
            {
                $quantity_prediction_division_1=0;
                $quantity_prediction_division_2=0;
                $quantity_prediction_division_3=0;
                if(isset($quantity_info['quantity_prediction_division_1']))
                {
                    $quantity_prediction_division_1=$quantity_info['quantity_prediction_division_1'];
                }
                if(isset($quantity_info['quantity_prediction_division_2']))
                {
                    $quantity_prediction_division_2=$quantity_info['quantity_prediction_division_2'];
                }
                if(isset($quantity_info['quantity_prediction_division_3']))
                {
                    $quantity_prediction_division_3=$quantity_info['quantity_prediction_division_3'];
                }

                if(isset($items_old_division[$variety_id][$division_id]))
                {
                    if(($items_old_division[$variety_id][$division_id]['quantity_prediction_1']!=$quantity_prediction_division_1) || ($items_old_division[$variety_id][$division_id]['quantity_prediction_2']!=$quantity_prediction_division_2) || ($items_old_division[$variety_id][$division_id]['quantity_prediction_3']!=$quantity_prediction_division_3))
                    {
                        $data=array();
                        $data['quantity_prediction_1']=$quantity_prediction_division_1;
                        $data['quantity_prediction_2']=$quantity_prediction_division_2;
                        $data['quantity_prediction_3']=$quantity_prediction_division_3;
                        $data['date_updated_prediction_target']=$time;
                        $data['user_updated_prediction_target']=$user->user_id;
                        $this->db->set('revision_count_target_prediction','revision_count_target_prediction+1',false);
                        Query_helper::update($this->config->item('table_bms_di_budget_target_division'),$data,array('id='.$items_old_division[$variety_id][$division_id]['id']),false);
                    }
                }
                else
                {
                    $data=array();
                    $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                    $data['division_id']=$division_id;
                    $data['variety_id']=$variety_id;
                    $data['quantity_prediction_1']=$quantity_prediction_division_1;
                    $data['quantity_prediction_2']=$quantity_prediction_division_2;
                    $data['quantity_prediction_3']=$quantity_prediction_division_3;
                    $data['revision_count_target_prediction']=1;
                    $data['date_updated_prediction_target']=$time;
                    $data['user_updated_prediction_target']=$user->user_id;
                    Query_helper::add($this->config->item('table_bms_di_budget_target_division'),$data,false);
                }
            }
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
            $this->system_list_target_di_next_year($item_head['fiscal_year_id']);

        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
            $this->json_return($ajax);
        }
    }

    private function system_forward_target_di_next_year($fiscal_year_id=0)
    {
        //$user = User_helper::get_user();
        $method='forward_target_di_next_year';
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
            $info_target=$this->get_info_budget_target($fiscal_year_id);
            if(($info_target['status_target_di_next_year_forward']==$this->config->item('system_status_forwarded')))
            {
                $ajax['status']=false;
                $ajax['system_message']='Next 3 Years DI Target Already Assigned.';
                $this->json_return($ajax);
            }

            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['fiscal_years_previous_sales']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
            $data['fiscal_years_next_budgets']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            // get zone list
            $this->db->from($this->config->item('table_bms_di_budget_target_division').' budget_target_division');
            $this->db->join($this->config->item('table_bms_di_budget_target').' budget_target','budget_target.fiscal_year_id=budget_target_division.fiscal_year_id AND budget_target.division_id=budget_target_division.division_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_divisions').' divisions','divisions.id = budget_target_division.division_id','INNER');
            $this->db->select('divisions.id division_id, divisions.name division_name');
            $this->db->where('budget_target.status_budget_forward',$this->config->item('system_status_forwarded'));
            $this->db->where('budget_target_division.fiscal_year_id',$fiscal_year_id);
            $this->db->group_by('budget_target_division.division_id');
            $this->db->order_by('divisions.ordering, divisions.id');
            $data['divisions']=$this->db->get()->result_array();

            $data['acres']=$this->get_acres();

            $data['fiscal_year_budget_target']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['title']="HOM Next 3 Years Assign Target Forward To DI";
            $data['options']['fiscal_year_id']=$fiscal_year_id;

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/forward_target_di_next_year",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/forward_target_di_next_year/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_forward_target_di_next_year()
    {
        $items=array();
        //$this->json_return($items);
        $fiscal_year_id=$this->input->post('fiscal_year_id');

        // get division budgeted quantity & division ids
        $results=Query_helper::get_info($this->config->item('table_bms_di_budget_target_division'),'*',array('fiscal_year_id ='.$fiscal_year_id));
        $division_ids[0]=0;
        $items_old=array();
        foreach($results as $result)
        {
            $division_ids[$result['division_id']]=$result['division_id'];
            $items_old[$result['variety_id']][$result['division_id']]=$result;
        }

        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_hom_budget_target_hom'),'*',array('fiscal_year_id ='.$fiscal_year_id));
        $target_hom=array();
        foreach($results as $result)
        {
            $target_hom[$result['variety_id']]=$result;
        }

        $fiscal_years_previous_sales=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id <'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_PREVIOUS_SALE,0,array('id DESC'));
        $sales_previous=$this->get_sales_previous_years_hom($fiscal_years_previous_sales);
        $fiscal_years_next_budgets=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);

        //variety lists
        $results=Budget_helper::get_crop_type_varieties();

        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;

        $type_total=$this->initialize_row_forward_target_di_next_year($fiscal_years_previous_sales, $fiscal_years_next_budgets,$division_ids,'','','Total Type','');
        $crop_total=$this->initialize_row_forward_target_di_next_year($fiscal_years_previous_sales, $fiscal_years_next_budgets,$division_ids,'','Total Crop','','');
        $grand_total=$this->initialize_row_forward_target_di_next_year($fiscal_years_previous_sales, $fiscal_years_next_budgets, $division_ids,'Grand Total','','','');

        foreach($results as $result)
        {
            $info=$this->initialize_row_forward_target_di_next_year($fiscal_years_previous_sales, $fiscal_years_next_budgets,$division_ids,$result['crop_name'],$result['crop_type_name'],$result['variety_name']);
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

            $quantity_prediction_division_total=0;
            $fiscal_year_serial=0;
            foreach($fiscal_years_next_budgets as $fy)
            {
                ++$fiscal_year_serial;
                $quantity_prediction_sub_total_division=0;
                foreach($division_ids as $division_id)
                {
                    if(isset($items_old[$result['variety_id']][$division_id]))
                    {
                        $info['quantity_prediction_division_'.$fiscal_year_serial.'_'.$division_id]=$items_old[$result['variety_id']][$division_id]['quantity_prediction_'.$fiscal_year_serial];

                        $quantity_prediction_division_total+=$info['quantity_prediction_division_'.$fiscal_year_serial.'_'.$division_id];
                        $quantity_prediction_sub_total_division+=$info['quantity_prediction_division_'.$fiscal_year_serial.'_'.$division_id];
                    }
                }
                $info['quantity_prediction_sub_total_division_'.$fiscal_year_serial]+=$quantity_prediction_sub_total_division;
            }
            $info['quantity_prediction_division_total']= $quantity_prediction_division_total;

            if(isset($target_hom[$result['variety_id']]))
            {
                $info['quantity_target']=$target_hom[$result['variety_id']]['quantity_target'];
                $info['quantity_prediction_1']=$target_hom[$result['variety_id']]['quantity_prediction_1'];
                $info['quantity_prediction_2']=$target_hom[$result['variety_id']]['quantity_prediction_2'];
                $info['quantity_prediction_3']=$target_hom[$result['variety_id']]['quantity_prediction_3'];
            }

            /*$type_total['quantity_target_division_total']+=$quantity_target_division_total;
            $crop_total['quantity_target_division_total']+=$quantity_target_division_total;
            $grand_total['quantity_target_division_total']+=$quantity_target_division_total;*/

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
    private function initialize_row_forward_target_di_next_year($fiscal_years_previous_sales, $fiscal_years_next_budgets,$division_ids,$crop_name,$crop_type_name,$variety_name)
    {
        $row=$this->get_preference_headers('forward_target_di_next_year');
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
        $serial=0;
        foreach($fiscal_years_next_budgets as $fy)
        {
            ++$serial;
            foreach($division_ids as $division_id)
            {
                $row['quantity_prediction_division_'.$serial.'_'.$division_id]= 0;
            }
            $row['quantity_prediction_sub_total_division_'.$serial]=0;
        }

        return $row;
    }
    private function system_save_forward_target_di_next_year()
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
        if($item_head['status_target_di_next_year_forward']!=$this->config->item('system_status_forwarded'))
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
        $info_target=$this->get_info_budget_target($item_head['fiscal_year_id']);
        if(($info_target['status_target_di_next_year_forward']==$this->config->item('system_status_forwarded')))
        {
            $ajax['status']=false;
            $ajax['system_message']='Next 3 Years DI Target Already Assigned.';
            $this->json_return($ajax);
        }

        $this->db->trans_start();  //DB Transaction Handle START
        $data=array();
        $data['status_target_di_next_year_forward']=$item_head['status_target_di_next_year_forward'];
        $data['date_target_di_next_year_forwarded']=$time;
        $data['user_target_di_next_year_forwarded']=$user->user_id;
        Query_helper::update($this->config->item('table_bms_hom_budget_target'),$data,array('id='.$info_target['id']),false);

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

    private function system_details($fiscal_year_id=0)
    {
        $user = User_helper::get_user();
        $method='search_details';//this is because after save preference it will go to list view.because details view need additional parameter
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            //for jqx grid
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $data['options']['area_id']=0;

            $data['title']='National Budget and Target details';
            $data['areas']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $data['sub_column_group_name']='Divisions';
            $data['fiscal_years_next_predictions']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);
            $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            //jqx grid section end

            //details section start
            $data['fiscal_year_budget_target']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);


            $data['info_area']=array();
            $data['acres']=$this->get_acres();

            $budget_target=$this->get_info_budget_target($fiscal_year_id);
            $user_ids=array();
            $user_ids[$budget_target['user_created']]=$budget_target['user_created'];
            if($budget_target['user_budget_forwarded']>0)
            {
                $user_ids[$budget_target['user_budget_forwarded']]=$budget_target['user_budget_forwarded'];
            }
            if($budget_target['user_target_forwarded']>0)
            {
                $user_ids[$budget_target['user_target_forwarded']]=$budget_target['user_target_forwarded'];
            }
            if($budget_target['user_target_di_forwarded']>0)
            {
                $user_ids[$budget_target['user_target_di_forwarded']]=$budget_target['user_target_di_forwarded'];
            }
            if($budget_target['user_target_di_next_year_forwarded']>0)
            {
                $user_ids[$budget_target['user_target_di_next_year_forwarded']]=$budget_target['user_target_di_next_year_forwarded'];
            }
            $users=System_helper::get_users_info($user_ids);

            $data['info_basic']=array();
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
            //target forward area(to HOM from National Target)
            $result=array();
            $result['label_1']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA').' Status';
            $result['value_1']=$this->config->item('system_status_pending');
            if($budget_target['status_target_forward']==$this->config->item('system_status_forwarded'))
            {
                $result['value_1']=$this->config->item('system_status_forwarded');
            }
            $result['label_2']='';
            $result['value_2']='';
            $data['info_basic'][]=$result;

            if($budget_target['status_target_forward']==$this->config->item('system_status_forwarded'))
            {
                $result=array();
                $result['label_1']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA').' By';
                $result['value_1']=$users[$budget_target['user_target_forwarded']]['name'];
                $result['label_2']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA').' Time';
                $result['value_2']=System_helper::display_date_time($budget_target['date_target_forwarded']);
                $data['info_basic'][]=$result;
            }
            //target forward area(to di from hom)
            $result=array();
            $result['label_1']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA_SUB').' Status';
            $result['value_1']=$this->config->item('system_status_pending');
            if($budget_target['status_target_di_forward']==$this->config->item('system_status_forwarded'))
            {
                $result['value_1']=$this->config->item('system_status_forwarded');
            }
            $result['label_2']='';
            $result['value_2']='';
            $data['info_basic'][]=$result;

            if($budget_target['status_target_di_forward']==$this->config->item('system_status_forwarded'))
            {
                $result=array();
                $result['label_1']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA_SUB').' By';
                $result['value_1']=$users[$budget_target['user_target_di_forwarded']]['name'];
                $result['label_2']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA_SUB').' Time';
                $result['value_2']=System_helper::display_date_time($budget_target['date_target_di_forwarded']);
                $data['info_basic'][]=$result;
            }
            //target forward area 3yr(to DI from HOM)
            $result=array();
            $result['label_1']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA_SUB_NEXT_YEAR').' Status';
            $result['value_1']=$this->config->item('system_status_pending');
            if($budget_target['status_target_di_next_year_forward']==$this->config->item('system_status_forwarded'))
            {
                $result['value_1']=$this->config->item('system_status_forwarded');
            }
            $result['label_2']='';
            $result['value_2']='';
            $data['info_basic'][]=$result;

            if($budget_target['status_target_di_next_year_forward']==$this->config->item('system_status_forwarded'))
            {
                $result=array();
                $result['label_1']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA_SUB_NEXT_YEAR').' By';
                $result['value_1']=$users[$budget_target['user_target_di_next_year_forwarded']]['name'];
                $result['label_2']=$this->lang->line('LABEL_STATUS_TARGET_FORWARD_AREA_SUB_NEXT_YEAR').' Time';
                $result['value_2']=System_helper::display_date_time($budget_target['date_target_di_next_year_forwarded']);
                $data['info_basic'][]=$result;
            }

            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->common_view_location."/details",$data,true));

            $ajax['status']=true;
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$fiscal_year_id);
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
        $areas=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

        //get variety pricing
        $variety_pricing=array();
        $results=Query_helper::get_info($this->config->item('table_bms_setup_budget_config_variety_pricing'),array('variety_id','amount_price_net amount_price'),array('fiscal_year_id ='.$fiscal_year_id));
        foreach($results as $result)
        {
            $variety_pricing[$result['variety_id']]=$result['amount_price'];
        }

        //getting sub area budget and target
        $budget_target_sub=array();//division budget_target

        $this->db->from($this->config->item('table_bms_di_budget_target_division').' bt');
        $this->db->select('bt.division_id area_id');
        $this->db->select('bt.variety_id,bt.quantity_budget,bt.quantity_target');
        $this->db->where('bt.fiscal_year_id',$fiscal_year_id);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $budget_target_sub[$result['variety_id']][$result['area_id']]=$result;
        }
        //getting budget and target
        $budget_target=array();
        $this->db->from($this->config->item('table_bms_hom_budget_target_hom').' bt');
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
    private function get_info_budget_target($fiscal_year_id)
    {
        $this->set_info_budget_target_di($fiscal_year_id);
        $info=Query_helper::get_info($this->config->item('table_bms_hom_budget_target'),'*',array('fiscal_year_id ='.$fiscal_year_id),1);
        if(!$info)
        {
            $user = User_helper::get_user();
            $data=array();
            $data['fiscal_year_id'] = $fiscal_year_id;
            $data['date_created'] = time();
            $data['user_created'] = $user->user_id;
            $id=Query_helper::add($this->config->item('table_bms_hom_budget_target'),$data,false);
            $info=Query_helper::get_info($this->config->item('table_bms_hom_budget_target'),'*',array('id ='.$id),1);
        }
        return $info;
    }
    private function set_info_budget_target_di($fiscal_year_id)
    {
        $user = User_helper::get_user();
        $this->db->from($this->config->item('table_bms_di_budget_target').' item');
        $this->db->select('item.*');
        $this->db->where('item.fiscal_year_id',$fiscal_year_id);
        $results=$this->db->get()->result_array();
        $budget_target_division=array();
        foreach($results as $result)
        {
            $budget_target_division[$result['division_id']]=$result;
        }
        //$division_ids[0]=0;
        $divisions=User_helper::get_assigned_divisions();
        foreach($divisions as $division)
        {
            if(!isset($budget_target_division[$division['division_id']]))
            {
                $data=array();
                $data['fiscal_year_id'] = $fiscal_year_id;
                $data['division_id'] = $division['division_id'];
                $data['date_created'] = time();
                $data['user_created'] = $user->user_id;
                Query_helper::add($this->config->item('table_bms_di_budget_target'),$data,false);
                //$division_ids[$division['division_id']]=$division['division_id'];
            }
        }
    }
    private function get_sales_previous_years_hom($fiscal_years)
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
        /*$this->db->where_in('acres.upazilla_id',$upazilla_ids);*/
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
