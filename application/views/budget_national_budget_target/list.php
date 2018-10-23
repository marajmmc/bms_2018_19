<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))||(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_EDIT').' National Target',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_target_hom')

    );
}
if((isset($CI->permissions['action7']) && ($CI->permissions['action7']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Forward National Target',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/forward_target_hom')
    );
}
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))||(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_EDIT').' Next 3Y HOM Target',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_target_hom_prediction')

    );
}
if((isset($CI->permissions['action7']) && ($CI->permissions['action7']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Forward Next 3Y HOM Target',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/forward_target_hom_prediction')
    );
}
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_DETAILS'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/details')
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
                    ?>
                { name: '<?php echo $key ?>', type: 'string' },
                <?php
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
                { text: 'Principal Qty Confirm Time(s)', dataField: 'revision_count_principal_quantity_confirm', width:'200',filtertype: 'list'},
                { text: 'HOM Targeted Qty Time(s)', dataField: 'revision_count_quantity_target_hom', width:'200',filtertype: 'list'},
                { text: 'HOM Target Fwd Status', dataField: 'status_forward_hom_target', width:'200',filtertype: 'list'},
                { text: 'Nxt 3Y HOM Target Fwd Status', dataField: 'status_forward_hom_next_year_target', width:'200',filtertype: 'list'}
            ]
        });
    });
</script>
