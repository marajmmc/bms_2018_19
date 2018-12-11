<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
if ((isset($CI->permissions['action1']) && ($CI->permissions['action1'] == 1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2'] == 1)))
{
    $action_buttons[] = array
    (
        'type' => 'button',
        'label' => $CI->lang->line("ACTION_SAVE"),
        'id' => 'button_action_save',
        'data-form' => '#save_form'
    );
}

$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save_indirect_cost'); ?>" method="post">
    <input type="hidden" id="fiscal_year_id" name="item[fiscal_year_id]" value="<?php echo $fiscal_year_data['fiscal_year_id'] ?>"/>

    <div class="row widget">

        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FISCAL_YEAR'); ?> :</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $fiscal_year_data['fiscal_year_name'] ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?> :</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['crop_name'] ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE_NAME'); ?> :</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['crop_type_name'] ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?> :</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['variety_name'] ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Total Direct Cost Percentage :</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['total_direct_cost_percentage'] ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Total Packing Cost Percentage :</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $item['total_packing_cost_percentage'] ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Currency Rates :</label>
            </div>
            <div class="col-xs-3">
                <?php
                if ($currencies)
                {
                    ?>
                    <table class="table table-bordered">
                        <tr>
                            <th>Currency Name</th>
                            <th>Rate (BDT)</th>
                        </tr>
                        <?php foreach ($currencies as $currency)
                        {
                            ?>
                            <tr>
                                <td><?php echo $currency['name']; ?></td>
                                <td><?php echo $currency['currency_rate']; ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                <?php
                }
                ?>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">HOM Budget :</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo (float)0.0 ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Total COGS :</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo (float)0.0 ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Average COGS :</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo (float)0.0 ?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right" style="text-decoration:underline">Month-wise Budget Allocation :</label>
            </div>
            <div class="col-xs-4">
                &nbsp;
            </div>
        </div>

        <!---------------------- Each Block of a Single Principle ---------------------->
        <?php
        $months = array(1 => "Jan", 2 => "Feb", 3 => "Mar", 4 => "Apr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Aug", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dec");
        $total_month = 12;

        /*echo '<pre>';
        print_r($item['principals']);
        echo '</pre>';*/
        //$item['principals'] =array(array('id'=>1, 'name'=>'p1'), array('id'=>2, 'name'=>'p2')); // This Line is Just for testing Multiple principle

        foreach ($item['principals'] as $principal)
        {
            ?>
            <div style="background:#cfcfcf;padding-top:10px;margin-bottom:10px"><!---Each Block of a Single Principle--->
                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Principal Name :</label>
                    </div>
                    <div class="col-xs-6">
                        <div class="row show-grid">
                            <label class="control-label"><?php echo $principal['name']; ?></label>
                        </div>
                    </div>
                </div>

                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Monthly Allocation :</label>
                    </div>
                    <div class="col-xs-8">
                        <div class="row show-grid">
                            <div class="col-xl-12">
                                <table>
                                    <tr>
                                        <?php
                                        for ($i = 1; $i <= 12; $i++)
                                        {
                                            ?>
                                            <td style="width:70px;text-align:right;font-weight:bold;padding:0 5px 10px 0"><?php echo $months[$i]; ?> :</td>
                                            <td style="padding-bottom:10px">
                                                <input type="text" class="form-control float_type_positive" value="">
                                            </td>
                                            <?php
                                            if (($i % 4 == 0) && ($i != $total_month))
                                            {
                                                echo '</tr><tr>';
                                            }
                                        }
                                        ?>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row show-grid">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Sub Total :</label>
                    </div>
                    <div class="col-xs-6">
                        <div class="row show-grid">
                            <label class="control-label"><?php echo 0.0; ?></label>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="clearfix"></div>
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function () {
        system_off_events();


    });
</script>
