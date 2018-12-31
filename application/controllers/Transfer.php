<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transfer extends CI_Controller
{
    public function index()
    {
        //$this->configs();
        //$this->hom();
        //$this->di();
        //$this->zi();
        //$this->si();
        //$this->principal_quantity();
    }
    private function configs()
    {
        $source_tables=array(
            'variety_price'=>'arm_login_2018_19.login_setup_classification_varieties'
        );
        $destination_tables=array(
            'budget_config'=>'arm_bms_2018_19.bms_setup_budget_config',
            'variety_pricing_packing'=>'arm_bms_2018_19.bms_setup_budget_config_variety_pricing'
        );
        /*$currency_rates=array();
        $currency_rates[1]=85;
        $currency_rates[2]=95;*/
        //$results=Query_helper::get_info($source_tables['dc_percentage'],'*',array('fiscal_year_id = 2'),0,0,array('item_id ASC'));
        /*$percentage_direct_cost=array();
        $percentage_direct_cost[1]=.6;
        $percentage_direct_cost[2]=.4;
        $percentage_direct_cost[3]=.1;
        $percentage_direct_cost[4]=2.5;
        $percentage_direct_cost[5]=.3;
        $percentage_direct_cost[6]=3;
        $percentage_direct_cost[7]=.2;
        $percentage_direct_cost[8]=0;
        $percentage_direct_cost[9]=.2;

        $percentage_packing_cost=array();
        $percentage_packing_cost[1]=.4;
        $percentage_packing_cost[2]=.9;
        $percentage_packing_cost[3]=1.8;*/

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
        $data['revision_count_pricing'] =1;
        $data['date_pricing_updated'] = $time;
        $data['user_pricing_updated'] = 2;
//        $data['amount_currency_rate'] = json_encode($currency_rates);
//        $data['revision_count_currency_rate'] =1;
//        $data['date_currency_rate'] = $time;
//        $data['user_currency_rate'] = 2;
//        $data['percentage_air_freight'] = 2.5;
//        $data['percentage_direct_cost'] = json_encode($percentage_direct_cost);
//        $data['revision_count_percentage_direct_cost'] =1;
//        $data['date_percentage_direct_cost'] = $time;
//        $data['user_percentage_direct_cost'] = 2;
//
//        $data['percentage_packing_cost'] = json_encode($percentage_packing_cost);
//        $data['revision_count_percentage_packing_cost'] =1;
//        $data['date_percentage_packing_cost'] = $time;
//        $data['user_percentage_packing_cost'] = 2;

        Query_helper::add($destination_tables['budget_config'],$data,false);
        foreach($variety_prices as $result)
        {
            $data=array();
            $data['fiscal_year_id'] = 4;
            $data['variety_id'] = $result['id'];
            $data['amount_price_net'] = $result['price_kg'];
            $data['amount_price_trade'] = $result['price_kg'];
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
            $data['status_target_zi_forward']=$this->config->item('system_status_forwarded');
            $data['date_target_zi_forwarded']=$result['date_assigned'];
            $data['user_target_zi_forwarded']=$result['user_assigned'];

            Query_helper::add($destination_tables['budget_target'],$data,false);
        }

        foreach($details_old as $result)
        {
            $data=array();
            $data['fiscal_year_id']=4;
            $data['division_id']=$result['division_id'];
            $data['variety_id']=$result['variety_id'];

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
    private function zi()
    {
        $source_tables=array(
            'budget_target'=>'arm_ems.bms_forward_zi',
            'details'=>'arm_ems.bms_zi_bud_zi_bt'
        );
        $destination_tables=array(
            'budget_target'=>'arm_bms_2018_19.bms_zi_budget_target',
            'details'=>'arm_bms_2018_19.bms_zi_budget_target_zone'
        );
        $this->db->from($source_tables['budget_target'].' bt');
        $this->db->where('bt.status_forward','Yes');
        $this->db->where('bt.status_assign','Yes');
        $this->db->where('bt.year0_id',4);
        $this->db->group_by('bt.zone_id');

        $budget_target_old=$this->db->get()->result_array();


        $details_old=Query_helper::get_info($source_tables['details'],'*',array('year0_id = 4'));
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($budget_target_old as $result)
        {
            $data=array();
            $data['fiscal_year_id']=4;
            $data['zone_id']=$result['zone_id'];
            $data['status_budget_forward']=$this->config->item('system_status_forwarded');
            $data['date_budget_forwarded']=$result['date_forwarded'];
            $data['user_budget_forwarded']=$result['user_forwarded'];
            $data['date_created']=$result['date_created'];
            $data['user_created']=$result['user_created'];
            //may be set pending
            $data['status_target_outlet_forward']=$this->config->item('system_status_forwarded');
            $data['date_target_outlet_forwarded']=$result['date_assigned'];
            $data['user_target_outlet_forwarded']=$result['user_assigned'];

            Query_helper::add($destination_tables['budget_target'],$data,false);
        }

        foreach($details_old as $result)
        {
            $data=array();
            $data['fiscal_year_id']=4;
            $data['zone_id']=$result['zone_id'];
            $data['variety_id']=$result['variety_id'];

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
            echo 'Success Transfer ZI';
        }
        else
        {
            echo 'Failed Transfer ZI';
        }

    }
    private function si()
    {
        $source_tables=array(
            'beeztola_user'=>'arm_beeztola_2018_19.pos_setup_user',
            'login_user'=>'arm_login_2018_19.login_setup_user',
            'districts'=>'arm_login_2018_19.login_setup_location_districts',
            'outlets'=>'arm_login_2018_19.login_csetup_customer',
            'outlets_info'=>'arm_login_2018_19.login_csetup_customer_info',
            'budget_target'=>'arm_ems.bms_forward_ti',
            'details'=>'arm_ems.bms_ti_bud_ti_bt'
        );


        $destination_tables=array(
            'budget_target'=>'arm_beeztola_2018_19.pos_si_budget_target',
            'details'=>'arm_beeztola_2018_19.pos_si_budget_target_outlet'
        );
        $results=Query_helper::get_info($source_tables['beeztola_user'],array('id','employee_id'),array('employee_id IS NOT null'));
        $beeztola_user=array();
        foreach($results as $result)
        {
            $beeztola_user[$result['employee_id']]=$result['id'];
        }
        $results=Query_helper::get_info($source_tables['login_user'],array('id','employee_id'),array());
        $login_beeztola=array();
        $total=0;
        foreach($results as $result)
        {
            $login_beeztola[$result['id']]=2;
            if(isset($beeztola_user[$result['employee_id']]))
            {
                $total++;
                $login_beeztola[$result['id']]=$beeztola_user[$result['employee_id']];
            }

        }
        $this->db->from($source_tables['outlets_info'].' cus_info');
        $this->db->select('cus_info.customer_id outlet_id');
        $this->db->where('cus_info.revision',1);
        $this->db->where('cus_info.type',1);
        $this->db->join($this->config->item('table_login_setup_location_districts').' d','d.id = cus_info.district_id','INNER');
        $this->db->select('d.territory_id territory_id');
        $this->db->order_by('d.territory_id','ASC');
        $results=$this->db->get()->result_array();
        $territory_outlet=array();
        foreach($results as $result)
        {
            $territory_outlet[$result['territory_id']]=$result['outlet_id'];
        }

        $this->db->from($source_tables['budget_target'].' bt');
        $this->db->where('bt.status_forward','Yes');
        $this->db->where('bt.year0_id',4);
        $this->db->group_by('bt.territory_id');

        $budget_target_old=$this->db->get()->result_array();


        $details_old=Query_helper::get_info($source_tables['details'],'*',array('year0_id = 4'));
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($budget_target_old as $result)
        {
            $data=array();
            $data['fiscal_year_id']=4;
            $data['outlet_id']=$territory_outlet[$result['territory_id']];
            $data['status_budget_forward']=$this->config->item('system_status_forwarded');
            $data['date_budget_forwarded']=$result['date_forwarded'];
            $data['user_budget_forwarded']=$login_beeztola[$result['user_forwarded']];
            $data['date_created']=$result['date_created'];
            $data['user_created']=$login_beeztola[$result['user_created']];
            Query_helper::add($destination_tables['budget_target'],$data,false);
        }
        foreach($details_old as $result)
        {
            if((isset($territory_outlet[$result['territory_id']])))
            {
                $data=array();
                $data['fiscal_year_id']=4;
                $data['outlet_id']=$territory_outlet[$result['territory_id']];
                $data['variety_id']=$result['variety_id'];

                $data['quantity_budget']=$result['year0_budget_quantity']?$result['year0_budget_quantity']:0;
                $data['revision_count_budget']=($data['quantity_budget']>0)?1:0;
                $data['date_updated_budget']=$result['date_budgeted'];
                $data['user_updated_budget']=$result['user_budgeted']?$login_beeztola[$result['user_budgeted']]:$result['user_budgeted'];

                $data['quantity_target']=$result['year0_target_quantity']?$result['year0_target_quantity']:0;
                $data['revision_count_target']=($data['quantity_target']>0)?1:0;
                $data['date_updated_target']=$result['date_targeted'];
                $data['user_updated_target']=$result['user_targeted']?$login_beeztola[$result['user_targeted']]:$result['user_targeted'];
                Query_helper::add($destination_tables['details'],$data,false);

            }
        }

        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success Transfer SI';
        }
        else
        {
            echo 'Failed Transfer SI';
        }
    }
    private function principal_quantity()
    {
        $time=time();
        $source_tables=array(
            'principal_quantity'=>'arm_bms_2018_19.bms_principal_quantity'
        );
        $destination_tables=array(
            'hom'=>'arm_bms_2018_19.bms_hom_budget_target_hom'
        );
        $principal_quantity=Query_helper::get_info($source_tables['principal_quantity'],'*',array('fiscal_year_id = 4'));
        $this->db->trans_start();  //DB Transaction Handle START

        foreach($principal_quantity as $result)
        {
            $data=array();

            //$data['fiscal_year_id']=4;
            //$data['variety_id']=$result['variety_id'];
            $data['quantity_principal_quantity_confirm']=$result['quantity_total'];
            $data['revision_count_principal_quantity_confirm']=($data['quantity_principal_quantity_confirm']>0)?1:0;
            $data['date_updated_principal_quantity_confirm']=$time;
            $data['user_updated_principal_quantity_confirm']=1;
            Query_helper::update($destination_tables['hom'],$data,array('fiscal_year_id= 4','variety_id ='.$result['variety_id']),false);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'Success Transfer principal_quantity';
        }
        else
        {
            echo 'Failed Transfer principal_quantity';
        }
    }
}
