<?php shopp('catalog')?>
<?php if (shopp('product','found')): ?>

<?php
$slug_hook = shopp('product','slug', 'return=true');
query_posts("post_type=product_extras&product_titles=$slug_hook");
?>
 
<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
	<?php endwhile; endif; ?>

	<div class="product-image">

		<?php if (get_field('upload_pdf_excerpt')): ?>
		
		<a href="<?php echo get_field('upload_pdf_excerpt')?>" title="Read Excerpt" target="_blank"><img src="<?php bloginfo('template_directory'); ?>/images/book-corner.png" width="98" height="117" alt="Book Corner" class="book-corner"></a>
		
		<?php else: ?>
			
			<img src="<?php bloginfo('template_directory'); ?>/images/book-corner-ph.png" width="98" height="117" alt="Book Corner" class="book-corner">
		
		<?php endif; ?>
		
			<?php shopp('product','gallery', 'p.width=180&p.height=270'); ?>
			
		<div class="pi-tools">
			
			<a href="<?php shopp('product','coverimage', 'size=500&property=url'); ?>" class="enlarge-image shopp-zoom"><img src="<?php bloginfo('template_directory'); ?>/images/enlarge-image.png" width="98" height="16" alt="Enlarge Image"></a>
			
			<?php if(get_field('upload_audio_preview')): ?>
			<a href="<?php the_field('upload_audio_preview')?>" class="preview-audio" target="_blank"><img src="<?php bloginfo('template_directory'); ?>/images/preview-audio.png" width="96" height="14" alt="Preview Audio"></a>
			<?php endif; ?>
			
			
			<?php if (get_field('upload_pdf_excerpt')): ?>
			<a href="<?php echo get_field('upload_pdf_excerpt')?>" class="read-excerpt" target="_blank"><img src="<?php bloginfo('template_directory'); ?>/images/read-excerpt.png" width="97" height="10" alt="Read Excerpt"></a>
		<?php endif; ?>
		</div>
	</div>

	<h3><?php shopp('product','name'); ?></h3>
	<?php if(shopp('product','has-specs')): ?>
	<dl class="details">
		<?php while(shopp('product','specs')): ?>
		<dt><?php shopp('product','spec','name'); ?>:</dt><dd><?php shopp('product','spec','content'); ?></dd>
		<?php endwhile; ?>
	</dl>
	<?php endif; ?>
	

	<?php if (shopp('product','onsale')): ?>
		<h3 class="original price"><?php shopp('product','price'); ?></h3>
		<h3 class="sale price"><?php shopp('product','saleprice'); ?></h3>
		<?php if (shopp('product','has-savings')): ?>
			<p class="savings">You save <?php shopp('product','savings'); ?> (<?php shopp('product','savings','show=%'); ?>)!</p>
		<?php endif; ?>
	<?php else: ?>
		<p class="price"><span>Price:</span> <?php shopp('product','price'); ?></p>
	<?php endif; ?>
	
	<?php if (shopp('product','freeshipping')): ?>
	<p class="freeshipping">Free Shipping!</p>
	<?php endif; ?>
	
	<form action="<?php shopp('cart','url'); ?>" method="post" class="shopp product validate">
		<?php if(shopp('product','has-variations')): ?>
		<ul class="variations">
			
			<?php shopp('product','variations','mode=single&label=true&defaults=Select a format&before_menu=<li>&after_menu=</li>'); ?>
		</ul>
		<?php endif; ?>
		<?php if(shopp('product','has-addons')): ?>
			<ul class="addons">
				<?php shopp('product','addons','mode=menu&label=true&defaults=Select an add-on&before_menu=<li>&after_menu=</li>'); ?>
			</ul>
		<?php endif; ?>
				
		<p><?php shopp('product','quantity','class=selectall&input=menu'); ?>
		<?php shopp('product','addtocart'); ?></p>

	</form>

	<div class="product-description clear">
		<h4>About this Item</h4>
		<?php shopp('product','description'); ?>
	</div>
	

<?php else: ?>
<h3>Product Not Found</h3>
<p>Sorry! The product you requested is not found in our catalog!</p>
<?php endif; ?>
