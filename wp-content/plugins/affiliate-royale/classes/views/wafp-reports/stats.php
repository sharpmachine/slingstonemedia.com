<div id="wafp-admin-affiliate-panel">
<h3><?php _e('Affiliate Stats', 'affiliate-royale'); ?></h3>
<p><?php _e('Select the period you want to view', 'affiliate-royale'); ?>:<br/><?php WafpReportsHelper::periods_dropdown('wafp-report-period', $period, 'javascript:wafp_view_admin_affiliate_page( \'admin_affiliate_stats\', this.value, 1);'); ?>&nbsp;&nbsp;<img src="<?php echo WAFP_SITEURL . '/wp-admin/images/loading.gif'; ?>" alt="<?php _e('Loading...', 'affiliate-royale'); ?>" style="display: none;" class="wafp-stats-loader" /></p>
<?php
$oclicks       = "[";
$ouniques      = "[";
$otransactions = "[";

$omax = 0;
$start_date = false;

foreach($stats as $row)
{
  $omax = ($row->clicks > $omax)?$row->clicks:$omax;
  $omax = ($row->uniques > $omax)?$row->uniques:$omax;
  $omax = ($row->transactions > $omax)?$row->transactions:$omax;
  $datestr = date('Y/m/d', $row->tsdate);

  $oclicks       .= ((!$start_date)?"":",") . "[(new Date(\"{$datestr}\")).getTime(), {$row->clicks}]";
  $ouniques      .= ((!$start_date)?"":",") . "[(new Date(\"{$datestr}\")).getTime(), {$row->uniques}]";
  $otransactions .= ((!$start_date)?"":",") . "[(new Date(\"{$datestr}\")).getTime(), {$row->transactions}]";
                                 
  if(!$start_date)
    $start_date = $datestr;
}

$oclicks       .= "]";
$ouniques      .= "]";
$otransactions .= "]";

?>
<div id="wafp-stats-graph" style="width: 98%; height: 250px;"><?php _e('There was an error loading the Stats chart.', 'affiliate-royale'); ?></div>
<script id="source" language="javascript" type="text/javascript">
jQuery(function () {
    var oclicks = <?php echo $oclicks; ?>;
    var ouniques = <?php echo $ouniques; ?>;
    var otransactions = <?php echo $otransactions; ?>;

    var plot = jQuery.plot(jQuery("#wafp-stats-graph"),
           [ { data: oclicks, label: "clicks"}, { data: ouniques, label: "uniques" }, { data: otransactions, label: "sales" } ], {
               series: {
                   lines: { show: true },
                   points: { show: true }
               },
               grid: {   backgroundColor: { colors: ["#fff", "#eee"] },
               hoverable: true, clickable: true },
               yaxis: { min: 0, max: <?php echo $omax; ?> },
               xaxis: { 
                 mode: "time", 
                 min: (new Date("<?php echo $start_date; ?>")).getTime(), 
                 max: (new Date("<?php echo $datestr; ?>")).getTime()
               }
             });

    function showTooltip(x, y, contents) {
        jQuery('<div id="tooltip">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y + 5,
            'text-align': 'center',
            left: x + 5,
            border: '1px solid #fdd',
            padding: '5px',
            border: '3px solid #ababab',
            'background-color': '#fee',
            '-webkit-border-radius': '5px',
            '-moz-border-radius': '5px',
            'border-radius': '5px',
            opacity: 0.95
        }).appendTo("body").fadeIn(100);
    }

    var previousPoint = null;
    jQuery("#wafp-stats-graph").bind("plothover", function (event, pos, item) {
        jQuery("#x").text(pos.x);
        jQuery("#y").text(pos.y);

        if (item) {
            if (previousPoint != item.datapoint) {
                previousPoint = item.datapoint;

                jQuery("#tooltip").remove();
                var x = item.datapoint[0],
                    y = item.datapoint[1];

                var date = new Date(x);

                showTooltip(item.pageX, item.pageY,
                            date.toDateString() + "<br/><strong>" + y + " " + item.series.label + "</strong>");
            }

        }
    });

    jQuery("#wafp-stats-graph").bind("plotclick", function (event, pos, item) {
        if (item) {
            jQuery("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
            plot.highlight(item.series, item.datapoint);
        }
    });
});
</script>
<table class="widefat post fixed wafp-table" cellspacing="0">
<thead>
  <tr>
    <th scope="col" class="manage-column"><?php _e('Date', 'affiliate-royale'); ?></th>
    <th scope="col" class="manage-column"><?php _e('Clicks', 'affiliate-royale'); ?></th>
    <th scope="col" class="manage-column"><?php _e('Uniques', 'affiliate-royale'); ?></th>
    <th scope="col" class="manage-column"><?php _e('Transactions', 'affiliate-royale'); ?></th>
    <th scope="col" class="manage-column"><?php _e('Commissions', 'affiliate-royale'); ?></th>
    <th scope="col" class="manage-column"><?php _e('Corrections', 'affiliate-royale'); ?></th>
    <th scope="col" class="manage-column"><?php _e('Total', 'affiliate-royale'); ?></th>
  </tr>
</thead>

<tbody>
<?php
  $clicks_total = 0;
  $uniques_total = 0;
  $transactions_total = 0;
  $commissions_total = 0;
  $corrections_total = 0;
  $totals_total = 0;
  foreach($stats as $row)
  {
    $clicks_total += $row->clicks;
    $uniques_total += $row->uniques;
    $transactions_total += $row->transactions;
    $commissions_total += $row->commissions;
    $corrections_total += $row->corrections;
    $totals_total += ((float)$row->commissions - (float)$row->corrections);
  ?>
<tr>
  <td><?php echo $row->date; ?></td>
  <td><?php echo $row->clicks; ?></td>
  <td><?php echo $row->uniques; ?></td>
  <td><?php echo $row->transactions; ?></td>
  <td><?php echo WafpAppHelper::format_currency( $row->commissions ); ?></td>
  <td><?php echo WafpAppHelper::format_currency( $row->corrections ); ?></td>
  <td><?php echo WafpAppHelper::format_currency( ((float)$row->commissions - (float)$row->corrections) ); ?></td>
</tr>
<?php } ?>
</tbody>
  <tfoot style="text-align: left;">
    <tr>
      <th scope="col" class="manage-column"><?php _e("Totals", 'affiliate-royale'); ?></th>
      <th scope="col" class="manage-column"><?php echo $clicks_total; ?></th>
      <th scope="col" class="manage-column"><?php echo $uniques_total; ?></th>
      <th scope="col" class="manage-column"><?php echo $transactions_total; ?></th>
      <th scope="col" class="manage-column"><?php echo WafpAppHelper::format_currency( $commissions_total ); ?></th>
      <th scope="col" class="manage-column"><?php echo WafpAppHelper::format_currency( $corrections_total ); ?></th>
      <th scope="col" class="manage-column"><?php echo WafpAppHelper::format_currency( $totals_total ); ?></th>
    </tr>
  </tfoot>
</table>
</div>
