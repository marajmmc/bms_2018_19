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
print_r($fiscal_years_next_budgets);
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
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse3" href="#">+ Acres Information ( <?php echo sizeof($acres)?> number of record)</a></label>
            </h4>
        </div>
        <div id="collapse3" class="panel-collapse <?php if($acres){ echo 'collapse';}?>">
            <?php
            if(!$acres)
            {
                ?>
                <div class="alert alert-danger text-center"><strong>Acres not setup</strong></div>
                <?php
            }
            else
            {
            ?>
                <table class="table table-bordered table-responsive system_table_details_view">
                    <thead>
                    <tr>
                        <th><label class="control-label"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label></th>
                        <th><label class="control-label"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME');?></label></th>
                        <th class="text-right"><label class="control-label">Acres</label></th>
                        <th class="text-right"><label class="control-label">Seeds per Acre(kg)</label></th>
                        <th class="text-right"><label class="control-label">Total Seeds(kg)</label></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $quantity_acres_total=0;
                    $quantity_acres_kg_total=0;
                    $quantity_acres_seed_total=0;
                    foreach($acres as $result)
                    {
                        $quantity_acres_total+=$result['quantity'];
                        $quantity_acres_kg_total+=$result['quantity_kg_acre'];
                        $quantity_acres_seed_total+=($result['quantity']*$result['quantity_kg_acre']);
                        ?>
                        <tr>
                            <td><?php echo $result['crop_name']; ?></td>
                            <td><?php echo $result['crop_type_name']; ?></td>
                            <td class="text-right"><?php echo System_helper::get_string_kg($result['quantity']); ?></td>
                            <td class="text-right"><?php echo System_helper::get_string_kg($result['quantity_kg_acre']); ?></td>
                            <td class="text-right"><?php echo System_helper::get_string_kg(($result['quantity']*$result['quantity_kg_acre'])); ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="2" class="text-right"><?php echo $CI->lang->line('LABEL_TOTAL');?></th>
                        <th class="text-right"><?php echo System_helper::get_string_kg($quantity_acres_total);?></th>
                        <th class="text-right"><?php echo System_helper::get_string_kg($quantity_acres_kg_total);?></th>
                        <th class="text-right"><?php echo System_helper::get_string_kg($quantity_acres_seed_total);?></th>
                    </tr>
                    </tfoot>
                </table>
            <?php
            }
            ?>
        </div>
    </div>
    <form id="save_form_jqx" action="<?php echo site_url($CI->controller_url.'/index/save_target_hom_next_year');?>" method="post">
        <input type="hidden" name="item[fiscal_year_id]" value="<?php echo $options['fiscal_year_id']; ?>" />
        <div id="jqx_inputs">
        </div>
    </form>
    <div style="font-size: 12px;margin-top: -10px;font-style: italic; color: red;" class="row show-grid">
        <div class="col-xs-4"></div>
        <div class="col-sm-4 col-xs-8 text-center">
            <strong>Note:</strong> <?php echo $CI->lang->line('LABEL_ALL_ITEM_SHOWING_KG');?>
        </div>
    </div>
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
                /*$('#save_form_jqx  #jqx_inputs').append('<input type="hidden" name="items['+data[i]['variety_id']+'][quantity_principal_quantity_confirm]" value="'+data[i]['quantity_principal_quantity_confirm']+'">');*/
                <?php 
                $serial=0;
                foreach($fiscal_years_next_budgets as $budget)
                {
                    ++$serial;
                        ?>
                    $('#save_form_jqx  #jqx_inputs').append('<input type="hidden" name="items_quantity_target[<?php echo $serial;?>]['+data[i]['variety_id']+']" value="'+data[i]['quantity_target_hom_<?php echo $serial; ?>']+'">');
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

        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_edit_target_hom_next_year');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                foreach($system_preference_items as $key=>$item)
                {
                    ?>
                    { name: '<?php echo $key ?>', type: 'string' },
                    <?php
                }
                $serial=0;
                foreach($fiscal_years_next_budgets as $budget)
                {
                    ++$serial;
                        ?>
                    { name: 'quantity_prediction_<?php echo $serial; ?>', type: 'string' },
                    <?php
                }
                $serial=0;
                foreach($fiscal_years_next_budgets as $budget)
                {
                    ++$serial;
                        ?>
                    { name: 'quantity_target_hom_<?php echo $serial; ?>', type: 'string' },
                    <?php
                }
                ?>

            ],
            id: 'id',
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
            if(column.substr(0,20)=='quantity_target_hom_')
            {
                element.html('<div class="jqxgrid_input">'+value+'</div>');
            }
            else if(column.substr(0,20)=='quantity_prediction_')
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
                rowsheight: 35,
                editable:true,
                columns:
                [
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',width:'100', filtertype:'list',pinned:true,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>', dataField: 'crop_type_name',width:'100',pinned:true,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',width:'150',pinned:true,editable:false},
                    <?php
                    $serial=0;
                    foreach ($fiscal_years_next_budgets as $budget)
                    {
                        ++$serial;
                        ?>{columngroup: 'budget_next_years',text: '<?php echo $budget['name']; ?>', dataField: 'quantity_prediction_<?php echo $serial; ?>',width:'100',filterable: false,cellsrenderer: cellsrenderer,align:'center',cellsAlign:'right',editable:false},
                    <?php
                    }
                    ?>
                    <?php
                    $serial=0;
                    foreach ($fiscal_years_next_budgets as $budget)
                    {
                        ++$serial;
                        ?>
                        { columngroup: 'target_next_years',text: '<?php echo $budget['name']; ?>',datafield: 'quantity_target_hom_<?php echo $serial; ?>', width: 100,filterable: false,cellsalign: 'right',cellsrenderer: cellsrenderer,columntype: 'custom',
                            cellbeginedit: function (row)
                            {
                                var selectedRowData = $('#system_jqx_container').jqxGrid('getrowdata', row);//only last selected
                                return true;
                            },
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
                            }
                        },
                    <?php
                    }
                     ?>
                ],  
                columngroups:
                [
                    { text: 'Next Years Prediction ', align: 'center', name: 'budget_next_years' },
                    { text: 'Next Years Target', align: 'center', name: 'target_next_years' }
                ]
            });
    });
</script>
