<?php if(!defined('ABSPATH')) { die('You are not allowed to call this page directly.'); } ?>
<?php
class PrliAppHelper {
  public static function page_title($page_title) {
	require(PRLI_VIEWS_PATH . '/shared/title_text.php');
  }
}