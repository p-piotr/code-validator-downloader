<?php

session_start();
require_once('globals.php');

function redirect($url, $status_code = 303)
{
    header('Location: ' . $url, true, $status_code);
    die();
}

function add_download_log($dynamic_path, $serial_code, $product_id)
{
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    require_once('tables.php');
    global $wpdb, $table_name_products, $table_name_dynamic_links, $table_name_downloads;
    $visitor_ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $date_time = date_format(date_create('now', new DateTimeZone('Europe/Warsaw')), 'Y-m-d H:i:s');
    $wpdb->insert($table_name_downloads,
        array('serial_code' => $serial_code, 'product_id' => $product_id, 
        'visitor_ip' => $visitor_ip, 'date_time' => $date_time,
        'user_agent' => $user_agent, 'ctd' => 1));
    //echo $wpdb->last_error;
}

function send_file($file_path, $file_ext_name)
{
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    header('Content-Type: ' . finfo_file($finfo, $file_path));
    finfo_close($finfo);
    //Use Content-Disposition: attachment to specify the filename
    header('Content-Disposition: attachment; filename='.$file_ext_name);
    //No cache
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    //Define file size
    header('Content-Length: ' . filesize($file_path));
    ob_clean();
    flush();
    readfile($file_path);
}

function check_path($dynamic_path)
{
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    require_once('tables.php');
    require_once('serial-code-status.php');
    global $wpdb, $table_name_products, $table_name_dynamic_links, $table_name_downloads, $product_downloads_amount, 
        $shortcode_rendered_url;
        
    $sql = "SELECT * FROM $table_name_dynamic_links INNER JOIN $table_name_products
        ON $table_name_dynamic_links.product_id = $table_name_products.product_id
        WHERE $table_name_dynamic_links.dynamic_path = \"$dynamic_path\";";
    $result = $wpdb->get_results($sql);
    $file_path = $result[0]->file_url;
    $serial_code = $result[0]->serial_code;
    $product_id = $result[0]->product_id;
    $file_ext_name = $result[0]->file_ext_name;
    if (is_serial_code_valid($serial_code)['status'] == CODE_RESULT_EXPIRED)
    {
        redirect($_SESSION['shortcode_rendered_url'] . "?serial_code=" . $serial_code);
        return null;
    }
    if (!file_exists($file_path))
        return null; // błąd wewnętrzny, plik powinien istnieć

    $sql = "SELECT * FROM $table_name_downloads WHERE serial_code = $serial_code AND product_id = $product_id AND ctd = 1;";
    $result = $wpdb->get_results($sql);
    //echo 'count($result) = ' . count($result) . ' $product_downloads_amount = ' . $product_downloads_amount;
    if (count($result) >= $product_downloads_amount)
    {
        redirect($_SESSION['shortcode_rendered_url'] . "?serial_code=" . $serial_code . "&cpe=" . $product_id);
        return null;
    }

    // plik istnieje oraz kod nadal jest ważny, można przesłać plik
    return array('serial_code' => $serial_code, 'product_id' => $product_id, 'file_path' => $file_path, 'file_ext_name' => $file_ext_name);
}

$path = get_query_var('download'); // dynamiczna sciezka dostepu
$check_result = check_path($path);
if ($check_result != null)
{
    $serial_code = $check_result['serial_code'];
    $product_id = $check_result['product_id'];
    $file_path = $check_result['file_path'];
    $file_ext_name = $check_result['file_ext_name'];
    add_download_log($dynamic_path, $serial_code, $product_id);
    //echo $file_path . ' sent';
    send_file($file_path, $file_ext_name);
}
else
    echo 'empty';