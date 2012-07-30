<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');

class PrliLink
{
    var $table_name;

    function __construct()
    {
      global $wpdb;
      $this->table_name = "{$wpdb->prefix}prli_links";
    }

    function create( $values )
    {
      global $wpdb;

      if($values['redirect_type'] == 'pixel')
        $values['name'] = (!empty($values['name'])?$values['name']:$values['slug']);
      else
        $values['name'] = (!empty($values['name'])?$values['name']:PrliUtils::get_page_title($values['url'],$values['slug']));

      $query_str = "INSERT INTO {$this->table_name} " . 
                     '(url,'.
                      'slug,'.
                      'name,'.
                      'param_forwarding,'.
                      'param_struct,'.
                      'redirect_type,'.
                      'description,'.
                      'track_me,'.
                      'nofollow,'.
                      'group_id,'.
                      'created_at) ' .
                      'VALUES (%s,%s,%s,%s,%s,%s,%s,%d,%d,%d,NOW())';

      $query = $wpdb->prepare( $query_str,
                               $values['url'],
                               $values['slug'],
                               $values['name'],
                               $values['param_forwarding'],
                               $values['param_struct'],
                               $values['redirect_type'],
                               $values['description'],
                               (int)isset($values['track_me']),
                               (int)isset($values['nofollow']),
                               (isset($values['group_id'])?(int)$values['group_id']:'NULL') );
      $query_results = $wpdb->query($query);

      if($query_results)
        $link_id = $wpdb->insert_id;
      else
        $link_id = false;

      do_action('prli-create-link', $link_id, $values);

      return $link_id;
    }

    function update( $id, $values )
    {
      global $wpdb;

      if($values['redirect_type'] == 'pixel')
        $values['name'] = (!empty($values['name'])?$values['name']:$values['slug']);
      else
        $values['name'] = (!empty($values['name'])?$values['name']:PrliUtils::get_page_title($values['url'],$values['slug']));

      $query_str = "UPDATE {$this->table_name} " . 
                      'SET url=%s, ' .
                          'slug=%s, ' .
                          'name=%s, ' .
                          'param_forwarding=%s, ' .
                          'param_struct=%s, ' .
                          'redirect_type=%s, ' .
                          'description=%s, ' .
                          'track_me=%d, ' .
                          'nofollow=%d, ' .
                          'group_id=%d ' .
                     ' WHERE id=%d';

      $query = $wpdb->prepare( $query_str,
                               isset($values['url'])?$values['url']:'',
                               isset($values['slug'])?$values['slug']:'',
                               isset($values['name'])?$values['name']:'',
                               isset($values['param_forwarding'])?$values['param_forwarding']:'',
                               isset($values['param_struct'])?$values['param_struct']:'',
                               isset($values['redirect_type'])?$values['redirect_type']:'',
                               isset($values['description'])?$values['description']:'',
                               (int)isset($values['track_me']),
                               (int)isset($values['nofollow']),
                               (isset($values['group_id'])?(int)$values['group_id']:'NULL'),
                               $id );

      $query_results = $wpdb->query($query);
      return $query_results;
    }

    function bulk_update( $ids, $values ) {
      if( !empty($ids) and is_array($values) and !empty($values) ) {
        global $wpdb;
        $query = "UPDATE {$this->table_name} SET ";

        $link_columns = array( 'name',
                               'description',
                               'url',
                               'slug',
                               'nofollow',
                               'track_me',
                               'param_forwarding',
                               'param_struct',
                               'redirect_type',
                               'group_id' );
        
        $sets = array();
        foreach($values as $lnkkey => $lnkval) {
          // make sure this is an option contained in the links table
          if(in_array($lnkkey, $link_columns)) {
            // check to see if this is a checkbox
            if(in_array(strtolower($lnkval), array('on','off')))
              $sets[] = $wpdb->prepare("{$lnkkey}=%d", (strtolower($lnkval)=='on')); 
            else
              $sets[] = $wpdb->prepare("{$lnkkey}=%s", $lnkval); 
          }
        }

        $query .= implode(',', $sets);
        
        if(preg_match('/,/',$ids))
          $query .= " WHERE id IN ({$ids})";
        else
          $query .= $wpdb->prepare(" WHERE id=%d", $ids);
         
        $query_results = $wpdb->query($query);
        return $query_results;
      }
      
      return false;
    }

    function update_group( $id, $value, $group_id )
    {
      global $wpdb;
      $query = 'UPDATE ' . $this->table_name . 
                  ' SET group_id=' . (isset($value)?$group_id:'NULL') . 
                  ' WHERE id='.$id;
      $query_results = $wpdb->query($query);
      return $query_results;
    }

