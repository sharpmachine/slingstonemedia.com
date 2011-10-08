<?php
class WafpLinksController {
  function route()
  {
    $action = (isset($_REQUEST['action'])?$_REQUEST['action']:null);
    if($action=='process-form')
      return $this->update_links();
    else
      return $this->display_links();
  }

  function display_links()
  {
    $links =& WafpLink::get_all_objects('image, id');
    require(WAFP_VIEWS_PATH . "/wafp-links/list.php");
  }
  
  function update_links()
  {
    $errors = array();
    
    WafpLink::validate($_POST['wafp_new_link_url'], &$errors, true);
	
	if (!empty($_POST['wafp_link_url'])) //Paul added this check
		foreach( $_POST['wafp_link_url'] as $link_url )
			WafpLink::validate($link_url, &$errors);

    if(empty($errors))
    {
      // Add New Links
      if(isset($_POST['wafp_new_link_url']) and !empty($_POST['wafp_new_link_url']))
      {
        if( isset($_FILES['wafp_new_link_image']) and 
            !empty($_FILES['wafp_new_link_image']) and
            !empty($_FILES['wafp_new_link_image']['name']) )
        {
          extract( WafpLink::add_file( $_FILES['wafp_new_link_image'] ) );
          WafpLink::create( $_POST['wafp_new_link_url'], $image, $width, $height );
        }
        else
          WafpLink::create( $_POST['wafp_new_link_url'] );
      }
      
      // Update Links
      if (!empty($_POST['wafp_link_url'])) //Paul added this check
      {
        foreach( $_POST['wafp_link_url'] as $id => $link_url )
        {
          $link =& WafpLink::get_stored_object($id);
        
          $file_info = array( 'image'  => $link->rec->image,
                              'width'  => $link->rec->width,
                              'height' => $link->rec->height );
        
          if( isset($_FILES['wafp_link_image']) and
              !empty($_FILES['wafp_link_image']['name'][$id]) and
              isset($_FILES['wafp_link_image']['size'][$id]) )
          {
            $ufile = array( 'name'     => $_FILES['wafp_link_image']['name'][$id],
                            'type'     => $_FILES['wafp_link_image']['type'][$id],
                            'tmp_name' => $_FILES['wafp_link_image']['tmp_name'][$id],
                            'error'    => $_FILES['wafp_link_image']['error'][$id],
                            'size'     => $_FILES['wafp_link_image']['size'][$id] );
          
            $file_info = WafpLink::add_file( $ufile );
            
            @unlink($link->image_path());
          
            WafpLink::update_image( $id, $file_info['image'], $file_info['width'], $file_info['height'] );
          }

          WafpLink::update_target_url( $id, $link_url );
        }
      }

      // Display form again...
      $links =& WafpLink::get_all_objects('image, id', '', true);
      require(WAFP_VIEWS_PATH . "/wafp-links/links_saved.php");
      require(WAFP_VIEWS_PATH . "/wafp-links/list.php");
    }
    else
    {    
      $links =& WafpLink::get_all_objects('image, id', '', true);
      require(WAFP_VIEWS_PATH . "/shared/errors.php");
      require(WAFP_VIEWS_PATH . "/wafp-links/list.php");
    }
  }
  
  function redirect_link($link_id, $affiliate_id)
  {
    $link =& WafpLink::get_stored_object($link_id);
    $link->track_and_redirect($affiliate_id);
  }

  function track_link($affiliate_id)
  {
    WafpLink::track($affiliate_id);
    exit; // This method just tracks link and bails
  }

  function delete_link($id)
  {
    WafpLink::delete($id);
  }
}
?>
