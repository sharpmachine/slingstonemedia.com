<?php get_header(); ?>

		<div id="content-container" class="pagearoni">
			<section id="content" role="main">
				
				

			<?php if (have_posts()) : ?>
			
	<?php while (have_posts()) : the_post(); ?>
			
		<?php the_title(); ?>
			
	<?php endwhile; ?>
			
		<?php // Navigation ?>
			
	<?php else : ?>
			
		<?php // No Posts Found ?>
			
<?php endif; ?>

			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
