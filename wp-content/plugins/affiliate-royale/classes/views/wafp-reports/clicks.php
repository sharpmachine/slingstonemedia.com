<div id="wafp-admin-affiliate-panel">
<h3><?php _e('Clicks', 'affiliate-royale'); ?></h3>
<table class="widefat post fixed wafp-table" cellspacing="0">
<thead>
  <tr>
    <th class="manage-column"><?php _e('Time', 'affiliate-royale'); ?></th>
    <th class="manage-column"><?php _e('Affiliate', 'affiliate-royale'); ?></th>
    <th class="manage-column"><?php _e('URL', 'affiliate-royale'); ?></th>
    <th class="manage-column"><?php _e('IP', 'affiliate-royale'); ?></th>
    <th class="manage-column"><?php _e('Referrer', 'affiliate-royale'); ?></th>
  </tr>
</thead>
<tbody>
<?php
  foreach($clicks as $row)
  {
  ?>
<tr>
  <td><?php echo $row->created_at; ?></td>
  <td><?php echo $row->user_login; ?></td>
  <td><?php echo $row->target_url; ?></td>
  <td><?php echo $row->ip; ?></td>
  <td><?php echo $row->referrer; ?></td>
</tr>
<?php } ?>
</tbody>
</table>
<?php
if($prev_page)
{
  ?>
<span style="float: right;"><a href="javascript:wafp_view_admin_affiliate_page('admin_affiliate_clicks','current',<?php echo $prev_page; ?>);"><?php _e('Newer Clicks', 'affiliate-royale'); ?></a>&nbsp;&raquo;</span>
  <?php
}

if($next_page)
{
  ?>
<span>&laquo;&nbsp;<a href="javascript:wafp_view_admin_affiliate_page('admin_affiliate_clicks','current',<?php echo $next_page; ?>);"><?php _e('Older Clicks', 'affiliate-royale'); ?></a></span>
  <?php
}
?>
</div>