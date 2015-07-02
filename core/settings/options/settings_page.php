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

$settings['General']['fields'] = $fields;

new OptionPageBuilderSingle($page, $settings);