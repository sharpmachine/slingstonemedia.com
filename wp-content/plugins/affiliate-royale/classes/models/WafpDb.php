<?php
class WafpDb
{
  var $links;
  var $clicks;
  var $transactions;
  var $payments;
  var $commissions;
  
  function WafpDb()
  {
    global $wpdb;

    $this->links        = "{$wpdb->prefix}wafp_links";
    $this->clicks       = "{$wpdb->prefix}wafp_clicks";
    $this->transactions = "{$wpdb->prefix}wafp_transactions";
    $this->payments     = "{$wpdb->prefix}wafp_payments";
    $this->commissions  = "{$wpdb->prefix}wafp_commissions";
  }
  
  function upgrade()
  {
    global $wpdb, $wafp_db_version;
    
    $old_db_version = get_option('wafp_db_version');

    if($wafp_db_version != $old_db_version)
    {
      $this->before_upgrade($old_db_version);

      $charset_collate = '';
      if( $wpdb->has_cap( 'collation' ) )
      {
        if( !empty($wpdb->charset) )
          $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if( !empty($wpdb->collate) )
          $charset_collate .= " COLLATE $wpdb->collate";
      }
      
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      
      /* Create/Upgrade Clicks Table */
      $sql = "CREATE TABLE {$this->clicks} (
                id int(11) NOT NULL auto_increment,
                ip varchar(255) default NULL,
                browser varchar(255) default NULL,
                referrer varchar(255) default NULL,
                uri varchar(255) default NULL,
                robot tinyint default 0,
                first_click tinyint default 0,
                created_at datetime NOT NULL,
                link_id int(11) default NULL,
                affiliate_id int(11) default NULL,
                PRIMARY KEY  (id),
                KEY link_id (link_id),
                KEY created_at (created_at),
                KEY affiliate_id (affiliate_id)
              ) {$charset_collate};";

      dbDelta($sql);
      
      /* Create/Upgrade Friend Requests Table */
      $sql = "CREATE TABLE {$this->links} (
                id int(11) NOT NULL auto_increment,
                target_url text NOT NULL,
                image text default NULL,
                width int(11) default NULL,
                height int(11) default NULL,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id)
              ) {$charset_collate};";
      
      dbDelta($sql);
      
      /* Create/Upgrade Board Posts Table */
      $sql = "CREATE TABLE {$this->transactions} (
                id int(11) NOT NULL auto_increment,
                affiliate_id int(11) NOT NULL,
                item_name varchar(255) DEFAULT NULL,
                sale_amount float(9,2) NOT NULL,
                commission_amount float(9,2) NOT NULL,
                refund_amount float(9,2) DEFAULT 0.00,
                correction_amount float(9,2) DEFAULT 0.00,
                commission_percentage float(9,2) DEFAULT 0.00,
                subscr_id varchar(255) DEFAULT NULL,
                subscr_paynum int(11) DEFAULT 0,
                ip_addr varchar(255) DEFAULT NULL,
                cust_email varchar(255) DEFAULT NULL,
                cust_name varchar(255) DEFAULT NULL,
                trans_num varchar(255) DEFAULT NULL,
                type varchar(255) DEFAULT NULL,
                status varchar(255) DEFAULT NULL,
                response text DEFAULT NULL,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY affiliate_id (affiliate_id),
                KEY trans_num (trans_num),
                KEY subscr_id (subscr_id),
                KEY subscr_paynum (subscr_paynum),
                KEY type (type)
              ) {$charset_collate};";
      
      dbDelta($sql);

      /* Create/Upgrade Board Posts Table */
      $sql = "CREATE TABLE {$this->payments} (
                id int(11) NOT NULL auto_increment,
                affiliate_id int(11) NOT NULL,
                amount float(9,2) NOT NULL,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY created_at (created_at),
                KEY affiliate_id (affiliate_id)
              ) {$charset_collate};";
      
      dbDelta($sql);

      /* Create/Upgrade Board Posts Table */
      $sql = "CREATE TABLE {$this->commissions} (
                id int(11) NOT NULL auto_increment,
                affiliate_id int(11) NOT NULL,
                transaction_id int(11) NOT NULL,
                commission_level int(11) DEFAULT 0,
                commission_percentage float(9,2) NOT NULL,
                commission_amount float(9,2) NOT NULL,
                correction_amount float(9,2) DEFAULT 0.00,
                payment_id int(11) DEFAULT 0,
                created_at datetime NOT NULL,
                PRIMARY KEY  (id),
                KEY created_at (created_at),
                KEY transaction_id (transaction_id),
                KEY payment_id (payment_id),
                KEY affiliate_id (affiliate_id)
              ) {$charset_collate};";
      
      dbDelta($sql);
      
      $this->after_upgrade($old_db_version);
    }
    
    /***** SAVE DB VERSION *****/
    update_option('wafp_db_version', $wafp_db_version);
  }
  
  function before_upgrade($curr_db_version)
  {
    // Nothing here yet
  }
  
  function after_upgrade($curr_db_version)
  {
    global $wpdb, $wafp_db;
    
    if((int)$curr_db_version < 11)
    {
      $transactions = $wpdb->get_results("SELECT * FROM {$wafp_db->transactions}");
      
      foreach($transactions as $transaction)
      {
        if( !empty($transaction->affiliate_id) and 
            $transaction->commission_amount > 0.00 )
        {
          $commission_id = WafpCommission::create( $transaction->affiliate_id, $transaction->id, 0, $transaction->commission_percentage, $transaction->commission_amount, $transaction->payment_id, $transaction->correction_amount );

          // Manually update the timestamp
          $query = $wpdb->prepare("UPDATE {$wafp_db->commissions} SET created_at=%s WHERE id=%d", $transaction->created_at, $commission_id);
          $wpdb->query($query);
        }
      }
      
      $wpdb->query("ALTER TABLE {$wafp_db->transactions} MODIFY commission_percentage float(9,2) DEFAULT 0.00");
      $wpdb->query("ALTER TABLE {$wafp_db->transactions} MODIFY subscr_id varchar(255) DEFAULT NULL");
    }
  }

  function create_record($table, $args, $record_created_at=true)
  {
    global $wpdb;
  
    $cols = array();
    $vars = array();
    $values = array();
  
    $i = 0;
    foreach($args as $key => $value)
    {  
      $cols[$i] = $key;
      if(is_float($value))
        $vars[$i] = '%f';
      else if(is_int($value))
        $vars[$i] = '%d';
      else
        $vars[$i] = '%s';
      $values[$i] = $value;
      $i++;
    }
  
    if($record_created_at)
    {
      $cols[$i] = 'created_at';
      $vars[$i] = 'NOW()';
    }
  
    if(empty($cols))
      return false;
  
    $cols_str = implode(',',$cols);
    $vars_str = implode(',',$vars);
  
    $query = "INSERT INTO {$table} ( {$cols_str} ) VALUES ( {$vars_str} )";
    $query = $wpdb->prepare( $query, $values );

    $query_results = $wpdb->query($query);
  
    if($query_results)
      return $wpdb->insert_id;
    else
      return false;
  }
  
  function update_record( $table, $id, $args )
  {
    global $wpdb;
  
    if(empty($args) or empty($id))
      return false;
  
    $set = '';
    $values = array();
    foreach($args as $key => $value)
    {
      if(empty($set))
        $set .= ' SET';
      else
        $set .= ',';
  
      $set .= " {$key}=";
  
      if(is_float($value))
        $set .= "%f";
      else if(is_int($value))
        $set .= "%d";
      else
        $set .= "%s";
  
      $values[] = $value;
    }
  
    $values[] = $id;
    $query = "UPDATE {$table}{$set} WHERE id=%d";
  
    $query = $wpdb->prepare( $query, $values );
  
    return $wpdb->query($query);
  }
  
  function delete_records($table, $args)
  {
    global $wpdb;
    extract(WafpDb::get_where_clause_and_values( $args ));

    $query = "DELETE FROM {$table}{$where}";
    $query = $wpdb->prepare($query, $values);

    return $wpdb->query($query);
  }
  
  function get_count($table, $args=array())
  {
    global $wpdb;
    extract(WafpDb::get_where_clause_and_values( $args ));
    
    $query = "SELECT COUNT(*) FROM {$table}{$where}";
    $query = $wpdb->prepare($query, $values);
    return $wpdb->get_var($query);
  }
  
  function get_where_clause_and_values( $args )
  {
    $where = '';
    $values = array();
    foreach($args as $key => $value)
    {
      if(!empty($where))
        $where .= ' AND';
      else
        $where .= ' WHERE';
  
      $where .= " {$key}=";
  
      if(is_float($value))
        $where .= "%f";
      else if(is_int($value))
        $where .= "%d";
      else
        $where .= "%s";
  
      $values[] = $value;
    }
    
    return compact('where','values');
  }
  
  function get_one_record($table, $args=array())
  {
    global $wpdb;

    extract(WafpDb::get_where_clause_and_values( $args ));

    $query = "SELECT * FROM {$table}{$where} LIMIT 1";
    $query = $wpdb->prepare($query, $values);
    return $wpdb->get_row($query);
  }
  
  function get_records($table, $args=array(), $order_by='', $limit='', $joins=array())
  {
    global $wpdb;

    extract(WafpDb::get_where_clause_and_values( $args ));
    $join = '';
  
    if(!empty($order_by))
      $order_by = " ORDER BY {$order_by}";
  
    if(!empty($limit))
      $limit = " LIMIT {$limit}";
    
    if(!empty($joins)) {
      foreach($joins as $join_clause) {
        $join .= " {$join_clause}";
      }
    }
  
    $query = "SELECT * FROM {$table}{$join}{$where}{$order_by}{$limit}";
    $query = $wpdb->prepare($query, $values);
    return $wpdb->get_results($query);
  }
  
  /** Built to work with the datatables plugin for jQuery */
  public function datatable($cols, $from, $order_by='', $limit='', $joins=array(), $args=array())
  {
    global $wpdb;
    
    # defaults
    $col_str_array = array();
    foreach( $cols as $col => $code )
      $col_str_array[] = "{$code} AS {$col}";
    
    $col_names = array_keys($cols);
    
    $col_str = implode(", ",$col_str_array);
    
    if(!empty($order_by))
      $order_by = " ORDER BY {$order_by}";
      
    if(!empty($limit))
      $limit = " LIMIT {$limit}";
    
    if(!empty($joins))
      $join_str = " " . implode( " ", $joins );
    
    $args_str = implode(' AND ', $args);
    
    # Paging
	  if( isset($_REQUEST['iDisplayStart']) and
	      isset($_REQUEST['iDisplayLength']) and
	      $_REQUEST['iDisplayLength'] != '-1' ) {
	    $limit = " LIMIT {$_REQUEST['iDisplayStart']},{$_REQUEST['iDisplayLength']}";
	  }

    # Ordering
    if(isset($_REQUEST['iSortCol_0'])) {
  	  $orders = array();
  	  for($i=0; $i < $_REQUEST['iSortingCols']; $i++) {
  	    if( $_REQUEST['bSortable_' . $_REQUEST['iSortCol_' . $i]] == "true" ) {
  	      $col = $col_names[ $_REQUEST['iSortCol_' . $i] ];
  	      $orders[] = "{$col} {$_REQUEST['sSortDir_' . $i]}";
  	    }
  	  }

      if(!empty($orders))
  	    $order_by = " ORDER BY " . implode(", ", $orders);
    }
    
  	# Searching
  	$search_str = "";
  	$searches = array();
  	if(isset($_REQUEST['sSearch']) and !empty($_REQUEST['sSearch'])) {
  	  foreach($cols as $col => $code) {
        $searches[] = "{$code} LIKE '%{$_REQUEST['sSearch']}%'";
  	  }
    	
    	if(!empty($searches))
    	  $search_str = implode(' OR ', $searches);
  	}
    
  	# Filtering
  	$filter_str = "";
  	$filters = array();
  	$i=0;
  	foreach($cols as $col => $code) {
  	  if( $_REQUEST['bSearchable_' . $i] == "true" and !empty($_REQUEST['sSearch_' . $i]) ) {
  	    $filters[] = "{$code} LIKE '%{$_REQUEST['sSearch_' . $i]}%'";
  	  }
  	  $i++;
  	}
  	
  	if(!empty($filters))
  	  $filter_str = implode(' AND ', $filters );
    
    $conditions = "";
    
  	# Pull Searching & Filtering into where

    if(!empty($args))
    {
  	  if(!empty($searches) and !empty($filters))
        $conditions = " WHERE $args_str AND ({$search_str}) AND {$filter_str}";
      elseif(!empty($searches))
        $conditions = " WHERE $args_str AND ({$search_str})";
      elseif(!empty($filters))   
        $conditions = " WHERE $args_str AND {$filter_str}";
      else
        $conditions = " WHERE $args_str";
    }
    else {
  	  if(!empty($searches) and !empty($filters))
        $conditions = " WHERE ({$search_str}) AND {$filter_str}";
      elseif(!empty($searches))
        $conditions = " WHERE {$search_str}";
      elseif(!empty($filters))
        $conditions = " WHERE {$filter_str}";
    }
      
    $query = "SELECT {$col_str} FROM {$from}{$join_str}{$conditions}{$order_by}{$limit}";
    $total_query = "SELECT COUNT(*) FROM {$from}{$join_str}{$conditions}";
    $results = $wpdb->get_results($query, ARRAY_N);
    $total = $wpdb->get_var($total_query);
    
    // Datatables needs the aaData thing here to work
    $json = json_encode(array("sEcho" => $_REQUEST['sEcho'],
  	                          "iTotalRecords" => (int)$total,
  	                          "iTotalDisplayRecords" => (int)$total,
                              "aaData" => $results));
    
    return $json;
    //return $query;
  }
}
?>