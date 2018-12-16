<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function index()
    {
        //echo EPSILON;

        $fiscal_year_id=4;
        $variety_id=86;
        $this->db->from($this->config->item('table_login_basic_setup_fiscal_year') . ' fiscal_year');
        $this->db->select('fiscal_year.name fiscal_year_name');

        $this->db->join($this->config->item('table_bms_setup_budget_config') . ' budget_config', 'budget_config.fiscal_year_id = fiscal_year.id', 'INNER');
        $this->db->select('budget_config.*');

        $this->db->where('fiscal_year.id', $fiscal_year_id);
        $fiscal_year_info = $this->db->get()->row_array();

        $data['item'] = array();
        /*$data['item']['fiscal_year_id'] = $fiscal_year_id;
        $data['item']['fiscal_year_name'] = $fiscal_year_info['fiscal_year_name'];

        $results = Budget_helper::get_crop_type_varieties(array(), array(), array($variety_id));
        $data['item']['crop_name'] = $results[0]['crop_name'];
        $data['item']['crop_type_name'] = $results[0]['crop_type_name'];
        $data['item']['variety_name'] = $results[0]['variety_name'];
        $data['item']['variety_id'] = $variety_id;*/

        $data['item']['percentage_direct_cost'] = 0;
        if ($fiscal_year_info['revision_count_percentage_direct_cost'] > 0)
        {
            $results = json_decode($fiscal_year_info['percentage_direct_cost'], true);
            foreach ($results as $value)
            {
                $data['item']['percentage_direct_cost'] += $value;

            }
        }
        else
        {
            $data['message_warning_config'][] = '<b>Direct Cost Percentage</b> - NOT Configured for this Fiscal Year';
        }

        $data['item']['percentage_packing_cost'] = 0;
        if ($fiscal_year_info['revision_count_percentage_packing_cost'] > 0)
        {
            $results = json_decode($fiscal_year_info['percentage_packing_cost'], true);
            foreach ($results as $value)
            {

                $data['item']['percentage_packing_cost'] += $value;
            }
        }
        //var_dump($data['item']['percentage_direct_cost']);
        $results = Query_helper::get_info($this->config->item('table_bms_principal_quantity_principal'), '*', array('fiscal_year_id=' . $fiscal_year_id, 'variety_id =' . $variety_id,'revision = 1'));

        foreach ($results as $result)
        {
            $old_item_principles[$result['principal_id']] = $result;
            $principle_ids_old[$result['principal_id']] = $result['principal_id']; // Storing Principal Id's for Comparison
            //if((abs($result['percentage_direct_cost'] - ($data['item']['percentage_direct_cost']))>=EPSILON))
            if($result['percentage_direct_cost'] != ($data['item']['percentage_direct_cost']))
            {
                echo 'not equal';
            }
            else
            {
                echo 'equal';
            }

        }
//        $a=(string)"8.8";
//        $b=(float)8.8;
//
//        if($a!=$b)
//        {
//            var_dump($a);
//            var_dump($b);
//        }
//        else
//        {
//            echo 'equal';
//            var_dump($a);
//            var_dump($b);
//        }
    }
}
