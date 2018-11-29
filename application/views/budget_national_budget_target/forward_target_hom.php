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
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FISCAL_YEAR');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $fiscal_year['name'];?></label>
        </div>
    </div>
    <?php
    echo $CI->load->view("budget_zi_budget_target/acres_info",$acres,true);
    ?>
    <!--<div style="font-size: 12px;margin-top: -10px;font-style: italic; color: red;" class="row show-grid">
        <div class="col-xs-4"></div>
        <div class="col-sm-4 col-xs-8 text-center">
            <strong>Note:</strong> All item amount showing to kg.
        </div>
    </div>-->
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>

<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_forward_target_hom');?>" method="post">
    <input type="hidden" name="item[fiscal_year_id]" value="<?php echo $options['fiscal_year_id']; ?>" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                Forward HOM Target
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Forward Target<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select class="form-control" name="item[status_target_forward]">
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

        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_forward_target_hom');?>";

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
            foreach($fiscal_years_previous_sales as $fy)
            {
                    ?>
                { name: 'quantity_sale_<?php echo $fy['id']; ?>', type: 'string' },
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
                        {
                        ?>
                            {columngroup: 'previous_years',text: '<?php echo $fiscal_years_previous_sales[$i]['name']; ?>', dataField: 'quantity_sale_<?php echo $fiscal_years_previous_sales[$i]['id']; ?>',width:'100',filterable: false,cellsrenderer: cellsrenderer,align:'center',cellsAlign:'right',editable:false,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                            <?php
                        }
                        ?>
                        { text: 'HQ CS', dataField: 'stock_current_hq',width:'100',filterable:false,cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                        { text: 'HOM Qty', dataField: 'quantity_budget_hom',width:'100',filterable:false,cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                        { text: 'Qty Needed', dataField: 'quantity_budget_needed',width:'100',filterable:false,cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                        { text: 'Principal Qty<br> Confirm <?php echo $fiscal_year['name'];?>', dataField: 'quantity_principal_quantity_confirm',width:'100',filterable:false,cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                        { text: 'Available<br> Target Qty', dataField: 'quantity_target_available',width:'100',filterable:false,cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg},
                        { text: 'HOM Target<br> Qty <?php echo "".$fiscal_year['name'];?>', dataField: 'quantity_target',width:'100',filterable:false,cellsalign: 'right',editable:false,cellsrenderer: cellsrenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer_kg}
                    ],
                columngroups:
                    [
                        { text: '<?php echo $CI->lang->line('LABEL_PREVIOUS_YEARS'); ?> Achieved', align: 'center', name: 'previous_years' }
                    ]
            });
    });
</script>
