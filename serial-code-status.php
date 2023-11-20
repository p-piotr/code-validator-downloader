<?php

require_once('serial-code-response.php');
require_once('globals.php');

// zwraca array('status', 'package') -> status - status kodu, package - id pakietu w przypadku gdy status = CODE_RESULT_VALID (inaczej -1)
function is_serial_code_valid($serial_code)
{
    if ($serial_code == null || str_contains($serial_code, ' '))
        return array('status' => CODE_RESULT_INVALID, 'package' => -1);

    $shared_package = get_package($serial_code);
    if ($shared_package == -1)
        return array('status' => CODE_RESULT_EXPIRED, 'package' => -1);
    else if ($shared_package == -2)
        return array('status' => CODE_RESULT_NOT_FOUND, 'package' => -1);
    
    return array('status' => CODE_RESULT_VALID, 'package' => $shared_package);
}

function set_code_expired($serial_code)
{
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    require_once('tables.php');
    global $wpdb, $table_name_codes;

    $sql = "UPDATE $table_name_codes SET status = 'expired' WHERE serial_code = '$serial_code';";
    dbDelta($sql);
}

// zwraca id pakietu jezeli kod jest wazny, inaczej -1 (wygasl) lub -2 (nie znajduje sie w bazie)
function get_package($serial_code)
{
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    require_once('tables.php');
    global $wpdb, $table_name_codes, $code_expiry_time_days;

    $sql = "SELECT * FROM $table_name_codes WHERE serial_code = '$serial_code';";
    $resp = $wpdb->get_results($sql);
    if ($resp == null)
        return -2;
    $element = $resp[0];

    if ($element->status == 'expired')
        return -1;

    $date = date_create('now', new DateTimeZone('Europe/Warsaw'));
    if ($element->expires_at != null)
    {
        $timestamp_now = date_timestamp_get($date);
        $timestamp_expiry = date_timestamp_get(date_create($element->expires_at, new DateTimeZone('Europe/Warsaw')));
        if ($timestamp_now > $timestamp_expiry)
        {
            set_code_expired($serial_code);
            return -1;
        }
    }
    else // $element->expires_at == null
    {
        date_modify($date, '+' . strval($code_expiry_time_days) . 'day');
        $date_expires_at = date_format($date, 'Y-m-d H:i:s');
        $sql = "UPDATE $table_name_codes SET expires_at = '$date_expires_at' WHERE serial_code = '$serial_code';";
        dbDelta($sql);
    }
    return $element->package_reference;
}