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
<form id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save'); ?>" method="post">
<input type="hidden" name="item[fiscal_year_id]" value="<?php echo $fiscal_year_data['fiscal_year_id'] ?>"/>
<input type="hidden" name="item[variety_id]" value="<?php echo $item['variety_id'] ?>"/>

<div class="row widget">

<div class="widget-header">
    <div class="title">
        <?php echo $title; ?>
    </div>
    <div class="clearfix"></div>
</div>

<?php if ($config_warning['status'])
{
    ?>
    <div class="row show-grid bg-warning text-warning" style="padding:10px 0 0">
        <div class="col-xs-4">
            <label class="control-label pull-right" style="font-size:1.2em">Warning :</label>
        </div>
        <div class="col-xs-8">
            <ul style="padding:0;list-style:none">
                <?php
                foreach ($config_warning['messages'] as $message)
                {
                    echo '<li>' . $message . '</li>';
                }
                ?>
            </ul>
        </div>
    </div>
<?php } ?>

<?php if ($changes_warning['status'])
{
    ?>
    <div class="row show-grid bg-danger text-danger" style="padding:10px 0 0">
        <div class="col-xs-4">
            <label class="control-label pull-right" style="font-size:1.2em">Warning :</label>
        </div>
        <div class="col-xs-8">
            <ul style="padding:0;list-style:none">
                <?php
                foreach ($changes_warning['messages'] as $message)
                {
                    echo '<li>' . $message . '</li>';
                }
                ?>
            </ul>
        </div>
    </div>
<?php } ?>

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
        <label class="control-label" id="percentage_direct_cost"><?php echo $item['total_direct_cost_percentage'] ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right">Total Packing Cost Percentage :</label>
    </div>
    <div class="col-xs-4">
        <label class="control-label" id="percentage_packing_cost"><?php echo $item['total_packing_cost_percentage'] ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right">Currency Rates :</label>
    </div>
    <div class="col-xs-3">
        <table class="table table-bordered">
            <tr>
                <th>Currency Name</th>
                <th>Rate (BDT)</th>
            </tr>
            <?php foreach ($currencies as $currency_id => $currency)
            {
                ?>
                <tr>
                    <td><?php echo $currency['name']; ?></td>
                    <td id="currency_rate_<?php echo $currency_id; ?>"><?php echo $currency['currency_rate']; ?></td>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right">Quantity Confirm : <br>(At HOM Target)</label>
    </div>
    <div class="col-xs-4">
        <label class="control-label"><?php echo 0.0 ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right">Quantity Confirm :</label>
    </div>
    <div class="col-xs-4">
        <label class="control-label" id="quantity_grand"><?php echo 0.0 ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right">Total COGS :</label>
    </div>
    <div class="col-xs-4">
        <label class="control-label" id="cogs_total_grand"><?php echo 0.0 ?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right">Average COGS :</label>
    </div>
    <div class="col-xs-4">
        <label class="control-label" id="cogs_grand"><?php echo 0.0 ?></label>
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

<!---------------------- Each Block of a Single principal ---------------------->
<?php
foreach ($item['principals'] as $principal)
{
    ?>
    <div style="background:#cfcfcf;padding-top:20px;margin-bottom:20px"><!---Each Block of a Single principal--->
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
                <label class="control-label pull-right">Monthly Allocation (Kg) :</label>
            </div>
            <div class="col-xs-8">
                <div class="row show-grid">
                    <div class="col-xl-12">
                        <table>
                            <tr>
                                <?php
                                $sub_total = 0.0;
                                for ($i = 1; $i <= 12; $i++)
                                {
                                    ?>
                                    <td style="width:130px;text-align:right;font-weight:bold;padding:0 5px 10px 0"><?php echo $CI->lang->line('LABEL_MONTH_' . $i); ?> :</td>
                                    <td style="padding-bottom:10px">
                                        <input type="text" name="items[<?php echo $principal['id']; ?>][quantities][<?php echo $i; ?>]" class="form-control float_type_positive quantity_month" id="quantity_<?php echo $i . '_' . $principal['id']; ?>" data-principal-id="<?php echo $principal['id']; ?>" value=""/>
                                    </td>
                                    <?php
                                    if (($i % 3 == 0) && ($i != 12))
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
                <label class="control-label pull-right">Principal Total :</label>
            </div>
            <div class="col-xs-6">
                <div class="row show-grid">
                    <label class="control-label quantity_total_principal" id="quantity_total_<?php echo $principal['id']; ?>"><?php echo $sub_total; ?></label>
                </div>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UNIT_PRICE'); ?> :</label>
            </div>
            <div class="col-xs-2">
                <div class="row show-grid">
                    <input type="text" name="items[<?php echo $principal['id']; ?>][unit_price]" class="form-control float_type_positive unit_price" id="amount_unit_price_<?php echo $principal['id']; ?>" data-principal-id="<?php echo $principal['id']; ?>" value=""/>
                </div>
            </div>
            <div class="col-xs-2">
                <div class="row show-grid">
                    <select name="items[<?php echo $principal['id']; ?>][currency_id]" class="form-control currency_dropdown" id="currency_dropdown_<?php echo $principal['id']; ?>" data-principal-id="<?php echo $principal['id']; ?>">
                        <?php
                        foreach ($currencies as $currency_id => $currency)
                        {
                            ?>
                            <option value="<?php echo $currency_id; ?>"><?php echo $currency['name']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Principal COGS :</label>
            </div>
            <div class="col-xs-6">
                <div class="row show-grid">
                    <label class="control-label" id="cogs_<?php echo $principal['id']; ?>"><?php echo 0.0; ?></label>
                </div>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Principal Total COGS :</label>
            </div>
            <div class="col-xs-6">
                <div class="row show-grid">
                    <label class="control-label cogs_total_principal" id="cogs_total_<?php echo $principal['id']; ?>"><?php echo 0.0; ?></label>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div class="clearfix"></div>

</div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_off_events();
        $(document).off('input', '.quantity_month');
        $(document).on('input', '.quantity_month', function () {
            var principal_id = $(this).attr('data-principal-id');
            calculate_principal(principal_id);
            calculate_grand();
        });
        $(document).off('input', '.unit_price');
        $(document).on('input', '.unit_price', function () {
            var principal_id = $(this).attr('data-principal-id');
            calculate_principal(principal_id);
            calculate_grand();
        });
        $(document).off('change', '.currency_dropdown');
        $(document).on('change', '.currency_dropdown', function () {
            var principal_id = $(this).attr('data-principal-id');
            calculate_principal(principal_id);
            calculate_grand();
        });
    });
    function calculate_principal(principal_id) {
        var percentage_direct_cost = parseFloat($('#percentage_direct_cost').html().replace(/,/g, ''));

        var percentage_packing_cost = parseFloat($('#percentage_packing_cost').html().replace(/,/g, ''));

        var currency_id = $('#currency_dropdown_' + principal_id).val();

        var unit_price = parseFloat($('#amount_unit_price_' + principal_id).val().replace(/,/g, ''));
        var currency_rate = parseFloat($('#currency_rate_' + currency_id).html().replace(/,/g, ''));
        var total_quantity = 0;
        for (i = 1; i < 13; i++) {
            var quantity = parseFloat($('#quantity_' + i + '_' + principal_id).val().replace(/,/g, ''));
            if (quantity > 0) {
                total_quantity += quantity;
            }
        }
        $('#quantity_total_' + principal_id).html(total_quantity);
        if ((unit_price > 0) && (currency_id > 0)) {
            var a = unit_price * currency_rate;
            var b = a * percentage_direct_cost / 100;
            var c = a * percentage_packing_cost / 100;
            var cogs = a + b + c;
            var total_cogs = cogs * total_quantity;
            $('#cogs_' + principal_id).html(get_string_amount(cogs));
            $('#cogs_total_' + principal_id).html(get_string_amount(total_cogs));

        }
        else {
            $('#cogs_' + principal_id).html('0');
            $('#cogs_total_' + principal_id).html('0');
        }
    }
    function calculate_grand() {

        var grand_total = 0;
        $('.quantity_total_principal').each(function (i, obj) {
            var quantity = parseFloat($(obj).html().replace(/,/g, ''));
            if (quantity > 0) {
                grand_total += quantity;
            }

        });
        var grand_cogs_total = 0;
        $('.cogs_total_principal').each(function (i, obj) {
            var total_cogs = parseFloat($(obj).html().replace(/,/g, ''));
            if (total_cogs > 0) {
                grand_cogs_total += total_cogs;
            }

        });
        //cogs_total_principal
        $('#quantity_grand').html(grand_total);
        $('#cogs_total_grand').html(get_string_amount(grand_cogs_total));
        var grand_cogs = 0;
        if (grand_total > 0) {
            grand_cogs = grand_cogs_total / grand_total;
        }
        $('#cogs_grand').html(get_string_amount(grand_cogs));
    }
</script>
