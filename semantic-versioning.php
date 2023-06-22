<?php
/**
 * Plugin Name: Semantic Versioning
 * Plugin URI: https://blake.id
 * Description: A plugin to warn and protect against auto updates for WordPress plugins that use Semantic Versioning
 * Version: 1.0.0
 * Author: Blake Wilson
 * Author URI: https://blake.id
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: semantic-versioning
 * Semantic Versioning: true
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SEMANTIC_VERSIONING_PLUGIN_HEADER', 'Semantic Versioning' );

require_once 'vendor/autoload.php';

use PHLAK\SemVer;
use PHPHtmlParser\Dom;

/**
 * Adds the Semantic Versioning header to plugins.
 */
function add_semver_plugin_header( $headers ) {
	if ( ! in_array( SEMANTIC_VERSIONING_PLUGIN_HEADER, $headers ) ) {
		$headers[] = SEMANTIC_VERSIONING_PLUGIN_HEADER;
	}

	return $headers;
}

add_filter( 'extra_plugin_headers', 'add_semver_plugin_header' );

function has_breaking_changes( $new_version, $current_version ) {
	$_current_version = SemVer\Version::parse( $current_version );
	$_new_version     = SemVer\Version::parse( $new_version );

	return $_new_version->major > $_current_version->major;
}

function disable_auto_updates_for_major_versions( $update, $item ) {
	if ( ! isset( $update[ SEMANTIC_VERSIONING_PLUGIN_HEADER ] ) ) {
		return $update;
	}

	if ( $update[ SEMANTIC_VERSIONING_PLUGIN_HEADER ] === 'true' && has_breaking_changes( $update['new_version'], $update['Version'] ) ) {
		return false;
	} else {
		return $update;
	}
}
add_filter( 'auto_update_plugin', 'disable_auto_updates_for_major_versions', 10, 2 );

function get_major_version_updates() {
	if ( ! function_exists( 'get_plugin_updates' ) ) {
		include_once ABSPATH . 'wp-admin/includes/update.php';
	}

	// https://developer.wordpress.org/reference/functions/get_plugin_updates/
	$plugin_updates = array_filter(
		get_plugin_updates(),
		function( $plugin ) {
			return $plugin->{SEMANTIC_VERSIONING_PLUGIN_HEADER} === 'true' && has_breaking_changes( $plugin->update->new_version, $plugin->Version );
		}
	);

	return $plugin_updates;
}

function plugins_list_show_breaking_changes_message() {
	$plugin_updates = get_major_version_updates();

	$plugin_update_filenames = array_keys( $plugin_updates );

	foreach ( $plugin_update_filenames as $plugin_filename ) {
		$action_name  = 'in_plugin_update_message-';
		$action_name .= $plugin_filename;

		add_action(
			$action_name,
			function( $data, $response ) {
				printf( '<br><br><b>THIS UPDATE MAY CONTAIN BREAKING CHANGES:</b> This plugin uses semantic versioning, and this new version is a major release. Please review the changelog before updating. <a href="https://semver.org">Learn more</a>' );
			},
			10,
			2
		);
	}
}

add_action( 'plugins_loaded', 'plugins_list_show_breaking_changes_message' );

function modify_plugin_auto_update_setting_html_defaults( $html, $plugin_file, $plugin_data ) {
	$major_semver_updates = get_major_version_updates();

	$plugin_file_names = array_keys( $major_semver_updates );

	if ( in_array( $plugin_file, $plugin_file_names ) ) {
		$dom = new Dom();
		$dom->loadStr( $html );
		$auto_update_time_class = $dom->find( '.auto-update-time' )[0];

		$auto_update_text = 'This major version update <b>will not</b> be auto updated.';
		apply_filters( 'semver_auto_update_text', $auto_update_text, $plugin_file, $plugin_data );

		$auto_update_time_class->firstChild()->setText( $auto_update_text );

		return $dom;
	}

	return $html;
}

add_filter( 'plugin_auto_update_setting_html', 'modify_plugin_auto_update_setting_html_defaults', 10, 3 );


function uses_semver_link( $links_array, $plugin_file_name, $plugin_data, $status ) {
	$semver_plugins = array_filter(
		get_plugins(),
		function( $plugin ) {
			return $plugin[ SEMANTIC_VERSIONING_PLUGIN_HEADER ] === 'true';
		}
	);

	$semver_plugin_filenames = array_keys( $semver_plugins );

	if ( in_array( $plugin_file_name, $semver_plugin_filenames ) ) {
		if ( str_contains( $links_array[0], 'Version' ) ) {
			$links_array[0] = $links_array[0] . ' (uses Semantic Versioning)';
		} else {
			$links_array[] = 'Uses Semantic Versioning';
		}
	}

	return $links_array;
}

add_filter( 'plugin_row_meta', 'uses_semver_link', 10, 4 );
