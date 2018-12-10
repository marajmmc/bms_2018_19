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
        if($zone_id>0)
        {
            $areas=$this->get_outlets($zone_id);
        }
        elseif($division_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_login_setup_location_zones'),array('id value','name text'),array('division_id ='.$division_id,'status ="'.$this->config->item('system_status_active').'"'));
        }
        else
        {
            $areas=Query_helper::get_info($this->config->item('table_login_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
        }
        //get variety pricing
        $variety_pricing=array();
        $results=Query_helper::get_info($this->config->item('table_bms_setup_budget_config_variety_pricing'),array('variety_id','amount_price_net amount_price'),array('fiscal_year_id ='.$fiscal_year_id));
        foreach($results as $result)
        {
            $variety_pricing[$result['variety_id']]=$result['amount_price'];
        }
        //getting sub area budget and target
        $budget_target_sub=array();
        if($zone_id>0)
        {
            $this->db->from($this->config->item('table_pos_si_budget_target_outlet').' bt');
            $this->db->select('bt.outlet_id area_id');
            $this->db->select('bt.variety_id,bt.quantity_budget,bt.quantity_target');

            $this->db->join($this->config->item('table_login_csetup_cus_info').' cus_info','cus_info.customer_id = bt.outlet_id','INNER');
            $this->db->where('cus_info.revision',1);

            $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
            $this->db->join($this->config->item('table_login_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->where('t.zone_id',$zone_id);

        }
        elseif($division_id>0)
        {
            $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' bt');
            $this->db->select('bt.zone_id area_id');
            $this->db->select('bt.variety_id,bt.quantity_budget,bt.quantity_target');
            $this->db->join($this->config->item('table_login_setup_location_zones').' zone','zone.id = bt.zone_id','INNER');
            $this->db->where('zone.division_id',$division_id);
        }
        else
        {
            $this->db->from($this->config->item('table_bms_di_budget_target_division').' bt');
            $this->db->select('bt.division_id area_id');
            $this->db->select('bt.variety_id,bt.quantity_budget,bt.quantity_target');

        }

        $this->db->where('bt.fiscal_year_id',$fiscal_year_id);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $budget_target_sub[$result['variety_id']][$result['area_id']]=$result;
        }
        //getting budget and target
        $budget_target=array();
        if($zone_id>0)
        {
            $this->db->from($this->config->item('table_bms_zi_budget_target_zone').' bt');
            $this->db->where('bt.zone_id',$zone_id);
        }
        elseif($division_id>0)
        {
            $this->db->from($this->config->item('table_bms_di_budget_target_division').' bt');
            $this->db->where('bt.division_id',$division_id);
        }
        else
        {
            $this->db->from($this->config->item('table_bms_hom_budget_target_hom').' bt');
        }
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

        $type_total=$this->initialize_row(array('variety_name'=>'Total Type'),$areas);
        $crop_total=$this->initialize_row(array('crop_type_name'=>'Total Crop'),$areas);
        $grand_total=$this->initialize_row(array('crop_name'=>'Grand Total'),$areas);

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
            $info=$this->initialize_row($result,$areas);
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
    private function initialize_row($info,$areas)
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
        foreach($info  as $key=>$r)
        {
            if(!(($key=='crop_name')||($key=='crop_type_name')||($key=='variety_name')))
            {
                $info[$key]='';
            }
        }
        return $info;
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
