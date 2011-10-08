<?php 
/*
* This is the template for the authors custom post type.  
*/
get_header(); ?>

		<div id="content-container" class="pagearoni">
			<section id="content" class="mcontent" role="main">

<?php
	if ( have_posts() )
		the_post();
?>

			<h2 class="page-title">Slingstone Authors</h2>

<?php
	/* Since we called the_post() above, we need to
	 * rewind the loop back to the beginning that way
	 * we can run the loop properly, in full.
	 */
	rewind_posts();

	/* Run the loop for the archives page to output the posts.
	 * If you want to overload this in a child theme then include a file
	 * called loop-archive.php and that will be used instead.
	 */
	 get_template_part( 'loop', 'authors' );
?>

			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
