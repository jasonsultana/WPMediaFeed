<?php
/*
Plugin Name: WPYoutubeFeed
Plugin URI:  
Description: A Youtube Feed plugin for Wordpress.
Version:     0.1-alpha
Author:      Jason Sultana
Author URI:  https://wordpress.org/support/profile/kowboykoder
Text Domain: 
Domain Path: /
 */
	function autoload() {
		spl_autoload_register(function($class) {
			$dir = dirname(__FILE__) . '/core/' . $class . '.class.php';

			if (file_exists( $dir )) 
				require_once( $dir );
		});
	}
	autoload();

	function admin_render_settings() {
		require_once(dirname(__FILE__) . '/core/settings/load-plugin.php');
		require_once(dirname(__FILE__) . '/core/settings/options/settings_page.php');
	}
	admin_render_settings();

	function WPYoutubeFeed_content_filter($content) {
		return $content;
	}
	add_filter( 'the_content', 'WPYoutubeFeed_content_filter');