<?php
if(!defined('ABSPATH')) die('You are not allowed to call this page directly.');

// Escape all variables used on this page
$target_url_raw   = esc_url_raw( $target_url, array('http','https') );
$target_url       = esc_url( $target_url, array('http','https') );
$pretty_link_raw  = esc_url_raw( $pretty_link, array('http','https') );
$pretty_link      = esc_url( $pretty_link, array('http','https') );
$prli_blogurl_raw = esc_url_raw( $prli_blogurl, array('http','https') );
$prli_blogurl     = esc_url( $prli_blogurl, array('http','https') );
$target_url_title = esc_html( $target_url_title );
$twitter_status   = esc_html( $twitter_status );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title><?php echo __('Here\'s your Pretty Link', 'pretty-link'); ?></title>
  <script type='text/javascript' src='<?php echo site_url('/wp-includes/js/jquery/jquery.js'); ?>'></script>
  <script type='text/javascript' src='<?php echo PRLI_JS_URL . '/jquery.clippy.js'; ?>'></script>
  <script type="text/javascript">
    jQuery(document).ready(function() {
      /* Set up the clippies! */
      jQuery('.clippy').clippy({clippy_path: '<?php echo PRLI_JS_URL; ?>/clippy.swf', width: '100px'});
    });
  </script>
  <style type="text/css">
    body {
      font-family: Arial;
      text-align: center;
      margin-top: 25px;
    }
    
    h4 {
      font-size: 18px;
      color: #aaaaaa;
    }

    h2 {
      font-size: 24px;
      font-weight: bold;
    }

    h2 a {
      text-decoration: none;
      color: #1f487e;
    }

    h2 a:hover {
      text-decoration: none;
      color: blue;
    }
    .clippy {
      padding-left: 75px;
    }
  </style>
</head>
<body>
  <p><a href="http://prettylinkpro.com"><img src="<?php echo PRLI_IMAGES_URL; ?>/prettylink_logo.jpg" /></a></p>
  <h4><em><?php _e('here\'s your pretty link for:', 'pretty-link'); ?></em><br/><?php echo $target_url_title; ?><br/>(<span title="<?php echo $target_url; ?>"><?php echo substr($target_url,0,50) . ((strlen($target_url)>50)?"...":''); ?></span>)</h4>
  <h2><a href="<?php echo $pretty_link_raw; ?>"><?php echo $pretty_link; ?></a><br/><span class="clippy"><?php echo $pretty_link_raw; ?></span></h2>
  <p>send this link to:<br/>
  <a href="http://www.delicious.com/save" onclick="window.open('http://www.delicious.com/save?v=5&noui&jump=close&url=<?php echo urlencode($pretty_link_raw); ?>&title=<?php echo urlencode($target_url_title); ?>', 'delicious','toolbar=no,width=550,height=550'); return false;"><img src="<?php echo PRLI_IMAGES_URL; ?>/delicious_32.png" title="delicious" width="32px" height="32px" border="0" /></a>&nbsp;&nbsp;
  <!-- Place this tag where you want the su badge to render -->
  <su:badge layout="6"></su:badge>&nbsp;&nbsp;

  <!-- Place this snippet wherever appropriate -->
  <script type="text/javascript">
    (function() {
      var li = document.createElement('script'); li.type = 'text/javascript'; li.async = true;
      li.src = 'https://platform.stumbleupon.com/1/widgets.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(li, s);
    })();
  </script>
  <a href="http://digg.com/submit?phase=2&url=<?php echo urlencode($pretty_link_raw) ?>&title=<?php echo urlencode($target_url_title); ?>" target="_blank"><img src="<?php echo PRLI_IMAGES_URL; ?>/digg_32.png" title="digg" width="32px" height="32px" border="0" /></a>&nbsp;&nbsp;
  <a href="http://twitter.com/home?status=<?php echo urlencode($twitter_status); ?>" target="_blank"><img src="<?php echo PRLI_IMAGES_URL; ?>/twitter_32.png" title="twitter" width="32px" height="32px" border="0" /></a>&nbsp;&nbsp;
  <a href="http://www.facebook.com/sharer.php?u=<?php echo urlencode($pretty_link_raw) ?>&t=<?php echo urlencode($target_url_title); ?>" target="_blank"><img src="<?php echo PRLI_IMAGES_URL; ?>/facebook_32.png" title="facebook" width="32px" height="32px" border="0" /></a>&nbsp;&nbsp;
  <a href="http://reddit.com/submit?url=<?php echo urlencode($pretty_link_raw) ?>&title=<?php echo urlencode($target_url_title); ?>" target="_blank"><img src="<?php echo PRLI_IMAGES_URL; ?>/reddit_32.png" title="reddit" width="32px" height="32px" border="0" /></a>&nbsp;&nbsp;
  <a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($pretty_link_raw) ?>&title=<?php echo urlencode($target_url_title); ?>" target="_blank"><img src="<?php echo PRLI_IMAGES_URL; ?>/linkedin_32.png" title="linkedin" width="32px" height="32px" border="0" /></a>&nbsp;&nbsp;
  <p><a href="<?php echo $target_url_raw; ?>">&laquo; <?php _e('back', 'pretty-link'); ?></a></p>
</body>
</html>
