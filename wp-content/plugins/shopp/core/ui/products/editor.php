<div class="wrap shopp">
	<?php if (!empty($Shopp->Notice)): ?><div id="message" class="updated fade"><p><?php echo $Shopp->Notice; ?></p></div><?php endif; ?>

	<div class="icon32"></div>
	<h2><?php _e('Product Editor','Shopp'); ?></h2>

	<div id="ajax-response"></div>
	<form name="product" id="product" action="<?php echo admin_url('admin.php'); ?>" method="post">
		<?php wp_nonce_field('shopp-save-product'); ?>
		<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
		<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>

		<div id="poststuff" class="metabox-holder has-right-sidebar">

			<div id="side-info-column" class="inner-sidebar">
			<?php
			do_action('submitpage_box');
			$side_meta_boxes = do_meta_boxes('shopp_page_shopp-products', 'side', $Product);
			?>
			</div>

			<div id="post-body" class="<?php echo $side_meta_boxes ? 'has-sidebar' : 'has-sidebar'; ?>">
			<div id="post-body-content" class="has-sidebar-content">

				<div id="titlediv">
					<div id="titlewrap">
						<label class="hide-if-no-js hidden" id="title-prompt-text" for="title"><?php _e('Enter product name','Shopp'); ?></label>
						<input name="name" id="title" type="text" value="<?php echo esc_attr($Product->name); ?>" size="30" tabindex="1" autocomplete="off" />
					</div>
					<div class="inside">
						<?php if (SHOPP_PRETTYURLS && !empty($Product->id)): ?>
							<div id="edit-slug-box"><strong><?php _e('Permalink','Shopp'); ?>:</strong>
							<span id="sample-permalink"><?php echo $permalink; ?><span id="editable-slug" title=<?php _jse('Click to edit this part of the permalink','Shopp'); ?>><?php echo esc_attr($Product->slug); ?></span><span id="editable-slug-full"><?php echo esc_attr($Product->slug); ?></span><?php echo user_trailingslashit(""); ?></span>
							<span id="edit-slug-buttons"><button type="button" class="edit-slug button"><?php _e('Edit','Shopp'); ?></button><?php if ($Product->status == "publish"): ?><button id="view-product" type="button" class="view button"><?php _e('View','Shopp'); ?></button><?php endif; ?></span>
							</div>
						<?php else: ?>
							<?php if (!empty($Product->id)): ?>
							<div id="edit-slug-box"><strong><?php _e('Product ID','Shopp'); ?>:</strong>
							<span id="editable-slug"><?php echo $Product->id; ?></span>
							</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
				<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea">
				<?php the_editor($Product->description,'content','Description', false); ?>
				</div>

			<?php
			do_meta_boxes('shopp_page_shopp-products', 'normal', $Product);
			do_meta_boxes('shopp_page_shopp-products', 'advanced', $Product);
			?>

			</div>
			</div>

		</div> <!-- #poststuff -->
	</form>
</div>

<div id="publish-calendar" class="calendar"></div>

