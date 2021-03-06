<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
if ((isset($CI->permissions['action1']) && ($CI->permissions['action1'] == 1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1)))
{
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_SAVE"),
        'id' => 'button_action_save',
        'data-form' => '#save_form'
    );
}
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/add_edit_indirect_cost/'.$item['fiscal_year_id'])
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_indirect_cost'); ?>" method="post">
    <input type="hidden" id="fiscal_year_id" name="item[fiscal_year_id]" value="<?php echo $item['fiscal_year_id'] ?>"/>
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0"/>

    <div class="row widget">

        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_GENERAL_EXPENSE'); ?> (%) <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="items[percentage_general]" id="percentage_general" class="form-control float_type_positive" value="<?php echo $item['percentage_general']; ?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_MARKETING_EXPENSE'); ?> (%) <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="items[percentage_marketing]" id="percentage_marketing" class="form-control float_type_positive" value="<?php echo $item['percentage_marketing']; ?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FINANCIAL_EXPENSE'); ?> (%) <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="items[percentage_finance]" id="percentage_finance" class="form-control float_type_positive" value="<?php echo $item['percentage_finance']; ?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_INCENTIVE'); ?> (%) <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="items[percentage_incentive]" id="percentage_incentive" class="form-control float_type_positive" value="<?php echo $item['percentage_incentive']; ?>"/>
            </div>
        </div>

        <hr style="border-top:1px solid #cfcfcf; margin-top:40px"/>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PROFIT'); ?> (%) <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="items[percentage_profit]" id="percentage_profit" class="form-control float_type_positive" value="<?php echo $item['percentage_profit']; ?>"/>
            </div>
        </div>

        <hr style="border-top:1px solid #cfcfcf; margin-top:40px"/>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SALES_COMMISSION'); ?> (%) <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="items[percentage_sales_commission]" id="percentage_sales_commission" class="form-control float_type_positive" value="<?php echo $item['percentage_sales_commission']; ?>"/>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function () {
        system_off_events();
    });
</script>
