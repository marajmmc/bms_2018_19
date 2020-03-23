<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url . '/index/list_deleted')
);
if(!(isset($no_back_button) && $no_back_button)){
    $CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
}

if($details){
?>
<div class="row widget">

    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
        $slno = 1;
        foreach($details as $key => $detail)
        {
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#delete_history_<?php echo $key; ?>" href="#">+ Delete History <?php echo ($slno++) ?></a></label>
                    &nbsp;&nbsp;&nbsp;
                    <i style="font-size:0.85em"><?php echo $detail['title_date']; ?></i>
                </h4>
            </div>
            <div id="delete_history_<?php echo $key; ?>" class="panel-collapse collapse">
                <div class="row show-grid" style="margin:10px 0">
                    <div class="col-lg-4 col-sm-5 col-xs-5">
                        <table class="table table-bordered" style="margin:0">
                            <tr>
                                <th>Location</th>
                                <th><?php echo $CI->lang->line('LABEL_AMOUNT_TARGET'); ?></th>
                            </tr>
                            <?php
                            $sum = 0;
                            foreach($detail['items'] as $items)
                            {
                                ?>
                                <tr>
                                    <td><?php echo $items['name']; ?></td>
                                    <td style="text-align:right"><?php echo System_helper::get_string_amount($items['amount_target']); ?></td>
                                </tr>
                            <?php
                                $sum += $items['amount_target'];
                            }
                            ?>
                            <tr>
                                <th style="text-align:left">Total Target Amount :</th>
                                <th style="text-align:right"><?php echo System_helper::get_string_amount($sum); ?></th>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-size:0.8em">
                                    <?php echo '<b>In-words: </b>'.Target_helper::get_string_amount_inword($sum); ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-lg-8 col-sm-7 col-xs-7" style="padding-left:0">
                        <table class="table table-bordered table-responsive">
                            <?php foreach($detail['user_history'] as $history){ ?>
                                <tr>
                                    <td class="widget-header header_caption"><label class="control-label pull-right"><?php echo $history['label'];?></label></td>
                                    <td class="warning header_value"><label class="control-label"><?php echo $history['value'];?></label></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<?php
}
?>
