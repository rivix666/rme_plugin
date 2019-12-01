<?php 

include "global.php";

add_filter('woocommerce_default_address_fields', 'overrideDefaultAddressFields', 9999);
add_filter('woocommerce_checkout_fields', 'woocommerce_checkout_field_editor', 10000);

// Override checkout fields. We want to add NIP and other custom fields only into 'billing' section.
// Thats why we don't add them in 'defaultAddressFields', cause then they will bie also visible in for example my account
function woocommerce_checkout_field_editor( $fields ) 
{
    // Make all base fields required (beside 'address_2')
    foreach($fields['billing'] as &$field)
    {
        $field['required'] = true;
    }
    $fields['billing']['billing_address_2']['required'] = false;

    // Add NIP info at the beggining of the array
    $fields['billing'] = array('billing_nip' => array(
        'label'     => __('NIP', 'woocommerce'),
        'placeholder'   => _x('NIP', 'placeholder', 'woocommerce'),
        'required'  => true
    )) + $fields['billing'];

    // At at the end
    // $fields['billing']['billing_licences_num'] = array( // TODO na razie jeden order to jedna licencja, pomyslimy nad zmiana tego w przyszlosci
    //     'label'     => __('Liczba licencji', 'woocommerce'),
    //     'placeholder'   => _x('Liczba licencji', 'placeholder', 'woocommerce'),
    //     'required'  => true
    // );

    return $fields;
}

// Override address fields used in my account and checkout
function overrideDefaultAddressFields($address_fields) 
{
    // Make all base fields required (beside 'address_2' and 'company')
    foreach($address_fields as &$field)
    {
        $field['required'] = true;
    }
    unset($address_fields['address_2']['required']);
    unset($address_fields['company']['required']);
    return $address_fields;
}

?>