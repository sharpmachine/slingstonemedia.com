<?php
class WafpReportsHelper
{
  function periods_dropdown($field_name, $curr_period, $onchange='')
  {
    $field_value = (isset($_POST[$field_name])?$_POST[$field_name]:'');

    $periods =& WafpReportsHelper::get_periods();

    rsort($periods);
    ?>
      <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" onchange="<?php echo $onchange; ?>" class="wafp-dropdown wafp-periods-dropdown">
      <?php
        foreach($periods as $period)
        {
          $period_time = $period['time'];
          $period_label = $period['label'];
          ?>
          <option value="<?php echo $period_time; ?>" <?php echo (((isset($_POST[$field_name]) and $_POST[$field_name] == $curr_period) or (!isset($_POST[$field_name]) and $period_time == $curr_period))?' selected="selected"':''); ?>><?php echo $period_label; ?>&nbsp;</option>
          <?php
        }
      ?>
      </select>
    <?php
  }
  
  function &get_periods()
  {
    $first_click =& WafpClick::get_first_click();
    $first_timestamp = ($first_click?$first_click->created_at_ts:time());
    $periods = array();
    $first_click_ts = $first_timestamp - ((60*60*24*(int)date('t',$first_timestamp)));
    $timestamp = mktime(0, 0, 0, date('n', $first_click_ts), 1, date('Y', $first_click_ts));
    $curr_time = time();

    while( $timestamp < $curr_time )
    {
      $periods[] = array( 'time' => $timestamp, 'label' => date('F 01-t, Y', $timestamp));
      $timestamp = (60*60*24*(int)date('t',$timestamp)) + $timestamp; // advance one month
    }

    return $periods;
  }
}
?>
