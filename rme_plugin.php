<?php
/**
 * Plugin Name: Radio Max Elektro - Custom Plugin
 * Plugin URI: localhost/wordpress
 * Description: Custom plugin for Radio Max Elektro Website
 * Author: Radio Max Elektro
 * Author URI: localhost/wordpress
 * Version: 1.0
 * Text Domain: rme_plugin
 *
 * Copyright: (c) 2019 Radio Max Elektro
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @author    Radio Max Elektro
 * @copyright Copyright (c) 2019, Radio Max Elektro
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 */

defined('ABSPATH') or exit;

include "includes/checkout.php";
include "includes/order_complete.php";
include "includes/radio.php";

/**
 * Test functions
 */

// Tests, append js script
function test_js_shortcode() {
   wp_enqueue_script("testrun", plugin_dir_url(__FILE__)."js/testrun.js");
}
add_shortcode('test_js', 'test_js_shortcode');

// Edit my account menu order
function my_account_menu_order() {
   $menuOrder = array(
      'orders'             => __('Orders', 'woocommerce'),
      'downloads'          => __('Download', 'woocommerce'),
      'edit-address'       => __('Addresses', 'woocommerce'),
      'edit-account'    	=> __('Account Details', 'woocommerce'),
      'customer-logout'    => __('Logout', 'woocommerce'),
      'dashboard'          => __('Dashboard', 'woocommerce'),
   );
   return $menuOrder;
}

// Edit my account menu order - add shortcode
function test_menu_order_shortcode() {
   add_filter ('woocommerce_account_menu_items', 'my_account_menu_order');
}
add_shortcode('test_menu_order', 'test_menu_order_shortcode');