    function destroy( $id )
    {
      require_once(PRLI_MODELS_PATH.'/models.inc.php');
      global $wpdb, $prli_click, $prli_link_meta;

      do_action('prli_delete_link', $id);
      $metas = $wpdb->prepare("DELETE FROM {$prli_link_meta->table_name} WHERE link_id=%d",$id);
      $reset = $wpdb->prepare("DELETE FROM {$prli_click->table_name} WHERE link_id=%d",$id);
      $destroy = $wpdb->prepare("DELETE FROM {$this->table_name} WHERE id=%d",$id);

      $wpdb->query($metas);
      $wpdb->query($reset);
      return $wpdb->query($destroy);
    }

    function reset( $id )
    {
      require_once(PRLI_MODELS_PATH.'/models.inc.php');
      global $wpdb, $prli_click, $prli_link_meta;

      $prli_link_meta->delete_link_meta($id, 'static-clicks');
      $prli_link_meta->delete_link_meta($id, 'static-uniques');

      $reset = $wpdb->prepare("DELETE FROM {$prli_click->table_name} WHERE link_id=%d", $id);
      return $wpdb->query($reset);
    }

    function getOneFromSlug( $slug, $return_type = OBJECT, $include_stats = false )
    {
      global $wpdb, $prli_click, $prli_options, $prli_link_meta;
      if($include_stats)
      {
        $query = 'SELECT li.*, ';
        if($prli_options->extended_tracking != 'count')
        {
          $query .= '(SELECT COUNT(*) FROM ' . $prli_click->table_name . ' cl ' .
                        'WHERE cl.link_id = li.id' . $prli_click->get_exclude_where_clause( ' AND' ) . ') as clicks, ' .
                    '(SELECT COUNT(*) FROM ' . $prli_click->table_name . ' cl ' .
                        'WHERE cl.link_id = li.id ' .
                        'AND cl.first_click <> 0' . $prli_click->get_exclude_where_clause( ' AND' ) . ') as uniques ';
        }
        else
        {
          $query .= '(SELECT lm.meta_value FROM ' . $prli_link_meta->table_name . ' lm ' .
                        'WHERE lm.meta_key="static-clicks" AND lm.link_id=li.id LIMIT 1) as clicks, ' .
                    '(SELECT lm.meta_value FROM ' . $prli_link_meta->table_name . ' lm ' .
                        'WHERE lm.meta_key="static-uniques" AND lm.link_id=li.id LIMIT 1) as uniques ';
        }
        $query .= "FROM {$this->table_name} li " .
                  'WHERE slug=%s';
      }
      else
        $query = "SELECT * FROM {$this->table_name} WHERE slug=%s";

      $query = $wpdb->prepare($query, $slug);
      $link = $wpdb->get_row($query, $return_type);

      if( $include_stats and $link and $prli_options->extended_tracking == 'count' )
      {
        $link->clicks  = $prli_link_meta->get_link_meta($link->id,'static-clicks',true);
        $link->uniques = $prli_link_meta->get_link_meta($link->id,'static-uniques',true);
      }

      return $link;
    }

    function getOne( $id, $return_type = OBJECT, $include_stats = false )
    {
      global $wpdb, $prli_click, $prli_link_meta, $prli_options;
      if( !isset($id) or empty($id) )
          return false;

      if($include_stats)
      {
        $query = 'SELECT li.*, ';
        if($prli_options->extended_tracking != 'count')
        {
          $query .= '(SELECT COUNT(*) FROM ' . $prli_click->table_name . ' cl ' .
                        'WHERE cl.link_id = li.id' . $prli_click->get_exclude_where_clause( ' AND' ) . ') as clicks, ' .
                    '(SELECT COUNT(*) FROM ' . $prli_click->table_name . ' cl ' .
                        'WHERE cl.link_id = li.id ' .
                        'AND cl.first_click <> 0' . $prli_click->get_exclude_where_clause( ' AND' ) . ') as uniques ';
        }
        else
        {
          $query .= '(SELECT lm.meta_value FROM ' . $prli_link_meta->table_name . ' lm ' .
                        'WHERE lm.meta_key="static-clicks" AND lm.link_id=li.id LIMIT 1) as clicks, ' .
                    '(SELECT lm.meta_value FROM ' . $prli_link_meta->table_name . ' lm ' .
                        'WHERE lm.meta_key="static-uniques" AND lm.link_id=li.id LIMIT 1) as uniques ';
        }
        $query .= 'FROM ' . $this->table_name . ' li ' .
                  'WHERE id=%d';
      }
      else
        $query = "SELECT * FROM {$this->table_name} WHERE id=%d";

      $query = $wpdb->prepare($query, $id);
      return $wpdb->get_row($query, $return_type);
    }

