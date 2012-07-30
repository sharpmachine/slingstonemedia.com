<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');

  // Only show the pager bar if there is more than 1 page
  if($page_count > 1)
  {
      ?>
    <div class="tablenav"<?php echo (isset($navstyle)?" style=\"$navstyle\"":''); ?>>
      <div class='tablenav-pages'><span class="displaying-num">Displaying <?php echo "$page_first_record&#8211;$page_last_record of $record_count"; ?></span>
        
        <?php
        // Only show the prev page button if the current page is not the first page
        if($current_page > 1)
        {
          ?>
          <a class='prev page-numbers' href='?page=<?php echo esc_html($_REQUEST['page'].$page_params); ?>&paged=<?php echo ($current_page-1); ?>&size=<?php echo $_REQUEST['size']; ?>'>&laquo;</a>
          <?php
        }
      
        // First page is always displayed
        if($current_page==1)
        {
          ?>
          <span class='page-numbers current'>1</span>
          <?php
        }
        else
        {
          ?>
          <a class='page-numbers' href='?page=<?php echo esc_html($_REQUEST['page'].$page_params); ?>&paged=1&size=<?php echo $_REQUEST['size']; ?>'>1</a>
          <?php
        }
      
        // If the current page is more than 2 spaces away from the first page then we put some dots in here
        if($current_page >= 5)
        {
          ?>
          <span class='page-numbers dots'>...</span>
          <?php
        }
      
        // display the current page icon and the 2 pages beneath and above it
        $low_page = (($current_page >= 5)?($current_page-2):2);
        $high_page = ((($current_page + 2) < ($page_count-1))?($current_page+2):($page_count-1));
        for($i = $low_page; $i <= $high_page; $i++)
        {
          if($current_page==$i)
          {
            ?>
            <span class='page-numbers current'><?php echo $i; ?></span>
            <?php
          }
          else
          {
            ?>
            <a class='page-numbers' href='?page=<?php echo esc_html($_REQUEST['page'].$page_params); ?>&paged=<?php echo $i; ?>&size=<?php echo $_REQUEST['size']; ?>'><?php echo $i; ?></a>
            <?php
          }
        }
      
        // If the current page is more than 2 away from the last page then show ellipsis
        if($current_page < ($page_count - 3))
        {
          ?>
          <span class='page-numbers dots'>...</span>
          <?php
        }
      
        // Display the last page icon
        if($current_page == $page_count)
        {
          ?>
          <span class='page-numbers current'><?php echo $page_count; ?></span>
          <?php
        }
        else
        {
          ?>
          <a class='page-numbers' href='?page=<?php echo esc_html($_REQUEST['page'].$page_params); ?>&paged=<?php echo $page_count; ?>&size=<?php echo $_REQUEST['size']; ?>'><?php echo $page_count; ?></a>
          <?php
        }
      
        // Display the next page icon if there is a next page
        if($current_page < $page_count)
        {
          ?>
          <a class='next page-numbers' href='?page=<?php echo esc_html($_REQUEST['page'].$page_params); ?>&paged=<?php echo ($current_page + 1); ?>&size=<?php echo $_REQUEST['size']; ?>'>&raquo;</a>
          <?php
        }
        ?>
        <select class="prli-page-size" onchange="location='<?php echo admin_url("admin.php?page=" . esc_html($_REQUEST['page'].$page_params) . "&paged=1&size='+this.options[this.selectedIndex].value"); ?>">
          <option value="10" selected="selected">10</option>
          <option value="25" <?php if($_REQUEST['size'] == 25) echo 'selected="selected"'; ?>>25</option>
          <option value="50" <?php if($_REQUEST['size'] == 50) echo 'selected="selected"'; ?>>50</option>
          <option value="100" <?php if($_REQUEST['size'] == 100) echo 'selected="selected"'; ?>>100&nbsp;</option>
        </select>
      </div>
      <?php if(!$footer): ?>
      <?php PrliLinksHelper::bulk_action_dropdown(); ?>
      <?php endif; ?>
    </div>
    <?php
  }
  else
  {
    ?>
    <div class="tablenav"<?php echo (isset($navstyle)?" style=\"$navstyle\"":''); ?>>
      <div class='tablenav-pages'>
        <span class="displaying-num">Displaying <?php echo "$page_first_record&#8211;$page_last_record of $record_count"; ?></span>
        <select class="prli-page-size" onchange="location='<?php echo admin_url("admin.php?page=" . esc_html($_REQUEST['page'].$page_params) . "&paged=1&size='+this.options[this.selectedIndex].value"); ?>">
          <option value="10" selected="selected">10</option>
          <option value="25" <?php if($_REQUEST['size'] == 25) echo 'selected="selected"'; ?>>25</option>
          <option value="50" <?php if($_REQUEST['size'] == 50) echo 'selected="selected"'; ?>>50</option>
          <option value="100" <?php if($_REQUEST['size'] == 100) echo 'selected="selected"'; ?>>100&nbsp;</option>
        </select>
      </div>
      <?php if(!$footer): ?>
      <?php PrliLinksHelper::bulk_action_dropdown(); ?>
      <?php endif; ?>
    </div>
    <?php
  }
