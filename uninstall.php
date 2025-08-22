<?php
/**
 * Uninstall script for Retain Admin Color plugin
 *
 * This file runs when the plugin is deleted via the WordPress admin.
 *
 * @package RetainAdminColor
 */

// If uninstall not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// No data to clean up for this simple plugin
// The plugin doesn't store any options or user meta
// Admin color preferences are native WordPress user meta and should be preserved

/**
 * Fires during plugin uninstall
 *
 * @since 1.0.0
 */
do_action( 'retain_admin_color_uninstall' );
