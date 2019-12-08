<?php
/**
 * Plugin Name: Radio Max Elektro - Custom Plugin
 * Plugin URI: localhost/wordpress
 * Description: Custom plugin for Radio Max Elektro Website
 * Author: Radio Max Elektro
 * Author URI: localhost/wordpress
 * Version: 0.8
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

defined('ABSPATH') || exit;

// TODO include these files only when we need them (like in download_m3u.php, but prettier)
include_once "includes/ice_auth/checkout.php";
include_once "includes/order.php";
include_once "includes/order_details.php";
include_once "includes/account.php";
include_once "includes/radio.php";
include_once "includes/utils/db_utils.php";

// Create db tables if needed, during plugin activation
//---------------------------------------------------------------------------------------------------
function rme_plugin_activated()
{
    createRmeDbTablesIfNeeded();
}

register_activation_hook(__FILE__, 'rme_plugin_activated');

// Shows 404 on shop page
//---------------------------------------------------------------------------------------------------
function redirect_shop() // TODO name and clean
{
    if (is_shop() || is_cart() || is_product()) {
        remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
        wp_redirect(home_url());
        exit;
    }
}

add_action('template_redirect', 'redirect_shop');


// TODO IMPORTANT in subs page shows only subs that orders have status completed
// TODO add bool active into rme_subs table and deactivate it when order status change to any other than completed
// TODO currently after refund order data from rme_sub is deleted. We should just deactivate it, and delete only when order is deleted.
// TODO exp_date should be calculated from order date not today