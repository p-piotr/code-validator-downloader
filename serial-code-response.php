<?php

require_once 'globals.php';
require_once 'dynamic-pages.php';

function invalid_serial_code_not_found()
{
    global $client_comments_array;
    return '<div style="width: 100%; text-align: center;">' . $client_comments_array['CODE_RESULT_NOT_FOUND'] . '</div>';
}

function invalid_serial_code_illegal_characters()
{
    global $client_comments_array;
    return '<div style="width: 100%; text-align: center;">' . $client_comments_array['CODE_RESULT_ILLEGAL_CHARACTERS'] . '</div>';
}

function invalid_serial_code_expired()
{
    global $client_comments_array;
    return '<div style="width: 100%; text-align: center;">' . $client_comments_array['CODE_RESULT_EXPIRED'] . '</div>';
}

function invalid_serial_code_certain_product_expired()
{
    global $client_comments_array;
    return '<div style="width: 100%; text-align: center;">' . $client_comments_array['CODE_RESULT_CPE'] . '</div>';
}

function get_dynamic_path($serial_code, $product_id)
{
    $dynamic_path = product_download_link_exists($serial_code, $product_id);
    if ($dynamic_path == null)
        $dynamic_path = add_product_download_link($serial_code, $product_id);
    return $dynamic_path;
}

function get_package_products($package_id)
{
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    require_once('tables.php');
    global $wpdb, $table_name_packages, $table_name_products;

    $sql = "SELECT products_included FROM $table_name_packages WHERE package_id = $package_id;";
    $products = $wpdb->get_results($sql);
    $products = explode(';', $products[0]->products_included);
    sort($products);
    $sql = "SELECT product_id, product_name, file_url FROM $table_name_products WHERE product_id IN (";
    foreach ($products as $product)
        $sql .= "\"" . $product . "\", ";
    $sql = substr($sql, 0, -2);
    $sql .= ") ORDER BY product_id ASC;";
    
    $products = $wpdb->get_results($sql);
    return $products;
}

function valid_serial_code($serial_code, $shared_package_id, $cpe)
{
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    require_once('tables.php');
    global $wpdb, $table_name_packages, $table_name_products, $client_comments_array;

    $output = '';
    // tworzenie zapytania do bazy o dane konkretnych produktow do udostepnienia
    $products = get_package_products($shared_package_id);
    $products_count = count($products);

    //$output .= "<div style=\"text-align:center;display:flex;\">";
    for ($i = 0; $i < $products_count; $i++)
    {
        $product = $products[$i];
        $dynamic_path = get_dynamic_path($serial_code, $product->product_id);
        $output .= "<div style=\"text-align:center;display:flex;position:relative;width:100%;\">";
        if ($cpe == $product->product_id)
            $output .= "<div style=\"margin:auto;\">";
        $output .= "<div style=\"margin:auto;\"><a>Pobierz " . $product->product_name . ":&nbsp&nbsp</a></div>";
        if ($cpe == $product->product_id)
            $output .= "<div id=\"div_forbidden\" style=\"text-align:center;\">" . $client_comments_array['CODE_RESULT_CPE'] . "</div></div>";
        $output .= "<div style=\"margin-left:15px;float:right;\"><button onclick=\"redirect('" . get_site_url() . "/download/" . $dynamic_path . 
        "')\">POBIERZ</button></div>";
        $output .= "</div>";
        if ($i < $products_count - 1)
            $output .= "<br>";
    }
    return $output;
}