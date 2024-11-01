<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://assistro.co/
 * @since      1.0.0
 *
 * @package    Syncmate_By_Assistro
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}


// Security check: Verify the request came from WordPress admin
if ( ! current_user_can( 'activate_plugins' ) ) {
    exit;
}


// Delete the table
global $wpdb;
$table_name = $wpdb->prefix . 'syncmate_form'; // Replace 'your_table_name' with your table name
$wpdb->query( "DROP TABLE IF EXISTS $table_name" );

// $table_countries = $wpdb->prefix . 'syncmate_countries'; // Replace 'your_table_name' with your table name
// $wpdb->query( "DROP TABLE IF EXISTS $table_countries" );