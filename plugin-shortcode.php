<?php

class ContentHeadingsListShortcode {
	
	private $title;
	private $size;
	private $level;
	
	/**
	 * Constructor
	 */
	public function __construct() {

		add_action('init', array(&$this, 'register_content_headings_list_shortcode'));
	}
	
	/**
	 * Registers the shortcode
	 */
	function register_content_headings_list_shortcode() {
		add_shortcode('headings', array(&$this, 'content_headings_list_shortcode'));
	}

	/**
	 * Creates the shortcode
	 */
	function content_headings_list_shortcode($atts) {
		
		$this->title = (isset($atts['title'])) ? $atts['title'] : __('Content Headings Tree', 'content-headings-tree-locale');
		$this->size = (isset($atts['size'])) ? $atts['size'] : 20;
		$this->level = (isset($atts['level'])) ? $atts['level'] : '6';
		
		return $this->content_headings_list();
	}
	
	
	/**
	 * Outputs the list
	 */
	function content_headings_list() {
		
		$return_string = "";

		$content = get_the_content();
		$nodes = extract_tags($content, 'h\d+', false);

		$headingsList = "";

		$currentHeading = 0;
		$previousHeading = 0;

		$level = 0;

		if ($this->level)
			$level = $this->level;
		
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
			if ($this->size)
				$headingsList .= mb_substr(strip_tags($node['contents']), 0, $this->size);
			else
				$headingsList .= strip_tags($node['contents']);
			$headingsList .= '</a>';
		}
		
		$headingsList .= '</li>';
		$headingsList .= '</ul>';
		
		if (count($nodes)) {
			$return_string .= "<div class=\"content-headings-tree\">";
			if (strlen($this->title) > 0) {
				$return_string .= $this->title;
			}
			$return_string .= $headingsList;
			$return_string .= "</div>";
		}
		
		return $return_string;
	}
}

?>
