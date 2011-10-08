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
  $datestr = $row->rdate;

  $oclicks       .= ((!$start_date)?"":",") . "[(new Date(\"{$datestr}\")).getTime(), {$row->clicks}]";
  $ouniques      .= ((!$start_date)?"":",") . "[(new Date(\"{$datestr}\")).getTime(), {$row->uniques}]";
  $otransactions .= ((!$start_date)?"":",") . "[(new Date(\"{$datestr}\")).getTime(), {$row->transactions}]";
                                 
  if(!$start_date)
    $start_date = $datestr;
}

$oclicks       .= "]";
$ouniques      .= "]";
$otransactions .= "]";

$chart_height = (isset($chart_height)?$chart_height:"200px");
$chart_width  = (isset($chart_width)?$chart_width:"100%");
$chart_id     = (isset($chart_id)?$chart_id:"wafp-stats-graph");
?>
<div id="<?php echo $chart_id; ?>" style="width: <?php echo $chart_width; ?>; height: <?php echo $chart_height; ?>;"><?php _e('There was an error loading the Stats chart.', 'affiliate-royale'); ?></div>
<script id="source" language="javascript" type="text/javascript">
jQuery(function () {
    var oclicks = <?php echo $oclicks; ?>;
    var ouniques = <?php echo $ouniques; ?>;
    var otransactions = <?php echo $otransactions; ?>;

    var plot = jQuery.plot(jQuery("#<?php echo $chart_id; ?>"),
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
    jQuery("#<?php echo $chart_id; ?>").bind("plothover", function (event, pos, item) {
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

    jQuery("#<?php echo $chart_id; ?>").bind("plotclick", function (event, pos, item) {
        if (item) {
            jQuery("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
            plot.highlight(item.series, item.datapoint);
        }
    });
});
</script>
