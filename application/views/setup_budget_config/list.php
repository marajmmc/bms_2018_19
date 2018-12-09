<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))||(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Edit Pricing',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/add_edit_pricing')

    );
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Currency Rate Setup',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/add_edit_currency_rate')

    );
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Direct Cost Percentage Setup',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/add_edit_direct_cost')

    );
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Packing Cost Percentage Setup',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/add_edit_packing_cost')

    );
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Indirect Cost Setup',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/add_edit_indirect_cost')
    );
}

$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list')
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
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_off_events();
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                 foreach($system_preference_items as $key=>$item)
                 {
                    if(($key=='fiscal_year'))
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
            id: 'id',
            type: 'POST',
            url: url
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                source: dataAdapter,
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                selectionmode: 'singlerow',
                altrows: true,
                height: '250px',
                columnsreorder: true,
                enablebrowserselection: true,
                columns:
                [
                    { text: '<?php echo $CI->lang->line('LABEL_FISCAL_YEAR'); ?>', dataField: 'fiscal_year',width:'80',filtertype: 'list'},
                    { text: 'Pricing Time(s)', dataField: 'revision_pricing_count', width:'150',cellsAlign:'right'},
                    { text: 'Currency Rate Time(s)', dataField: 'revision_currency_rate_count', width:'150',cellsAlign:'right'},
                    { text: 'Direct Cost Percentage Time(s)', dataField: 'revision_direct_cost_percentage_count', width:'150',cellsAlign:'right'},
                    { text: 'Packing Cost Percentage Time(s)', dataField: 'revision_packing_cost_percentage_count', width:'150',cellsAlign:'right'},
                    { text: 'Indirect Cost Time(s)', dataField: 'revision_indirect_cost_percentage_count', width:'150',cellsAlign:'right'}
                ]
            });
    });
</script>
