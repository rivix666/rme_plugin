<?php

include_once __DIR__ . "/../utils/global.php";
include_once __DIR__ . "/../models/models.php";

use models\Subs;
use models\SubsOrderData;

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
        // Go through all bought items and create entry for every one
        foreach ($this->order->get_items() as $it) {
            $product = $this->getProductFromItem($it);

            // Create subscription entry
            $sub = new Subs();
            $sub->user_id = $this->user->ID;
            $sub->url = $this->createUniqUrl($this->user);
            $sub->exp_date = $this->createExpDate($this->order, $product);
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
        }
    }

    //---------------------------------------------------------------------------------------------------
    public function orderRefunded()
    {
        $order_id = $this->order->get_id();
        $sub_data = SubsOrderData::query()
            ->where('order_id', $order_id)
            ->find();

        if (sizeof($sub_data) < 1) {
            throw new ErrorException(sprintf("[%s::%s] There is no SubData for given order: $order_id", __CLASS__, __FUNCTION__));
        }

        foreach ($sub_data as $data) {
            $data_id = $data->id;
            $subs = Subs::query()
                ->where('id', $data->sub_id)
                ->find();

            if (sizeof($subs) < 1) {
                throw new ErrorException(sprintf("[%s::%s] There is no Sub for given order: $order_id", __CLASS__, __FUNCTION__));
            }

            if (!$data->delete()) {
                throw new ErrorException(sprintf("[%s::%s] Cannot remove SubData: $data_id", __CLASS__, __FUNCTION__));
            }

            foreach ($subs as $sub) {
                $sub_id = $sub->id;
                $this->unregisterSubscriptionFromIceAuth($sub);

                if (!$sub->delete()) {
                    throw new ErrorException(sprintf("[%s::%s] Cannot remove Sub: $sub_id", __CLASS__, __FUNCTION__));
                }
            }
        }
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

        if ($httpcode != 200) {
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

        if ($httpcode != 200) {
            throw new Exception(sprintf("[%s::%s] Cannot unregister listener in ice_auth. Response code: $httpcode", __CLASS__, __FUNCTION__));
        }
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
    protected function createExpDate($order, $product)
    {
        switch ($product->get_sku()) {
            case "6_miechow_radio":
                return date('Y-m-d', strtotime("+6 months")); // TODO should be +6 months from order date not today
        }
        return null;
    }
}
