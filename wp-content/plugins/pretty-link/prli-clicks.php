<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');

require_once 'prli-config.php';
require_once(PRLI_MODELS_PATH . '/models.inc.php');
require_once(PRLI_PATH . '/prli-image-lookups.php');

$controller_file = basename(__FILE__);
$max_rows_per_file = 5000;

if(!isset($_REQUEST['action']))
{
  $page_params = '';

  $params = $prli_click->get_params_array();

  $current_page = $params['paged'];

  $start_timestamp = $prli_utils->get_start_date($params);
  $end_timestamp = $prli_utils->get_end_date($params);

  $start_timestamp = mktime(0, 0, 0, date('n', $start_timestamp), date('j', $start_timestamp), date('Y', $start_timestamp));
  $end_timestamp   = mktime(0, 0, 0, date('n', $end_timestamp),   date('j', $end_timestamp),   date('Y', $end_timestamp)  );

  $sdyear = date('Y',$start_timestamp);
  $sdmon  = date('n',$start_timestamp);
  $sddom  = date('j',$start_timestamp);

  $edyear = date('Y',$end_timestamp);
  $edmon  = date('n',$end_timestamp);
  $eddom  = date('j',$end_timestamp);

  $where_clause = $wpdb->prepare(" cl.created_at BETWEEN '%d-%d-%d 00:00:00' AND '%d-%d-%d 23:59:59'",$sdyear,$sdmon,$sddom,$edyear,$edmon,$eddom);

  if(!empty($params['sdate']) and preg_match('/^\d\d\d\d-\d\d-\d\d$/', $params['sdate']))
    $page_params .= "&sdate={$params['sdate']}";

  if(!empty($params['edate']) and preg_match('/^\d\d\d\d-\d\d-\d\d$/', $params['edate']))
    $page_params .= "&edate={$params['edate']}";

  if(!empty($params['l']) and $params['l'] != 'all')
  {
    $where_clause .= (($params['l'] != 'all') ? $wpdb->prepare(" AND cl.link_id=%d",$params['l']):'');
    $link_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$wpdb->prefix}prli_links WHERE id=%d", $params['l']));
    $link_slug = $wpdb->get_var($wpdb->prepare("SELECT slug FROM {$wpdb->prefix}prli_links WHERE id=%d", $params['l']));

    $page_params .= "&l={$params['l']}";
  }
  else if(!empty($params['ip']))
  {
    $link_name = __("IP Address: ", 'pretty-link') . esc_html($params['ip']);
    $where_clause .= $wpdb->prepare(" AND cl.ip=%d", $params['ip']);
    $page_params .= "&ip={$params['ip']}";
  }
  else if(!empty($params['vuid']))
  {
    $link_name = __("Visitor: ", 'pretty-link') . esc_html($params['vuid']);
    $where_clause .= $wpdb->prepare(" AND cl.vuid=%s",$params['vuid']);
    $page_params .= "&vuid={$params['vuid']}";
  }
  else if(!empty($params['group']))
  {
    $group = $prli_group->getOne($params['group']);
    $link_name = __("Group: ", 'pretty-link') . esc_html($group->name);
    $where_clause .= $wpdb->prepare(" AND cl.link_id IN (SELECT id FROM {$prli_link->table_name} WHERE group_id=%d)",$params['group']);
    $page_params .= "&group={$params['group']}";
  }
  else
  {
    $link_name = __("All Links", 'pretty-link');
    $where_clause .= "";
    $page_params .= "";
  }

  if($params['type'] == "unique")
  {
    $where_clause .= " AND first_click=1";
    $page_params .= "&type=unique";
  }

  $click_vars = prli_get_click_sort_vars($params,$where_clause);
  $sort_params = $page_params . $click_vars['sort_params'];
  $page_params .= $click_vars['page_params'];
  $sort_str = $click_vars['sort_str'];
  $sdir_str = $click_vars['sdir_str'];
  $search_str = $click_vars['search_str'];

  $where_clause = $click_vars['where_clause'];
  $order_by = $click_vars['order_by'];
  $count_where_clause = $click_vars['count_where_clause'];

  $record_count = $prli_click->getRecordCount($count_where_clause);
  $page_count = $prli_click->getPageCount($page_size,$count_where_clause);
  $clicks = $prli_click->getPage($current_page,$page_size,$where_clause,$order_by,true);
  $page_last_record = $prli_utils->getLastRecordNum($record_count,$current_page,$page_size);
  $page_first_record = $prli_utils->getFirstRecordNum($record_count,$current_page,$page_size);

  require_once PRLI_VIEWS_PATH . '/prli-clicks/list.php';
}
else if(isset($_REQUEST['action']) and $_REQUEST['action'] == 'csv')
{
  $param_string = '';
  $where_clause = '';

  if(isset($_GET['l']))
  {
    $where_clause = $wpdb->prepare(" link_id=%d",$_GET['l']);
    $link_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$wpdb->prefix}prli_links WHERE id=%d",$_GET['l']));
    $link_slug = $wpdb->get_var($wpdb->prepare("SELECT slug FROM {$wpdb->prefix}prli_links WHERE id=%d",$_GET['l']));
    $param_string .= "l={$_GET['l']}";
  }
  else if(isset($_GET['ip']))
  {
    $link_name = "ip_addr_" . $_GET['ip'];
    $where_clause = $wpdb->prepare(" cl.ip=%s",$_GET['ip']);
    $param_string .= "ip={$_GET['ip']}";
  }
  else if(isset($_GET['vuid']))
  {
    $link_name = "visitor_" . $_GET['vuid'];
    $where_clause = $wpdb->prepare(" cl.vuid=%s",$_GET['vuid']);
    $param_string .= "vuid={$_GET['vuid']}";
  }
  else if(isset($_GET['group']))
  {
    $group = $prli_group->getOne($_GET['group']);
    $link_name = "group_{$group->name}";
    $where_clause .= $wpdb->prepare(" cl.link_id IN (SELECT id FROM {$prli_link->table_name} WHERE group_id=%d)",$_GET['group']);
    $param_string .= "group={$_GET['group']}";
  }
  else
    $link_name = "all_links";
  
  $hit_record_count = $prli_click->getRecordCount($where_clause);
  $hit_page_count   = (int)ceil($hit_record_count / $max_rows_per_file);

  $param_string       = (empty($param_string)?'':"&{$param_string}");
  $hit_report_url     = "{$prli_blogurl}/index.php?action=prli_download_csv_hit_report{$param_string}";

  require_once PRLI_VIEWS_PATH . '/prli-clicks/csv_download.php';
}
else if(isset($_REQUEST['action']) and $_REQUEST['action'] == 'prli_download_csv_hit_report')
{
  if(isset($_GET['l']))
  {
    $where_clause = $wpdb->prepare(" link_id=%d",$_GET['l']);
    $link_name = $wpdb->get_var($wpdb->prepare("SELECT name FROM {$wpdb->prefix}prli_links WHERE id=%d",$_GET['l']));
    $link_slug = $wpdb->get_var($wpdb->prepare("SELECT slug FROM {$wpdb->prefix}prli_links WHERE id=%d",$_GET['l']));
  }
  else if(isset($_GET['ip']))
  {
    $link_name = "ip_addr_{$_GET['ip']}";
    $where_clause = $wpdb->prepare(" cl.ip=%s",$_GET['ip']);
  }
  else if(isset($_GET['vuid']))
  {
    $link_name = "visitor_{$_GET['vuid']}";
    $where_clause = $wpdb->prepare(" cl.vuid=%s",$_GET['vuid']);
  }
  else if(isset($_GET['group']))
  {
    $group = $prli_group->getOne($_GET['group']);
    $link_name = "group_{$group->name}";
    $where_clause .= $wpdb->prepare(" cl.link_id IN (SELECT id FROM {$prli_link->table_name} WHERE group_id=%d)", $_GET['group']);
  }
  else
  {
    $link_name = "all_links";
    $where_clause = "";
  }

  $link_name = stripslashes($link_name);
  $link_name = preg_replace("#[ ,]#",'',$link_name);

  $record_count = $prli_click->getRecordCount($where_clause);
  $page_count   = (int)ceil($record_count / $max_rows_per_file);
  $prli_page = esc_html($_GET['prli_page']);
  $hmin = 0;

  if($prli_page)
    $hmin = ($prli_page - 1) * $max_rows_per_file;

  if($prli_page==$page_count)
    $hmax = $record_count;
  else
    $hmax = ($prli_page * $max_rows_per_file) - 1;

  $hlimit = "{$hmin},{$max_rows_per_file}";
  $clicks = $prli_click->getAll($where_clause,'',false,$hlimit);
  require_once PRLI_VIEWS_PATH . '/prli-clicks/csv.php';
}

