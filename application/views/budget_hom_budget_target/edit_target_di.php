<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url.'/index/list_target_di/'.$options['fiscal_year_id'])
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
/*echo '<pre>';
print_r($system_preference_items);
echo '</pre>';*/
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
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $crop['name'];?></label>
        </div>
    </div>
    <?php
    echo $CI->load->view($this->common_view_location."/info_acres",'',true);
    ?>
    <form id="save_form_jqx" action="<?php echo site_url($CI->controller_url.'/index/save_target_di');?>" method="post">
        <input type="hidden" name="item[fiscal_year_id]" value="<?php echo $options['fiscal_year_id']; ?>" />
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
                foreach($divisions as $division)
                {
                ?>
                $('#save_form_jqx  #jqx_inputs').append('<input type="hidden" name="items['+data[i]['variety_id']+'][<?php echo $division['division_id']?>][quantity_target]" value="'+data[i]['quantity_target_division_<?php echo $division['division_id']; ?>']+'">');
                <?php
            }
            ?>
            }
            var sure = confirm('<?php echo $CI->lang->line('MSG_CONFIRM_SAVE'); ?>');
            if(sure)
            {
                $("#save_form_jqx").submit();
            }
        });

        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_edit_target_di');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                foreach($system_preference_items as $key=>$item)
                {
                    if(($key=='crop_type_name') || ($key=='variety_name'))
                    {
                        ?>
                        { name: '<?php echo $key ?>', type: 'string' },
                        <?php
                    }
                    else
                    {
                        ?>
                        { name: '<?php echo $key ?>', type: 'number' },
                        <?php
                    }
                }
                foreach($divisions as $division)
                {
                ?>
                    { name: 'quantity_budget_division_<?php echo $division['division_id']?>', type: 'number' },
                    { name: 'quantity_target_division_<?php echo $division['division_id']?>', type: 'number' },
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
            if(column=='quantity_target_division_total')
            {
                var quantity_target_division_total=0;
                <?php
                $serial=0;
                foreach($divisions as $division)
                {
                ++$serial;
                ?>
                quantity_target_division_total+=parseFloat(record['quantity_target_division_<?php echo $division['division_id']?>']);
                <?php
                }
                ?>
                if(quantity_target_division_total>0)
                {
                    if(quantity_target_division_total==parseFloat(record['quantity_target']))
                    {
                        element.css({ 'background-color': 'green','color': '#ffffff','margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                    }
                    else
                    {
                        element.css({ 'background-color': 'red','color': '#ffffff','margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                    }
                    element.html(get_string_kg(quantity_target_division_total));
                }
                else
                {
                    element.html('');
                }
            }
            else if(column.substr(0,25)=='quantity_target_division_')
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
                else if(value>0)
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
                    { columngroup:'hom_budget_target',text: 'Budget', dataField: 'quantity_budget',width:'100',filterable:false, align: 'center',cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_kg},
                    { columngroup:'hom_budget_target',text: 'Target', dataField: 'quantity_target',width:'100',filterable:false, align: 'center',cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_kg},
                    <?php
                    $serial=0;
                    foreach($divisions as $division)
                    {
                        ++$serial;
                        ?>
                        { columngroup:'division_<?php echo $division['division_id']?>',text: 'Budget',dataField: 'quantity_budget_division_<?php echo $division['division_id']?>', width:100,filterable:false, align: 'center',cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_kg},
                        { columngroup:'division_<?php echo $division['division_id']?>',text: 'Target',datafield: 'quantity_target_division_<?php echo $division['division_id']?>', width:100,filterable:false, align: 'center',cellsalign: 'right',cellsrenderer: cellsrenderer,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_kg,columntype: 'textbox',
                            initeditor: function (row, cellvalue, editor, celltext, pressedkey)
                            {
                                editor.wrap( '<div style="margin: 0px;width: 100%;height: 100%;padding: 5px;;line-height: 25px;">');
                                editor.wrap( '<div class="jqxgrid_input">');
                                editor.addClass('float_type_positive');
                                editor.css('width','100%');
                                editor.css('height','100%');
                                editor.css('border-width','0');
                            },
                            cellvaluechanging: function (row, datafield, columntype, oldvalue, newvalue)
                            {
                                if (newvalue != oldvalue)
                                {
                                    var selectedRowData = $('#system_jqx_container').jqxGrid('getrowdata', row);//only last selected
                                    var quantity_target_division_total=parseFloat(selectedRowData['quantity_target_division_total'])-parseFloat(oldvalue)+parseFloat(newvalue);
                                    //console.log(selectedRowData);
                                    $("#system_jqx_container").jqxGrid('setcellvalue', row, 'quantity_target_division_total', quantity_target_division_total);

                                }
                            }
                        },
                        <?php
                    }
                    ?>
                    { text: 'Total DI Target', dataField: 'quantity_target_division_total',width:'100',filterable:false,cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_kg}
                ],
                columngroups:
                [
                    <?php
                    $serial=0;
                    foreach($divisions as $division)
                    {
                    ++$serial;
                        ?>
                        { text: '<?php echo $serial.'. '.$division['division_name']?>', align: 'center', name: 'division_<?php echo $division['division_id']?>' },
                        <?php
                    }
                    ?>
                    { text: 'Total HOM', align: 'center', name: 'hom_budget_target' }
                ]
            });
    });
</script>
