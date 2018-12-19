<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

$action_buttons = array();
if ((isset($CI->permissions['action1']) && ($CI->permissions['action1'] == 1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1)))
{
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => 'Edit quantity',
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/list_variety')
    );
}
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_REFRESH"),
    'href' => site_url($CI->controller_url . '/index/list')
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
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function () {
        system_off_events();
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items'); ?>";
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
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_FISCAL_YEAR'); ?>', dataField: 'fiscal_year', width: '150', filtertype: 'list'},
                    { columngroup: 'number_of_variety',text: 'Active', dataField: 'number_of_variety_active',width:'70', cellsalign:'right', align:'right'},
                    { columngroup: 'number_of_variety',text: 'Completed', dataField: 'number_of_variety_done',width:'70', cellsalign:'right', align:'right'},
                    { columngroup: 'number_of_variety',text: 'Remaining', dataField: 'number_of_variety_due',width:'70', cellsalign:'right', align:'right'}
                ],
                columngroups:
                    [
                        { text: 'Number of Variety', align: 'center', name: 'number_of_variety' }
                    ]
            });
    });
</script>
