<?php get_header(); ?>

		<div id="content-container" class="pagearoni">
			<section id="content" class="mcontent" role="main">

			<?php get_template_part( 'loop', 'page' ); ?>

			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
