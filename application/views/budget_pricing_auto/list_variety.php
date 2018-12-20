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
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PRICE_NET'); ?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label">NP= COGS + General + Marketing + Finance + Incentive + Profit</label><br>
            <label class="control-label">OR</label><br>
            <label class="control-label">Incentive=(Np*Incentive%/100)</label><br>
            <label class="control-label">NP= COGS + General + Marketing + Finance + (Np*Incentive%/100) + Profit</label><br>
            <label class="control-label">NP=100*(COGS+General+Marketing+Finance+Profit)/(100-Incentive%)</label>
        </div>
    </div>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PRICE_TRADE'); ?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label">TP=NP+Commission</label>
            <label class="control-label">Commission=TP*Commission%/100</label>
            <label class="control-label">TP=100*NP/(100-Commission%)</label>

        </div>
    </div>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PERCENTAGE_PROFIT'); ?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label">= (Profit/Net Price)*100</label>
        </div>
    </div>
    <?php
    if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
    {
        $CI->load->view('preference',array('system_preference_items'=>$system_preference_items));
    }
    ?>
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
                    if(($key=='crop_name') ||($key=='crop_type_name')||($key=='variety_name'))
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
                ?>
            ],
            type: 'POST',
            url: url,
            data: JSON.parse('<?php echo json_encode($options);?>')
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
        }
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            if(column.substr(0,9)=='quantity_')
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
            else
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
            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            return element[0].outerHTML;
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
        var aggregatesrenderer_amount=function (aggregates)
        {
            var text='';
            if(!((aggregates['total']=='0.00')||(aggregates['sum']=='')))
            {
                text=get_string_amount(aggregates['sum']);
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
                columnsheight: 60,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name', width: '100', filtertype: 'list', pinned: true},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>', dataField: 'crop_type_name', width: '100', pinned: true},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name', width: '150', pinned: true},
                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL'); ?>', dataField: 'quantity_total', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_kg},
                    { text: '<?php echo $CI->lang->line('LABEL_COGS'); ?>', dataField: 'cogs', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_GENERAL').' ('.(isset($budget_config['percentage_general'])?$budget_config['percentage_general']:0).'%)'; ?>', dataField: 'general', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render},
                    { text: '<?php echo $CI->lang->line('LABEL_MARKETING').' ('.(isset($budget_config['percentage_marketing'])?$budget_config['percentage_marketing']:0).'%)'; ?>', dataField: 'marketing', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render},
                    { text: '<?php echo $CI->lang->line('LABEL_FINANCE').' ('.(isset($budget_config['percentage_finance'])?$budget_config['percentage_finance']:0).'%)'; ?>', dataField: 'finance', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render},
                    { text: '<?php echo $CI->lang->line('LABEL_PROFIT').' ('.(isset($budget_config['percentage_profit'])?$budget_config['percentage_profit']:0).'%)'; ?>', dataField: 'profit', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render},
                    { text: '<?php echo $CI->lang->line('LABEL_INCENTIVE').' ('.(isset($budget_config['percentage_incentive'])?$budget_config['percentage_incentive']:0).'%)'; ?>', dataField: 'incentive', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render},
                    { text: '<?php echo $CI->lang->line('LABEL_PRICE_NET'); ?>', dataField: 'price_net', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_SALES_COMMISSION').' ('.(isset($budget_config['percentage_sales_commission'])?$budget_config['percentage_sales_commission']:0).'%)'; ?>', dataField: 'sales_commission', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render},
                    { text: '<?php echo $CI->lang->line('LABEL_PRICE_TRADE'); ?>', dataField: 'price_trade', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_PERCENTAGE_PROFIT'); ?>', dataField: 'percentage_profit', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render}
                ]
            });
    });
</script>
