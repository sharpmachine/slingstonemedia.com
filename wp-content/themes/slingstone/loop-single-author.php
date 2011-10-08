<?php
/**
 * The loop that displays a single post for book authors.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 * This can be overridden in child themes with loop-single.php.
 *
 */
?>



<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="page-title">Slingstone Authors</h2>

					<div class="entry-content">
						<div class="author-headshot">
							<img src="<?php the_field('author_headshot'); ?>" alt="<?php the_title(); ?>" width="125" height="125" class="alignleft">
							<a href="<?php bloginfo('url'); ?>/contact"><img src="<?php bloginfo('template_directory'); ?>/images/speaking-request.png" width="117" height="18" alt="Speaking Request"></a>
							<a href="<?php bloginfo('url'); ?>/shop/tag/<?php 
		$split_name = explode(" ",the_title('','',false));
		echo $split_name[0];
		echo "+";
		echo $split_name[1];
?>"><img src="<?php bloginfo('template_directory'); ?>/images/books-by-author.png" width="129" height="18" alt="Books By Author"></a>
						</div>

				<h3 class="author-name-single"><?php the_title(); ?></h3>
				<?php the_content(); ?>
				<!--<h3 class="clear">Books by <?php the_title(); ?></h3>-->
					</div><!-- .entry-content -->
				</div><!-- #post-## -->

<?php endwhile; // end of the loop. ?>