// Helpers
function prli_get_click_sort_vars($params,$where_clause = '')
{
  global $wpdb;
  
  $count_where_clause = $where_clause;
  $page_params = '';
  $order_by = '';

  // These will have to work with both get and post
  $sort_str   = $params['sort'];
  $sdir_str   = $params['sdir'];
  $search_str = $params['search'];

  // Insert search string
  if(!empty($search_str))
  {
    $search_params = explode(" ", esc_html($search_str));

    $first_pass = true;
    foreach($search_params as $search_param)
    {
      if($first_pass)
      {
        if($where_clause != '') {
          $where_clause .= ' AND';
          $count_where_clause .= ' AND';
        }

        $first_pass = false;
      }
      else {
        $where_clause .= ' AND';
        $count_where_clause .= ' AND';
      }

      $search_param = $sp = "%" . like_escape($search_param) . "%";
      $where_clause .= $wpdb->prepare( " (cl.ip LIKE %s OR ".
                                       "cl.vuid LIKE %s OR ".
                                       "cl.btype LIKE %s OR ".
                                       "cl.bversion LIKE %s OR ".
                                       "cl.host LIKE %s OR ".
                                       "cl.referer LIKE %s OR ".
                                       "cl.uri LIKE %s OR ".
                                       "cl.created_at LIKE %s", $sp, $sp, $sp, $sp, $sp, $sp, $sp, $sp );
      $count_where_clause .= $wpdb->prepare( " (cl.ip LIKE %s OR ".
                                             "cl.vuid LIKE %s OR ".
                                             "cl.btype LIKE %s OR ".
                                             "cl.bversion LIKE %s OR ".
                                             "cl.host LIKE %s OR ".
                                             "cl.referer LIKE %s OR ".
                                             "cl.uri LIKE %s OR ".
                                             "cl.created_at LIKE %s", $sp, $sp, $sp, $sp, $sp, $sp, $sp, $sp );
      $count_where_clause .= ")";
      $where_clause .= $wpdb->prepare( " OR li.name LIKE %s)", $sp );
    }

    $page_params .= "&search=" . urlencode($search_str);
  }

  // Have to create a separate var so sorting doesn't get screwed up
  $sort_params = $page_params;

  // make sure page params stay correct
  if(!empty($sort_str))
    $page_params .="&sort=$sort_str";

  if(!empty($sdir_str))
    $page_params .= "&sdir=$sdir_str";

  if(empty($count_where_clause))
    $count_where_clause = $where_clause;

  // Add order by clause
  switch($sort_str)
  {
    case "ip":
    case "vuid":
    case "btype":
    case "bversion":
    case "host":
    case "referer":
    case "uri":
      $order_by .= " ORDER BY cl.$sort_str";
      break;
    case "link":
      $order_by .= " ORDER BY li.name";
      break;
    default:
      $order_by .= " ORDER BY cl.created_at";
  }

  // Toggle ascending / descending
  if((empty($sort_str) and empty($sdir_str)) or $sdir_str == 'desc')
  {
    $order_by .= ' DESC';
    $sdir_str = 'desc';
  }
  else
    $sdir_str = 'asc';

  return compact( 'count_where_clause', 'sort_str', 'sdir_str', 'search_str',
                  'where_clause', 'order_by', 'sort_params', 'page_params' );
}
