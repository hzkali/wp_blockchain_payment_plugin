<?php

/*
 * Plugin Name: WooCommerce Smart Pack
 * Plugin URI: https://codecanyon.net/item/woocommerce-smart-pack-gift-card-wallet-refund-reward/20265145
 * Description: WooCommerce Smart Pack is a woocommerce wallet, refund, reward and gift card plugin
 * Version: 1.3.11
 * Author: zendcrew
 * Author URI: https://codecanyon.net/user/zendcrew/portfolio
 * Text Domain: wooznd-smartpack
 * Domain Path: /languages/
 * Requires at least: 4.0
 * Tested up to: 6.0.2
 * Requires PHP: 5.6
 * 
 * WC requires at least: 3.0
 * WC tested up to: 6.8.2
 * 
 */

if ( !defined( 'WOOZND_MAIN_FILE' ) ) {

    define( 'WOOZND_MAIN_FILE', __FILE__ );
}

if ( !defined( 'WOOZND_ASSET_URL' ) ) {

    define( 'WOOZND_ASSET_URL', plugins_url( 'assets/', WOOZND_MAIN_FILE ) );
}

if ( !function_exists( 'wooznd_init' ) ) {

    function wooznd_init() {

        if ( wooznd_is_plugin_active( 'woocommerce.php' ) ) {

            include_once('inc/class-util.php');
            include_once('inc/paginator.php');
            include_once('extensions/extensions.php');

            load_plugin_textdomain( 'wooznd-smartpack', false, dirname( plugin_basename( WOOZND_MAIN_FILE ) ) . '/languages/' );

            register_activation_hook( __FILE__, 'wooznd_plugin_activate' );

            add_action( 'admin_enqueue_scripts', 'wznd_enqueue_all_assets' );
            add_action( 'wp_enqueue_scripts', 'wznd_enqueue_frontend' );
        }
    }

}


if ( !function_exists( 'wooznd_is_plugin_active' ) ) {

    function wooznd_is_plugin_active( $plugin_filename ) {

        $active_plugins = ( array ) get_option( 'active_plugins', array() );

        foreach ( $active_plugins as $active_plugin ) {
            if ( stripos( $active_plugin, $plugin_filename ) ) {
                return true;
            }
        }


        if ( !is_multisite() ) {
            return false;
        }

        $active_site_plugins = get_site_option( 'active_sitewide_plugins' );

        $active_plugins_keys = array_keys( $active_site_plugins );
        foreach ( $active_plugins_keys as $active_plugins_key ) {
            if ( stripos( $active_plugins_key, $plugin_filename ) ) {
                return true;
            }
        }

        return false;
    }

}


wooznd_init();

if ( !function_exists( 'wznd_enqueue_all_assets' ) ) {

    function wznd_enqueue_all_assets() {
        wp_enqueue_style( 'znd_admin_css', WOOZND_ASSET_URL . 'css/styles.css', array(), '1.0', 'all' );
        wp_enqueue_style( 'znd_admin_popup_css', WOOZND_ASSET_URL . 'css/popup.css', array(), '1.0', 'all' );
        wp_enqueue_style( 'jquery_ui_css', WOOZND_ASSET_URL . 'css/jquery-ui.min.css', array(), '1.0', 'all' );
        wp_enqueue_script( 'znd_admin_popup_js', WOOZND_ASSET_URL . 'js/jquery.popup.js', array( 'jquery', 'jquery-ui-datepicker' ), '1.0', true );
        wp_enqueue_script( 'znd_admin_custom_js', WOOZND_ASSET_URL . 'js/custom.js', array( 'znd_admin_popup_js' ), '1.0', true );
        wp_enqueue_style( 'select2' );
        wp_enqueue_style( 'wc-enhanced-select' );
    }

}


if ( !function_exists( 'wznd_enqueue_frontend' ) ) {

    function wznd_enqueue_frontend() {
        
        wp_enqueue_style( 'dashicons' );
        wp_enqueue_style( 'znd_frontend_css', WOOZND_ASSET_URL . 'css/front-end.css', array(), '1.0', 'all' );
        wp_enqueue_style( 'jquery_ui_frontend_css', WOOZND_ASSET_URL . 'css/jquery-ui.min.css', array(), '1.0', 'all' );
        wp_enqueue_script( 'znd_frontend_custom_js', WOOZND_ASSET_URL . 'js/front-custom.js', array( 'jquery', 'jquery-ui-datepicker' ), '1.0', true );
    }

}

