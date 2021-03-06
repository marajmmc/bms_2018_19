<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url . '/index/list')
);
if ((isset($CI->permissions['action1']) && ($CI->permissions['action1'] == 1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1)))
{
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => 'Edit',
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/add_edit/'.$options['fiscal_year_id'])
    );
}
if (isset($CI->permissions['action4']) && ($CI->permissions['action4'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_PRINT"),
        'class' => 'button_action_download',
        'data-title' => "Print",
        'data-print' => true
    );
}
if (isset($CI->permissions['action5']) && ($CI->permissions['action5'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_DOWNLOAD"),
        'class' => 'button_action_download',
        'data-title' => "Download"
    );
}

$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list_variety/'.$fiscal_year['id'])
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
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
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FISCAL_YEAR'); ?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $fiscal_year['name']; ?></label>
        </div>
    </div>
    <form id="save_form_jqx" action="<?php echo site_url($CI->controller_url . '/index/save_pricing_packing'); ?>" method="post">
        <input type="hidden" name="item[fiscal_year_id]" value="<?php echo $options['fiscal_year_id']; ?>"/>

        <div id="jqx_inputs"></div>
    </form>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>

<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function () {
        system_off_events();
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_list_variety');?>";
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
            ?>
            ],
            type: 'POST',
            url: url,
            data: JSON.parse('<?php echo json_encode($options);?>')
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            if (record['quantity_total_task']!=record['quantity_total_hom'])
            {
                if((column=='quantity_total_task')||(column=='quantity_total_hom'))
                {
                    element.css({ 'background-color': 'red','margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                }
            }
            if((column=='quantity_total_task')||(column=='quantity_total_hom'))
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
            else if((column=='amount_unit_price_taka')||(column=='cogs')||(column=='cogs_total'))
            {
                if(value==0)
                {
                    element.html('');
                }
                else
                {
                    element.html(get_string_amount(value));
                }
            }

            return element[0].outerHTML;
        };
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
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', dataField: 'variety_id', width: '50', cellsalign:'right', pinned: true},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name', width: '100', filtertype: 'list', pinned: true},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>', dataField: 'crop_type_name', width: '100'},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name', width: '150'},
                    { text: 'Unit Price(BDT)', dataField: 'amount_unit_price_taka', width: '120', cellsalign:'right',cellsrenderer: cellsrenderer},
                    { text: 'COGS', dataField: 'cogs', width: '120', cellsalign:'right',cellsrenderer: cellsrenderer},
                    { text: 'Total COGS', dataField: 'cogs_total', width: '120', cellsalign:'right',cellsrenderer: cellsrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_TASK'); ?>', dataField: 'quantity_total_task', width: '120', cellsalign:'right',cellsrenderer: cellsrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL_HOM'); ?>', dataField: 'quantity_total_hom', width: '120', cellsalign:'right',cellsrenderer: cellsrenderer}
                ]
            });
    });
</script>
