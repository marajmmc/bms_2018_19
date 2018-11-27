<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
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
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FISCAL_YEAR');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $fiscal_year['name'];?></label>
        </div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $division['name'];?></label>
        </div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $zone['name'];?></label>
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
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_target_outlet_forward');?>" method="post">
    <input type="hidden" name="item[fiscal_year_id]" value="<?php echo $options['fiscal_year_id']; ?>" />
    <input type="hidden" name="item[zone_id]" value="<?php echo $options['zone_id']; ?>" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                Forward Target
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Forward Target<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select class="form-control" name="item[status_target_outlet_forward]">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <option value="<?php echo $this->config->item('system_status_forwarded')?>">Forward</option>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-sm-4 col-xs-4">
                <div class="action_button">
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form" data-message-confirm="Are you sure to Forward?">Forward</button>
                </div>
            </div>
            <div class="col-sm-4 col-xs-4">

            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_off_events();
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_forward_assign_target_outlet');?>";

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
                foreach($outlets as $outlet)
                {
                        ?>
                    { name: 'quantity_target_outlet_<?php echo $outlet['outlet_id']?>', type: 'number' },
                    <?php
                }
            ?>
            ],
            id: 'id',
            type: 'POST',
            url: url,
            data:JSON.parse('<?php echo json_encode($options);?>')
        };
        var header_render=function (text, align)
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
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            if(column=='quantity_target_zi')
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
            else if(column=='quantity_target_outlet_total')
            {
                var quantity_target_outlet_total=0;
                <?php
                foreach($outlets as $outlet)
                {
                    ?>
                    quantity_target_outlet_total+=parseFloat(record['quantity_target_outlet_<?php echo $outlet['outlet_id']?>']);
                    <?php
                }
                ?>
                if(quantity_target_outlet_total>0)
                {
                    if(quantity_target_outlet_total==parseFloat(record['quantity_target_zi']))
                    {
                        element.css({ 'background-color': 'green','color': '#ffffff','margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                    }
                    else
                    {
                        element.css({ 'background-color': 'red','color': '#ffffff','margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                    }
                    element.html(get_string_kg(quantity_target_outlet_total));
                }
                else
                {
                    element.html('');
                }
            }
            else if(column.substr(0,23)=='quantity_target_outlet_')
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
            else if(column.substr(0,14)=='quantity_sale_')
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
            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
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
                text=get_string_kg(aggregates['total'])
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
                editable:true,
                columns:
                [
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',width:'100', filtertype:'list',pinned:true,editable:false,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>', dataField: 'crop_type_name',width:'100', pinned:true,editable:false,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',width:'150',pinned:true,editable:false,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Total ZI <br />Target', dataField: 'quantity_target_zi',width:'100',filterable:false, align: 'center',cellsalign: 'right',cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                    <?php
                    $serial=0;
                    foreach($outlets as $outlet)
                    {
                    ++$serial;
                    ?>
                    { columngroup: 'outlets', text: '<?php echo $serial.'. '.$outlet['outlet_name']?>', dataField: 'quantity_target_outlet_<?php echo $outlet['outlet_id']?>',width:'100',editable:false,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                    <?php
                    }
                    ?>
                    { text: 'Total Outlet <br />Target', dataField: 'quantity_target_outlet_total',width:'100',filterable:false, align: 'center',cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg}
                ],
                columngroups:
                [
                    { text: 'Outlet Target', align: 'center', name: 'outlets' }
                ]
            });
    });
</script>
