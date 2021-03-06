<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))||(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_EDIT').' Budget',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/list_budget_division')

    );
}
if((isset($CI->permissions['action7']) && ($CI->permissions['action7']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Forward Budget',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/forward_budget_division')
    );
}
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))||(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>' Assign ZSC Target',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/list_target_zone')

    );
}
if((isset($CI->permissions['action7']) && ($CI->permissions['action7']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Forward ZSC Target',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/forward_target_zone')
    );
}
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))||(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>' Next 3Y ZSC Target',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/list_target_zone_next_year')

    );
}
if((isset($CI->permissions['action7']) && ($CI->permissions['action7']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>'Forward Next 3Y ZSC Target',
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/forward_target_zone_next_year')
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
                    if(($key=='id'))
                    {
                        ?>
                        { name: '<?php echo $key ?>', type: 'number' },
                        <?php
                    }
                    else
                    {
                        ?>
                        { name: '<?php echo $key ?>', type: 'string' },
                        <?php
                    }
                 }
                ?>
            ],
            type: 'POST',
            url: url
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
            pageable: true,
            pagesize:50,
            pagesizeoptions: ['50', '100', '200','300','500','1000','5000'],
            selectionmode: 'singlerow',
            altrows: true,
            rowsheight: 35,
            columnsheight: 40,
            columnsreorder: true,
            enablebrowserselection: true,
            columns:
            [
                { text: '<?php echo $CI->lang->line('LABEL_FISCAL_YEAR'); ?>', dataField: 'fiscal_year',width:'80',filtertype: 'list',renderer: header_render},
                { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>', dataField: 'division_name',width:'200',filtertype: 'list',renderer: header_render},
                { text: '<?php echo $CI->lang->line('LABEL_STATUS_BUDGET_FORWARD'); ?>', dataField: 'status_budget_forward', width:'80',filtertype: 'list',renderer: header_render},
                { text: '(DI) Target From HOM', dataField: 'status_target_di_forward', width:'80',filtertype: 'list',renderer: header_render},
                { text: '(ZSC) <?php echo $CI->lang->line('LABEL_STATUS_TARGET_FORWARD'); ?>', dataField: 'status_target_zi_forward', width:'80',filtertype: 'list',renderer: header_render},
                { text: '(DI NXT 3Y) <?php echo $CI->lang->line('LABEL_STATUS_TARGET_FORWARD'); ?>', dataField: 'status_target_di_next_year_forward', width:'80',filtertype: 'list',renderer: header_render},
                { text: '(ZI NXT 3Y) <?php echo $CI->lang->line('LABEL_STATUS_TARGET_FORWARD'); ?>', dataField: 'status_target_zi_next_year_forward', width:'80',filtertype: 'list',renderer: header_render}
            ]
        });
    });
</script>
