<?php
/**
 * ShoppTagCloudWidget class
 * A WordPress widget that shows a cloud of the most popular product tags
 *
 * @author Jonathan Davis
 * @version 1.0
 * @copyright Ingenesis Limited, 8 June, 2009
 * @package shopp
 **/

if ( class_exists('WP_Widget') && ! class_exists('ShoppTagCloudWidget') ) {

class ShoppTagCloudWidget extends WP_Widget {

    function ShoppTagCloudWidget() {
        parent::WP_Widget(false,
			$name = __('Shopp Tag Cloud','Shopp'),
			array('description' => __('Popular product tags in a cloud format','Shopp'))
		);
    }

    function widget($args, $options) {
		global $Shopp;
		if (!empty($args)) extract($args);

		if (empty($options['title'])) $options['title'] = "Product Tags";
		$title = $before_title.$options['title'].$after_title;

		$tagcloud = $Shopp->Catalog->tag('tagcloud',$options);
		echo $before_widget.$title.$tagcloud.$after_widget;
    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    function form($options) {
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?></label>
		<input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" class="widefat" value="<?php echo $options['title']; ?>"></p>
		<?php
    }

} // class ShoppTagCloudWidget

register_widget('ShoppTagCloudWidget');

}
?>