<script type="text/javascript">
/* <![CDATA[ */
var flashuploader = <?php echo ($uploader == 'flash' && !(false !== strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'mac') && apache_mod_loaded('mod_security')))?'true':'false'; ?>,
	product = <?php echo (!empty($Product->id))?$Product->id:'false'; ?>,
	prices = <?php echo json_encode($Product->prices) ?>,
	specs = <?php echo json_encode($Product->specs) ?>,
	options = <?php echo json_encode($Product->options) ?>,
	priceTypes = <?php echo json_encode($priceTypes) ?>,
	shiprates = <?php echo json_encode($shiprates); ?>,
	buttonrsrc = '<?php echo includes_url('images/upload.png'); ?>',
	uidir = '<?php echo SHOPP_ADMIN_URI; ?>',
	siteurl = '<?php echo $Shopp->siteurl; ?>',
	canonurl = '<?php echo trailingslashit(shoppurl()); ?>',
	adminurl = '<?php echo $Shopp->wpadminurl; ?>',
	ajaxurl = adminurl+'admin-ajax.php',
	sugg_url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), "wp_ajax_shopp_storage_suggestions"); ?>',
	spectemp_url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), "wp_ajax_shopp_spec_template"); ?>',
	opttemp_url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), "wp_ajax_shopp_options_template"); ?>',
	catmenu_url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), "wp_ajax_shopp_category_menu"); ?>',
	addcategory_url = '<?php echo wp_nonce_url($Shopp->wpadminurl."admin-ajax.php", "wp_ajax_shopp_add_category"); ?>',
	editslug_url = '<?php echo wp_nonce_url($Shopp->wpadminurl."admin-ajax.php", "wp_ajax_shopp_edit_slug"); ?>',
	fileverify_url = '<?php echo wp_nonce_url($Shopp->wpadminurl."admin-ajax.php", "wp_ajax_shopp_verify_file"); ?>',
	fileimport_url = '<?php echo wp_nonce_url($Shopp->wpadminurl."admin-ajax.php", "wp_ajax_shopp_import_file"); ?>',
	fileimportp_url = '<?php echo wp_nonce_url($Shopp->wpadminurl."admin-ajax.php", "wp_ajax_shopp_import_file_progress"); ?>',
	imageul_url = '<?php echo wp_nonce_url($Shopp->wpadminurl."admin-ajax.php", "wp_ajax_shopp_upload_image"); ?>',
	adminpage = '<?php echo $this->Admin->pagename('products'); ?>',
	request = <?php echo json_encode(stripslashes_deep($_GET)); ?>,
	worklist = <?php echo json_encode($this->products(true)); ?>,
	filesizeLimit = <?php echo wp_max_upload_size(); ?>,
	weightUnit = '<?php echo $this->Settings->get('weight_unit'); ?>',
	dimensionUnit = '<?php echo $this->Settings->get('dimension_unit'); ?>',
	storage = '<?php echo $this->Settings->get('product_storage'); ?>',
	productspath = '<?php /* realpath needed for relative paths */ chdir(WP_CONTENT_DIR); echo addslashes(trailingslashit(sanitize_path(realpath($this->Settings->get('products_path'))))); ?>',
	imageupload_debug = <?php echo (defined('SHOPP_IMAGEUPLOAD_DEBUG') && SHOPP_IMAGEUPLOAD_DEBUG)?'true':'false'; ?>,
	fileupload_debug = <?php echo (defined('SHOPP_FILEUPLOAD_DEBUG') && SHOPP_FILEUPLOAD_DEBUG)?'true':'false'; ?>,
	dimensionsRequired = <?php echo $Shopp->Shipping->dimensions?'true':'false'; ?>,
	startWeekday = <?php echo get_option('start_of_week'); ?>,
	calendarTitle = '<?php $df = date_format_order(true); $format = $df["month"]." ".$df["year"]; echo $format; ?>',

	// Warning/Error Dialogs
	DELETE_IMAGE_WARNING = <?php _jse('Are you sure you want to delete this product image?','Shopp'); ?>,
	SERVER_COMM_ERROR = <?php _jse('There was an error communicating with the server.','Shopp'); ?>,

	// Dynamic interface label translations
	LINK_ALL_VARIATIONS = <?php _jse('Link All Variations','Shopp'); ?>,
	UNLINK_ALL_VARIATIONS = <?php _jse('Unlink All Variations','Shopp'); ?>,
	LINK_VARIATIONS = <?php _jse('Link Variations','Shopp'); ?>,
	UNLINK_VARIATIONS = <?php _jse('Unlink Variations','Shopp'); ?>,
	ADD_IMAGE_BUTTON_TEXT = <?php _jse('Add New Image','Shopp'); ?>,
	UPLOAD_FILE_BUTTON_TEXT = <?php _jse('Upload&nbsp;File','Shopp'); ?>,
	SELECT_FILE_BUTTON_TEXT = <?php _jse('Select File','Shopp'); ?>,
	SAVE_BUTTON_TEXT = <?php _jse('Save','Shopp'); ?>,
	CANCEL_BUTTON_TEXT = <?php _jse('Cancel','Shopp'); ?>,
	TYPE_LABEL = <?php _jse('Type','Shopp'); ?>,
	PRICE_LABEL = <?php _jse('Price','Shopp'); ?>,
	AMOUNT_LABEL = <?php _jse('Amount','Shopp'); ?>,
	SALE_PRICE_LABEL = <?php _jse('Sale Price','Shopp'); ?>,
	NOT_ON_SALE_TEXT = <?php _jse('Not on Sale','Shopp'); ?>,
	NOTAX_LABEL = <?php _jse('Not Taxed','Shopp'); ?>,
	SHIPPING_LABEL = <?php _jse('Shipping','Shopp'); ?>,
	FREE_SHIPPING_TEXT = <?php _jse('Free Shipping','Shopp'); ?>,
	WEIGHT_LABEL = <?php _jse('Weight','Shopp'); ?>,
	LENGTH_LABEL = <?php _jse('Length','Shopp'); ?>,
	WIDTH_LABEL = <?php _jse('Width','Shopp'); ?>,
	HEIGHT_LABEL = <?php _jse('Height','Shopp'); ?>,
	DIMENSIONAL_WEIGHT_LABEL = <?php _jse('3D Weight','Shopp'); ?>,
	SHIPFEE_LABEL = <?php _jse('Handling Fee','Shopp'); ?>,
	SHIPFEE_XTRA = <?php _jse('Amount added to shipping costs for each unit ordered (for handling costs, etc)','Shopp'); ?>,
	INVENTORY_LABEL = <?php _jse('Inventory','Shopp'); ?>,
	NOT_TRACKED_TEXT = <?php _jse('Not Tracked','Shopp'); ?>,
	IN_STOCK_LABEL = <?php _jse('In Stock','Shopp'); ?>,
	OPTION_MENU_DEFAULT = <?php _jse('Option Menu','Shopp'); ?>,
	NEW_OPTION_DEFAULT = <?php _jse('New Option','Shopp'); ?>,
	ADDON_GROUP_DEFAULT = <?php _jse('Add-on Group','Shopp'); ?>,
	SKU_LABEL = <?php _jse('SKU','Shopp'); ?>,
	SKU_LABEL_HELP = <?php _jse('Stock Keeping Unit','Shopp'); ?>,
	SKU_XTRA = <?php _jse('Enter a unique stock keeping unit identification code.','Shopp'); ?>,
	DONATIONS_VAR_LABEL = <?php _jse('Accept variable amounts','Shopp'); ?>,
	DONATIONS_MIN_LABEL = <?php _jse('Amount required as minimum','Shopp'); ?>,
	PRODUCT_DOWNLOAD_LABEL = <?php _jse('Product Download','Shopp'); ?>,
	NO_PRODUCT_DOWNLOAD_TEXT = <?php _jse('No product download.','Shopp'); ?>,
	NO_DOWNLOAD = <?php _jse('No download file.','Shopp'); ?>,
	UNKNOWN_UPLOAD_ERROR = <?php _jse('An unknown error occurred. The upload could not be saved.','Shopp'); ?>,
	DEFAULT_PRICELINE_LABEL = <?php _jse('Price & Delivery','Shopp'); ?>,
	FILE_NOT_FOUND_TEXT = <?php _jse('The file you specified could not be found.','Shopp'); ?>,
	FILE_NOT_READ_TEXT = <?php _jse('The file you specified is not readable and cannot be used.','Shopp'); ?>,
	FILE_ISDIR_TEXT = <?php _jse('The file you specified is a directory and cannot be used.','Shopp'); ?>,
	FILE_UNKNOWN_IMPORT_ERROR = <?php _jse('An unknown error occured while attempting to attach the file.','Shopp'); ?>,
	IMAGE_DETAILS_TEXT = <?php _jse('Image Details','Shopp'); ?>,
	IMAGE_DETAILS_TITLE_LABEL = <?php _jse('Title','Shopp'); ?>,
	IMAGE_DETAILS_ALT_LABEL = <?php _jse('Alt','Shopp'); ?>,
	IMAGE_DETAILS_DONE = <?php _jse('OK','Shopp'); ?>,
	IMAGE_DETAILS_CROP_LABEL = <?php _jse('Cropped images','Shopp'); ?>;
/* ]]> */
</script>