<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');

// We'll do a full refactor later -- for now we'll just implement the add group ajax method
class PrliGroupsController
{
  public static function load_hooks() {
    add_action('wp_ajax_add_new_prli_group', 'PrliGroupsController::ajax_new_group');
  }

  public static function ajax_new_group() {
	global $prli_group;
    
	// Default response
	$response = json_encode( array( 'status' => 'failure', 
	  	                            'message' => __('An unknown error occurred when creating your group.', 'pretty-link') ) );
	
	if(isset($_REQUEST['_prli_nonce']) and wp_verify_nonce($_REQUEST['_prli_nonce'], 'prli-add-new-group')) {
	  if(isset($_REQUEST['new_group_name'])) {
	    $new_group_name = stripslashes($_REQUEST['new_group_name']);
	    $group_id = $prli_group->create( array( 'name' => $new_group_name, 'description' => '' ) );
        
        if( $group_id )
	      $response = json_encode( array( 'status' => 'success',
	                                      'message' => __('Group Created', 'pretty-link'),
	                                      'group_id' => $group_id,
	                                      'group_option' => "<option value=\"{$group_id}\">{$new_group_name}</option>" ) );
	  }
	  else
	    $response = json_encode( array( 'status' => 'failure',
	                                    'message' => __('A name must be specified for your new group name', 'pretty-link') ) );
	}
	else
	  $response = json_encode( array( 'status' => 'failure',
	                                  'message' => __('Cannot add group because security nonce failed', 'pretty-link') ) );

	header( "Content-Type: application/json" );
	echo $response;
	
	exit;
  }
}
