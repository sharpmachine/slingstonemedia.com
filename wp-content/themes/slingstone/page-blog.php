<?php get_header(); ?>

		<div id="content-container" class="pagearoni">
			<section id="content" class="mcontent" role="main">
				
				<?php $category_count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->term_taxonomy WHERE taxonomy = 'category' AND parent = '0' ");?>

				<h2><?php echo $category_count; ?> Flavors to Choose from:</h2>
				
				<?php get_template_part( 'loop', 'page' ); ?>
			<?php wp_reset_query(); ?>

			<?php get_cat_images_descripts(); ?>
<hr>
			
		<?php
		$temp = $wp_query;
		$wp_query= null;
		$wp_query = new WP_Query();
		$wp_query->query('&paged='.$paged);
		while ($wp_query->have_posts()) : $wp_query->the_post();
		?>
		
		<div class="listing">
			<h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title();  ?></a></h3>
			<div class="entry-meta">
					<?php twentyten_posted_on(); ?> - <?php twentyten_posted_in(); ?>
				</div><!-- .entry-meta -->
			
			<?php the_excerpt(); ?>

		</div>
	<?php endwhile; ?>
	
			<?php if (  $wp_query->max_num_pages > 1 ) : ?>
							<?php if(function_exists('wp_paginate')) {
			    wp_paginate();
			} ?>
			<?php endif; ?>

			
		<?php $wp_query = null; $wp_query = $temp;?>

			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
