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
                    if($key=='id' ||(substr($key, 0, 10)=='number_of_'))
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
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            if(column.substr(0,18)=='number_of_variety_')
            {
                if(value==0)
                {
                    element.html('');
                }
                else if(value>0)
                {
                    element.html(get_string_quantity(value));
                }
            }
            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            return element[0].outerHTML;
        };
        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
        {
            source: dataAdapter,
            width: '100%',
            height: '250px',
            filterable: true,
            sortable: true,
            showfilterrow: true,
            columnsresize: true,
            selectionmode: 'singlerow',
            altrows: true,
            rowsheight: 35,
            columnsheight: 55,
            columnsreorder: true,
            enablebrowserselection: true,
            columns:
            [
                { text: '<?php echo $CI->lang->line('LABEL_FISCAL_YEAR'); ?>', dataField: 'fiscal_year',width:'80',filtertype: 'list',renderer: header_render},
                { text: 'Active Number of Variety', dataField: 'number_of_variety_active',width:'80', cellsalign:'right', align:'right',renderer: header_render,cellsrenderer: cellsrenderer},
                { columngroup: 'principal_number_of_variety',text: 'Confirm Qty', dataField: 'number_of_variety_principal_confirm',width:'80', cellsalign:'right', align:'right',renderer: header_render,cellsrenderer: cellsrenderer},
                { columngroup: 'principal_number_of_variety',text: 'Due Qty', dataField: 'number_of_variety_principal_confirm_due',width:'80', cellsalign:'right', align:'right',renderer: header_render,cellsrenderer: cellsrenderer},
                { columngroup: 'budgeted_number_of_variety',text: 'Targeted', dataField: 'number_of_variety_targeted',width:'80', cellsalign:'right', align:'right',renderer: header_render,cellsrenderer: cellsrenderer},
                { columngroup: 'budgeted_number_of_variety',text: 'Due Target', dataField: 'number_of_variety_target_due',width:'80', cellsalign:'right', align:'right',renderer: header_render,cellsrenderer: cellsrenderer},
                { text: 'HOM Target Fwd Status', dataField: 'status_target_forward', width:'100',filtertype: 'list',renderer: header_render}
            ],
            columngroups:
            [
                { text: 'Number of Variety (Principal)', align: 'center', name: 'principal_number_of_variety',renderer: header_render },
                { text: 'Number of Variety (Budgeted)', align: 'center', name: 'budgeted_number_of_variety',renderer: header_render }
            ]
        });
    });
</script>
