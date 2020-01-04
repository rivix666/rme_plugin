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

// Returns order status 'completed' when order has only virtual products. Used to auto change status of PayU payments
//---------------------------------------------------------------------------------------------------
function virtualOrderPaymentCompleteOrderStatus($order_status, $order_id)
{
    $order = wc_get_order($order_id);

    if ('processing' == $order_status &&
        ('on-hold' == $order->status || 'pending' == $order->status || 'failed' == $order->status)) {

        $virtual_order = null;
        if (count($order->get_items()) > 0) {
            foreach ($order->get_items() as $item) {
                if ('line_item' == $item['type']) {
                    $_product = $order->get_product_from_item($item);
                    if (!$_product->is_virtual()) {
                        // once we've found one non-virtual product we know we're done, break out of the loop
                        $virtual_order = false;
                        break;
                    } else {
                        $virtual_order = true;
                    }
                }
            }
        }

        // virtual order, mark as completed
        if ($virtual_order) {
            return 'completed';
        }
    }

    // non-virtual order, return original status
    return $order_status;
}

add_filter('woocommerce_payment_complete_order_status', 'virtualOrderPaymentCompleteOrderStatus', 10, 2);

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
