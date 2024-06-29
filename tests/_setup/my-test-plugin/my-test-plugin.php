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
