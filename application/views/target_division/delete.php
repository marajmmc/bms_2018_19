<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url . '/index/list_all')
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_delete'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $id; ?>"/>

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
                    <tr>
                        <th>Location</th>
                        <th><?php echo $CI->lang->line('LABEL_AMOUNT_TARGET'); ?></th>
                    </tr><?php
                    $sum=0;
                    foreach($details as $detail){ ?>
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
                </table>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_STATUS_DELETE'); ?> <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <select name="item[status]" class="form-control status-combo">
                    <option value=""><?php echo $CI->lang->line('SELECT'); ?></option>
                    <option value="<?php echo $CI->config->item('system_status_delete'); ?>"><?php echo $CI->lang->line('LABEL_DELETED'); ?></option>
                </select>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REASON_REMARKS'); ?> <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <textarea id="remarks" name="item[remarks_delete]" class="form-control"></textarea>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4"> &nbsp; </div>
            <div class="col-xs-4">
                <div class="action_button pull-right">
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>

<style>
    #target_distribution table{width:50%; margin:0 auto}
    th{text-align:center}
    #target_distribution td:last-child{text-align:right}
</style>

<script type="text/javascript">
    $(document).ready(function () {
        system_off_events(); // Triggers

        $(".status-combo").on('change', function (event) {
            var options = $(this).val();
            if (options == '<?php echo $CI->config->item('system_status_delete'); ?>') {
                $("#button_action_save").attr('data-message-confirm', '<?php echo $CI->lang->line('MSG_CONFIRM_DELETE'); ?>');
            } else {
                $("#button_action_save").removeAttr('data-message-confirm');
            }
        });
    });
</script>
