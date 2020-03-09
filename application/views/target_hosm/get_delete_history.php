<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$task_tables = array(
    'hosm'  => $CI->config->item('table_bms_target_hosm'),
    'dsm'   => $CI->config->item('table_bms_target_dsm'),
    'ams'   => $CI->config->item('table_bms_target_ams'),
    'tsme'  => $CI->config->item('table_bms_target_tsme')
);

$location = array(
    'hosm' => array(
        'location_field' => '',
        'location_id'    => 0
    ),
    'dsm' => array(
        'location_field' => 'division_id',
        'location_id'    => $CI->locations['division_id']
    ),
    'ams' => array(
        'location_field' => 'zone_id',
        'location_id'    => $CI->locations['zone_id']
    ),
    'tsme' => array(
        'location_field' => 'territory_id',
        'location_id'    => $CI->locations['territory_id']
    )
);

$title_history = "Delete history of ".(DateTime::createFromFormat('!m', $month)->format('F')). ", {$year}";

$results =array();
if(($task != '') && isset($task_tables[$task]))
{
    $CI->db->from($task_tables[$task] . ' target');
    $CI->db->select('target.*, target.revision_count AS no_of_edit');

    $CI->db->join($CI->config->item('table_login_setup_user_info') . ' user_info', 'user_info.user_id = target.user_created');
    $CI->db->select('user_info.name requested_by');

    $CI->db->join($CI->config->item('table_login_setup_user_info') . ' user_info1', 'user_info1.user_id = target.user_deleted');
    $CI->db->select('user_info1.name deleted_by');

    if(isset($location[$task]['location_field']) && ($location[$task]['location_field'] != '') && isset($location[$task]['location_id']) && ($location[$task]['location_id'] > 0))
    {
        $CI->db->where('target.'.$location[$task]['location_field'], $location[$task]['location_id']);
    }

    $CI->db->where('user_info.revision', 1);
    $CI->db->where('user_info1.revision', 1);
    $CI->db->where('target.status', $CI->config->item('system_status_delete'));
    $CI->db->where('target.year', $year);
    $CI->db->where('target.month', $month);
    $CI->db->order_by('target.id', 'DESC');
    $results = $CI->db->get()->result_array();
}
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <label class=""><a class="external text-danger" data-toggle="collapse" data-target="#deleted_target_history" href="#">+ <?php echo $title_history; ?></a></label>
        </h4>
    </div>
    <div id="deleted_target_history" class="display panel-collapse collapse in">
        <?php if($results){ ?>
            <table class="table table-bordered">
                <tr>
                    <th style="width:1%;white-space:nowrap"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></th>
                    <th><?php echo $CI->lang->line('LABEL_AMOUNT_TARGET'); ?></th>
                    <th style="width:1%;white-space:nowrap"><?php echo $CI->lang->line('LABEL_NO_OF_EDIT'); ?></th>
                    <th><?php echo $CI->lang->line('LABEL_CREATED_BY'); ?></th>
                    <th><?php echo $CI->lang->line('LABEL_DELETED_BY'); ?></th>
                </tr>
                <?php
                $i = 1;
                foreach($results as $result){ ?>
                    <tr>
                        <td style="text-align:right"><?php echo $i++; ?></td>
                        <?php if(isset($result['amount_target_total'])) { ?>
                            <td style="text-align:right"><?php echo System_helper::get_string_amount($result['amount_target_total']); ?></td>
                        <?php } else { ?>
                            <td style="text-align:right"><?php echo System_helper::get_string_amount($result['amount_target']); ?></td>
                        <?php } ?>
                        <td style="text-align:right"><?php echo $result['revision_count']; ?></td>
                        <td><?php echo $result['requested_by'].'<br/> <span style="font-size:0.85em">(Created On: <b>'.(System_helper::display_date($result['date_created'])).'</b>)</span>'; ?></td>
                        <td><?php echo $result['deleted_by'].'<br/> <span style="font-size:0.85em">(Deleted On: <b>'.(System_helper::display_date($result['date_deleted'])).'</b>)</span>'; ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <h4 style="text-align:center">- <i>No History Found</i> -</h4>
        <?php } ?>
    </div>
</div>
