<?php if(shopp('category','hasproducts','load=prices,images')): ?>
	<div class="category">
	<?php shopp('catalog'); ?>
	<?php shopp('catalog','views','label=Views: '); ?><br \>
	<h2 class=page-title>
		<?php if (is_page('shop')): ?>
				<?php the_title(); ?>
			<?php else: ?>
				<?php shopp('category','name'); ?>
		<?php endif; ?>
		
	</h2>
	
	
	<p><?php shopp('category','subcategory-list','hierarchy=true&showall=true&class=subcategories&dropdown=1'); ?></p>
	
	<span><?php shopp('catalog','orderby-list','dropdown=on&title=Sort by'); ?></span>

	<div class="alignright"><?php shopp('category','pagination','show=10'); ?></div>
	

	<ul class="products">
		<div class="entry-meta"><p class="products-cat"><?php shopp('category','name'); ?></p></div>
		
		<li class="row"><ul>
		<?php while(shopp('category','products')): ?>
		<?php if(shopp('category','row')): ?></ul></li><li class="row"><ul><?php endif; ?>
			<li class="product">
				<div class="frame">
				<a href="<?php shopp('product','url'); ?>"><?php shopp('product','coverimage','width=125&height=188'); ?></a>
					<div class="details">
						
						<h4 class="name"><a href="<?php shopp('product','url'); ?>"><?php shopp('product','name'); ?></a></h4>
	<?php if(shopp('product','has-specs')): ?>
	
		<?php while(shopp('product','specs')): ?>
		<p><strong><?php shopp('product','spec','name'); ?>:</strong> <?php shopp('product','spec','content'); ?></p>
		<?php endwhile; ?>

	<?php endif; ?>
						
					<p class="price"><?php shopp('product','saleprice','starting=from'); ?> </p>
					<?php if (shopp('product','has-savings')): ?>
						<p class="savings">Save <?php shopp('product','savings','show=percent'); ?></p>
					<?php endif; ?>
					
						<div class="listview">
						<p><?php shopp('product','summary'); ?> ... <a href="<?php shopp('product','url'); ?>">[read more]</a></p>
						<form action="<?php shopp('cart','url'); ?>" method="post" class="shopp product" style="float: right;">
						
						</form>
						</div>
					</div>
					
				</div>
			</li>
		<?php endwhile; ?>
		</ul></li>
	</ul>
	
	<div class="alignright"><?php shopp('category','pagination'); ?></div>
	
	</div>
<?php else: ?>
	<?php if (!shopp('catalog','is-landing')): ?>
	<?php shopp('catalog','breadcrumb'); ?>
	<h3><?php shopp('category','name'); ?></h3>
	<p>No products were found.</p>
	<?php endif; ?>
<?php endif; ?>
