<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

define('PLUGIN_VERSION', '0.4.3');

define('PRODUCT_DOWNLOADS_AMOUNT_DEFAULT', 3);
define('CODE_EXIPRY_TIME_DAYS_DEFAULT', 1);

global $product_downloads_amount;
$product_downloads_amount = 3;

global $code_expiry_time_days;
$code_expiry_time_days = 1;

global $table_name_codes;
global $table_name_products;
global $table_name_packages;
global $table_name_dynamic_links;
global $table_name_downloads;
global $code_expiry_time_days;

$table_name_codes = 'serial_codes_p';
$table_name_products = 'products_p';
$table_name_packages = 'packages_p';
$table_name_dynamic_links = 'dynamic_links_p';
$table_name_downloads = 'downloads_p';

define('CODE_RESULT_VALID', 0);
define('CODE_RESULT_EXPIRED', 1);
define('CODE_RESULT_NOT_FOUND', 2);
define('CODE_RESULT_ILLEGAL_CHARACTERS', 3);
// CODE_RESULT_CPE

define('CODE_RESULT_EXPIRED_DEFAULT_COMMENT', '<a style="font-size: 20px; color: #FFFFFF">Twój numer seryjny wygasł</a>');
define('CODE_RESULT_NOT_FOUND_DEFAULT_COMMENT', '<a style="font-size: 20px; color: #FFFFFF">Twój numer seryjny nie znajduje się w bazie</a>');
define('CODE_RESULT_ILLEGAL_CHARACTERS_DEFAULT_COMMENT', '<a style="font-size: 20px; color: #FFFFFF">Twój numer seryjny zawiera nielegalne znaki</a>');
define('CODE_RESULT_CPE_DEFAULT_COMMENT', '<a style="font-size: 15px; color: #A83232">Przekroczyłeś dozwoloną liczbę pobrań tego produktu z Twojego kodu</a>');

global $client_comments_array;
$client_comments_array = array(
    'CODE_RESULT_EXPIRED' => CODE_RESULT_EXPIRED_DEFAULT_COMMENT,
    'CODE_RESULT_NOT_FOUND' => CODE_RESULT_NOT_FOUND_DEFAULT_COMMENT,
    'CODE_RESULT_ILLEGAL_CHARACTERS' => CODE_RESULT_ILLEGAL_CHARACTERS_DEFAULT_COMMENT,
    'CODE_RESULT_CPE' => CODE_RESULT_CPE_DEFAULT_COMMENT
);

global $main_page_directory;
$main_page_directory = $_SERVER['HTTP_HOST'];

$comments_file = esc_attr(get_option('comments_file_location'));
if (file_exists($comments_file))
{
    $array_file = file($comments_file);
    foreach ($array_file as $line)
    {
        $array = explode('`', $line);
        $client_comments_array[$array[0]] = $array[1];
    }
}

$cets = esc_attr(get_option('code_expiry_time_days'));
if (is_numeric($cets))
    $code_expiry_time_days = intval($cets);
else
    $code_expiry_time_days = CODE_EXIPRY_TIME_DAYS_DEFAULT;

$pda = esc_attr(get_option('product_downloads_amount'));
if (is_numeric($pda))
    $product_downloads_amount = intval($pda);
else
    $product_downloads_amount = PRODUCT_DOWNLOADS_AMOUNT_DEFAULT;