<?php

include_once __DIR__ . "/ice_auth/ice_auth_mgr.php";

function orderStatusCompleted($order_id)
{
    $ice_mgr = new IceAuthOrderMgr($order_id);
    $ice_mgr->orderComplete();
}

add_action('woocommerce_order_status_completed', 'orderStatusCompleted');

//---------------------------------------------------------------------------------------------------
function orderStatusRefunded($order_id)
{
    $ice_mgr = new IceAuthOrderMgr($order_id);
    $ice_mgr->orderRefunded();
}

add_action('woocommerce_order_status_refunded', 'orderStatusRefunded');