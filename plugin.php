<?php

/*
  Plugin Name: Content Headings Tree
  Plugin URI: http://wordpress.org/extend/plugins/content-headings-tree/
  Description: Content Headings Tree Plugin enables a widgets which prints a tree list of the page content headings, which are links to the corresponding page headings.
  Version: 1.3.2
  Author: Konstantinos Kouratoras
  Author URI: http://www.kouratoras.gr
  Author Email: kouratoras@gmail.com
  License: GPL v2

  Copyright 2012 Konstantinos Kouratoras (kouratoras@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class ContentHeadingsList {
	
	/* -------------------------------------------------- */
	/* Constructor
	/*-------------------------------------------------- */
	public function __construct() {
		
		load_plugin_textdomain('content-headings-tree-locale', false, plugin_dir_path(__FILE__) . '/lang/');

		//Include libraries
		include( plugin_dir_path(__FILE__) . '/lib/tags.php' );
		include( plugin_dir_path(__FILE__) . '/lib/replaceTags.php' );
		
		// Register scripts
		add_action('wp_enqueue_scripts', array(&$this, 'register_plugin_scripts'));
		
		//Shortcode
		require_once( plugin_dir_path(__FILE__) . '/plugin-shortcode.php' );
		new ContentHeadingsListShortcode();
		
		//Widget
		require_once( plugin_dir_path(__FILE__) . '/plugin-widget.php' );
		add_action('widgets_init', create_function('', 'register_widget("ContentHeadingsListWidget");'));
	}

	/* -------------------------------------------------- */
	/* Registers and enqueues scripts.
	/* -------------------------------------------------- */
	public function register_plugin_scripts() {

		wp_deregister_script('jquery');
		wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"), true);
		wp_enqueue_script('jquery');

		wp_register_script('content-headings-tree-widget-script', plugins_url('content-headings-tree/js/widget.js'));
		wp_enqueue_script('content-headings-tree-widget-script');
	}

}

new ContentHeadingsList();

?>
