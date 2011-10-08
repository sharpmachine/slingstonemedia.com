<h3><?php _e('My Links &amp; Banners', 'affiliate-royale'); ?></h3>

<ul class="wafp-link-list">
<?php 
  foreach($links as $link)
  {  
    ?>
  <li>
    <strong><?php _e('Target URL', 'affiliate-royale'); ?>:</strong>&nbsp;<?php echo $link->rec->target_url; ?><br/>
    <strong><?php _e('Code', 'affiliate-royale'); ?>:</strong>&nbsp;<input type="text" onfocus="this.select();" onclick="this.select();" readonly="true" style="max-width: 600px; min-width: 400px;" value="<?php echo htmlentities($link->link_code($affiliate_id)); ?>" /><br/>
    <strong><?php _e('Preview', 'affiliate-royale'); ?>:</strong>
    <?php
      if(isset($link->rec->image) and !empty($link->rec->image))
        echo '<br/>' . $link->link_code($affiliate_id);
      else
        echo '<a href="'. $link->link_code($affiliate_id) .'">'.__('Affiliate Link', 'affiliate-royale').'</a>';
    ?>
  </li>
    <?php
  }
?>  
</ul>
</div> <!--END MAIN DASHBOARD WRAPPER-->