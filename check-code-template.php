<?php

require_once('globals.php');
require_once('serial-code-status.php');
require_once('serial-code-response.php');
require_once('download.php');

//global $main_page_directory;

$serial_code = $_GET['ser'];
$check = $_GET['chk'];
$log = $_GET['log'];
if ($log == '')
    $log = '0';
if ($log != '0')
    $log = '1';

if ($check == '1')
{
    $return_array = array();
    $result = is_serial_code_valid($serial_code);
    $package_id = $result['package'];
    switch ($result['status'])
    {
        case CODE_RESULT_NOT_FOUND:
            $return_array['status'] = 'not_found';
            break;
        case CODE_RESULT_EXPIRED:
            $return_array['status'] = 'expired';
            break;
        case CODE_RESULT_INVALID:
            $return_array['status'] = 'invalid';
            break;
        case CODE_RESULT_VALID:
            $return_array['status'] = 'valid';
            $products = get_package_products($package_id);
            for ($i = 0; $i < count($products); $i++)
            {
                $product = $products[$i];
                $dynamic_path = get_dynamic_path($serial_code, $product->product_id);
                $return_array['products'][$product->product_id] = 
                    array('product_name' => $product->product_name,
                        'download_link' => get_site_url() . "/download/" . $dynamic_path . "?api=1&log=" . $log);
            }
            break;
    }
    $encoded_json = json_encode($return_array, JSON_PRETTY_PRINT);
    header('Content-Type: application/json; charset=utf-8');
    echo $encoded_json;
}