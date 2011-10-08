<?php
class WafpAppHelper
{
  function get_pages()
  {
    global $wpdb;
    
    $query = "SELECT * FROM {$wpdb->posts} WHERE post_status=%s AND post_type=%s";
    
    $query = $wpdb->prepare( $query, "publish", "page" );
    
    $results = $wpdb->get_results( $query );
    
    if($results)
      return $results;
    else
      return array();
  }
  
  function get_extension( $mimetype )
  {
    switch( $mimetype )
    {
      case "application/msword":
      case "application/rtf":
      case "text/richtext":
        return "doc";
      case "application/vnd.ms-excel":
        return "xls";
      case "application/vnd.ms-powerpoint":
        return "ppt";
      case "application/pdf":
        return "pdf";
      case "application/zip":
        return "zip";
      case "image/jpeg":
        return "jpg";
      case "image/gif":
        return "gif";
      case "image/png":
        return "png";
      case "image/tiff":
        return "tif";
      case "text/plain":
        return "txt";
      case "text/html":
        return "html";
      case "video/quicktime":
        return "mov";
      case "video/x-msvideo":
        return "avi";
      case "video/x-ms-wmv":
        return "wmv";
      case "video/ms-wmv":
        return "wmv";
      case "video/mpeg":
        return "mpg";
      case "audio/mpg":
        return "mp3";
      case "audio/x-m4a":
        return "aac";
      case "audio/m4a":
        return "aac";
      case "audio/x-wav":
        return "wav";
      case "audio/wav":
        return "wav";
      case "application/x-zip-compressed":
        return "zip";
      default:
        return "bin";
    }
  }
  
  function format_currency($number,$show_symbol=true)
  {
    global $wafp_options;
    
    if($wafp_options->number_format == "#.###,##") {
      $dec = ',';
      $tho = '.';
    }
    else {
      $dec = '.';
      $tho = ',';
    }
    
    return ($show_symbol?$wafp_options->currency_symbol:"") . number_format($number, 2, $dec, $tho);
  }
}
?>