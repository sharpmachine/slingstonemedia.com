<?php get_header(); ?>

	<div id="content-container" class="full-width">
		<section id="content" class="mcontent" role="main">

			<div id="post-0" class="post error404 not-found">
				
				<div class="entry-content">
					<h1 class="entry-title"><?php _e( 'Not Found', 'twentyten' ); ?></h1>
					<p><?php _e( 'Apologies, but the page you requested could not be found. Perhaps searching will help.', 'twentyten' ); ?></p>
					<?php get_search_form(); ?>
				</div><!-- .entry-content -->
			</div><!-- #post-0 -->

		</section><!-- #content -->
	</div><!-- #content-container -->
	<script type="text/javascript">
		// focus on search field after it has loaded
		document.getElementById('s') && document.getElementById('s').focus();
	</script>

<?php get_footer(); ?>