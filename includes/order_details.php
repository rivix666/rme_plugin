<?php

// Add additional info with shop addres to order page
//---------------------------------------------------------------------------------------------------
function orderShopDetails($order)
{
    if ($addr1 = $order->get_meta('shop_address1')) {

        echo "<h2 class='woocommerce-column__title'>Adres licencji</h2><address>";
        echo $addr1;

        // Not required
        if ($addr2 = $order->get_meta('shop_address2')) {
            echo "<br>$addr2";
        }

        $postcode = $order->get_meta('shop_postcode');
        echo "<br>$postcode";

        $city = $postcode = $order->get_meta('shop_city');
        echo "<br>$city";

        // Not required
        if ($phone = $order->get_meta('shop_phone')) {
            echo "<p class='woocommerce-customer-details--phone'>$phone</p>";
        }

        echo "</address>";
    }
}

add_action('woocommerce_order_details_after_customer_details', 'orderShopDetails');

// Remove order again buttn from order page
//---------------------------------------------------------------------------------------------------
function removeOrderAgainBtn()
{
    remove_action('woocommerce_order_details_after_order_table', 'woocommerce_order_again_button');
}

add_action('after_setup_theme', 'removeOrderAgainBtn');

// For test purposes
// hooks from order-details.php and order-details-customer.php
//---------------------------------------------------------------------------------------------------
// function test_order_page00()
// {
//     print_r('00');
// }

// add_action('woocommerce_order_details_after_customer_details', 'test_order_page00');

// function test_order_page()
// {
//     print_r('0');
// }

// add_action('woocommerce_order_details_after_order_table', 'test_order_page');

// function test_order_page1()
// {
//     print_r('1');
// }

// add_action('woocommerce_order_details_after_order_table_items', 'test_order_page1');

// function test_order_page2()
// {
//     print_r('2');
// }

// add_action('woocommerce_order_details_before_order_table', 'test_order_page2');

// function test_order_page3()
// {
//     print_r('3');
// }

// add_action('woocommerce_order_details_before_order_table_items', 'test_order_page3');

// function test_order_page4()
// {
//     print_r('4');
// }

// add_action('woocommerce_purchase_note_order_statuses', 'test_order_page4');

// function test_order_page5()
// {
//     print_r('5');
// }

// add_action('woocommerce_purchase_order_item_types', 'test_order_page5');