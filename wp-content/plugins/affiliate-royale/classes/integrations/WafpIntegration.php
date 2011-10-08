<?php
/** This is a special controller that handles all of the
  * PayPal specific functions for Affiliate Royale.
  */
class WafpIntegration
{
  protected $classname;

  function __construct($classname)
  {
    $this->classname = $classname;

    add_filter('wafp_integrations_array', array(&$this, 'register_integration'));
  }
  
  public function register_integration($array)
  {
    $array[] = $this->classname;
  }
  
  public function get_classname()
  {
    return $this->classname;
  }
  
  public function set_classname( $value )
  {
    $this->classname = $value;
  }
  
  abstract public function display_instructions();
}
