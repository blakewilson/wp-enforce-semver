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
 *
 * @package semantic-versioning
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SEMANTIC_VERSIONING_PLUGIN_HEADER', 'Semantic Versioning' );

require_once 'vendor/autoload.php';

use PHLAK\SemVer;

/**
 * Adds the Semantic Versioning header to plugins.
 *
 * @param string[] $headers The plugin headers.
 *
 * @see https://justintadlock.com/archives/2011/06/16/customizing-plugin-and-theme-file-headers
 */
function add_semver_plugin_header( $headers ) {
	if ( ! in_array( SEMANTIC_VERSIONING_PLUGIN_HEADER, $headers, true ) ) {
		$headers[] = SEMANTIC_VERSIONING_PLUGIN_HEADER;
	}

	return $headers;
}
add_filter( 'extra_plugin_headers', 'add_semver_plugin_header' );

/**
 * Detects if the "new version" has a major change compared to the "current version".
 *
 * @param string $new_version The new version of the plugin.
 * @param string $current_version The current version of the plugin.
 *
 * @return boolean
 */
function does_new_version_have_major_change( $new_version, $current_version ) {
	$_current_version = SemVer\Version::parse( $current_version );
	$_new_version     = SemVer\Version::parse( $new_version );

	return $_new_version->major > $_current_version->major;
}

/**
 * Disables auto updates from occurring for plugins that have the
 * Semantic Versioning header and who's new version has a major change compared
 * to the current version.
 *
 * @see https://developer.wordpress.org/reference/hooks/auto_update_type/
 *
 * @param bool|null $update Whether to update the given plugin.
 * @param object    $item The plugin object.
 */
function disable_auto_updates_for_major_versions( $update, $item ) {
	// If the plugin header is not set, return the unchanged $update value.
	if ( empty( $item->{SEMANTIC_VERSIONING_PLUGIN_HEADER} ) ) {
		return $update;
	}

	if ( empty( $item->new_version ) ) {
		return $update;
	}

	/**
	 * If the plugin header is set to the correct value and there is
	 * a major change, change the $update value to be false so auto updates don't occur.
	 *
	 * Otherwise, return the unchanged $update value.
	 */
	if (
			'true' === $item->{SEMANTIC_VERSIONING_PLUGIN_HEADER} &&
			does_new_version_have_major_change( $item->new_version, $item->Version ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		return false;
	} else {
		return $update;
	}
}
add_filter( 'auto_update_plugin', 'disable_auto_updates_for_major_versions', 10, 2 );

/**
 * Get all the plugins that are using Semantic Versioning and who's new version
 * is a major release.
 *
 * @see https://developer.wordpress.org/reference/functions/get_plugin_updates/
 */
function get_major_version_updates() {
	if ( ! function_exists( 'get_plugin_updates' ) ) {
		include_once ABSPATH . 'wp-admin/includes/update.php';
	}

	$major_version_updates = array_filter(
		get_plugin_updates(),
		function( $plugin ) {
			return 'true' === $plugin->{SEMANTIC_VERSIONING_PLUGIN_HEADER} &&
				does_new_version_have_major_change( $plugin->update->new_version, $plugin->Version ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}
	);

	return $major_version_updates;
}

/**
 * Show the plugins with major releases incoming, the breaking change notice.
 */
function plugins_list_show_breaking_changes_message() {
	$major_version_updates    = get_major_version_updates();
	$major_updates_file_names = array_keys( $major_version_updates );

	foreach ( $major_updates_file_names as $plugin_file_name ) {
		$action_name  = 'in_plugin_update_message-';
		$action_name .= $plugin_file_name;

		$notice_text = __( '<br><br><b>THIS UPDATE MAY CONTAIN BREAKING CHANGES:</b> This plugin uses Semantic Versioning, and this new version is a major release. Please review the changelog before updating. <a href="https://semver.org" target="_blank">Learn more</a>', 'semantic-versioning' );
		

		add_action(
			$action_name,
			function( $data, $response ) use ( $notice_text, $plugin_file_name ) {
				$notice_text = apply_filters( 'semantic_versioning_notice_text', $notice_text, $plugin_file_name, $data, $response );

				printf( wp_kses_post($notice_text) );
			},
			10,
			2
		);
	}
}
add_action( 'plugins_loaded', 'plugins_list_show_breaking_changes_message' );

/**
 * Displays a "uses Semantic Versioning" message in the plugin meta row of
 * plugins that use the Semantic Versioning header.
 *
 * @see https://developer.wordpress.org/reference/hooks/plugin_row_meta/
 *
 * @param string[] $plugin_meta An array of the plugin's metadata, including the version, author, author URI, and plugin URI.
 * @param string   $plugin_file_name Path to the plugin file relative to the plugins directory.
 * @param array    $plugin_data An array of plugin data.
 * @param string   $status Status filter currently applied to the plugin list.
 */
function uses_semver_link( $plugin_meta, $plugin_file_name, $plugin_data, $status ) {
	$semver_plugins = array_filter(
		get_plugins(),
		function( $plugin ) {
			return 'true' === $plugin[ SEMANTIC_VERSIONING_PLUGIN_HEADER ];
		}
	);

	$semver_plugin_filenames = array_keys( $semver_plugins );

	if ( in_array( $plugin_file_name, $semver_plugin_filenames, true ) ) {
		if ( str_contains( $plugin_meta[0], 'Version' ) ) {
			$plugin_meta[0] = $plugin_meta[0] . ' (Uses Semantic Versioning)';
		} else {
			$plugin_meta[] = 'Uses Semantic Versioning';
		}
	}

	return $plugin_meta;
}
add_filter( 'plugin_row_meta', 'uses_semver_link', 10, 4 );
