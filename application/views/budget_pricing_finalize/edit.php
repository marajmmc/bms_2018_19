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
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save_jqx'
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
    'href'=>site_url($CI->controller_url.'/index/edit/'.$fiscal_year['id'])
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
    <?php
    if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
    {
        $CI->load->view('preference',array('system_preference_items'=>$system_preference_items));
    }
    ?>
    <form id="save_form_jqx" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
        <input type="hidden" name="item[fiscal_year_id]" value="<?php echo $options['fiscal_year_id']; ?>" />
        <div id="jqx_inputs">
        </div>
    </form>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>

<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function () {
        system_off_events();
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
        $(document).on("click", "#button_action_save_jqx", function(event)
        {
            $('#save_form_jqx #jqx_inputs').html('');
            var data=$('#system_jqx_container').jqxGrid('getrows');
            for(var i=0;i<data.length;i++)
            {
                $('#save_form_jqx  #jqx_inputs').append('<input type="hidden" name="items['+data[i]['variety_id']+'][amount_price_trade]" value="'+data[i]['price_trade']+'">');
                $('#save_form_jqx  #jqx_inputs').append('<input type="hidden" name="items['+data[i]['variety_id']+'][percentage_sales_commission]" value="'+data[i]['percentage_sales_commission']+'">');
                $('#save_form_jqx  #jqx_inputs').append('<input type="hidden" name="items['+data[i]['variety_id']+'][quantity_target]" value="'+data[i]['quantity_target']+'">');
            }
            var sure = confirm('<?php echo $CI->lang->line('MSG_CONFIRM_SAVE'); ?>');
            if(sure)
            {
                $("#save_form_jqx").submit();
            }
        });
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_edit');?>";
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
                { name: 'variety_id', type: 'number' }
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
            if((column=='price_trade')|| (column=='percentage_sales_commission')|| (column=='quantity_target'))
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
                editable:true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name', width: '100', filtertype: 'list', pinned: true,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?>', dataField: 'crop_type_name', width: '100', pinned: true,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name', width: '150', pinned: true,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_PRICE_TRADE_AUTO'); ?>', dataField: 'price_trade_auto', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_PRICE_TRADE'); ?>', dataField: 'price_trade', width: '150', cellsalign:'right',cellsrenderer: cellsrenderer,columntype: 'custom',
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
                                var selectedRowData = $('#system_jqx_container').jqxGrid('getrowdata', row);
                                var price_trade=newvalue;
                                var percentage_sales_commission=parseFloat(selectedRowData['percentage_sales_commission']);
                                var sales_commission=price_trade*percentage_sales_commission/100;
                                var price_net=price_trade-sales_commission;
                                var percentage_incentive=<?php echo (isset($budget_config['percentage_incentive'])?$budget_config['percentage_incentive']:0);?>;
                                var incentive=price_net*percentage_incentive/100;
                                var cogs=parseFloat(selectedRowData['cogs']);
                                var general=parseFloat(selectedRowData['general']);
                                var marketing=parseFloat(selectedRowData['marketing']);
                                var finance=parseFloat(selectedRowData['finance']);
                                var profit=price_net-cogs-general-marketing-finance-incentive;

                                var quantity_target=parseFloat(selectedRowData['quantity_target']);
                                var price_net_total=price_net*quantity_target;
                                var profit_total=profit*quantity_target;

                                //console.log(selectedRowData);
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'sales_commission', sales_commission);
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'price_net', price_net);
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'incentive', incentive);
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'profit', profit);
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'price_net_total', price_net_total);
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'profit_total', profit_total);
                                var percentage_profit_np=0;
                                if(price_net>0)
                                {
                                    percentage_profit_np=profit*100/price_net;
                                }
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'percentage_profit_np', percentage_profit_np);
                                var percentage_profit_cogs=0;
                                if(cogs>0)
                                {
                                    percentage_profit_cogs=profit*100/cogs;
                                }
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'percentage_profit_cogs', percentage_profit_cogs);

                            }
                        }
                    },
                    { text: '<?php echo $CI->lang->line('LABEL_PERCENTAGE_SALES_COMMISSION'); ?>', dataField: 'percentage_sales_commission', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render,columntype: 'custom',
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
                                var selectedRowData = $('#system_jqx_container').jqxGrid('getrowdata', row);
                                var price_trade=parseFloat(selectedRowData['price_trade']);
                                var percentage_sales_commission=newvalue;
                                var sales_commission=price_trade*percentage_sales_commission/100;
                                var price_net=price_trade-sales_commission;
                                var percentage_incentive=<?php echo (isset($budget_config['percentage_incentive'])?$budget_config['percentage_incentive']:0);?>;
                                var incentive=price_net*percentage_incentive/100;
                                var cogs=parseFloat(selectedRowData['cogs']);
                                var general=parseFloat(selectedRowData['general']);
                                var marketing=parseFloat(selectedRowData['marketing']);
                                var finance=parseFloat(selectedRowData['finance']);
                                var profit=price_net-cogs-general-marketing-finance-incentive;
                                var quantity_target=parseFloat(selectedRowData['quantity_target']);
                                var price_net_total=price_net*quantity_target;
                                var profit_total=profit*quantity_target;

                                //console.log(selectedRowData);
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'sales_commission', sales_commission);
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'price_net', price_net);
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'incentive', incentive);
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'profit', profit);
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'price_net_total', price_net_total);
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'profit_total', profit_total);
                                var percentage_profit_np=0;
                                if(price_net>0)
                                {
                                    percentage_profit_np=profit*100/price_net;
                                }
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'percentage_profit_np', percentage_profit_np);
                                var percentage_profit_cogs=0;
                                if(cogs>0)
                                {
                                    percentage_profit_cogs=profit*100/cogs;
                                }
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'percentage_profit_cogs', percentage_profit_cogs);

                            }
                        }
                    },
                    { text: '<?php echo $CI->lang->line('LABEL_SALES_COMMISSION'); ?>', dataField: 'sales_commission', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_PRICE_NET'); ?>', dataField: 'price_net', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_INCENTIVE').' ('.(isset($budget_config['percentage_incentive'])?$budget_config['percentage_incentive']:0).'%)'; ?>', dataField: 'incentive', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_COGS'); ?>', dataField: 'cogs', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_GENERAL').' ('.(isset($budget_config['percentage_general'])?$budget_config['percentage_general']:0).'%)'; ?>', dataField: 'general', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_MARKETING').' ('.(isset($budget_config['percentage_marketing'])?$budget_config['percentage_marketing']:0).'%)'; ?>', dataField: 'marketing', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_FINANCE').' ('.(isset($budget_config['percentage_finance'])?$budget_config['percentage_finance']:0).'%)'; ?>', dataField: 'finance', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_PROFIT'); ?>', dataField: 'profit', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_PERCENTAGE_PROFIT_NP'); ?>', dataField: 'percentage_profit_np', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_PERCENTAGE_PROFIT_COGS'); ?>', dataField: 'percentage_profit_cogs', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_STOCK_CURRENT_HQ'); ?>', dataField: 'stock_current_hq', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_PRINCIPAL_QUANTITY_CONFIRM'); ?>', dataField: 'quantity_principal_quantity_confirm', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_AVAILABLE'); ?>', dataField: 'quantity_available', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render,editable:false},
                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TARGET'); ?>', dataField: 'quantity_target', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render,columntype: 'custom',
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
                                var selectedRowData = $('#system_jqx_container').jqxGrid('getrowdata', row);
                                var price_net=parseFloat(selectedRowData['price_net']);
                                var profit=parseFloat(selectedRowData['profit']);
                                var price_net_total=price_net*newvalue;
                                var profit_total=profit*newvalue;
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'price_net_total', price_net_total);
                                $("#system_jqx_container").jqxGrid('setcellvalue', row, 'profit_total', profit_total);
                            }
                        }
                    },
                    { text: '<?php echo $CI->lang->line('LABEL_PRICE_NET_TOTAL'); ?>', dataField: 'price_net_total', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render,editable:false,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount},
                    { text: '<?php echo $CI->lang->line('LABEL_PROFIT_TOTAL'); ?>', dataField: 'profit_total', width: '100', cellsalign:'right',cellsrenderer: cellsrenderer,renderer:header_render,editable:false,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount}
                ]
            });
    });
</script>
