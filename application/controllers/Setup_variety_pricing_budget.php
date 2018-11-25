<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_variety_pricing_budget extends Root_Controller
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
        elseif($action=="add_edit")
        {
            $this->system_add_edit($id);
        }
        elseif($action=="get_items_add_edit")
        {
            $this->system_get_items_add_edit();
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_list();
        }
    }
    private function get_preference_headers($method)
    {
        $data=array();
        $data['fiscal_year_id']= 1;
        $data['fiscal_year']= 1;
        $data['revision_count']= 1;
        if($method=='add_edit')
        {
            $data['crop_name']= 1;
            $data['crop_type_name']= 1;
            $data['variety_name']= 1;
            $data['variety_id']= 1;
            $data['price']= 1;
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
            $data['title']="Variety Pricing Setup For Budget";
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

        $this->db->from($this->config->item('table_bms_setup_variety_pricing_budget').' item');
        $this->db->select('MAX(item.revision_count) revision_count, item.fiscal_year_id');
        $this->db->group_by('item.fiscal_year_id');
        $results=$this->db->get()->result_array();
        $varieties=array();
        foreach($results as $result)
        {
            $varieties[$result['fiscal_year_id']]=$result;
        }
        $items=array();
        foreach($fiscal_years as $fy)
        {
            $data=array();
            $data['fiscal_year_id']=$fy['id'];
            $data['fiscal_year']=$fy['text'];
            if(isset($varieties[$fy['id']]))
            {
                $data['revision_count']=$varieties[$fy['id']]['revision_count'];
            }
            else
            {
                $data['revision_count']=0;
            }
            $items[]=$data;
        }
        $this->json_return($items);
    }
    private function system_add_edit($fiscal_year_id=0)
    {
        $method='add_edit';
        if((isset($this->permissions['action1']) && ($this->permissions['action1']==1))||(isset($this->permissions['action2']) && ($this->permissions['action2']==1)))
        {
            if(!($fiscal_year_id>0))
            {
                $fiscal_year_id=$this->input->post('fiscal_year_id');
            }
            $data['fiscal_year']=Query_helper::get_info($this->config->item('table_login_basic_setup_fiscal_year'),'*',array('id ='.$fiscal_year_id),1);
            $data['system_preference_items']= $this->get_preference_headers($method);
            $data['title']="Variety Pricing Setup for (".$data['fiscal_year']['name'].') Fiscal Year';
            $data['options']['fiscal_year_id']=$fiscal_year_id;
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add_edit/'.$fiscal_year_id);
            $this->json_return($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->json_return($ajax);
        }
    }
    private function system_get_items_add_edit()
    {
        $items=array();
        $fiscal_year_id=$this->input->post('fiscal_year_id');
        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_setup_variety_pricing_budget'),'*',array('fiscal_year_id ='.$fiscal_year_id));
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
            $info=$this->initialize_row_add_edit($result);
            if(isset($items_old[$result['variety_id']]))
            {
                if($items_old[$result['variety_id']]['price']>0)
                {
                    $info['price']=$items_old[$result['variety_id']]['price'];
                }
            }
            $items[]=$info;
        }

        $this->json_return($items);
    }
    private function initialize_row_add_edit($info)
    {
        $row=array();
        $row['crop_name']=$info['crop_name'];
        $row['crop_type_name']=$info['crop_type_name'];
        $row['variety_name']=$info['variety_name'];
        $row['variety_id']=$info['variety_id'];
        $row['price']=0;
        return $row;
    }
    private function system_save()
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
        //old items
        $results=Query_helper::get_info($this->config->item('table_bms_setup_variety_pricing_budget'),'*',array('fiscal_year_id ='.$item_head['fiscal_year_id']));
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
                if($items_old[$variety_id]['price']!=$price)
                {
                    $data['price']=$price;
                    $data['date_updated']=$time;
                    $data['user_updated']=$user->user_id;
                    $this->db->set('revision_count','revision_count+1',false);
                    Query_helper::update($this->config->item('table_bms_setup_variety_pricing_budget'),$data,array('id='.$items_old[$variety_id]['id']));
                }
            }
            else
            {
                $data=array();
                $data['fiscal_year_id']=$item_head['fiscal_year_id'];
                $data['variety_id']=$variety_id;
                if($price>0)
                {
                    $data['price']=$price;
                    $data['revision_count']=1;
                }
                else
                {
                    $data['price']=0;
                }
                $data['date_created'] = $time;
                $data['user_created'] = $user->user_id;
                Query_helper::add($this->config->item('table_bms_setup_variety_pricing_budget'),$data,false);
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
}
