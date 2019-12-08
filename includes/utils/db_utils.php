<?php

function dbPrefix()
{
    global $wpdb;
    return $wpdb->prefix;
}

function createRmeDbTablesIfNeeded()
{
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Create table rme_subs if needed
    $table_name = $wpdb->prefix.'rme_subs';
    $sql = "CREATE TABLE $table_name (
        id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id MEDIUMINT UNSIGNED NOT NULL,
        url TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        exp_date DATE NOT NULL,
        licenses_num MEDIUMINT NOT NULL,
        PRIMARY KEY (id)) $charset_collate;";

    maybe_create_table($table_name, $sql);

    // Create table rme_subs_products if needed
    $table_name = $wpdb->prefix.'rme_subs_products';
    $sql = "CREATE TABLE $table_name (
        id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
        sub_id MEDIUMINT UNSIGNED NOT NULL,
        order_id MEDIUMINT NOT NULL,
        product_id MEDIUMINT NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (sub_id) REFERENCES ".$wpdb->prefix."rme_subs(id)
        ) $charset_collate;";
    
    maybe_create_table($table_name, $sql);
}