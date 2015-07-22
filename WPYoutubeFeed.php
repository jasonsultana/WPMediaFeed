<?php
	/*
	Plugin Name: WPMediaFeed
	Plugin URI:  
	Description: A Media Feed plugin for Wordpress that can display a feed of your Youtube and / or SoundCloud account. 
				 Use [WPMediaFeed-Youtube] on any page or post where you want to load your Youtube feed and [WPMediaFeed-Soundcloud] on any page or post where
				 you want to load your SoundCloud feed. Both feeds can not be shown on the same page / post.

	Version:     0.1-alpha
	Author:      Jason Sultana
	Author URI:  https://wordpress.org/support/profile/kowboykoder
	Text Domain: 
	Domain Path: /
	 */

	function WPMediaFeed_init() {
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

	function WPMediaFeed_content_filter($content) {
		$url = "";
		$delimiter = "";
		$start = 0;
		$mode = "";
		$dest_url = "";

		//Some validation and filtering
		if(strpos($content, "[WPMediaFeed-Youtube]") !== false) {
			$content = str_replace("[WPMediaFeed-Youtube]", "", $content);
			$url = get_option('WPMediaFeed-Youtube-Channel');
			$delimiter = "watch?v=";
			$start = 1;	//For YT, skip the first record. It's garbage.
			$dest_url = "http://www.youtube.com/watch?v=";
			$mode = "youtube";
		}
		else if(strpos($content, "[WPMediaFeed-Soundcloud]") !== false) {
			$content = str_replace("[WPMediaFeed-Soundcloud]", "", $content);
			$url = get_option('WPMediaFeed-Soundcloud-Channel');
			$delimiter = '<a class="sound__coverArt" href="';
			$dest_url = "https://w.soundcloud.com/player/?url=" . $url;
			$mode = "soundcloud";
		}
		else {
			//Only get the feed if the shortcode is present.
			return $content;
		}

		if(!$url || $url == "")
			return $content;

		//Download the channel HTML
		require_once dirname(__FILE__) . '/HTTPRequest.class.php';
		$req = new HTTPRequest($url);
		$html = $req->DownloadToString();

		//Next, parse it to get the media IDs
		$mediaParts = explode($delimiter, $html);
		$mediaIds = array();

		//skip the first one - it's bogus
		for($i = $start; $i < sizeof($mediaParts); $i++) {
			$part = $mediaParts[$i];
			$mediaId = strstr($part, "\"", true);

			//echo "VideoId: $videoId , length: " . strlen($videoId) . "<br>";

			//Youtube videoIds should always be 11 chars long, but let's keep it open for future compatibility
			if( ($mode == "youtube" && strlen($mediaId) > 10 ) || $mode == "soundcloud") {
				if(!in_array($mediaId, $mediaIds)) {
					array_push($mediaIds, $mediaId);
				}
			}
		}

		//Finally, output the embed links to the unique videos
		foreach($mediaIds as $mediaId) {
			$content .= '[embed]' . $dest_url . $mediaId . '[/embed]';
		}

		return $content;
	}
	add_filter( 'the_content', 'WPMediaFeed_content_filter');