    function find_first_target_url($target_url)
    {
      global $wpdb;
      $query_str = "SELECT id FROM {$this->table_name} WHERE url=%s LIMIT 1";
      $query = $wpdb->prepare($query_str,$target_url);
      return $wpdb->get_var($query);
    }

    function &get_or_create_pretty_link_for_target_url( $target_url, $group=0 )
    {
      $pretty_link_id = $this->find_first_target_url( $target_url );
      $pretty_link = $this->getOne($pretty_link_id);

      if(empty($pretty_link) or !$pretty_link)
      {
        $pl_insert_id = prli_create_pretty_link( $target_url, '', '', '', $group );
        $pretty_link = $this->getOne($pl_insert_id);
      }
      else
        prli_update_pretty_link( $pretty_link->id, '', '', '', '', $group );

      if( !isset($pretty_link) or
          empty($pretty_link) or
          !$pretty_link )
        return false;
      else
        return $pretty_link;
    }

    function is_pretty_link($url, $check_domain=true)
    {
      global $prli_blogurl;

      if( !$check_domain or preg_match( '#^' . preg_quote( $prli_blogurl ) . '#', $url ) )
      {
        $uri = preg_replace('#' . preg_quote($prli_blogurl) . '#', '', $url);

        // Resolve WP installs in sub-directories
        preg_match('#^(https?://.*?)(/.*)$#', $prli_blogurl, $subdir);
        
        $struct = PrliUtils::get_permalink_pre_slug_regex();

        $subdir_str = (isset($subdir[2])?$subdir[2]:'');

        $match_str = '#^'.$subdir_str.'('.$struct.')([^\?]*?)([\?].*?)?$#';
        
        if(preg_match($match_str, $uri, $match_val))
        {
          // Match longest slug -- this is the most common
          $params = (isset($match_val[3])?$match_val[3]:'');
          if( $pretty_link_found =& $this->is_pretty_link_slug( $match_val[2] ) )
            return compact('pretty_link_found','pretty_link_params');

          // Trim down the matched link
          $matched_link = preg_replace('#/[^/]*?$#','',$match_val[2],1);

          // cycle through the links (maximum depth 25 folders so we don't get out
          // of control -- that should be enough eh?) and trim the link down each time
          for( $i=0; ($i < 25) and 
                     $matched_link and 
                     !empty($matched_link) and
                     $matched_link != $match_val[2]; $i++ )
          {
            $new_match_str ="#^{$subdir_str}({$struct})({$matched_link})(.*?)?$#";

            $params = (isset($match_val[3])?$match_val:'');
            if( $pretty_link_found =& $this->is_pretty_link_slug( $match_val[2] ) )
              return compact('pretty_link_found','pretty_link_params');

            // Trim down the matched link and try again
            $matched_link = preg_replace('#/[^/]*$#','',$match_val[2],1);
          }
        }
      }
      
      return false;
    }

    function is_pretty_link_slug($slug)
    {
	  return apply_filters('prli-check-if-slug', $this->getOneFromSlug( urldecode($slug) ), urldecode($slug));
    }

    function get_link_min( $id, $return_type = OBJECT )
    {
      global $wpdb;
      $query_str = "SELECT * FROM {$this->table_name} WHERE id=%d";
      $query = $wpdb->prepare($query_str, $id);
      return $wpdb->get_row($query, $return_type);
    }

    function getAll($where = '', $order_by = '', $return_type = OBJECT, $include_stats = false)
    {
      global $wpdb, $prli_click, $prli_group, $prli_link_meta, $prli_options, $prli_utils;

      if($include_stats)
      {
        $query = 'SELECT li.*, ';
        if($prli_options->extended_tracking != 'count')
        {
          $query .= '(SELECT COUNT(*) FROM ' . $prli_click->table_name . ' cl ' .
                        'WHERE cl.link_id = li.id' . $prli_click->get_exclude_where_clause( ' AND' ) . ') as clicks, ' .
                    '(SELECT COUNT(*) FROM ' . $prli_click->table_name . ' cl ' .
                        'WHERE cl.link_id = li.id ' .
                        'AND cl.first_click <> 0' . $prli_click->get_exclude_where_clause( ' AND' ) . ') as uniques, ';
        }
        else
        {
          $query .= '(SELECT lm.meta_value FROM ' . $prli_link_meta->table_name . ' lm ' .
                        'WHERE lm.meta_key="static-clicks" AND lm.link_id=li.id LIMIT 1) as clicks, ' .
                    '(SELECT lm.meta_value FROM ' . $prli_link_meta->table_name . ' lm ' .
                        'WHERE lm.meta_key="static-uniques" AND lm.link_id=li.id LIMIT 1) as uniques, ';
        }
        $query .= 'gr.name as group_name ' .
                 'FROM '. $this->table_name . ' li ' .
                 'LEFT OUTER JOIN ' . $prli_group->table_name . ' gr ON li.group_id=gr.id' . 
                 $prli_utils->prepend_and_or_where(' WHERE', $where) . $order_by;
      }
      else
      {
        $query = "SELECT li.*, gr.name as group_name FROM {$this->table_name} li " . 
                 'LEFT OUTER JOIN ' . $prli_group->table_name . ' gr ON li.group_id=gr.id' . 
                 $prli_utils->prepend_and_or_where(' WHERE', $where) . $order_by;
      }
       
      return $wpdb->get_results($query, $return_type);
    }

