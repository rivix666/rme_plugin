<?php

include_once __DIR__."/../utils/global.php";

add_filter('woocommerce_default_address_fields', 'overrideDefaultAddressFields', 9999);
add_filter('woocommerce_checkout_fields', 'woocommerce_checkout_field_editor', 9999);

// Override checkout fields. We want to add NIP and other custom fields only into 'billing' section.
// Thats why we don't add them in 'defaultAddressFields', cause then they will bie also visible in for example my account
function woocommerce_checkout_field_editor($fields)
{
    // Make all base fields required (beside 'address_2')
    foreach ($fields['billing'] as &$field) {
        $field['required'] = true;
    }
    $fields['billing']['billing_address_2']['required'] = false;
    $fields['billing']['billing_state']['required'] = false;

    // Add NIP info at the beggining of the array
    $fields['billing'] = array('billing_nip' => array(
        'label' => __('NIP', 'woocommerce'),
        'placeholder' => _x('NIP', 'placeholder', 'woocommerce'),
        'required' => true,
    )) + $fields['billing'];

    return $fields;
}

// Override address fields used in my account and checkout
function overrideDefaultAddressFields($address_fields)
{
    // Make all base fields required (beside 'address_2' and 'company')
    foreach ($address_fields as &$field) {
        $field['required'] = true;
    }
    unset($address_fields['address_2']['required']);
    unset($address_fields['company']['required']);
    unset($address_fields['state']['required']);
    return $address_fields;
}

// Adds additional section with necessary fields that will be used to determine shop address
/////////////////////////////////////////////////////////////////////////////////////////////////

// Add the field to the checkout page
add_action('woocommerce_before_order_notes', 'shopAddressCheckoutFields');
function shopAddressCheckoutFields($checkout)
{
    echo '<div id="customise_checkout_field"><h3>Dane sklepu do licencji</h3>';

    // Address 1
    woocommerce_form_field('shop_address1', array(
        'type' => 'text',
        'class' => array(
            'form-row-wide',
        ),
        'label' => 'Ulica',
        'placeholder' => 'Nazwa ulicy, numer budynku / numer lokalu',
        'required' => true,
    ), $checkout->get_value('shop_address1'));

    // Address 2
    woocommerce_form_field('shop_address2', array(
        'type' => 'text',
        'class' => array(
            'form-row-wide',
        ),
        'label' => '',
        'placeholder' => 'Ciąg dalszy adresu',
        'required' => false,
    ), $checkout->get_value('shop_address2'));

    // Postcode
    woocommerce_form_field('shop_postcode', array(
        'type' => 'text',
        'class' => array(
            'form-row-wide',
        ),
        'label' => 'Kod pocztowy',
        'placeholder' => '',
        'required' => true,
    ), $checkout->get_value('shop_postcode'));

    // City
    woocommerce_form_field('shop_city', array(
        'type' => 'text',
        'class' => array(
            'form-row-wide',
        ),
        'label' => 'Miasto',
        'placeholder' => '',
        'required' => true,
    ), $checkout->get_value('shop_city'));

    // Phone
    woocommerce_form_field('shop_phone', array(
        'type' => 'text',
        'class' => array(
            'form-row-wide',
        ),
        'label' => 'Telefon',
        'placeholder' => '',
        'required' => false,
    ), $checkout->get_value('shop_phone'));

    echo '</div>';
}

//Checkout Process
add_action('woocommerce_checkout_process', 'shopAddressCheckoutFieldProcess');
function shopAddressCheckoutFieldProcess()
{
    // if the field is set, if not then show an error message.
    if (!$_POST['shop_address1']) {
        wc_add_notice(__('Proszę uzupełnić adres sklepu pod który wykupywana jest licencja.'), 'error');
    } else if (!$_POST['shop_postcode']) {
        wc_add_notice(__('Proszę uzupełnić adres sklepu pod który wykupywana jest licencja.'), 'error');
    } else if (!$_POST['shop_city']) {
        wc_add_notice(__('Proszę uzupełnić adres sklepu pod który wykupywana jest licencja.'), 'error');
    }
}

// Update value of field
add_action('woocommerce_checkout_update_order_meta', 'shopAddressCheckoutFieldsUpdateOrderMeta');
function shopAddressCheckoutFieldsUpdateOrderMeta($order_id)
{
    if (!empty($_POST['shop_address1'])) {
        update_post_meta($order_id, 'shop_address1', sanitize_text_field($_POST['shop_address1']));
    }
    if (!empty($_POST['shop_address2'])) {
        update_post_meta($order_id, 'shop_address2', sanitize_text_field($_POST['shop_address2']));
    }
    if (!empty($_POST['shop_postcode'])) {
        update_post_meta($order_id, 'shop_postcode', sanitize_text_field($_POST['shop_postcode']));
    }
    if (!empty($_POST['shop_city'])) {
        update_post_meta($order_id, 'shop_city', sanitize_text_field($_POST['shop_city']));
    }
    if (!empty($_POST['shop_phone'])) {
        update_post_meta($order_id, 'shop_phone', sanitize_text_field($_POST['shop_phone']));
    }
}