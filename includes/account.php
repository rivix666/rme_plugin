<?php

include_once __DIR__ . "/utils/global.php";
include_once __DIR__ . "/utils/download_m3u.php";
include_once __DIR__ . "/utils/subs_page_factory.php";
include_once __DIR__ . "/models/models.php";

// Edit my account menu order
//---------------------------------------------------------------------------------------------------
function myAccountMenuOrder()
{

    $menuOrder = array(
        SUBS_ENDPOINT => __(' Dostęp do radia', 'woocommerce'),
        'orders' => __('Orders', 'woocommerce'),
        'edit-address' => __('Addresses', 'woocommerce'),
        'edit-account' => __('Szczegóły Konta', 'woocommerce'),
        'customer-logout' => __('Logout', 'woocommerce'),
        // 'downloads' => __('Download', 'woocommerce'),
        // 'dashboard' => __('Dashboard', 'woocommerce'),
    );

    return $menuOrder;
}

add_filter('woocommerce_account_menu_items', 'myAccountMenuOrder');

// Register new endpoints to use inside My Account page.
//---------------------------------------------------------------------------------------------------
function myAccountNewEndpoints()
{
    add_rewrite_endpoint(SUBS_ENDPOINT, EP_ROOT | EP_PAGES);
}

add_action('init', 'myAccountNewEndpoints');

//---------------------------------------------------------------------------------------------------
function enqueueMuAccountScripts()
{
    wp_enqueue_style('rmeMyAccount', plugin_dir_url(__FILE__) . '../css/account.css');
}

add_action('wp_enqueue_scripts', 'enqueueMuAccountScripts');

// Get new endpoint content
//---------------------------------------------------------------------------------------------------
function subscriptionsEndpointContent()
{
    $user = wp_get_current_user();
    if (!$user) {
        throw new RmeException(sprintf("[%s::%s] User is null", __CLASS__, __FUNCTION__));
    }

    $factory = new SubsPageFactory();
    $factory->showSubscriptionsTable($user);
}

add_action('woocommerce_account_subscriptions_endpoint', 'subscriptionsEndpointContent');
