<?php

include_once __DIR__."/../utils/global.php";
include_once __DIR__."/../models/models.php";

use models\Subs;
use models\SubsOrderData;

add_action('woocommerce_order_status_completed', 'test_order_status_completed');
add_action('woocommerce_payment_complete', 'test_payment_complete');

// TEST - ORDER COMPLETE, WYWALIC POTEM!!!!
add_shortcode('test_order_complete', 'test_order_status_completed');

//---------------------------------------------------------------------------------------------------
function test_order_status_completed($order_id)
{
    $order_id = 651; // Just for tests
    $ice_mgr = new IceAuthOrderMgr($order_id);
    $ice_mgr->orderComplete();
}

//---------------------------------------------------------------------------------------------------
function test_payment_complete($order_id)
{
    write_log('test_payment_complete');
    $order = wc_get_order($order_id);
    $user = $order->get_user();
    if ($user) {
        write_log("Payment complete for order $order_id, $order, $user->user_login");
    }
}

//---------------------------------------------------------------------------------------------------
class IceAuthOrderMgr
{
    public $order;
    public $user;

    private const SUB_REGISTER_URL = 'http://jmpiano.pl:7000/icecast/listener/manage/register';
    private const SUB_UNREGISTER_URL = 'http://jmpiano.pl:7000/icecast/listener/manage/unregister';

    //---------------------------------------------------------------------------------------------------
    public function __construct($order_id)
    {
        // Get order
        $this->order = wc_get_order($order_id);
        if (!$this->order) {
            throw new ErrorException(sprintf("[%s::%s] Cannot find order with id: $order_id", __CLASS__, __FUNCTION__));
        }

        if (count($this->order->get_items()) < 1) {
            throw new ErrorException(sprintf("[%s::%s] Order with id: $order_id has no items", __CLASS__, __FUNCTION__));
        }

        // Get user
        $this->user = $this->order->get_user();
        if (!$this->user) {
            throw new ErrorException(sprintf("[%s::%s] Cannot find user for order: $order_id", __CLASS__, __FUNCTION__));
        }

        // Get customer
        //$customer = new WC_Customer($order->get_customer_id());
    }

    //---------------------------------------------------------------------------------------------------
    public function orderComplete()
    {
        // Creates db tables if they are missing
        $this->createMissingTables();

        // Go through all bought items and create entry for every one
        foreach ($this->order->get_items() as $it) {
            $product = $this->getProductFromItem($it);

            // Create subscription entry
            $sub = new Subs();
            $sub->user_id = $this->user->ID;
            $sub->url = $this->createUniqUrl($this->user);
            $sub->exp_date = $this->createExpDate($product);
            $sub->licenses_num = 1; //$order->get_meta('_billing_licences_num'); // TODO for now we will have on licence per order
            $sub->save();

            // Create subscription order data entry
            $sub_order_data = new SubsOrderData();
            $sub_order_data->sub_id = $sub->id;
            $sub_order_data->order_id = $this->order->get_id();
            $sub_order_data->product_id = $product->get_id();
            $sub_order_data->save();

            // Send data to ice_auth
            $this->registerSubscriptionInIceAuth($sub);

            // TESTS: just for tests unregister new listener
            $this->unregisterSubscriptionFromIceAuth($sub);
        }

        write_log($this->order->get_data());
        print_r($this->order->get_meta('_billing_nip'));
    }

    //---------------------------------------------------------------------------------------------------
    protected function registerSubscriptionInIceAuth($sub)
    {
        // Send POST to ice_auth to create user
        $url = $this::SUB_REGISTER_URL;
        $myvars = 'uuid=' . $sub->url . '&exp_date=' . $sub->exp_date . '&licences_num=' . $sub->licenses_num;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HEADER, 1);  
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($httpcode != 200)
        {
            throw new Exception(sprintf("[%s::%s] Cannot register listener in ice_auth. Response code: $httpcode", __CLASS__, __FUNCTION__));
        }
    }

    //---------------------------------------------------------------------------------------------------
    protected function unregisterSubscriptionFromIceAuth($sub)
    {
        // Send POST to ice_auth to remove user
        $url = $this::SUB_UNREGISTER_URL;
        $myvars = 'uuid=' . $sub->url;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($httpcode != 200)
        {
            throw new Exception(sprintf("[%s::%s] Cannot unregister listener in ice_auth. Response code: $httpcode", __CLASS__, __FUNCTION__));
        }
    }

    //---------------------------------------------------------------------------------------------------
    protected function createMissingTables()
    {
        // TODO
    }

    //---------------------------------------------------------------------------------------------------
    protected function getProductFromItem($item)
    {
        $wcpf = new WC_Product_Factory();
        $product_id = $item['product_id'];
        $product = $wcpf->get_product($product_id);
        if (!$product) {
            throw new ErrorException(sprintf("[%s::%s] Cannot get product with id: $product_id", __CLASS__, __FUNCTION__));
        }
        return $product;
    }

    //---------------------------------------------------------------------------------------------------
    protected function createUniqUrl($user)
    {
        $date = date('Y-m-d H:i:s');
        return uniqid($user->ID . '_') . md5($date . $user->user_login);
    }

    //---------------------------------------------------------------------------------------------------
    protected function createExpDate($product)
    {
        // TODO na razie zahardkodujemy rozpoznawanie produktu by sku, potem jak to zadziała to pomyslimy jak to zrobic madrzej
        switch ($product->get_sku()) {
            case "6_miechow_radio":
                return date('Y-m-d', strtotime("+6 months"));
        }
        return null;
    }
}