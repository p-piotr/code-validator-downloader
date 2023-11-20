<?php

session_start();
require_once('globals.php');
require_once('download.php');

$path = get_query_var('download'); // dynamiczna sciezka dostepu
$api = $_REQUEST['api'];
$log = $_REQUEST['log'];
$check_result = check_path($path, !$api);
if ($check_result['status'] == 'expired' || $check_result['status'] == 'internal_error' || $check_result['status'] == 'invalid_url')
{
    switch ($check_result['status'])
    {
        case 'expired':
            status_header(403);
            break;
        case 'invalid_url':
            status_header(404);
            break;
        case 'internal_error':
            status_header(500);
            break;
    }
    if ($api == 1)
    {
        $encoded_json = json_encode($check_result, JSON_PRETTY_PRINT);
        header('Content-Type: application/json; charset=utf-8');
        echo $encoded_json;
    }
    else
        echo 'Wystąpił nieoczekiwany błąd: ' . $check_result['description'];
}
else
{
    $serial_code = $check_result['serial_code'];
    $product_id = $check_result['product_id'];
    $package_id = $check_result['package_id'];
    $file_path = $check_result['file_path'];
    $file_ext_name = $check_result['file_ext_name'];
    if ($log == '1')
    {
        $visitor_ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $date_time = date_format(date_create('now', new DateTimeZone('Europe/Warsaw')), 'Y-m-d H:i:s');
        add_download_log($serial_code, $product_id, $package_id, $visitor_ip, $date_time, $user_agent, 1);
    }
    send_file($file_path, $file_ext_name);
}