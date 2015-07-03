<?php
	/*
	Plugin Name: WPYoutubeFeed
	Plugin URI:  
	Description: A Youtube Feed plugin for Wordpress. Use [WPYoutubeFeed] on any page or post where you want to load your Youtube feed.
	Version:     0.1-alpha
	Author:      Jason Sultana
	Author URI:  https://wordpress.org/support/profile/kowboykoder
	Text Domain: 
	Domain Path: /
	 */

	function WPYoutubeFeed_init() {
		defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
	
		spl_autoload_register(function($class) {
			$dir = dirname(__FILE__) . $class . '.class.php';

			if (file_exists( $dir )) 
				require_once( $dir );
		});

		$pluginDir = plugin_dir_path( dirname(__FILE__) );
		$settingsDir = $pluginDir . 'Wordpress-Settings-Library';

		if(file_exists($settingsDir)) {
			require_once($settingsDir . '/settings/load-plugin.php');
			require_once(dirname(__FILE__) . '/init-settings.php');
		}
		else {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			deactivate_plugins(__FILE__);

			if(is_admin()) 
				die("Wordpress Settings library not found in: $settingsDir. Please upload it here to use this plugin");
		}
	}
	WPYoutubeFeed_init();

	function WPYoutubeFeed_content_filter($content) {
		$url = get_option('WPYoutubeFeed_channel');

		if(!$url || $url == "")
			return $content;

		//Only get the feed if the shortcode is present.
		if(strpos($content, "[WPYoutubeFeed]") === false)
			return $content;

		$content = str_replace("[WPYoutubeFeed]", "", $content);

		//First, download the channel HTML from youtube
		require_once dirname(__FILE__) . '/HTTPRequest.class.php';
		$req = new HTTPRequest($url);
		$html = $req->DownloadToString();

		//Next, parse it to get the video IDs
		$videoParts = explode("watch?v=", $html);
		$videoIds = array();

		//skip the first one - it's bogus
		for($i = 1; $i < sizeof($videoParts); $i++) {
			$part = $videoParts[$i];
			$videoId = strstr($part, "\"", true);

			//echo "VideoId: $videoId , length: " . strlen($videoId) . "<br>";

			//Youtube videoIds should always be 11 chars long, but let's keep it open for future compatibility
			if( strlen($videoId) > 10 ) {
				if(!in_array($videoId, $videoIds)) {
					array_push($videoIds, $videoId);
				}
			}
		}
		$margin = get_option('WPYoutubeFeed_margin');

		//Finally, output the embed links to the unique videos
		foreach($videoIds as $videoId) {
			$content .= '<iframe style = "margin-top: ' . $margin . 'px;" width="560" height="315" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allowfullscreen></iframe>';
		}

		return $content;
	}
	add_filter( 'the_content', 'WPYoutubeFeed_content_filter');