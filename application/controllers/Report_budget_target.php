<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_budget_target extends Root_Controller
{
    public $message;
    public $permissions;
    public $controller_url;
    public $locations;

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
        $this->load->helper('budget');
        $this->lang->load('budget');
    }
    public function index($action="search",$id=0)
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items_list")
        {
            $this->system_get_items_list();
        }
        elseif($action=="set_preference_search_list")
        {
            $this->system_set_preference('search_list');
        }
        elseif($action=="save_preference")
        {
            System_helper::save_preference();
        }
        else
        {
            $this->system_search();
        }
    }
    private function get_preference_headers($method)
    {
        $data=array();
        if($method=='search_list')
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
    private function system_search()
    {
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $fiscal_years=Budget_helper::get_fiscal_years();
            //$data['fiscal_years']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),array('id value','name text'),array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['text'],'value'=>$year['id']);
            }
            $data['divisions']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id'],'status ="'.$this->config->item('system_status_active').'"'));
            }

            $data['title']="Budget and Target Report Search";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/search",$data,true));
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
    private function system_list()
    {
        $user = User_helper::get_user();
        $method='search_list';
        if(isset($this->permissions['action0'])&&($this->permissions['action0']==1))
        {
            $reports=$this->input->post('report');
            $data['options']=$reports;
            if(!$reports['fiscal_year_id'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Fiscal Year Is Required';
                $this->json_return($ajax);
            }
            if($reports['zone_id']>0)
            {
                $data['areas']=$this->get_outlets($reports['zone_id']);
                $data['title']='Zone Budget and Target Report';
                $data['sub_column_group_name']='Outlets';
            }
            elseif($reports['division_id']>0)
            {
                $data['areas']=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$reports['division_id'],'status ="'.$this->config->item('system_status_active').'"'));
                $data['title']='Division Budget and Target Report';
                $data['sub_column_group_name']='Zones';
            }
            else
            {
                $data['areas']=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
                $data['title']='National Budget and Target Report';
                $data['sub_column_group_name']='Divisions';
            }
            $data['fiscal_years_next_predictions']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$reports['fiscal_year_id']),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);
            $data['system_preference_items']= System_helper::get_preference($user->user_id,$this->controller_url,$method,$this->get_preference_headers($method));
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));

            $ajax['status']=true;
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
    /* Start Transfer TO Wise report function */
    private function system_get_items_list()
    {
        $items=array();
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');

        $fiscal_year_id=$this->input->post('fiscal_year_id');
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');



        /*$fiscal_years_next_budgets=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id >'.$fiscal_year_id),Budget_helper::$NUM_FISCAL_YEAR_NEXT_BUDGET_TARGET,0);

        $divisions=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
        $zones=array();
        if($division_id>0)
        {
            $zones=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$division_id,'status ="'.$this->config->item('system_status_active').'"'));
        }

        $this->db->from($this->config->item('table_login_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name, v.price_kg price_unit_kg');
        $this->db->join($this->config->item('table_login_setup_classification_crop_types').' crop_type','crop_type.id=v.crop_type_id','INNER');
        $this->db->select('crop_type.id crop_type_id, crop_type.name crop_type_name');
        $this->db->join($this->config->item('table_login_setup_classification_crops').' crop','crop.id=crop_type.crop_id','INNER');
        $this->db->select('crop.id crop_id, crop.name crop_name');
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop.id','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('crop_type.id','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->order_by('v.id','ASC');
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->where('v.whose','ARM');
        if($crop_id>0)
        {
            $this->db->where('crop.id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
                if($variety_id>0)
                {
                    $this->db->where('v.id',$variety_id);
                }
            }
        }
        $results=$this->db->get()->result_array();
        $prev_crop_name='';
        $prev_type_name='';
        $first_row=true;

        $type_total=$this->initialize_row($fiscal_years_next_budgets,$divisions, $zones,'','','Total Type','list');
        $crop_total=$this->initialize_row($fiscal_years_next_budgets,$divisions, $zones,'','Total Crop','','list');
        $grand_total=$this->initialize_row($fiscal_years_next_budgets,$divisions, $zones,'Grand Total','','','list');

        foreach($results as $result)
        {
            $info=$this->initialize_row($fiscal_years_next_budgets,$divisions, $zones, $result['crop_name'],$result['crop_type_name'],$result['variety_name'],'list');

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
            $info['price_unit_kg']=$result['price_unit_kg'];
            $type_total['price_unit_kg']+=$info['price_unit_kg'];
            $crop_total['price_unit_kg']+=$info['price_unit_kg'];
            $grand_total['price_unit_kg']+=$info['price_unit_kg'];

            $info['area_budget_kg']=0;//$result['budget_area_kg'];
            $type_total['area_budget_kg']+=$info['area_budget_kg'];
            $crop_total['area_budget_kg']+=$info['area_budget_kg'];
            $grand_total['area_budget_kg']+=$info['area_budget_kg'];

            $info['area_budget_amount']=0;//$result['area_budget_amount'];
            $type_total['area_budget_amount']+=$info['area_budget_amount'];
            $crop_total['area_budget_amount']+=$info['area_budget_amount'];
            $grand_total['area_budget_amount']+=$info['area_budget_amount'];

            $info['area_target_kg']=0;//$result['area_target_kg'];
            $type_total['area_target_kg']+=$info['area_target_kg'];
            $crop_total['area_target_kg']+=$info['area_target_kg'];
            $grand_total['area_target_kg']+=$info['area_target_kg'];

            $info['area_target_amount']=0;//$result['area_target_amount'];
            $type_total['area_target_amount']+=$info['area_target_amount'];
            $crop_total['area_target_amount']+=$info['area_target_amount'];
            $grand_total['area_target_amount']+=$info['area_target_amount'];

            $serial=0;
            foreach($fiscal_years_next_budgets as $budget)
            {
                ++$serial;
                $info['prediction_'.$serial.'_kg']=0;//$result['area_target_amount'];
                $type_total['prediction_'.$serial.'_kg']+=$info['prediction_'.$serial.'_kg'];
                $crop_total['prediction_'.$serial.'_kg']+=$info['prediction_'.$serial.'_kg'];
                $grand_total['prediction_'.$serial.'_kg']+=$info['prediction_'.$serial.'_kg'];

                $info['prediction_'.$serial.'_amount']=0;//$result['area_target_amount'];
                $type_total['prediction_'.$serial.'_amount']+=$info['prediction_'.$serial.'_amount'];
                $crop_total['prediction_'.$serial.'_amount']+=$info['prediction_'.$serial.'_amount'];
                $grand_total['prediction_'.$serial.'_amount']+=$info['prediction_'.$serial.'_amount'];

                $info['prediction_'.$serial.'_amount']=0;//$result['area_target_amount'];
            }

            if(!$division_id && !$zone_id)
            {
                foreach($divisions as $division)
                {
                    $info['sub_area_'.$division['value'].'_budget_kg']=0;
                    $info['sub_area_'.$division['value'].'_budget_amount']=0;

                    $info['sub_area_'.$division['value'].'_target_kg']=0;
                    $info['sub_area_'.$division['value'].'_target_amount']=0;
                }
            }
            elseif($division_id && !$zone_id)
            {

            }

            //$grand_total['price_unit_kg']+=$info['price_unit_kg'];
            $items[]=$info;
        }
        $items[]=$type_total;
        $items[]=$crop_total;
        $items[]=$grand_total;*/
        $this->json_return($items);
    }

    private function initialize_row($fiscal_years_next_budgets,$divisions, $zones,$crop_name,$crop_type_name,$variety_name,$method)
    {
        $row=$this->get_preference_headers($method);
        foreach($row  as $key=>$r)
        {
            $row[$key]=0;
        }
        $row['crop_name']=$crop_name;
        $row['crop_type_name']=$crop_type_name;
        $row['variety_name']=$variety_name;
        $serial=0;
        foreach($fiscal_years_next_budgets as $fy)
        {
            ++$serial;
            $row['prediction_'.$serial.'_kg']=0;
            $row['prediction_'.$serial.'_amount']=0;
        }
        if((sizeof($divisions)>0) && !(sizeof($zones)>0))
        {
            foreach($divisions as $division)
            {
                $row['sub_area_'.$division['value'].'_budget_kg']=0;
                $row['sub_area_'.$division['value'].'_budget_amount']=0;

                $row['sub_area_'.$division['value'].'_target_kg']=0;
                $row['sub_area_'.$division['value'].'_target_amount']=0;
            }
        }
        else if($divisions && $zones)
        {

        }
        return $row;
    }
    private function reset_row($info)
    {
        foreach($info  as $key=>$r)
        {
            if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')))
            {
                $info[$key]='';
            }
        }
        return $info;
    }
    private function get_row($info)
    {
        $row=array();
        foreach($info  as $key=>$r)
        {
            if(substr($key,-3)=='pkt')
            {
                if($info[$key]==0)
                {
                    $row[$key]='';
                }
                else
                {
                    $row[$key]=$info[$key];
                }
            }
            elseif(substr($key,-2)=='kg')
            {
                if($info[$key]==0)
                {
                    $row[$key]='';
                }
                else
                {
                    $row[$key]=number_format($info[$key],3,'.','');
                }
            }
            elseif(substr($key,0,6)=='amount')
            {
                if($info[$key]==0)
                {
                    $row[$key]='';
                }
                else
                {
                    $row[$key]=number_format($info[$key],2);
                }
            }
            else
            {
                $row[$key]=$info[$key];
            }

        }
        return $row;
    }

    //query need to change according to fiscal year and budget
    private function get_outlets($zone_id)
    {
        $this->db->from($this->config->item('table_login_csetup_customer').' customer');
        $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = customer.id','INNER');
        $this->db->select('cus_info.customer_id value, cus_info.name text');

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

}
