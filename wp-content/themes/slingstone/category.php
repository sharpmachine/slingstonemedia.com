<?php get_header(); ?>

		<div id="content-container" class="pagearoni">
			<section id="content" class="mcontent" role="main">

				<h1 class="page-title cat-title"><?php
					printf( __( '%s', 'twentyten' ), '<span>' . single_cat_title( '', false ) . '</span>' );
				?></h1>
				<h6>
					<?php get_cat_image(); ?>
				</h6>

				<?php
						$category_description = category_description();
						if ( ! empty( $category_description ) )
							echo '<div class="archive-meta">' . $category_description . '</div>'; ?>
				<hr>
	
				
				<div class="listing">
					<?php get_template_part( 'loop', 'category' ); ?>
				</div>
			</section><!-- #content -->
		</div><!-- #content-container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
