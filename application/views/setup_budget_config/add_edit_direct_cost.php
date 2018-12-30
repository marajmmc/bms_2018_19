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
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/add_edit_direct_cost/'.$item['fiscal_year_id'])
);
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
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PERCENTAGE_AIR_FREIGHT'); ?> (%) <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="percentage_air_freight" id="percentage_air_freight" class="form-control float_type_positive" value="<?php echo $item['percentage_air_freight']; ?>"/>
            </div>
        </div>
        <hr style="border-top:1px solid #cfcfcf; margin-top:40px"/>
        <?php
        $percentage_direct_cost=array();
        if($item['percentage_direct_cost'])
        {
            $percentage_direct_cost=json_decode($item['percentage_direct_cost'],true);
        }
        foreach($direct_cost_items as $dc)
        {
        ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $dc['name']; ?> (%) <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="percentage_direct_cost_common[<?php echo $dc['id']?>]" class="form-control float_type_positive" value="<?php echo isset($percentage_direct_cost[0][$dc['id']])?$percentage_direct_cost[0][$dc['id']]:0;?>"/>
            </div>
        </div>
        <?php
        }
        ?>
        <hr style="border-top:1px solid #cfcfcf; margin-top:40px"/>
        <?php
        $index=0;
        ?>
        <div style="overflow-x: auto;" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Additional Setup for Crop</label>
            </div>
            <div class="col-sm-6 col-xs-8">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th style="width: 200px;">Crop</th>
                        <th style="width: 250px;">Item</th>
                        <th style="width: 150px;">Percentage</th>
                    </tr>
                    </thead>
                    <tbody id="items_container">
                    <?php
                    foreach($percentage_direct_cost as $crop_id=>$dc)
                    {
                        reset($dc);
                        $dc_id = key($dc);
                        $percentage_dc = $dc[$dc_id];
                        if($crop_id>0)
                        {
                            $index++;
                            ?>
                            <tr>
                                <td>
                                    <select class="form-control crop_id" name="percentage_direct_cost_crops[<?php echo $index;?>][crop_id]">
                                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                        <?php
                                        foreach($crops as $crop)
                                        {?>
                                            <option value="<?php echo $crop['value']?>" <?php if(($crop['value']==$crop_id)){ echo "selected";}?>><?php echo $crop['text'];?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control dc_id" name="percentage_direct_cost_crops[<?php echo $index;?>][dc_id]">
                                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                        <?php
                                        foreach($direct_cost_items as $dc)
                                        {?>
                                            <option value="<?php echo $dc['id']?>" <?php if(($dc['id']==$dc_id)){ echo "selected";}?>><?php echo $dc['name'];?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td class="text-right">
                                    <input type="text" name="percentage_direct_cost_crops[<?php echo $index;?>][percentage_dc]" class="form-control float_type_positive percentage_dc" value="<?php echo $percentage_dc; ?>"/>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    <?php
                    ?>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-xs-4">
                <button type="button" class="btn btn-warning system_button_add_more" data-current-id="<?php echo $index;?>"><?php echo $CI->lang->line('LABEL_ADD_MORE');?></button>
            </div>
            <div class="col-xs-4">

            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</form>
<div id="system_content_add_more" style="display: none;">
    <table>
        <tbody>
        <tr>
            <td>
                <select class="form-control crop_id">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <?php
                    foreach($crops as $crop)
                    {?>
                        <option value="<?php echo $crop['value']?>"><?php echo $crop['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </td>
            <td>
                <select class="form-control dc_id">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <?php
                    foreach($direct_cost_items as $dc)
                    {?>
                        <option value="<?php echo $dc['id']?>"><?php echo $dc['name'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </td>
            <td class="text-right">
                <input type="text" class="form-control float_type_positive percentage_dc" value=""/>
            </td>
            <td>
                <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_off_events();
        $(document).on("click", ".system_button_add_more", function(event)
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            current_id=current_id+1;
            $(this).attr('data-current-id',current_id);
            var content_id='#system_content_add_more table tbody';

            $(content_id+' .crop_id').attr('id','crop_id_'+current_id);
            $(content_id+' .crop_id').attr('name','percentage_direct_cost_crops['+current_id+'][crop_id]');

            $(content_id+' .dc_id').attr('id','dc_id_'+current_id);
            $(content_id+' .dc_id').attr('name','percentage_direct_cost_crops['+current_id+'][dc_id]');

            $(content_id+' .percentage_dc').attr('id','percentage_dc_'+current_id);
            $(content_id+' .percentage_dc').attr('name','percentage_direct_cost_crops['+current_id+'][percentage_dc]');

            var html=$(content_id).html();
            $("#items_container").append(html);
            $(content_id+' .crop_id').removeAttr('id');
            $(content_id+' .dc_id').removeAttr('id');
            $(content_id+' .percentage_dc').removeAttr('id');
        });
        $(document).on("click", ".system_button_add_delete", function(event)
        {
            $(this).closest('tr').remove();
            calculate_total();
        });
    });
</script>
