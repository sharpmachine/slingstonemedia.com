<div class="wrap shopp">
	<?php if (!empty($updated)): ?><div id="message" class="updated fade"><p><?php echo $updated; ?></p></div><?php endif; ?>

	<div class="icon32"></div>
	<h2><?php _e('Presentation Settings','Shopp'); ?></h2>

	<form name="settings" id="presentation" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
		<?php wp_nonce_field('shopp-settings-presentation'); ?>

		<?php include("navigation.php"); ?>

		<table class="form-table">
			<tr>
				<th scope="row" valign="top"><label for="theme-templates"><?php _e('Theme Templates','Shopp'); ?></label></th>
				<td>
				<?php switch ($status) {
					case "directory":?>
					<input type="button" name="template_instructions" id="show-instructions" value="<?php _e('Use Custom Templates','Shopp'); ?>" class="button-secondary" />
					<div id="template-instructions">
					<p><?php _e('To customize shopping templates for your current WordPress theme:','Shopp'); ?></p>
					<ol>
						<li><?php _e('Create a directory in your active theme named <code>shopp</code>','Shopp'); ?></li>
						<li><?php _e('Give your web server access to write to the <code>shopp</code> directory','Shopp'); ?></li>
						<li><?php _e('Refresh this page for more instructions','Shopp'); ?></li>
					</ol>
					<p><a href="<?php echo SHOPP_DOCS; ?>Setting_Up_Theme_Templates" target="_blank"><?php _e('More help setting up theme templates','Shopp'); ?></a></p>
					</div>
						<?php
						break;
					case "permissions":?>
					<p><?php _e('The <code>shopp</code> directory exists in your current WordPress theme, but is not writable.','Shopp'); ?></p>
					<p><?php _e('You need to give <code>write</code> permissions to the <code>shopp</code> directory to continue.','Shopp'); ?> (<a href="<?php echo SHOPP_DOCS; ?>Giving_the_Web_Server_Write_Permisssions" target="_blank"><?php _e('Giving the Web Server Write Permisssions','Shopp'); ?></a>)</p>
						<?php
						break;
					case "incomplete":?>
						<input type="submit" name="install" value="<?php _e('Reinstall Missing Templates','Shopp'); ?>" class="button-secondary" /><br />
						<p><?php _e('Some of the shopping templates for your current theme are missing.','Shopp'); ?></p>
						<?php
						break;
					case "ready":?>
						<input type="submit" name="install" value="<?php _e('Install Theme Templates','Shopp'); ?>" class="button-secondary" /><br />
						<p><?php _e('Click this button to copy Shopp\'s builtin templates into your theme as a starting point for customization.','Shopp'); ?></p>
						<?php
						break;
					default:?>
					<input type="hidden" name="settings[theme_templates]" value="off" /><input type="checkbox" name="settings[theme_templates]" value="on" id="theme-templates"<?php if ($this->Settings->get('theme_templates') != "off") echo ' checked="checked"'?> /><label for="theme-templates"> <?php _e('Enable theme templates','Shopp'); ?></label><br />
					<?php _e('Check this to use the templates installed in your currently active WordPress theme.','Shopp'); ?>
						<?php
				}
				?>
	            </td>
			</tr>
			<tr>
				<th scope="row" valign="top"><label for="outofstock-catalog"><?php _e('Catalog Inventory','Shopp'); ?></label></th>
				<td><input type="hidden" name="settings[outofstock_catalog]" value="off" /><input type="checkbox" name="settings[outofstock_catalog]" value="on" id="outofstock-catalog"<?php if ($this->Settings->get('outofstock_catalog') == "on") echo ' checked="checked"'?> /><label for="outofstock-catalog"> <?php _e('Show out-of-stock products in the catalog','Shopp'); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row" valign="top"><label for="default-catalog-view"><?php _e('Catalog View','Shopp'); ?></label></th>
				<td><select name="settings[default_catalog_view]" id="default-catalog-view">
					<?php echo menuoptions($category_views,$this->Settings->get('default_catalog_view'),true); ?>
				</select></td>
			</tr>
			<tr>
				<th scope="row" valign="top"><label for="row-products"><?php _e('Grid Rows','Shopp'); ?></label></th>
				<td><select name="settings[row_products]" id="row-products">
					<?php echo menuoptions($row_products,$this->Settings->get('row_products')); ?>
				</select>
	            <label for="row-products"><?php _e('products per row','Shopp'); ?></label></td>
			</tr>
			<tr>
				<th scope="row" valign="top"><label for="catalog-pagination"><?php _e('Pagination','Shopp'); ?></label></th>
				<td><input type="text" name="settings[catalog_pagination]" id="catalog-pagination" value="<?php echo esc_attr($this->Settings->get('catalog_pagination')); ?>" size="4" class="selectall" />
	            <label for="catalog-pagination"><?php _e('products per page','Shopp'); ?></label></td>
			</tr>
			<tr>
				<th scope="row" valign="top"><label for="product-order"><?php _e('Product Order','Shopp'); ?></label></th>
				<td><select name="settings[default_product_order]" id="product-order">
					<?php echo menuoptions($productOrderOptions,$this->Settings->get('default_product_order'),true); ?>
				</select>
				<br />
	            <?php _e('Set the default display order of products shown in categories.','Shopp'); ?></td>
			</tr>
			<tr>
				<th scope="row" valign="top"><label for="showcase-order"><?php _e('Image Order','Shopp'); ?></label></th>
				<td><select name="settings[product_image_order]" id="showcase-order">
					<?php echo menuoptions($orderOptions,$this->Settings->get('product_image_order'),true); ?>
				</select> by
				<select name="settings[product_image_orderby]" id="showcase-orderby">
					<?php echo menuoptions($orderBy,$this->Settings->get('product_image_orderby'),true); ?>
				</select>
				<br />
	            <?php _e('Set how to organize the presentation of product images.','Shopp'); ?></td>
			</tr>
		</table>

		<p class="submit"><input type="submit" class="button-primary" name="save" value="<?php _e('Save Changes','Shopp'); ?>" /></p>
	</form>
</div>

<script type="text/javascript">
/* <![CDATA[ */
(function($){
	$('#template-instructions').hide();
	$('#show-instructions').click(function () {
		$('#template-instructions').slideToggle(500);
	});
})(jQuery);
/* ]]> */
</script>