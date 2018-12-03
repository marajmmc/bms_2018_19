<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transfer extends CI_Controller
{
    public function index()
    {
        //$this->configs();
        //$this->hom();
        //$this->di();
    }
    private function configs()
    {
        $source_tables=array(
            'dc_percentage'=>'arm_ems.bms_mgt_direct_cost_percentage',
            'variety_price'=>'arm_login_2018_19.login_setup_classification_varieties'
        );
        $destination_tables=array(
            'budget_config'=>'arm_bms_2018_19.bms_setup_budget_config',
            'variety_pricing_packing'=>'arm_bms_2018_19.bms_setup_budget_config_variety_pricing_packing'
        );
        $currency_rates=array();
        $currency_rates[1]=83;
        $currency_rates[2]=95;
        $results=Query_helper::get_info($source_tables['dc_percentage'],'*',array('fiscal_year_id = 2'),0,0,array('item_id ASC'));
        $dc_percentage=array();
        foreach($results as $result)
        {
            $dc_percentage[$result['item_id']]=$result['percentage'];
        }
        $results=Query_helper::get_info($source_tables['variety_price'],'*',array('whose = "ARM"','price_kg >0','status ="Active"'),0,0,array('id ASC'));
        $variety_prices=array();
        foreach($results as $result)
        {
            $variety_prices[$result['id']]=$result;
        }
        $time=time();
        $this->db->trans_start();  //DB Transaction Handle START
        //main table data
        $data=array();
        $data['fiscal_year_id'] = 4;
        $data['date_created'] = $time;
        $data['user_created'] = 1;
        $data['revision_pricing_count'] =1;
        $data['date_pricing_updated'] = $time;
        $data['user_pricing_updated'] = 2;
        $data['amount_currency_rate'] = json_encode($currency_rates);
        $data['revision_currency_rate_count'] =1;
        $data['date_currency_rate'] = $time;
        $data['user_currency_rate'] = 2;
        $data['amount_direct_cost_percentage'] = json_encode($dc_percentage);
        $data['revision_direct_cost_percentage_count'] =1;
        $data['date_direct_cost_percentage'] = $time;
        $data['user_direct_cost_percentage'] = 2;
        Query_helper::add($destination_tables['budget_config'],$data,false);
        foreach($variety_prices as $result)
        {
            $data=array();
            $data['fiscal_year_id'] = 4;
            $data['variety_id'] = $result['id'];
            $data['amount_price'] = $result['price_kg'];
            Query_helper::add($destination_tables['variety_pricing_packing'],$data,false);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success Transfer Configs';
        }
        else
        {
            echo 'Failed Transfer Configs';
        }


    }
    private function hom()
    {
        $source_tables=array(
            'budget_target'=>'arm_ems.bms_forward_hom',
            'details'=>'arm_ems.bms_hom_bud_hom_bt'
        );
        $destination_tables=array(
            'budget_target'=>'arm_bms_2018_19.bms_hom_budget_target',
            'details'=>'arm_bms_2018_19.bms_hom_budget_target_hom'
        );
        $budget_target_old=Query_helper::get_info($source_tables['budget_target'],'*',array('year0_id = 4'),1);
        $details_old=Query_helper::get_info($source_tables['details'],'*',array('year0_id = 4'));
        $this->db->trans_start();  //DB Transaction Handle START
        $data=array();
        $data['id']=1;
        $data['fiscal_year_id']=4;
        $data['status_budget_forward']=$this->config->item('system_status_forwarded');
        $data['date_budget_forwarded']=$budget_target_old['date_forwarded'];
        $data['user_budget_forwarded']=$budget_target_old['user_forwarded'];
        $data['date_created']=$budget_target_old['date_created'];
        $data['user_created']=$budget_target_old['user_created'];
        $data['status_target_forward']=$this->config->item('system_status_forwarded');
        $data['date_target_forwarded']=$budget_target_old['date_target_finalized'];
        $data['user_target_forwarded']=$budget_target_old['user_target_finalized'];
        $data['status_target_di_forward']=$this->config->item('system_status_forwarded');
        $data['date_target_di_forwarded']=$budget_target_old['date_assigned'];
        $data['user_target_di_forwarded']=$budget_target_old['user_assigned'];
        Query_helper::add($destination_tables['budget_target'],$data,false);
        foreach($details_old as $result)
        {
            $data=array();
            $data['fiscal_year_id']=4;
            $data['variety_id']=$result['variety_id'];
            $data['quantity_budget']=$result['year0_budget_quantity']?$result['year0_budget_quantity']:0;
            $data['quantity_prediction_1']=$result['year1_target_quantity']?$result['year1_target_quantity']:0;
            $data['quantity_prediction_2']=$result['year2_target_quantity']?$result['year2_target_quantity']:0;
            $data['quantity_prediction_3']=$result['year3_target_quantity']?$result['year3_target_quantity']:0;
            $data['revision_count_budget']=($data['quantity_budget']>0)?1:0;
            $data['date_updated_budget']=$result['date_budgeted'];
            $data['user_updated_budget']=$result['user_budgeted'];

            $data['quantity_target']=$result['year0_target_quantity']?$result['year0_target_quantity']:0;
            $data['revision_count_target']=($data['quantity_target']>0)?1:0;
            $data['date_updated_target']=$result['date_targeted'];
            $data['user_updated_target']=$result['user_targeted'];
            Query_helper::add($destination_tables['details'],$data,false);
        }

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success Transfer Hom';
        }
        else
        {
            echo 'Failed Transfer Hom';
        }

    }
    private function di()
    {
        $source_tables=array(
            'budget_target'=>'arm_ems.bms_forward_di',
            'details'=>'arm_ems.bms_di_bud_di_bt'
        );
        $destination_tables=array(
            'budget_target'=>'arm_bms_2018_19.bms_di_budget_target',
            'details'=>'arm_bms_2018_19.bms_di_budget_target_division'
        );
        $this->db->from($source_tables['budget_target'].' bt');
        $this->db->where('bt.year0_id',4);
        $this->db->group_by('bt.division_id');

        $budget_target_old=$this->db->get()->result_array();

        $details_old=Query_helper::get_info($source_tables['details'],'*',array('year0_id = 4'));
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($budget_target_old as $result)
        {
            $data=array();
            $data['fiscal_year_id']=4;
            $data['division_id']=$result['division_id'];
            $data['status_budget_forward']=$this->config->item('system_status_forwarded');
            $data['date_budget_forwarded']=$result['date_forwarded'];
            $data['user_budget_forwarded']=$result['user_forwarded'];
            $data['date_created']=$result['date_created'];
            $data['user_created']=$result['user_created'];
            $data['status_target_forward']=$this->config->item('system_status_forwarded');
            $data['date_target_forwarded']=$result['date_assigned'];
            $data['user_target_forwarded']=$result['user_assigned'];
            //$data['status_target_di_forward']=$this->config->item('system_status_forwarded');
            //$data['date_target_di_forwarded']=$budget_target_old['date_assigned'];
            //$data['user_target_di_forwarded']=$budget_target_old['user_assigned'];
            //$data['status_target_di_forward']=$this->config->item('system_status_forwarded');
            Query_helper::add($destination_tables['budget_target'],$data,false);
        }

        foreach($details_old as $result)
        {
            $data=array();
            $data['fiscal_year_id']=4;
            $data['variety_id']=$result['variety_id'];
            $data['division_id']=$result['division_id'];
            $data['quantity_budget']=$result['year0_budget_quantity']?$result['year0_budget_quantity']:0;
            $data['revision_count_budget']=($data['quantity_budget']>0)?1:0;
            $data['date_updated_budget']=$result['date_budgeted'];
            $data['user_updated_budget']=$result['user_budgeted'];

            $data['quantity_target']=$result['year0_target_quantity']?$result['year0_target_quantity']:0;
            $data['revision_count_target']=($data['quantity_target']>0)?1:0;
            $data['date_updated_target']=$result['date_targeted'];
            $data['user_updated_target']=$result['user_targeted'];

            $data['quantity_prediction_1']=0;
            $data['quantity_prediction_2']=0;
            $data['quantity_prediction_3']=0;

            Query_helper::add($destination_tables['details'],$data,false);
        }

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success Transfer DI';
        }
        else
        {
            echo 'Failed Transfer DI';
        }

    }
}
