<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);

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
            <label class="control-label"><?php echo $fiscal_year_budget_target['name'];?></label>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#collapse3" href="#">+ Acres Information ( <?php echo sizeof($acres)?> number of record)</a></label>
            </h4>
        </div>
        <div id="collapse3" class="panel-collapse  <?php if($acres){ echo 'collapse';}?>">
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
                            <td class="text-right"><?php echo number_format($result['quantity'],3,'.',''); ?></td>
                            <td class="text-right"><?php echo number_format($result['quantity_kg_acre'],3,'.',''); ?></td>
                            <td class="text-right"><?php echo number_format($result['quantity']*$result['quantity_kg_acre'],3,'.',''); ?></td>
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
    <div style="font-size: 12px;margin-top: -10px;font-style: italic; color: red;" class="row show-grid">
        <div class="col-xs-4"></div>
        <div class="col-sm-4 col-xs-8 text-center">
            <strong>Note:</strong> All item amount showing to kg.
        </div>
    </div>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>

<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_forward_budget');?>" method="post">
    <input type="hidden" name="item[fiscal_year_id]" value="<?php echo $options['fiscal_year_id']; ?>" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                Forward Budget
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Forward Budget<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select class="form-control" name="item[status_budget_forward]">
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

<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_off_events();
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        var url = "<?php echo site_url($CI->controller_url.'/budget_forward_items');?>";

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
            foreach($divisions as $division)
            {
                    ?>
                { name: 'quantity_budget_division_<?php echo $division['division_id']?>', type: 'string' },
                <?php
            }
            foreach($fiscal_years_previous_sales as $fy)
            {
                    ?>
                { name: 'quantity_sale_<?php echo $fy['id']; ?>', type: 'string' },
                <?php
            }
            $serial=0;
            foreach($fiscal_years_next_budgets as $budget)
            {
                ++$serial;
                    ?>
                { name: 'quantity_budget_<?php echo $serial; ?>', type: 'string' },
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
            if(!((column=='crop_name')||(column=='crop_type_name')||(column=='variety_name')))
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
                text=number_format(aggregates['total'],3,'.','');
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
                        { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>', dataField: 'crop_type_name',width:'100',pinned:true,editable:false,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',width:'150',pinned:true,editable:false,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                        <?php
                        for($i=sizeof($fiscal_years_previous_sales)-1;$i>=0;$i--)
                        //foreach($fiscal_years_previous_sales as $fy)
                            {
                            ?>
                        {columngroup: 'previous_years',text: '<?php echo $fiscal_years_previous_sales[$i]['name']; ?>', dataField: 'quantity_sale_<?php echo $fiscal_years_previous_sales[$i]['id']; ?>',width:'100',filterable: false,cellsrenderer: cellsrenderer,align:'center',cellsAlign:'right',editable:false,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                                <?php
                            }
                        ?>
                        { text: 'Total</br>Budget', dataField: 'quantity_budget',width:'100',filterable:false,cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                        <?php
                            $serial=0;
                            foreach ($fiscal_years_next_budgets as $budget)
                            {
                                ++$serial;
                                ?>
                                {columngroup: 'next_years',text: '<?php echo $budget['name']; ?>', dataField: 'quantity_budget_<?php echo $serial; ?>',width:'100',filterable: false,cellsrenderer: cellsrenderer,align:'center',cellsAlign:'right',editable:false,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg
                                },
                                <?php
                            }
                         ?>
                        { text: 'Total</br>Division Budget', dataField: 'quantity_budget_division_total',width:'100',filterable:false,cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                        <?php
                    $serial=0;
                    foreach($divisions as $division)
                    {
                    ++$serial;
                    ?>
                        { text: '<?php echo $serial.'. '.$division['division_name']?>',renderer: header_render, dataField: 'quantity_budget_division_<?php echo $division['division_id']?>',width:'100',filterable:false,cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                        <?php
                        }
                        ?>

                    ],
                columngroups:
                    [
                        { text: '<?php echo $CI->lang->line('LABEL_PREVIOUS_YEARS'); ?> Achieved', align: 'center', name: 'previous_years' },
                        { text: 'Next Year Budget', align: 'center', name: 'next_years' }
                    ]
            });
    });
</script>
