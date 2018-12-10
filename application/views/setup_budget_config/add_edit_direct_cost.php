<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))||(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
    );
}

$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_direct_cost');?>" method="post">
    <input type="hidden" id="fiscal_year_id" name="item[fiscal_year_id]" value="<?php echo $item['fiscal_year_id']?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">&nbsp;</div>
            <div class="col-xs-4">
                <table class="table table-responsive table-bordered">
                    <thead>
                    <tr>
                        <th>Costing Head</th>
                        <th class="text-right">Percentage (%)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $amount_direct_cost_percentage=array();
                    if($item['amount_direct_cost_percentage'])
                    {
                        $amount_direct_cost_percentage=json_decode($item['amount_direct_cost_percentage'],true);
                    }
                    foreach($direct_cost_items as $items)
                    {
                        ?>
                        <tr>
                            <td>
                                <?php
                                if($items['status']==$CI->config->item('system_status_inactive'))
                                {
                                    echo '<s>'.$items['name'].'</s>'.'<i class="text-danger"> (In-Active)</i>';
                                }
                                else
                                {
                                    echo $items['name'];
                                }
                                ?>
                            </td>
                            <td><input type="text" name="items[<?php echo $items['id']?>]" id="row_per_page" class="form-control float_type_positive" value="<?php echo isset($amount_direct_cost_percentage[$items['id']])?$amount_direct_cost_percentage[$items['id']]:'';?>" /></td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="col-xs-4">&nbsp;</div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_off_events();
    });
</script>
