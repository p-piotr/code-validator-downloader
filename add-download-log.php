<?php

require_once('tables.php');

function die_with_code($http_code)
{
    status_header($http_code);
    die;
}

//$pass = $_GET['pass'];
$serial_code = $_POST['ser'];
$product_id = $_POST['prod_id'];
$visitor_ip = $_POST['visitor_ip'];
$date_time = $_POST['date'];
$user_agent = $_POST['user_agent'];
$ctd = $_POST['ctd'];

if (!isset($serial_code) || !isset($product_id))
    die_with_code(400);

if (!isset($visitor_ip))
    $visitor_ip = $_SERVER['REMOTE_ADDR'];
if (!isset($date_time))
    $date_time = date_format(date_create('now', new DateTimeZone('Europe/Warsaw')), 'Y-m-d H:i:s');
if (!isset($user_agent))
    $user_agent = $_SERVER['HTTP_USER_AGENT'];;
if (!isset($ctd))
    $ctd = 1;

$result = add_download_log($serial_code, $product_id, 
    $visitor_ip, $date_time, $user_agent, $ctd);
if ($result == false)
    die_with_code(400);
else
    die_with_code(200);