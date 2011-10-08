<hr>
			
<div id="carousel">
	<div class="slides_container">
	<?php query_posts('post_type=scroller_ads'); ?>
	<?php $i = 0; ?>
	<div class="slide">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		
		<div class="item">
			<?php if(get_field('scroller_ad_external_url')): ?>
				<a href="<?php the_field('scroller_ad_external_url'); ?>">
			<?php else: ?>
				<a href="<?php the_field('scroller_ad_internal_url'); ?>">
			<?php endif; ?>
					<img src="<?php the_field('scoller_image'); ?>" width="171" height="96" alt="<?php the_title(); ?>" />
				</a>
		</div><!-- .item -->
		
		<?php
			/*
				Loop through till match of 5 then reset to 0.
			*/
			if ($i == 4) 
			{
				print '</div><div class="slide">';
				$i = 0;
			} else {
				$i++;
			}
		
		?>
		
		<?php endwhile; ?>
				
	<?php endif; ?>
	</div>
		
	</div><!-- .slide_container -->
	<div class="scroll-inst ls">[ Use left &amp; right arrows to view more ]</div>
</div><!-- #carousel -->			
			

	</section><!-- #page -->
</div><!-- .container -->

<div id="footer-bar">
	<footer role="contentinfo">
		<nav id="footer-menu">
			<?php wp_nav_menu( array( 'container_class' => 'footer-nav', 'theme_location' => 'footer' ) ); ?>
		</nav>

<?php get_sidebar( 'footer' ); ?>

			<div id="site-info">
				&copy;<?php echo date ('Y'); ?><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a>
			</div><!-- #site-info -->
	</footer>
</div><!-- #footer-bar -->
  



<!-- load the javascript api from google-->
<?php if (is_home()): ?>
<script src="http://www.google.com/jsapi" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
//  Load jQuery
google.load("jquery", "1.6.1");
google.load("jqueryui", "1.8.15");
</script> 
	<?php endif; ?>
<?php wp_footer(); ?>
<script src="<?php bloginfo('template_directory'); ?>/js/slides.min.jquery.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/script.js"></script>

</body>
</html>
