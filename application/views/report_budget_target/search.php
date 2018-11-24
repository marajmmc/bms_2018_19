<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index')
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/list');?>" method="post">
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-6">
                <div id="container_product">
                    <div style="" class="row show-grid" id="crop_id_container">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
                        </div>
                        <div class="col-xs-6">
                            <select id="crop_id" name="report[crop_id]" class="form-control">
                                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            </select>
                        </div>
                    </div>
                    <div style="display: none;" class="row show-grid" id="crop_type_id_container">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?></label>
                        </div>
                        <div class="col-xs-6">
                            <select id="crop_type_id" name="report[crop_type_id]" class="form-control">
                                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            </select>
                        </div>
                    </div>
                    <div style="display: none;" class="row show-grid" id="variety_id_container">
                        <div class="col-xs-6">
                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?></label>
                        </div>
                        <div class="col-xs-6">
                            <select id="variety_id" name="report[variety_id]" class="form-control">
                                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <!-- Fiscal Year & Date Range Section-->
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <select id="fiscal_year_id" name="report[fiscal_year_id]" class="form-control">
                            <?php
                            foreach($fiscal_years as $year)
                            {?>
                                <option value="<?php echo $year['value']?>"><?php echo $year['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_FISCAL_YEAR');?> <span style="color:#FF0000">*</span></label>
                    </div>
                </div>
                <!-- Location Section-->
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <?php
                        if($CI->locations['division_id']>0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['division_name'];?></label>
                            <input type="hidden" id="division_id" name="report[division_id]" value="<?php echo $this->locations['division_id']?>" class="form-control" />
                        <?php
                        }
                        else
                        {
                            ?>
                            <select id="division_id" name="report[division_id]" class="form-control">
                                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                <?php
                                foreach($divisions as $division)
                                {?>
                                    <option value="<?php echo $division['value']?>"><?php echo $division['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
                    </div>
                </div>
                <div style="<?php if(!(sizeof($zones)>0)){echo 'display:none';} ?>" class="row show-grid" id="zone_id_container">
                    <div class="col-xs-6">
                        <?php
                        if($CI->locations['zone_id']>0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['zone_name'];?></label>
                            <input type="hidden" id="zone_id" name="report[zone_id]" value="<?php echo $this->locations['zone_id']?>" class="form-control" />
                        <?php
                        }
                        else
                        {
                            ?>
                            <select id="zone_id" name="report[zone_id]" class="form-control">
                                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                <?php
                                foreach($zones as $zone)
                                {?>
                                    <option value="<?php echo $zone['value']?>"><?php echo $zone['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?><span style="color:#FF0000">*</span></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-8">

            </div>
            <div class="col-xs-4">
                <div class="action_button">
                    <button id="button_action_report" type="button" class="btn" data-form="#save_form"><?php echo $CI->lang->line("ACTION_REPORT"); ?></button>
                </div>

            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>

<div id="system_report_container">

</div>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        system_off_events();
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $('#crop_id').html(get_dropdown_with_select(system_crops));
        $(document).on('change','#fiscal_year_id',function()
        {
            $("#system_report_container").html('');
        });
        $(document).on("change","#crop_id",function()
        {
            $('#system_report_container').html('');
            $('#crop_type_id').val("");
            $('#variety_id').val("");

            var crop_id=$('#crop_id').val();
            $('#crop_type_id_container').hide();
            $('#variety_id_container').hide();
            if(crop_id>0)
            {
                $('#crop_type_id_container').show();
                if(system_types[crop_id]!==undefined)
                {
                    $('#crop_type_id').html(get_dropdown_with_select(system_types[crop_id]));
                }
            }
        });
        $(document).on("change","#crop_type_id",function()
        {
            $('#system_report_container').html('');
            $('#variety_id').val("");
            var crop_type_id=$('#crop_type_id').val();
            $('#variety_id_container').hide();
            if(crop_type_id>0)
            {
                $('#variety_id_container').show();
                if(system_varieties[crop_type_id]!==undefined)
                {
                    $('#variety_id').html(get_dropdown_with_select(system_varieties[crop_type_id]));
                }
            }
        });
        $(document).on('change','#variety_id',function()
        {
            $("#system_report_container").html('');
        });
        /* Location Section */
        $(document).on('change','#division_id',function()
        {
            $('#zone_id').val('');
            var division_id=$('#division_id').val();
            $('#zone_id_container').hide();
            $("#system_report_container").html('');
            if(division_id>0)
            {
                if(system_zones[division_id]!==undefined)
                {
                    $('#zone_id_container').show();
                    $('#zone_id').html(get_dropdown_with_select(system_zones[division_id]));
                }
            }

        });
        $(document).on('change','#zone_id',function()
        {
            $("#system_report_container").html('');
        });

    });
</script>
