<?php namespace PaintCloud\WP\Settings;

$page = new Page('WPYoutubeFeed', array('type' => 'settings'));

$settings = array();

// Section One
// ------------------------//
$settings['General'] = array('info' => 'General Settings');

$fields = array();
$fields[] = array(
	'type' 	=> 'text',
	'name' 	=> 'WPYoutubeFeed_channel',
	'label' => 'Channel URL'
);

$fields[] = array(
	'type' => 'text',
	'name' => 'WPYoutubeFeed_margin',
	'label' => 'Video top margin (px)'
);

add_option('WPYoutubeFeed_margin', 0);

$settings['General']['fields'] = $fields;

new OptionPageBuilderSingle($page, $settings);