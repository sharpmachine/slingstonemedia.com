<?php get_header(); ?>

		<div id="content-container"  class="pagearoni">
			<section id="content" role="main" class="mcontent">

				<h1 class="page-title author"><?php
					printf( __( 'Tag Archives: %s', 'twentyten' ), '<span>' . single_tag_title( '', false ) . '</span>' );
				?></h1>

	<?php get_template_part( 'loop', 'tag' ); ?>
			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
