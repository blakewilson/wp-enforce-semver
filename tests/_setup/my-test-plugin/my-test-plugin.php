<?php
/**
Plugin Name: My Test Plugin
Description: This plugin is used to test the wp-enforce-semver class script
Version: 1.0.0
*/

// Init autoloader from Composer
if ( file_exists( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' ) ) {
	require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
}

use EnforceSemVer\EnforceSemVer;

new EnforceSemVer('my-test-plugin/my-test-plugin.php');

function mock_plugin_update($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    $plugin_file = 'my-test-plugin/my-test-plugin.php';
    $new_version = get_transient('my_test_plugin_version') ?: '2.0.0';

    $update_data = (object) [
        'slug'        => 'my-test-plugin',
        'new_version' => $new_version,
        'url'         => 'https://example.com/my-custom-plugin-changelog', // URL to the plugin changelog
        'package'     => 'https://example.com/my-custom-plugin-v2.0.0.zip', // URL to the new version zip file
    ];
    
    $transient->response[$plugin_file] = $update_data;

    return $transient;
}

add_filter('site_transient_update_plugins', 'mock_plugin_update');
