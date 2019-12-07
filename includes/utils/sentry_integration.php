<?php

function sentryClient()
{
    if (class_exists('WP_Sentry_Php_Tracker')) {
        return WP_Sentry_Php_Tracker::get_instance()->get_client(); 
    }
    return null;
}

// function filter_sentry_send_data(array $data)
// {
//     $data['tags']['my_custom_key'] = 'my_custom_value';
//     return $data;
// }
// add_filter('wp_sentry_send_data', 'filter_sentry_send_data');

// function customize_sentry_options($options)
// {
//     return array_merge($options, array(
//         'tags' => array(
//             'my-custom-tag' => 'custom value',
//         ),
//     ));
// }
// add_filter('wp_sentry_options', 'customize_sentry_options');

// throw new \ErrorException('Hey there, Sentry!');

// if (class_exists('WP_Sentry_Php_Tracker')) {
//     $sentryClient = WP_Sentry_Php_Tracker::get_instance()->get_client();

//     $sentryClient->captureMessage('dsdsdssd');
//     throw new Exception("My first Sentry error!");
// }

// try {

//     throw new Exception("My first Sentry error!");
// } catch (Exception $exc) {

// }
