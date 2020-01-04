<?php

include_once __DIR__ . "/ice_auth/ice_auth_mgr.php";

//---------------------------------------------------------------------------------------------------
function orderStatusChanged($order_id, $old_status, $new_status)
{
    if ($new_status == $old_status) {
        return;
    }

    if ($old_status == "completed") {
        $ice_mgr = new IceAuthOrderMgr($order_id);
        $ice_mgr->orderRefunded();
    } else if ($new_status == "completed") {
        $ice_mgr = new IceAuthOrderMgr($order_id);
        $ice_mgr->orderComplete();
    }
}

add_action('woocommerce_order_status_changed', 'orderStatusChanged', 10, 3);

// Fires before a post is sent to the trash.
//---------------------------------------------------------------------------------------------------
function orderTrash($order_id)
{
    global $post_type;
    if ($post_type != 'shop_order') {
        return;
    }

    $order = wc_get_order($order_id);
    if ($order->get_status() == "completed") {
        $ice_mgr = new IceAuthOrderMgr($order_id);
        $ice_mgr->orderRefunded();
    }
}

add_action('wp_trash_post', 'orderTrash', 10, 1);

// Fires after a post is restored from the trash.
//---------------------------------------------------------------------------------------------------
function orderUntrashed($order_id)
{
    global $post_type;
    if ($post_type != 'shop_order') {
        return;
    }

    $order = wc_get_order($order_id);
    if ($order->get_status() == "completed") {
        $ice_mgr = new IceAuthOrderMgr($order_id);
        $ice_mgr->orderComplete();
    }
}

add_action('untrashed_post', 'orderUntrashed', 10, 1);

// Uncomment this if you would want to do something with orders that were deleted permamently
//---------------------------------------------------------------------------------------------------
// function orderDeleted($order_id)
// {
//     global $post_type;

//     if ($post_type != 'shop_order') {
//         return;
//     }

//     // DO STUFF HERE...
// }

// add_action('before_delete_post', 'orderDeleted', 10, 1);