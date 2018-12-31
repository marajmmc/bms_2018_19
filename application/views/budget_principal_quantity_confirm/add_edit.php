<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();

$action_buttons = array();
$action_buttons[] = array
(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url.'/index/list_variety/'.$item['fiscal_year_id'])
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
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/add_edit/'.$item['fiscal_year_id'].'/'.$item['variety_id'])
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save'); ?>" method="post">
    <input type="hidden" name="item[fiscal_year_id]" value="<?php echo $item['fiscal_year_id'] ?>"/>
    <input type="hidden" name="item[variety_id]" value="<?php echo $item['variety_id'] ?>"/>
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php if ($message_warning_config)
        {
            ?>
            <div class="row show-grid bg-warning text-warning" style="padding:10px 0 0">
                <div class="col-xs-4">
                    <label class="control-label pull-right" style="font-size:1.2em">Warning :</label>
                </div>
                <div class="col-xs-8">
                    <ul style="padding:0;list-style:none">
                        <?php
                        foreach ($message_warning_config as $message)
                        {
                            ?>
                            <li><?php echo $message ?></li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
        <?php } ?>

        <?php if ($message_warning_changes)
        {
            ?>
            <div class="row show-grid bg-danger text-danger" style="padding:10px 0 0">
                <div class="col-xs-4">
                    <label class="control-label pull-right" style="font-size:1.2em">Attention :</label>
                </div>
                <div class="col-xs-8">
                    <ul style="padding:0;list-style:none">
                        <li><b>Please save this Quantity setup again because</b></li>
                        <?php
                        foreach ($message_warning_changes as $message)
                        {
                            ?>
                            <li><?php echo $message ?></li>
                            <?php
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
                <label class="control-label"><?php echo $item['fiscal_year_name'] ?></label>
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
                <label class="control-label pull-right">Air Freight & Docs Percentage :</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label" id="percentage_air_freight"><?php echo $item['percentage_air_freight'] ?></label>%
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Total Direct Cost Percentage :</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label" id="percentage_direct_cost"><?php echo $item['percentage_direct_cost'] ?></label>%
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Total Packing Cost Percentage :</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label" id="percentage_packing_cost"><?php echo $item['percentage_packing_cost'] ?></label>%
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
                            <td id="amount_currency_rate_<?php echo $currency_id; ?>"><?php echo $currency['amount_currency_rate']; ?></td>
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
                <label class="control-label"><?php echo $item['quantity_total_hom_target'];?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Quantity Confirm :</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label" id="quantity_total">--</label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Average Unit Price:</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label" id="amount_unit_price_taka">--</label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Average COGS :</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label" id="cogs">--</label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Total COGS :</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label" id="cogs_total">--</label>
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
                                for ($i = 1; $i <= 12; $i++)
                                {
                                    ?>
                                    <td style="width:130px;text-align:right;font-weight:bold;padding:0 5px 10px 0"><?php echo $CI->lang->line('LABEL_MONTH_' . $i); ?> :</td>
                                    <td style="padding-bottom:10px">
                                        <input type="text" name="items[<?php echo $principal['id']; ?>][quantities][<?php echo $i; ?>]" class="form-control float_type_positive quantity" id="quantity_<?php echo $i . '_' . $principal['id']; ?>" data-principal-id="<?php echo $principal['id']; ?>" value="<?php echo $principal['quantity_'.$i]; ?>"/>
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
                    <label class="control-label quantity_total" id="quantity_total_<?php echo $principal['id']; ?>">--</label>
                </div>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UNIT_PRICE'); ?> :</label>
            </div>
            <div class="col-xs-2">
                <div class="row show-grid">
                    <input type="text" name="items[<?php echo $principal['id']; ?>][amount_unit_price_currency]" class="form-control float_type_positive amount_unit_price_currency" id="amount_unit_price_currency_<?php echo $principal['id']; ?>" data-principal-id="<?php echo $principal['id']; ?>" value="<?php echo $principal['amount_unit_price_currency']; ?>"/>
                </div>
            </div>
            <div class="col-xs-2">
                <div class="row show-grid">
                    <select name="items[<?php echo $principal['id']; ?>][currency_id]" class="form-control currency_id" id="currency_id_<?php echo $principal['id']; ?>" data-principal-id="<?php echo $principal['id']; ?>">
                        <?php
                        foreach ($currencies as $currency_id => $currency)
                        {
                            ?>
                            <option value="<?php echo $currency_id;?>" <?php if($principal['currency_id']==$currency_id){echo 'selected';} ?>><?php echo $currency['name'];?></option>
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
                    <label class="control-label" id="cogs_<?php echo $principal['id']; ?>">--</label>
                </div>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Principal Total COGS :</label>
            </div>
            <div class="col-xs-6">
                <div class="row show-grid">
                    <label class="control-label cogs_total" id="cogs_total_<?php echo $principal['id']; ?>">--</label>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div class="clearfix"></div>

</div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function ($)
    {
        system_off_events();
        <?php
        foreach ($item['principals'] as $principal)
        {
        ?>
        calculate_principal(<?php echo $principal['id']; ?>);
        <?php
        }
        ?>
        calculate_grand();
        $(document).off('input', '.quantity');
        $(document).on('input', '.quantity', function ()
        {
            var principal_id = $(this).attr('data-principal-id');
            calculate_principal(principal_id);
            calculate_grand();
        });
        $(document).off('input', '.amount_unit_price_currency');
        $(document).on('input', '.amount_unit_price_currency', function ()
        {
            var principal_id = $(this).attr('data-principal-id');
            calculate_principal(principal_id);
            calculate_grand();
        });
        $(document).off('change', '.currency_id');
        $(document).on('change', '.currency_id', function ()
        {
            var principal_id = $(this).attr('data-principal-id');
            calculate_principal(principal_id);
            calculate_grand();
        });
    });
    function calculate_principal(principal_id)
    {
        var percentage_air_freight = parseFloat($('#percentage_air_freight').html().replace(/,/g, ''));
        var percentage_direct_cost = parseFloat($('#percentage_direct_cost').html().replace(/,/g, ''));
        var percentage_packing_cost = parseFloat($('#percentage_packing_cost').html().replace(/,/g, ''));
        var percentage_total=percentage_air_freight+percentage_direct_cost+percentage_packing_cost;

        var currency_id = $('#currency_id_' + principal_id).val();

        var amount_unit_price_currency = parseFloat($('#amount_unit_price_currency_' + principal_id).val().replace(/,/g, ''));
        var amount_currency_rate = parseFloat($('#amount_currency_rate_' + currency_id).html().replace(/,/g, ''));
        var quantity_total = 0;
        for (var i = 1; i < 13; i++)
        {
            var quantity = parseFloat($('#quantity_' + i + '_' + principal_id).val().replace(/,/g, ''));
            if (quantity > 0)
            {
                quantity_total += quantity;
            }
        }
        $('#quantity_total_' + principal_id).html(get_string_kg(quantity_total));
        if ((amount_unit_price_currency > 0) && (currency_id > 0))
        {
            var cogs=amount_unit_price_currency*amount_currency_rate*((100+percentage_total)/100);
            var total_cogs = cogs * quantity_total;
            $('#cogs_' + principal_id).html(cogs.toFixed(4));
            $('#cogs_total_' + principal_id).html(total_cogs.toFixed(4));

        }
        else
        {
            $('#cogs_' + principal_id).html('--');
            $('#cogs_total_' + principal_id).html('--');
        }
    }
    function calculate_grand()
    {

        var quantity_total = 0;
        $('.quantity_total').each(function (i, obj)
        {
            var principle_quantity_total = parseFloat($(obj).html().replace(/,/g, ''));
            if (principle_quantity_total > 0)
            {
                quantity_total += principle_quantity_total;
            }

        });
        var cogs_total = 0;
        $('.cogs_total').each(function (i, obj)
        {
            var principle_cogs_total = parseFloat($(obj).html().replace(/,/g, ''));
            if (principle_cogs_total > 0)
            {
                cogs_total += principle_cogs_total;
            }
        });
        $('#quantity_total').html(get_string_kg(quantity_total));
        if(cogs_total>0)
        {
            $('#cogs_total').html(cogs_total.toFixed(4));
        }
        else
        {
            $('#cogs_total').html('--');
        }
        if ((quantity_total > 0)&&(cogs_total > 0))
        {
            var cogs = (cogs_total / quantity_total);
            $('#cogs').html(cogs.toFixed(4));

            var percentage_air_freight = parseFloat($('#percentage_air_freight').html().replace(/,/g, ''));
            var percentage_direct_cost = parseFloat($('#percentage_direct_cost').html().replace(/,/g, ''));
            var percentage_packing_cost = parseFloat($('#percentage_packing_cost').html().replace(/,/g, ''));
            var percentage_total=percentage_air_freight+percentage_direct_cost+percentage_packing_cost;
            var amount_unit_price_taka=cogs*(100/(100+percentage_total));
            $('#amount_unit_price_taka').html(amount_unit_price_taka.toFixed(4));
        }
        else
        {
            $('#cogs').html('--');
        }

    }
</script>
