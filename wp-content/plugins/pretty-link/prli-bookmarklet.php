<?php
function get_params() {
  $accepted_params = array('k','target_url','action','rt','trk','grp');
  $param_str = '';
  foreach($_GET as $k => $v) {
    if(in_array($k,$accepted_params))
      $param_str .= "&{$k}={$v}"; 
  }
  return $param_str;
}

header("location: /index.php?action=prli_bookmarklet".get_params());
