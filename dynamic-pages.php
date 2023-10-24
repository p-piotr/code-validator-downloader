<?php

function generate_random_string($length = 16) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

function add_product_download_link($serial_code, $product_id)
{
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    require_once('tables.php');
    global $wpdb, $table_name_products, $table_name_dynamic_links;

    $sql = "SELECT file_url FROM $table_name_products WHERE product_id = $product_id;";
    $file_url = $wpdb->get_results($sql);
    $file_url = $file_url[0]->file_url;
    $random_path = generate_random_string();

    add_dynamic_link($serial_code, $product_id, $random_path);

    return $random_path;
}

function product_download_link_exists($serial_code, $product_id)
{
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    require_once('tables.php');
    global $wpdb, $table_name_products, $table_name_dynamic_links;

    $sql = "SELECT * FROM $table_name_dynamic_links WHERE serial_code = $serial_code AND product_id = $product_id;";
    $result = $wpdb->get_results($sql);
    if ($result == null)
        return null;
    return $result[0]->dynamic_path;
}