    // Pagination Methods
    function getRecordCount($where="")
    {
      global $wpdb, $prli_utils;
      $query = 'SELECT COUNT(*) FROM ' . $this->table_name . ' li' . $prli_utils->prepend_and_or_where(' WHERE', $where);
      return $wpdb->get_var($query);
    }

    function getPageCount($p_size, $where="")
    {
      return ceil((int)$this->getRecordCount($where) / (int)$p_size);
    }

    function getPage($current_p,$p_size, $where = "", $order_by = '', $return_type = OBJECT)
    {
      global $wpdb, $prli_click, $prli_utils, $prli_group, $prli_link_meta, $prli_options;
      $end_index = $current_p * $p_size;
      $start_index = $end_index - $p_size;
      $query = 'SELECT li.*, ';
      if($prli_options->extended_tracking != 'count')
      {
        $query .= '(SELECT COUNT(*) FROM ' . $prli_click->table_name . ' cl ' .
                      'WHERE cl.link_id = li.id' . $prli_click->get_exclude_where_clause( ' AND' ) . ') as clicks, ' .
                  '(SELECT COUNT(*) FROM ' . $prli_click->table_name . ' cl ' .
                      'WHERE cl.link_id = li.id ' .
                      'AND cl.first_click <> 0' . $prli_click->get_exclude_where_clause( ' AND' ) . ') as uniques, ';
      }
      else
      {
        $query .= '(SELECT lm.meta_value FROM ' . $prli_link_meta->table_name . ' lm ' .
                      'WHERE lm.meta_key="static-clicks" AND lm.link_id=li.id LIMIT 1) as clicks, ' .
                  '(SELECT lm.meta_value FROM ' . $prli_link_meta->table_name . ' lm ' .
                      'WHERE lm.meta_key="static-uniques" AND lm.link_id=li.id LIMIT 1) as uniques, ';
      }
      $query .= 'gr.name as group_name ' .
               'FROM ' . $this->table_name . ' li ' .
               'LEFT OUTER JOIN ' . $prli_group->table_name . ' gr ON li.group_id=gr.id' . 
               $prli_utils->prepend_and_or_where(' WHERE', $where) . $order_by . ' ' . 
               'LIMIT ' . $start_index . ',' . $p_size . ';';
      $results = $wpdb->get_results($query, $return_type);
      return $results;
    }

    public function generateValidSlug($num_chars = 4)
    {
      global $wpdb;
      
      $slug = PrliUtils::gen_random_string($num_chars);

      $query = "SELECT slug FROM {$this->table_name}";
      $slugs = $wpdb->get_col($query,0);

      // It is highly unlikely that we'll ever see 2 identical random slugs
      // but just in case, here's some code to prevent collisions
      while( in_array($slug,$slugs) or !PrliUtils::slugIsAvailable($slug) )
        $slug = PrliUtils::gen_random_string($num_chars);

      return $slug;
    }
    
    function get_pretty_link_url($slug)
    {
      global $prli_blogurl;

      $link = $this->getOneFromSlug($slug);

      if((isset($link->param_forwarding) and $link->param_forwarding == 'custom') and
         (isset($link->redirect_type) and $link->redirect_type == 'pixel'))
        return "&lt;img src=\"".$prli_blogurl . PrliUtils::get_permalink_pre_slug_uri() . $link->slug . $link->param_struct . "\" width=\"1\" height=\"1\" style=\"display: none\" /&gt;";
      else if((!isset($link->param_forwarding) or $link->param_forwarding != 'custom') and
              (isset($link->redirect_type) and $link->redirect_type == 'pixel'))
        return "&lt;img src=\"".$prli_blogurl . PrliUtils::get_permalink_pre_slug_uri() . $link->slug . "\" width=\"1\" height=\"1\" style=\"display: none\" /&gt;";
      else if((isset($link->param_forwarding) and $link->param_forwarding == 'custom') and
              (!isset($link->redirect_type) or $link->redirect_type != 'pixel'))
        return $prli_blogurl . PrliUtils::get_permalink_pre_slug_uri() . $link->slug . $link->param_struct;
      else
        return $prli_blogurl . PrliUtils::get_permalink_pre_slug_uri() . $link->slug;
    }

