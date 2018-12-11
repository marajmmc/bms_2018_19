<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url.'/index/list_target_zone_next_year/'.$options['fiscal_year_id'].'/'.$options['division_id'])
);
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))||(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save_jqx'
    );
}
if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5']) && ($CI->permissions['action5']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'class'=>'button_action_download',
        'data-title'=>"Download"
    );
}

$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FISCAL_YEAR');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $fiscal_year['name'];?></label>
        </div>
    </div>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $division['name'];?></label>
        </div>
    </div>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $crop['name'];?></label>
        </div>
    </div>
    <?php
    echo $CI->load->view($this->common_view_location."/info_acres",'',true);
    ?>
    <form id="save_form_jqx" action="<?php echo site_url($CI->controller_url.'/index/save_target_zone_next_year');?>" method="post">
        <input type="hidden" name="item[fiscal_year_id]" value="<?php echo $options['fiscal_year_id']; ?>" />
        <input type="hidden" name="item[division_id]" value="<?php echo $options['division_id']; ?>" />
        <div id="jqx_inputs">
        </div>
    </form>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>

<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_off_events();
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(document).on("click", "#button_action_save_jqx", function(event)
        {
            $('#save_form_jqx #jqx_inputs').html('');
            var data=$('#system_jqx_container').jqxGrid('getrows');
            for(var i=0;i<data.length;i++)
            {
                //$('#save_form_jqx  #jqx_inputs').append('<input type="hidden" name="items['+data[i]['variety_id']+']" value="'+data[i]['quantity_budget']+'">');
                <?php
                $serial=0;
                foreach($fiscal_years_next_budgets as $fy)
                {
                    ++$serial;
                    foreach($zones as $zone)
                    {
                    ?>
                        $('#save_form_jqx  #jqx_inputs').append('<input type="hidden" name="items['+data[i]['variety_id']+'][<?php echo $zone['zone_id'];?>][quantity_prediction_<?php echo $serial;?>]" value="'+data[i]['quantity_prediction_zi_<?php echo $serial;?>_<?php echo $zone['zone_id']?>']+'">');
                    <?php
                    }
                }
                ?>
            }
            var sure = confirm('<?php echo $CI->lang->line('MSG_CONFIRM_SAVE'); ?>');
            if(sure)
            {
                $("#save_form_jqx").submit();
            }
        });

        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_edit_target_zone_next_year');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                foreach($system_preference_items as $key=>$item)
                {
                    if(($key!='crop_type_name')||($key!='variety_name'))
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
                foreach($fiscal_years_previous_sales as $fy)
                {
                    ?>
                    { name: 'quantity_sale_<?php echo $fy['id']; ?>', type: 'number' },
                    <?php
                }
                $serial=0;
                foreach($fiscal_years_next_budgets as $fy)
                {
                    ++$serial;
                        ?>
                    { name: 'quantity_prediction_<?php echo $serial; ?>', type: 'number' },
                    <?php
                    foreach($zones as $zone)
                    {
                        ?>
                        { name: 'quantity_prediction_zi_<?php echo $serial; ?>_<?php echo $zone['zone_id']?>', type: 'number' },
                        <?php
                    }
                    ?>
                    { name: 'quantity_prediction_sub_total_zi_<?php echo $serial; ?>', type: 'number' },
                    <?php
                }
        ?>
            ],
            type: 'POST',
            url: url,
            data:JSON.parse('<?php echo json_encode($options);?>')
        };
        /*var header_render=function (text, align)
        {
            var words = text.split(" ");
            var label=words[0];
            var count=words[0].length;
            for (i = 1; i < words.length; i++)
            {
                if((count+words[i].length)>10)
                {
                    label=label+'</br>'+words[i];
                    count=words[i].length;
                }
                else
                {
                    label=label+' '+words[i];
                    count=count+words[i].length;
                }

            }
            return '<div style="margin: 5px;">'+label+'</div>';
        };*/
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            if((column=='quantity_prediction_total_zi')||(column.substr(0,33)=='quantity_prediction_sub_total_zi_'))
            {
                if(value==0)
                {
                    element.html('');
                }
                else
                {
                    element.html(get_string_kg(value));
                }
            }
            else if(column.substr(0,23)=='quantity_prediction_zi_')
            {
                if(value==0)
                {
                    value='';
                }
                element.html('<div class="jqxgrid_input">'+value+'</div>');
            }
            else if(column.substr(0,9)=='quantity_')
            {
                if(value==0)
                {
                    element.html('');
                }
                else
                {
                    element.html(get_string_kg(value));
                }
            }

            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            return element[0].outerHTML;
        };
        var aggregatesrenderer=function (aggregates)
        {
            //console.log('here');
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +aggregates['sum']+'</div>';

        };
        var aggregatesrenderer_kg=function (aggregates)
        {
            var text='';
            if(!((aggregates['sum']=='0.000')||(aggregates['sum']=='')))
            {
                text=get_string_kg(aggregates['sum']);
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
                altrows: true,
                showaggregates: true,
                showstatusbar: true,
                rowsheight: 35,
                /*columnsheight: 40,*/
                editable:true,
                columns:
                [
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>', dataField: 'crop_type_name',width:'100', filtertype:'list',pinned:true,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',width:'150',pinned:true,editable:false},
                    <?php
                        for($i=sizeof($fiscal_years_previous_sales)-1;$i>=0;$i--)
                        {
                            ?>
                            {columngroup: 'previous_years',text: '<?php echo $fiscal_years_previous_sales[$i]['name']; ?>', dataField: 'quantity_sale_<?php echo $fiscal_years_previous_sales[$i]['id']; ?>',width:'100',filterable: false,cellsrenderer: cellsrenderer,align:'center',cellsAlign:'right',editable:false,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_kg},
                            <?php
                        }
                    ?>
                    { text: 'Current Year<br> Target', dataField: 'quantity_target_di',width:'100',filterable:false,cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_kg},
                    <?php
                    $serial=0;
                    foreach($fiscal_years_next_budgets as $fy)
                    {
                        ++$serial;
                        ?>
                        { columngroup: 'next_years_<?php echo $serial;?>', text: 'Prediction', dataField: 'quantity_prediction_<?php echo $serial;?>',width:'100',filterable:false,cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_kg},
                        <?php
                        $zone_sl=0;
                        foreach($zones as $zone)
                        {
                            ++$zone_sl;
                            ?>
                            {columngroup: 'next_years_<?php echo $serial;?>', text: '<?php echo $zone_sl.'. '.$zone['zone_name']?>', datafield: 'quantity_prediction_zi_<?php echo $serial;?>_<?php echo $zone['zone_id']?>', width: 100, filterable: false, cellsalign: 'right',cellsrenderer: cellsrenderer,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_kg,columntype: 'custom',
                                initeditor: function (row, cellvalue, editor, celltext, pressedkey)
                                {
                                    editor.html('<div style="margin: 0px;width: 100%;height: 100%;padding: 5px;"><input style="z-index: 1 !important;" type="text" value="'+cellvalue+'" class="jqxgrid_input float_type_positive"><div>');
                                },
                                geteditorvalue: function (row, cellvalue, editor)
                                {
                                    // return the editor's value.
                                    var value=editor.find('input').val();
                                    var selectedRowData = $('#system_jqx_container').jqxGrid('getrowdata', row);
                                    return editor.find('input').val();
                                },
                                cellvaluechanging: function (row, datafield, columntype, oldvalue, newvalue)
                                {
                                    if (newvalue != oldvalue)
                                    {
                                        var selectedRowData = $('#system_jqx_container').jqxGrid('getrowdata', row);//only last selected
                                        var sub_total=parseFloat(selectedRowData['quantity_prediction_sub_total_zi_<?php echo $serial;?>'])-parseFloat(oldvalue)+parseFloat(newvalue);
                                        var outlet_total=parseFloat(selectedRowData['quantity_prediction_total_zi'])-parseFloat(oldvalue)+parseFloat(newvalue);

                                        //console.log(selectedRowData);
                                        $("#system_jqx_container").jqxGrid('setcellvalue', row, 'quantity_prediction_sub_total_zi_<?php echo $serial;?>', sub_total);
                                        $("#system_jqx_container").jqxGrid('setcellvalue', row, 'quantity_prediction_total_zi', outlet_total);

                                    }
                                }
                            },
                            <?php
                        }
                        ?>
                        { columngroup: 'next_years_<?php echo $serial;?>', text: 'Total Target', dataField: 'quantity_prediction_sub_total_zi_<?php echo $serial;?>',width:'100',filterable:false,cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_kg},
                        <?php
                    }
                    ?>
                    { text: 'Total ZI Target', dataField: 'quantity_prediction_total_zi',width:'100',filterable:false,cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_kg}
                ],
                columngroups:
                [
                    { text: '<?php echo $CI->lang->line('LABEL_PREVIOUS_YEARS'); ?> Achieved', align: 'center', name: 'previous_years' },
                    <?php
                    $serial=0;
                    foreach($fiscal_years_next_budgets as $fy)
                    {
                        ++$serial;
                        ?>
                        { text: '<?php echo $fy['name']; ?>', align: 'center', name: 'next_years_<?php echo $serial;?>' },
                        <?php
                    }
                    ?>
                ]
            });
    });
</script>
