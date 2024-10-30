<?php

class ContentHeadingsListWidget extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct(
			'content-headings-tree-id', __('Content Headings Tree', 'content-headings-tree-locale'), array(
		    'classname' => 'ContentHeadingsListWidget',
		    'description' => __('This widget creates a tree list of content headings.', 'content-headings-tree-locale')
			)
		);
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @args			The array of form elements
	 * @instance		The current instance of the widget
	 */
	public function widget($args, $instance) {

		extract($args, EXTR_SKIP);

		$content = get_the_content();

		$nodes = extract_tags($content, 'h\d+', false);

		$headingsList = "";

		$currentHeading = 0;
		$previousHeading = 0;

		$level = 0;

		if ($instance['level'])
			$level = substr($instance['level'], 1, 2);

		if ($level) {
			foreach ($nodes as $key => $node) {
				if (substr($node['tag_name'], 1, 2) > $level)
					unset($nodes[$key]);
			}
		}

		foreach ($nodes as $node) {

			$previousHeading = $currentHeading;
			$currentHeading = substr($node['tag_name'], 1, 2);

			if ($currentHeading < $previousHeading) {
				for ($i = 0; $i < $previousHeading - $currentHeading; $i++) {
					$headingsList .= '</ul>';
				}
				$headingsList .= '</li>';
			}

			if ($currentHeading > $previousHeading) {
				$headingsList .= '<ul>';
			}

			$headingsList .= '<li><a href="#';
			$headingsList .= str_replace(" ", "_", strip_tags($node['contents']));
			$headingsList .= '">';
			if ($instance['hchars'])
				$headingsList .= mb_substr(strip_tags($node['contents']), 0, $instance['hchars']);
			else
				$headingsList .= strip_tags($node['contents']);
			$headingsList .= '</a>';
		}
		
		$headingsList .= '</li>';
		$headingsList .= '</ul>';

		if (count($nodes) && (!$instance['posts_or_pages'] || ($instance['posts_or_pages'] && (is_single() || is_page())))) {

			echo "<div class=\"content-headings-tree";
			
			if ($instance['sticky'] == '1')
				echo ' content-headings-tree-sticky';

			echo "\">";
			
			echo $before_widget;
			if (strlen($instance['title']) > 0) {
				echo $before_title . $instance['title'] . $after_title;
			}
			echo $headingsList;
			echo $after_widget;
			
			"</div>";
		}
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @new_instance	The previous instance of values before the update.
	 * @old_instance	The new instance of values to be generated via the update.
	 */
	public function update($new_instance, $old_instance) {

		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['level'] = strip_tags($new_instance['level']);
		$instance['hchars'] = strip_tags($new_instance['hchars']);
		$instance['posts_or_pages'] = strip_tags($new_instance['posts_or_pages']);
		$instance['sticky'] = strip_tags($new_instance['sticky']);

		return $instance;
	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @instance	The array of keys and values for the widget.
	 */
	public function form($instance) {

		$instance = wp_parse_args(
			(array) $instance, array(
		    'title' => __('Content Headings Tree', 'content-headings-tree-locale'),
		    'level' => 'h6',
		    'hchars' => '20',
		    'posts_or_pages' => '1',
		    'sticky' => '0'
			)
		);
		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'content-headings-tree-locale') ?></label>
			<br/>
			<input type="text" class="regular-text" value="<?php echo esc_attr($instance['title']); ?>" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('hchars'); ?>"><?php _e('Headings title characters:', 'content-headings-tree-locale') ?></label>
			<br/>
			<input class="small-text" type="number" step="1" min="0" value="<?php echo esc_attr($instance['hchars']); ?>" id="<?php echo $this->get_field_id('hchars'); ?>" name="<?php echo $this->get_field_name('hchars'); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('level'); ?>"><?php _e('Headings nesting level:', 'content-headings-tree-locale') ?></label> 
			<select class="feature" id="<?php echo $this->get_field_id('level'); ?>" name="<?php echo $this->get_field_name('level'); ?>" style="width:100%;">
				<option <?php if ($instance["level"] == "h1") echo 'selected="selected"'; ?> value="h1">H1</option>
				<option <?php if ($instance["level"] == "h2") echo 'selected="selected"'; ?> value="h2">H2</option>
				<option <?php if ($instance["level"] == "h3") echo 'selected="selected"'; ?> value="h3">H3</option>
				<option <?php if ($instance["level"] == "h4") echo 'selected="selected"'; ?> value="h4">H4</option>
				<option <?php if ($instance["level"] == "h5") echo 'selected="selected"'; ?> value="h5">H5</option>
				<option <?php if ($instance["level"] == "h6") echo 'selected="selected"'; ?> value="h6">H6</option>
			</select>
		</p>

		<p>
			<input type="checkbox" <?php if ($instance["posts_or_pages"] == "1") echo 'checked="checked"'; ?> value="1" id="<?php echo $this->get_field_id('posts_or_pages'); ?>" name="<?php echo $this->get_field_name('posts_or_pages'); ?>" />
			<label for="<?php echo $this->get_field_id('posts_or_pages'); ?>"><?php _e('Display only in posts and pages', 'content-headings-tree-locale') ?></label>

		</p>

		<p>
			<input type="checkbox" <?php if ($instance["sticky"] == "1") echo 'checked="checked"'; ?> value="1" id="<?php echo $this->get_field_id('sticky'); ?>" name="<?php echo $this->get_field_name('sticky'); ?>" />
			<label for="<?php echo $this->get_field_id('sticky'); ?>"><?php _e('Sticky widget', 'content-headings-tree-locale') ?></label>
		</p>

		<?php
	}

}
?>