<?php

include "global.php";
include "models.php";

use models\Subs;
use models\SubsOrderData;

add_action('woocommerce_order_status_completed', 'test_order_status_completed');
add_action('woocommerce_payment_complete', 'test_payment_complete');

// TEST - ORDER COMPLETE, WYWALIC POTEM!!!!
add_shortcode('test_order_complete', 'test_order_status_completed');

function createUniqUrl($user)
{
    $date = date('Y-m-d H:i:s');
    return uniqid($user->ID.'_').md5($date.$user->user_login);
    //return uniqid($prefix_id.'_').md5($date);
}

function createExpDate($product)
{
    // TODO na razie zahardkodujemy rozpoznawanie produktu by sku, potem jak to zadziała to pomyslimy jak to zrobic madrzej
    switch ($product->get_sku())
    {
        case "6_miechow_radio":
            return date('Y-m-d', strtotime("+6 months"));
    }
}


// woocommerce_order_status_completed
function test_order_status_completed() //$order_id ){ // to bedzie jak juz skrypt bedzie dzialal przy zakupach
{
    $order_id = 651; // a tymczasem mamy dummy order
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Get order
    $order = wc_get_order( $order_id ); // zabezpieczyć, przed brakami ordera itp itd, jak cos sie nie uda to info do error_loga, a z tamtąd poprzez plugin wyślemy maila do admina
    if (!$order)
    {
        write_log("[".__FUNCTION__."] Cannot find order with id: $order_id");
        return;
    }
    
    // Get user
    $user = $order->get_user();
    if(!$user)
    {
        write_log("[".__FUNCTION__."] Cannot find user for order: $order_id");
        return;
    }

    // Get customer
    //$customer = new WC_Customer($order->get_customer_id());

    if (count($order->get_items()) < 1) // TODO use preprocessor to create macro that can be used instead of all that ifs
    {
        write_log("[".__FUNCTION__."] Error TODO");
        return;
    }
    
    
    // TODO tutaj albo gdzies indziej stworzyc odpwoeidnie tabele w db jei ich nie bedzie
    $wcpf = new WC_Product_Factory(); 

    // Go through all bought items and create entry for every one
    foreach($order->get_items() as $it)
    {
        $product = $wcpf->get_product($it['product_id']);
        if (!$product) { write_log("[".__FUNCTION__."] Error TODO"); return; }

        // test - mymodel
        $sub = new Subs();
        $sub->user_id = $user->ID;
        $sub->url = createUniqUrl($user);
	    $sub->exp_date = createExpDate($product);
	    $sub->licenses_num = 1; //$order->get_meta('_billing_licences_num'); // TODO na razie jeden order to jedna licencja, pomyslimy nad zmiana tego w przyszlosci
        $sub->save(); // TODO zabezpieczyc by nie lecial save jak sie okaze ze blad jakis poszedl 

        $sub_order_data = new SubsOrderData();
        $sub_order_data->sub_id = $sub->id;
        $sub_order_data->order_id = $order->get_id();
        $sub_order_data->product_id = $product->get_id();
        $sub_order_data->save(); // TODO zabezpieczyc by nie lecial save jak sie okaze ze blad jakis poszedl 

        print_r($sub);
        print_r("</p>");
        print_r($sub_order_data);
        print_r("</p>");
    }

    write_log($order->get_data());
    print_r($order->get_meta('_billing_nip'));
}

























// woocommerce_payment_complete
function test_payment_complete( $order_id ){
    write_log('test_payment_complete');
    $order = wc_get_order( $order_id );
    $user = $order->get_user();
    if( $user ){
        write_log( "Payment complete for order $order_id, $order, $user->user_login");
    }
}

?>