<?php
/**
 * Plugin Name:     Baseline
 * Description:     Provides basic setup and configuration as needed for our implementation WordPress. :)
 * Author:          AlexE
 * Text Domain:     base-setup
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Base_Setup
 */

/**
 * Deregister unneeded default WordPress styles
 */
wp_deregister_style( 'wp-block-library' );

/**
 * Disable the Gutenberg editor for now
 */
add_filter('gutenberg_can_edit_post_type', function( $can_edit, $post_type ) {
    return false;
});

/**
 * Security setup
 */
add_action('init', function() {
    // No need to access or edit files directly from the dasboard
    if(! defined( 'DISALLOW_FILE_EDIT' ) ) {
    	define( 'DISALLOW_FILE_EDIT', true );
    }
});

/**
 * Hide or create new menus and items in the admin bar.
 * Indentation shows sub-items.
 * @link https://codex.wordpress.org/Class_Reference/WP_Admin_Bar/add_menu
 * Code snippet from @link https://github.com/vincentorback/clean-wordpress-admin/blob/master/admin-bar.php
 */
add_action( 'wp_before_admin_bar_render', function () {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'wp-logo' );        // Remove the WordPress logo
    $wp_admin_bar->remove_menu( 'about' );          // Remove the about WordPress link
    $wp_admin_bar->remove_menu( 'wporg' );          // Remove the about WordPress link
    $wp_admin_bar->remove_menu( 'documentation' );  // Remove the WordPress documentation link
    $wp_admin_bar->remove_menu( 'support-forums' ); // Remove the support forums link
    $wp_admin_bar->remove_menu( 'feedback' );       // Remove the feedback link
    $wp_admin_bar->remove_menu( 'updates' );        // Remove the updates link
    $wp_admin_bar->remove_menu( 'comments' );       // Remove the comments link
}, 999);

/**
 * Admin clean-up; removes unnecessary components
 */
add_action( 'admin_init', function() {
    /**
     * Dashboard widget de-register
     */
    remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');

	add_action('admin_head', function() {
    	echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>';
    });
});

/**
 * Enable features from Soil when plugin is activated
 * @link https://roots.io/plugins/soil/
 */
add_action('after_setup_theme', function () {
    add_theme_support('soil-clean-up');
    add_theme_support('soil-disable-rest-api');
    add_theme_support('soil-nice-search');
    add_theme_support('soil-relative-urls');
    add_theme_support('soil-js-to-footer');
    add_theme_support('soil-google-analytics', 'UA-142130107-1');
    // add_theme_support('soil-jquery-cdn');
    add_theme_support('soil-disable-asset-versioning');
    add_theme_support('soil-disable-trackbacks');
    add_theme_support('soil-nav-walker');
});