    function validate( $values )
    {
      global $wpdb, $prli_utils, $prli_blogurl;

      $errors = array();
      if( ( $values['url'] == null or $values['url'] == '') and $values['redirect_type'] != 'pixel' )
        $errors[] = "Target URL can't be blank";

      if( $values['slug'] == null or $values['slug'] == '' )
        $errors[] = "Pretty Link can't be blank";

      if( $values['url'] == $prli_blogurl.PrliUtils::get_permalink_pre_slug_uri().$values['slug'] )
        $errors[] = "Target URL must be different than the Pretty Link";

      if( !empty($values['url']) and
          !preg_match('/^http.?:\/\/.*\..*$/', $values['url'] ) and
          !preg_match('!^(http|https)://(localhost|127\.0\.0\.1)(:\d+)?(/[\w- ./?%&=]*)?!', $values['url'] ) )
        $errors[] = "Link URL must be a correctly formatted url";

      if( preg_match('/^[\?\&\#]+$/', $values['slug'] ) )
        $errors[] = "Pretty Link slugs must not contain question marks, ampersands or number signs.";

      if( preg_match('#/$#', $values['slug']) )
        $errors[] = "Pretty Link slugs must not end with a slash (\"/\")";

      $id = isset($values['id'])?$values['id']:null;
      if( !$prli_utils->slugIsAvailable($values['slug'],$id) )
        $errors[] = "This Pretty Link Slug is already taken. Check to make sure it isn't being used by another pretty link, post, page, category or tag slug. If none of these are true then check to see that this slug isn't the name of a file in the root folder of your wordpress install.";

      if( isset($values['param_forwarding']) and $values['param_forwarding'] == 'custom' and empty($values['param_struct']) )
        $errors[] = "If Custom Parameter Forwarding has been selected then you must specify a forwarding format.";

      if( isset($values['param_forwarding']) and $values['param_forwarding'] == 'custom' and !preg_match('#%.*?%#', $values['param_struct']) )
        $errors[] = "Your parameter forwarding must have at least one parameter specified in the format ex: <code>/%var1%/%var_two%/%varname3% ...</code>";

      return $errors;
    }
    
    public function get_target_to_pretty_urls($urls=array(),$create_pretty_links=false) {
      global $wpdb, $prli_blogurl; 

      if(empty($urls))
        return false;

      $decoded_urls = array_map( create_function( '$url', 'return html_entity_decode(urldecode($url));' ), $urls );
      
      if( count($decoded_urls) > 1 )
        $where = "IN (" . implode( ',', array_map( create_function( '$url', 'return "\"{$url}\"";' ), $decoded_urls ) ) . ")";
      else 
        $where = "= '" . html_entity_decode(urldecode($decoded_urls[0])) . "'";

      $query = "SELECT li.url AS target_url, " .
                      "CONCAT(%s, li.slug) AS pretty_url " .
                 "FROM {$this->table_name} AS li " .
                "WHERE li.url {$where}";

      $query = $wpdb->prepare($query, $prli_blogurl.PrliUtils::get_permalink_pre_slug_uri());
      
      $results = (array)$wpdb->get_results($query);
      
      $prli_lookup = array();
      foreach($results as $url_hash) {
        if(isset($prli_lookup[$url_hash->target_url]))
          $prli_lookup[$url_hash->target_url][] = $url_hash->pretty_url; 
        else
          $prli_lookup[$url_hash->target_url] = array($url_hash->pretty_url); 
      }
      
      if($create_pretty_links) {
        foreach($decoded_urls as $url) {
          if(!isset($prli_lookup[$url])) {
            if( $id = prli_create_pretty_link( $url ) )
              $prli_lookup[$url] = array(prli_get_pretty_link_url($id));
          }
        }
      }

      return $prli_lookup;
    }

    public static function bookmarklet_link() {
      global $prli_options;
      $site_url = site_url();
      return "javascript:location.href='{$site_url}/index.php?action=prli_bookmarklet&k={$prli_options->bookmarklet_auth}&target_url='+escape(location.href);";
    }
}
