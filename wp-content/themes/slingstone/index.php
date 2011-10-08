<?php get_header(); ?>

		<div id="content-container">
			<section id="content" role="main">
				<div id="banner-container">
					<div id="banner">
							<div id="categories">
								<ul>
									<li><a href="#" class="banner"></a>
										<?php if ( is_active_sidebar( 'banner-widget-area' ) ) : ?>
										<ul>
											<?php dynamic_sidebar( 'banner-widget-area' ); ?>
										</ul>
									</li>
								</ul>
										<?php endif; ?>
									</li>
								</ul>
							</div><!-- #categories -->
	
							<div class="slides_container">

								<?php query_posts('post_type=banner_ads')?>
									<?php if (have_posts()) : ?>
										<?php while (have_posts()) : the_post(); ?>
											<?php if (get_field('banner_image_external_url')): ?>
												<a href="<?php the_field('banner_image_external_url'); ?>">
											<?php else: ?>
												<a href="<?php the_field('banner_ad_internal_url'); ?>">
											<?php endif; ?>
										<img src="<?php the_field('banner_image'); ?>" width="960" height="300" alt="<?php the_title(); ?>" /></a>
									<?php endwhile; endif; ?>
							</div><!-- .slides_container -->
					</div><!-- #banner -->
				</div><!-- #banner-container -->
				
				<div class="center-content">
		
				<div id="recent-articles">
					<h3>Recent Articles</h3>
				<?php if (have_posts()) : ?>
			<?php query_posts('posts_per_page=3'); ?>	
	<?php while (have_posts()) : the_post(); ?>
				
		<article>
			<div class="frontpage-article">
				
				<h2>
					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">	
						<?php get_short_title(); ?>		
					</a>
			</h2>
				<p><?php echo get_home_page_excerpt(); ?></p>
			</div>
		</article>
		
	<?php endwhile; ?>
		<h6><a href="<?php bloginfo('url'); ?>/blog">View all posts</a></h6>
	<?php else : ?>
				
		<article>
			<h2>No Posts Yet</h2>
			<p>check back soon...</p>
		</article>
				
<?php endif; ?>
				</div><!-- #recent-articles -->
				
				<div id="newsletter-signup">
					<h3>Newsletter</h3>

					<form action="http://slingstonemedia.us2.list-manage1.com/subscribe/post?u=1de3202d56f09279dd00d4bc7&amp;id=54982bf3bd" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
						<p>Sign up here to receive updates, tips on great resources, and special offers</p>
						<input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" placeholder="you@something.com">
						<input type="submit" value="Sign Up" name="subscribe" id="mc-embedded-subscribe" class="btn">
						<img src="<?php bloginfo('template_directory'); ?>/images/letter.png" width="212" height="81" alt="Letter">

					</form>
				</div>
		
			</div><!-- .center-content -->
			
			
			</section><!-- #content -->
		</div><!-- #content-container -->


<?php get_footer(); ?>
