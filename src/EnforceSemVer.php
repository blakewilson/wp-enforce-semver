<?php
/**
 * Class to setup the enforcement of semantic versioning in a WordPress plugin.
 *
 * @file
 * @package EnforceSemVerClass
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PHLAK\SemVer;

/**
 * Enforce semantic versioning in your WordPress plugin.
 */
class EnforceSemVer {
	/**
	 * The file name of the plugin you want to enforce semantic versioning for.
	 *
	 * @var $plugin_filename
	 * @example my-plugin/my-plugin.php
	 */
	protected $plugin_filename;

	/**
	 * Pass in your plugin filename to enforce semantic versioning.
	 *
	 * @param string $plugin_filename testing.
	 */
	public function __construct( string $plugin_filename ) {
		$this->plugin_filename = $plugin_filename;

		add_filter( 'auto_update_plugin', array( $this, 'disable_auto_updates_for_major_versions' ), 10, 2 );
		add_action( 'plugins_loaded', array( $this, 'plugins_list_show_breaking_changes_message' ) );
	}

	/**
	 * Disables auto updates from occurring when the plugin's new version has
	 * a major change compared to the current version.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/auto_update_type/
	 *
	 * @param bool|null $update Whether to update the given plugin.
	 * @param object    $item The plugin object.
	 */
	public function disable_auto_updates_for_major_versions( $update, $item ) {
		if ( empty( $item->new_version ) ) {
			return $update;
		}

		/**
		 * If the plugin has a major change, change the $update value
		 * to be false so auto updates don't occur.
		 *
		 * Otherwise, return the unchanged $update value.
		 */
		if (
				$this->plugin_filename === $item->plugin &&
				$this->does_new_version_have_major_change( $item->new_version, $item->Version ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return false;
		} else {
			return $update;
		}
	}

	/**
	 * Check if the plugin has a major version update.
	 *
	 * @see https://developer.wordpress.org/reference/functions/get_plugin_updates/
	 */
	private function has_major_update() {
		if ( ! function_exists( 'get_plugin_updates' ) ) {
			include_once ABSPATH . 'wp-admin/includes/update.php';
		}

		$updates = get_plugin_updates();

		if ( ! isset( $updates[ $this->plugin_filename ] ) ) {
			return false;
		}

		$plugin_update = $updates[ $this->plugin_filename ];

		if ( ! $this->does_new_version_have_major_change( $plugin_update->update->new_version, $plugin_update->Version ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			return false;
		}

		return true;
	}

	/**
	 * Show the breaking changes notice on the plugin's update.
	 */
	public function plugins_list_show_breaking_changes_message() {
		$has_major_update = $this->has_major_update();

		$action_name  = 'in_plugin_update_message-';
		$action_name .= $this->plugin_filename;

		$notice_text = __( '<br><br><b>THIS UPDATE MAY CONTAIN BREAKING CHANGES:</b> This plugin uses Semantic Versioning, and this new version is a major release. Please review the changelog before updating. <a href="https://semver.org" target="_blank">Learn more</a>', 'semantic-versioning' );
		$notice_text = apply_filters( 'semantic_versioning_notice_text', $notice_text, $this->plugin_filename );

		if ( $has_major_update ) {
			add_action(
				$action_name,
				function( $data, $response ) use ( $notice_text ) {
					printf( wp_kses_post( $notice_text ) );
				},
				10,
				2
			);
		}
	}

	/**
	 * Detects if the "new version" has a major change compared to the "current version".
	 *
	 * @param string $new_version The new version of the plugin.
	 * @param string $current_version The current version of the plugin.
	 *
	 * @return boolean
	 */
	private static function does_new_version_have_major_change( $new_version, $current_version ) {
		$_current_version = SemVer\Version::parse( $current_version );
		$_new_version     = SemVer\Version::parse( $new_version );

		return $_new_version->major > $_current_version->major;
	}
}

