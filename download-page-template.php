<?php

session_start();
require_once('globals.php');
require_once('download.php');

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