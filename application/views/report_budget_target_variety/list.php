<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons=array();
if(isset($CI->permissions['action4'])&&($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5'])&&($CI->permissions['action5']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'class'=>'button_action_download',
        'data-title'=>"Download"
    );
}
if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
{
    $action_buttons[]=array
    (
        'label'=>'Preference',
        'href'=>site_url($CI->controller_url.'/index/set_preference_list')
    );
}
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
$report=$CI->input->post('report');
$division_id=$report['division_id'];
$zone_id=$report['zone_id'];

?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
    if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
    {
        //$CI->load->view('preference',array('system_preference_items'=>$system_preference_items));
    ?>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="crop_name" <?php if($system_preference_items['crop_name']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="crop_type_name" <?php if($system_preference_items['crop_type_name']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="variety_name" <?php if($system_preference_items['variety_name']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="price_unit_kg" <?php if($system_preference_items['price_unit_kg']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_PRICE_UNIT_KG'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="area_budget_kg" <?php if($system_preference_items['area_budget_kg']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_AREA_BUDGET_KG'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="area_budget_amount" <?php if($system_preference_items['area_budget_amount']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_AREA_BUDGET_AMOUNT'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="area_target_kg" <?php if($system_preference_items['area_target_kg']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_AREA_TARGET_KG'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column" value="area_target_amount" <?php if($system_preference_items['area_target_amount']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_AREA_TARGET_AMOUNT'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column_prediction_kg" value="prediction_kg" <?php if($system_preference_items['prediction_kg']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_PREDICTION_KG'); ?></span></label></div></div>
        <div class="col-xs-2 "><div class="checkbox"><label><input type="checkbox" class="system_jqx_column_prediction_amount" value="prediction_amount" <?php if($system_preference_items['prediction_amount']){echo 'checked';}?>><span class=""><?php echo $CI->lang->line('LABEL_PREDICTION_AMOUNT'); ?></span></label></div></div>
    <?php
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_list');?>";
        $(document).off("click", ".system_jqx_column_prediction_kg");
        $(document).on("click", ".system_jqx_column_prediction_kg", function(event)
        {
            var jqx_grid_id='#system_jqx_container';
            $(jqx_grid_id).jqxGrid('beginupdate');
            if($(this).is(':checked'))
            {
                <?php
                $serial=0;
                foreach($fiscal_years_next_budgets as $budget)
                {
                ++$serial;
                ?>
                $(jqx_grid_id).jqxGrid('showcolumn', 'prediction_<?php echo $serial; ?>_kg');
                <?php
                }
                ?>
            }
            else
            {
                <?php
                $serial=0;
                foreach($fiscal_years_next_budgets as $budget)
                {
                ++$serial;
                ?>
                $(jqx_grid_id).jqxGrid('hidecolumn', 'prediction_<?php echo $serial; ?>_kg');
                <?php
                }
                ?>
            }
            $(jqx_grid_id).jqxGrid('endupdate');
        });
        $(document).off("click", ".system_jqx_column_prediction_amount");
        $(document).on("click", ".system_jqx_column_prediction_amount", function(event)
        {
            var jqx_grid_id='#system_jqx_container';
            $(jqx_grid_id).jqxGrid('beginupdate');
            if($(this).is(':checked'))
            {
                <?php
                $serial=0;
                foreach($fiscal_years_next_budgets as $budget)
                {
                ++$serial;
                ?>
                $(jqx_grid_id).jqxGrid('showcolumn', 'prediction_<?php echo $serial; ?>_amount');
                <?php
                }
                ?>
            }
            else
            {
                <?php
                $serial=0;
                foreach($fiscal_years_next_budgets as $budget)
                {
                ++$serial;
                ?>
                $(jqx_grid_id).jqxGrid('hidecolumn', 'prediction_<?php echo $serial; ?>_amount');
                <?php
                }
                ?>
            }
            $(jqx_grid_id).jqxGrid('endupdate');
        });

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                foreach($system_preference_items as $key=>$item)
                {
                    if((substr($key,-3)=='pkt') || (substr($key,-2)=='kg'))
                    {
                        ?>
                        { name: '<?php echo $key ?>', type: 'number' },
                        <?php
                    }
                    else
                    {
                        ?>
                        { name: '<?php echo $key ?>', type: 'string' },
                        <?php
                    }
                }
                $serial=0;
                foreach($fiscal_years_next_budgets as $budget)
                {
                    ++$serial;
                        ?>
                    { name: 'prediction_<?php echo $serial; ?>_kg', type: 'number' },
                    { name: 'prediction_<?php echo $serial; ?>_amount', type: 'number' },
                    <?php
                }
                if(!$division_id && !$zone_id)
                {
                    foreach($divisions as $division)
                    {
                        ?>
                        { name: 'sub_area_<?php echo $division['value']?>_budget_kg', type: 'string' },
                        { name: 'sub_area_<?php echo $division['value']?>_budget_amount', type: 'string' },

                        { name: 'sub_area_<?php echo $division['value']?>_target_kg', type: 'string' },
                        { name: 'sub_area_<?php echo $division['value']?>_target_amount', type: 'string' },
                        <?php
                    }
                }
                elseif($division_id && !$zone_id)
                {
                    // zone
                }
                ?>
            ],
            url: url,
            type: 'POST',
            data:JSON.parse('<?php echo json_encode($options);?>')
        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);

            if(column.substr(0,11)=='prediction_' || column.substr(0,9)=='sub_area_')
            {
                if(value==0)
                {
                    element.html('');
                }
                else if(value>0)
                {
                    element.html(get_string_kg(value));
                }
            }
            else if(column=='area_budget_kg' || column=='area_target_kg')
            {
                if(value==0)
                {
                    element.html('');
                }
                else if(value>0)
                {
                    element.html(get_string_kg(value));
                }
            }
            else if(column=='price_unit_kg' || column=='area_budget_amount' || column=='area_target_amount' )
            {
                if(value==0)
                {
                    element.html('');
                }
                else if(value>0)
                {
                    element.html(get_string_amount(value));
                }
            }


            if (record.variety_name=="Total Type")
            {
                if(!((column=='crop_name')||(column=='crop_type_name')))
                {
                    element.css({ 'background-color': system_report_color_type,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                }
            }
            else if (record.crop_type_name=="Total Crop")
            {
                if(column!='crop_name')
                {
                    element.css({ 'background-color': system_report_color_crop,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                }
            }
            else if (record.crop_name=="Grand Total")
            {
                element.css({ 'background-color': system_report_color_grand,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }
            else
            {
                element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }

            return element[0].outerHTML;
        };
        var aggregates=function (total, column, element, record)
        {
            if(record.crop_name=="Grand Total")
            {
                return record[element];

            }
            return total;
        };
        var aggregatesrenderer=function (aggregates)
        {
            //console.log('here');
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +aggregates['total']+'</div>';

        };
        var aggregatesrenderer_kg=function (aggregates)
        {
            var text='';
            if(!((aggregates['total']=='0.000')||(aggregates['total']=='')))
            {
                text=get_string_kg(aggregates['total']);
            }

            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +text+'</div>';
        };
        var aggregatesrenderer_amount=function (aggregates)
        {
            var text='';
            if(!((aggregates['total']=='0.000')||(aggregates['total']=='')))
            {
                text=get_string_amount(aggregates['total']);
            }

            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +text+'</div>';
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                source: dataAdapter,
                width: '100%',
                height: '350px',
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                columnsreorder: true,
                enablebrowserselection: true,
                selectionmode: 'singlerow',
                showaggregates: true,
                showstatusbar: true,
                altrows: true,
                rowsheight: 35,
                columns:
                    [
                        { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',filtertype: 'list',pinned:true,width:'100',hidden: <?php echo $system_preference_items['crop_name']?0:1;?>,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>', dataField: 'crop_type_name',pinned:true,width:'100',hidden: <?php echo $system_preference_items['crop_type_name']?0:1;?>,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',pinned:true,width:'100',hidden: <?php echo $system_preference_items['variety_name']?0:1;?>,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_PRICE_UNIT_KG'); ?>', dataField: 'price_unit_kg',width:'100',cellsAlign:'right',hidden: <?php echo $system_preference_items['price_unit_kg']?0:1;?>,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_amount},
                        { text: '<?php echo $CI->lang->line('LABEL_AREA_BUDGET_KG'); ?>', dataField: 'area_budget_kg',width:'100',cellsAlign:'right',hidden: <?php echo $system_preference_items['area_budget_kg']?0:1;?>,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                        { text: '<?php echo $CI->lang->line('LABEL_AREA_BUDGET_AMOUNT'); ?>', dataField: 'area_budget_amount',width:'100',cellsAlign:'right',hidden: <?php echo $system_preference_items['area_budget_amount']?0:1;?>,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_amount},
                        { text: '<?php echo $CI->lang->line('LABEL_AREA_TARGET_KG'); ?>', dataField: 'area_target_kg',width:'100',cellsAlign:'right',hidden: <?php echo $system_preference_items['area_target_kg']?0:1;?>,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                        { text: '<?php echo $CI->lang->line('LABEL_AREA_TARGET_AMOUNT'); ?>', dataField: 'area_target_amount',width:'100',cellsAlign:'right',hidden: <?php echo $system_preference_items['area_target_amount']?0:1;?>,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_amount},
                        <?php
                        // for budget
                        if(!$division_id && !$zone_id)
                        {
                            $serial=0;
                            foreach ($divisions as $division)
                            {
                                ++$serial;
                                ?>
                                {  columngroup: 'sub_area_budget',text: '(kg)<?php echo $division['text']; ?> ', dataField: 'sub_area_<?php echo $division['value']; ?>_budget_kg',width:'100',cellsAlign:'right',hidden: <?php echo $system_preference_items['sub_area_budget_kg']?0:1;?>,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                                {  columngroup: 'sub_area_budget',text: '(Amt)<?php echo $division['text']; ?> ', dataField: 'sub_area_<?php echo $division['value']; ?>_budget_amount',width:'100',cellsAlign:'right',hidden: <?php echo $system_preference_items['sub_area_budget_amount']?0:1;?>,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_amount},
                                <?php
                            }
                        }
                        elseif($division_id && !$zone_id)
                        {
                            // zone
                        }
                        // for target
                        if(!$division_id && !$zone_id)
                        {
                            $serial=0;
                            foreach ($divisions as $division)
                            {
                                ++$serial;
                                ?>
                                {  columngroup: 'sub_area_target',text: '(kg)<?php echo $division['text']; ?> ', dataField: 'sub_area_<?php echo $division['value']; ?>_target_kg',width:'100',cellsAlign:'right',hidden: <?php echo $system_preference_items['sub_area_target_kg']?0:1;?>,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                                {  columngroup: 'sub_area_target',text: '(Amt)<?php echo $division['text']; ?> ', dataField: 'sub_area_<?php echo $division['value']; ?>_target_amount',width:'100',cellsAlign:'right',hidden: <?php echo $system_preference_items['sub_area_target_amount']?0:1;?>,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_amount},
                                <?php
                            }
                        }
                        elseif($division_id && !$zone_id)
                        {
                            // zone
                        }
                        $serial=0;
                        foreach ($fiscal_years_next_budgets as $budget)
                        {
                            ++$serial;
                            ?>
                            {  columngroup: 'next_years',text: '<?php echo $budget['name']; ?> (kg)', dataField: 'prediction_<?php echo $serial; ?>_kg',width:'100',cellsAlign:'right',hidden: <?php echo $system_preference_items['prediction_kg']?0:1;?>,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                            {  columngroup: 'next_years',text: '<?php echo $budget['name']; ?> (Amount)', dataField: 'prediction_<?php echo $serial; ?>_amount',width:'100',cellsAlign:'right',hidden: <?php echo $system_preference_items['prediction_amount']?0:1;?>,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_amount},
                            <?php
                        }
                        ?>
                    ],
                    columngroups:
                    [
                        <?php
                        if(!$division_id && !$zone_id)
                        {
                            ?>
                            { text: 'All Division Budget', align: 'center', name: 'sub_area_budget' },
                            { text: 'All Division Target', align: 'center', name: 'sub_area_target' },
                            <?php
                        }
                        elseif($division_id && !$zone_id)
                        {
                            ?>
                            { text: 'Zone List', align: 'center', name: 'sub_area_budget' },
                            { text: 'Zone List', align: 'center', name: 'sub_area_target' },
                            <?php
                        }
                        ?>

                        { text: 'Next Years Prediction', align: 'center', name: 'next_years' }
                    ]
            });
    });
</script>