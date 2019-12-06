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

include_once "includes/ice_auth/checkout.php";
include_once "includes/ice_auth/order_complete.php";
include_once "includes/ice_auth/account.php";
include_once "includes/radio.php";

/**
 * Test functions
 */

// Tests, append js script
function test_js_shortcode() {
   wp_enqueue_script("testrun", plugin_dir_url(__FILE__)."js/testrun.js");
}
add_shortcode('test_js', 'test_js_shortcode');


