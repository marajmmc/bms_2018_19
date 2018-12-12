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

<!---------------------- Each Block of a Single Principle ---------------------->
<?php
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
                                        <input type="text" class="form-control float_type_positive quantity_month" id="quantity_<?php echo $i . '_' . $principal['id']; ?>" data-principal-id="<?php echo $principal['id']; ?>" value=""/>
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
                    <label class="control-label" id="quantity_total_<?php echo $principal['id']; ?>"><?php echo $sub_total; ?></label>
                </div>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Price per Kg :</label>
            </div>
            <div class="col-xs-2">
                <div class="row show-grid">
                    <input type="text" class="form-control float_type_positive unit_price" id="amount_unit_price_<?php echo $principal['id']; ?>" data-principal-id="<?php echo $principal['id']; ?>" value=""/>
                </div>
            </div>
            <div class="col-xs-2">
                <div class="row show-grid">
                    <select class="form-control currency_dropdown" id="currency_dropdown_<?php echo $principal['id']; ?>" data-principal-id="<?php echo $principal['id']; ?>">
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
                    <label class="control-label" id="cogs_total_<?php echo $principal['id']; ?>"><?php echo 0.0; ?></label>
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

        $(document).on('input', '.quantity_month', function () {
            var principal_id = $(this).attr('data-principal-id');
            var COGS = calculate_principal_cogs(principal_id);
            calculate_total_principal_quantity(principal_id)
            set_principle_cogs(principal_id, COGS);
        });

        // if Currency Rate Input changes
        $(document).on('input', '.unit_price', function () {
            var principal_id = $(this).attr('data-principal-id');
            var COGS = calculate_principal_cogs(principal_id);
            set_principle_cogs(principal_id, COGS);
        });

        // if Currency DropDown changes
        $(document).on('change', '.currency_dropdown', function () {
            var principal_id = $(this).attr('data-principal-id');
            var COGS = calculate_principal_cogs(principal_id);
            set_principle_cogs(principal_id, COGS);
        });

    });

    function calculate_total_principal_quantity(principal_id){
        var subTotal = 0.0;
        var qty_input = 0.0;
        for (var i = 1; i <= 12; i++) {
            qty_input = $('#quantity_' + i + '_' + principal_id).val();
            if (!(isNaN(qty_input) || qty_input == '')) {
                subTotal += parseFloat(qty_input);
            }
        }
        $('#quantity_total_' + principal_id).text(subTotal);
    }

    function calculate_principal_cogs(principal_id) {

        var unit_price = $('#amount_unit_price_' + principal_id).val();
        var currency_id = $('#currency_dropdown_' + principal_id).val();
        var currency_rate = $('#currency_rate_' + currency_id).text();

        var direct_cost_percentage = $('#percentage_direct_cost').text();
        var packing_cost_percentage = $('#percentage_packing_cost').text();

        var A = 0.0;
        var B = 0.0;
        var C = 0.0;

        // A (Unit price X Currency rate):
        if (!(isNaN(unit_price) || (unit_price == '')) && !(isNaN(currency_rate) || (currency_rate == ''))) {
            A = parseFloat(unit_price) * parseFloat(currency_rate);
        }

        // B ((A X DC percentage) / 100):
        if (!(isNaN(direct_cost_percentage) || direct_cost_percentage == '')) {
            B = (A * parseFloat(direct_cost_percentage)) / 100;
        }

        // C ((A X PC percentage) / 100):
        if (!(isNaN(packing_cost_percentage) || packing_cost_percentage == '')) {
            C = (A * parseFloat(packing_cost_percentage)) / 100;
        }

        /*console.log('A-->> ' + A);
        console.log('B-->> ' + B);
        console.log('C-->> ' + C);
        console.log('COGS= ' + (A + B + C));*/

        // Return COGS = (A + B + C) for each Principal
        return parseFloat(A + B + C);
    }

    /* Set Functions */
    function set_principle_cogs(principal_id, cogs){
        $('#quantity_total_'+principal_id).text(cogs);
    }


</script>
