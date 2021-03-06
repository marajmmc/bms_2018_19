<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
if (isset($CI->permissions['action0']) && ($CI->permissions['action0'] == 1))
{
    $action_buttons[] = array(
        'label' => 'All List',
        'href' => site_url($CI->controller_url . '/index/list_all')
    );
    $action_buttons[] = array(
        'label' => 'Delete List',
        'href' => site_url($CI->controller_url . '/index/list_deleted')
    );
}
if (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("LABEL_ASSIGN_TARGET"),
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/edit')
    );
}
if (isset($CI->permissions['action0']) && ($CI->permissions['action0'] == 1))
{
    $action_buttons[] = array(
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_DETAILS"),
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/details')
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
if (isset($CI->permissions['action6']) && ($CI->permissions['action6'] == 1))
{
    $action_buttons[] = array
    (
        'label' => 'Preference',
        'href' => site_url($CI->controller_url . '/index/set_preference_list')
    );
}
if (isset($CI->permissions['action7']) && ($CI->permissions['action7'] == 1))
{
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => $CI->lang->line('ACTION_FORWARD'),
        'class' => 'button_jqx_action',
        'data-action-link' => site_url($CI->controller_url . '/index/forward')
    );
}
$action_buttons[] = array(
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
    <?php
    if (isset($CI->permissions['action6']) && ($CI->permissions['action6'] == 1))
    {
        $CI->load->view('preference', array('system_preference_items' => $system_preference_items));
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function () {
        system_off_events(); // Triggers

        var url = "<?php echo site_url($CI->controller_url.'/index/get_items'); ?>";
        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                foreach($system_preference_items as $key => $value)
                {
                    if($key=='id')
                    {
                    ?>
                        { name: '<?php echo $key; ?>', type: 'integer' },
                    <?php
                    }
                    else
                    {
                    ?>
                        { name: '<?php echo $key; ?>', type: 'string' },
                    <?php
                    }
                }
                ?>
            ],
            id: 'id',
            type: 'POST',
            url: url
        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                source: dataAdapter,
                pageable: true,
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                pagesize: 50,
                pagesizeoptions: ['50', '100', '200', '300', '500', '1000', '5000'],
                selectionmode: 'singlerow',
                altrows: true,
                height: '350px',
                enablebrowserselection: true,
                columnsreorder: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', pinned: true, dataField: 'id', width: '50', cellsalign: 'right', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_YEAR'); ?>', pinned: true, dataField: 'year', width: '60', cellsalign: 'right', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['year']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_MONTH'); ?>', pinned: true, dataField: 'month', width: '100', cellsalign: 'right', filtertype: 'list', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['month']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_TARGET'); ?>', pinned: true, dataField: 'amount_target', width: '140', cellsalign: 'right', filtertype: 'none', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['amount_target']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_ALLOCATED'); ?>', pinned: true, dataField: 'amount_allocated', width: '140', cellsalign: 'right', filtertype: 'none', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['amount_allocated']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_REMAINING'); ?>', pinned: true, dataField: 'amount_remaining', width: '140', cellsalign: 'right', filtertype: 'none', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['amount_remaining']?0:1;?>},
                    <?php if(!($CI->locations['division_id'] > 0)){ ?>
                        { text: '<?php echo $CI->lang->line('LABEL_LOCATION'); ?>', dataField: 'location', width: '160', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['location']?0:1;?>},
                    <?php } ?>
                    { text: '<?php echo $CI->lang->line('LABEL_NO_OF_EDIT'); ?>', dataField: 'no_of_edit', width: '80', cellsalign: 'right', filtertype: 'none', rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['no_of_edit']?0:1;?>}
                ]
            });
    });
</script>