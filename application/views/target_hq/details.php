<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK") . ' to Pending List',
    'href' => site_url($CI->controller_url . '/index/list')
);
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK") . ' to All List',
    'href' => site_url($CI->controller_url . '/index/list_all')
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
    $data = array();
    $data['accordion']['data'] = $item;
    $data['accordion']['collapse'] = 'in';

    $CI->load->view('info_basic', $data);
    ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#target_distribution" href="#">+ <?php echo $details_title; ?></a></label>
            </h4>
        </div>
        <div id="target_distribution" class="panel-collapse collapse in">
            <table class="table table-bordered">
                <?php
                if($details)
                {
                    ?>
                    <tr>
                        <th>Location</th>
                        <th><?php echo $CI->lang->line('LABEL_AMOUNT_TARGET'); ?></th>
                    </tr>
                    <?php
                    $sum=0;
                    foreach($details as $detail)
                    {
                        ?>
                        <tr>
                            <td><?php echo $detail['name']; ?></td>
                            <td><?php echo System_helper::get_string_amount($detail['amount_target']); ?></td>
                        </tr>
                    <?php
                        $sum += $detail['amount_target'];
                    }
                    ?>
                    <tr>
                        <th style="text-align:right">Total:</th>
                        <th style="text-align:right"><?php echo System_helper::get_string_amount($sum); ?></th>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size:0.85em; text-align:center">
                            ( <b>Total In-words:</b> <?php echo Target_helper::get_string_amount_inword($sum); ?> )
                        </td>
                    </tr>
                <?php
                }
                else
                {
                    ?>
                    <tr>
                        <td colspan="2" style="text-align:center">- <i>No Target has been Allocated yet</i> -</td>
                    </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </div>

</div>

<style>
    #target_distribution table{width:50%; margin:0 auto}
    th{text-align:center}
    #target_distribution td:last-child{text-align:right}
</style>
