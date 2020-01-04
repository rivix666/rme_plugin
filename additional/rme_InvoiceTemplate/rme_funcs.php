<?php

function throwExcp($order_id, $context, $msg)
{
    $error_id = uniqid("", false);
    $message_to_user = "Wystąpił błąd. Skontaktuj się proszę z naszym działem obsługi 'kontakt@radiomaxelektro.pl'. W wiadomości załącz niniejszy numer błędu: $error_id";  
    echo "<script>alert(\"$message_to_user\")</script>";
    
    $e = new Exception("[ERROR $error_id][$context] $msg. order_id: $order_id");
    throw $e;
}

//---------------------------------------------------------------------------------------------------
function miscClientCompany($order)
{
    if ($cmp = $order->get_billing_company()) {
        return $cmp;
    } else {
        throwExcp($order->get_id(), "INVOICE AGREEMENT", "There is no company name");
    }
    return "";
}

//---------------------------------------------------------------------------------------------------
function miscClientAddress($order)
{
    $addr = "";
    if ($addr1 = $order->get_billing_address_1()) {
        $addr .= "$addr1";
        if ($addr2 = $order->get_billing_address_2())
            $addr .= " $addr2";
    }
    else {
        throwExcp($order->get_id(), "INVOICE AGREEMENT", "There is no address");
    }
    return $addr;
}

//---------------------------------------------------------------------------------------------------
function miscClientNIP($order)
{
    if ($nip = $order->get_meta('_billing_nip')) {
        return $nip;
    } else {
        throwExcp($order->get_id(), "INVOICE AGREEMENT", "There is no NIP");
    }
    return "";
}

//---------------------------------------------------------------------------------------------------
function infoClientAddress($order)
{
    $cmp = miscClientCompany($order);
    $nip = "NIP " . miscClientNIP($order);
    $addr = miscClientAddress($order);
    $city = $order->get_billing_city();
    $post_code = $order->get_billing_postcode();
    $fn = $order->get_billing_first_name();
    $ln = $order->get_billing_last_name();

    echo "<br><b>$cmp</b>";
    echo "<br>$nip";
    echo "<br>$fn $ln";
    echo "<br>$addr";
    echo "<br>$post_code, $city";
}

//---------------------------------------------------------------------------------------------------
function introStartDate($order)
{
    $date = $order->get_date_completed();
    echo $date->date("d-m-Y");
}

//---------------------------------------------------------------------------------------------------
function introExpDate($order)
{
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
    global $wpdb;
    $order_id =  $order->get_id();

    // Find subscription id
    $prod_table_name = $wpdb->prefix.'rme_subs_products';
    $result = $wpdb->get_results("SELECT sub_id FROM $prod_table_name WHERE order_id = $order_id");

    if (sizeof($result) != 1)
        throwExcp($order_id, "INVOICE AGREEMENT DB", "Query select from $prod_table_name returns array with size different than 1");

    // Find subscription expiration date
    $sub_id = $result[0]->sub_id;
    $table_name = $wpdb->prefix.'rme_subs';
    $result = $wpdb->get_results("SELECT exp_date FROM $table_name WHERE id = $sub_id");

    if (sizeof($result) != 1)
        throwExcp($order_id, "INVOICE AGREEMENT DB", "Query select from $table_name returns array with size different than 1");

    // Get date string from result and convert it to d-m-Y
    echo date("d-m-Y", strtotime($result[0]->exp_date));
}

//---------------------------------------------------------------------------------------------------
function introCity($order)
{
    echo $order->get_billing_city();
}

//---------------------------------------------------------------------------------------------------
function introClientData($order)
{
    echo miscClientCompany($order);
    echo ", " . miscClientAddress($order);
    echo ", " . $order->get_billing_city();
    echo " " . $order->get_billing_postcode();
    echo ", NIP " . miscClientNIP($order);

    $fn = $order->get_billing_first_name();
    $ln = $order->get_billing_last_name();
    echo ",<br>reprezentowaną przez - $fn $ln - zwanym w dalszej części <b>Klientem</b>.";
}

//---------------------------------------------------------------------------------------------------
function contentLicenceAddress($order)
{
    if ($addr1 = $order->get_meta('shop_address1')) {
        echo "Adres: $addr1";

        // Not required
        if ($addr2 = $order->get_meta('shop_address2')) {
            echo " $addr2";
        }

        $city = $order->get_meta('shop_city');
        echo ", $city";

        $postcode = $order->get_meta('shop_postcode');
        echo " $postcode";

        // Not required
        if ($phone = $order->get_meta('shop_phone')) {
            echo ". Telefon: $phone";
        }
    }
}

//---------------------------------------------------------------------------------------------------
function contentLicenceType($order)
{
    if (sizeof($order->get_items()) < 1)
        throwExcp($order->get_id(), "INVOICE AGREEMENT", "Number of items in given order is equal 0");

    foreach ($order->get_items() as $it) {
        if ($product = $it->get_product()) {
            $sku = $product->get_sku();
            switch ($sku) {
                case "6-months":
                    echo "6 miesięcy";
                    break;
                case "12-months":
                    echo "12 miesięcy";
                    break;
                case "24-months":
                    echo "24 miesięcy";
                    break;
                case "1-day-test":
                    echo "1 dzień";
                    break;
                default:
                    throwExcp($order->get_id(), "INVOICE AGREEMENT", "There is no product with given sku: $sku");
            }
        }
    }
}
