<?php

require_once('globals.php');
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

function create_code_table()
{
    global $wpdb, $table_name_codes, $table_name_products, $table_name_packages, $table_name_dynamic_links, $table_name_downloads;

    $table_codes_exist = false;
    $table_products_exist = false;
    $table_packages_exist = false;
    $table_dynamic_links_exist = false;
    $table_downloads_exist = false;
    $charset = $wpdb->get_charset_collate();

    $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . DB_NAME . "' AND TABLE_NAME = '" . $table_name_codes . "';"; // czy istnieje tabela z kodami
    if ($wpdb->get_results($sql) != null)
        $table_codes_exist = true;
    $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . DB_NAME . "' AND TABLE_NAME = '" . $table_name_products . "';"; // czy istnieje tabela z produktami
    if ($wpdb->get_results($sql) != null)
        $table_products_exist = true;
    $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . DB_NAME . "' AND TABLE_NAME = '" . $table_name_packages . "';"; // czy istnieje tabela z pakietami
    if ($wpdb->get_results($sql) != null)
        $table_packages_exist = true;
    $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . DB_NAME . "' AND TABLE_NAME = '" . $table_name_dynamic_links . "';"; // czy istnieje tabela z dynamicznymi linkami do pobrań produktów
    if ($wpdb->get_results($sql) != null)
        $table_dynamic_links_exist = true;
    $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '" . DB_NAME . "' AND TABLE_NAME = '" . $table_name_downloads . "';"; // czy istnieje tabela z historią pobrań
    if ($wpdb->get_results($sql) != null)
        $table_downloads_exist = true;

    if (!$table_products_exist)
    {
        $sql = "CREATE TABLE $table_name_products (
            product_id INT NOT NULL AUTO_INCREMENT,
            product_name VARCHAR(128),
            file_ext_name VARCHAR(128),
            file_url VARCHAR(512) NOT NULL,
            PRIMARY KEY(product_id)
            ) $charset;";
        dbDelta($sql);
    }
    if (!$table_packages_exist)
    {
        $sql = "CREATE TABLE $table_name_packages (
            package_id INT NOT NULL AUTO_INCREMENT,
            package_name VARCHAR(128),
            products_included VARCHAR(128),
            PRIMARY KEY(package_id)
            ) $charset;";
        dbDelta($sql);
    }
    if (!$table_codes_exist)
    {
        $sql = "CREATE TABLE $table_name_codes (
            serial_code INT NOT NULL,
            package_reference INT NOT NULL,
            expires_at DATETIME,
            status VARCHAR(16),
            PRIMARY KEY(serial_code),
            FOREIGN KEY(package_reference) REFERENCES $table_name_packages(package_id)
            ) $charset;";
        dbDelta($sql);
    }
    if (!$table_dynamic_links_exist)
    {
        $sql = "CREATE TABLE $table_name_dynamic_links (
            serial_code INT NOT NULL,
            product_id INT NOT NULL,
            dynamic_path VARCHAR(16),
            FOREIGN KEY(serial_code) REFERENCES $table_name_codes(serial_code),
            FOREIGN KEY(product_id) REFERENCES $table_name_products(product_id)
            ) $charset;";
        dbDelta($sql);
    }
    if (!$table_downloads_exist)
    {
        $sql = "CREATE TABLE $table_name_downloads (
            id INT NOT NULL AUTO_INCREMENT,
            serial_code INT NOT NULL,
            product_id INT NOT NULL,
            visitor_ip VARCHAR(48),
            date_time DATETIME,
            user_agent TEXT,
            ctd INT, /* count to downloads - czy dany rekord ma być brany pod uwagę przy
                obliczaniu łącznej liczby pobrań danego produktu z danego kodu - 
                1 jeżeli tak, inaczej 0 (default = 1, ale 0 należy ustawić jeżeli
                admin zdecyduje o 'odnowieniu ważności' kodu dla klienta) */
            PRIMARY KEY(id)
            ) $charset;";
        dbDelta($sql);
    }
}

function add_dynamic_link($serial_code, $product_id, $dynamic_link)
{
    global $wpdb, $table_name_dynamic_links;

    if ($wpdb->insert($table_name_dynamic_links, 
        array('serial_code' => $serial_code, 'product_id' => $product_id, 'dynamic_path' => $dynamic_link)) == false)
        {
            //var_dump($wpdb->last_error);
            return false;
        }
    return true;
}

function delete_dynamic_links($product_id)
{
    global $wpdb, $table_name_dynamic_links;

    if ($wpdb->delete($table_name_dynamic_links,
        array('product_id' => $product_id)) == false)
        {
            //var_dump($wpdb->last_error);
            return false;
        }
    return true;
}

function add_product($product_name, $file_ext_name, $file_url)
{
    global $wpdb, $table_name_products;

    if ($wpdb->insert($table_name_products,
        array('product_name' => $product_name, 'file_ext_name' => $file_ext_name, 'file_url' => $file_url)) == false)
        {
            //var_dump($wpdb->last_error);
            return false;
        }
    return true;
}

function edit_product($product_id, $product_name, $file_ext_name, $file_url)
{
    global $wpdb, $table_name_products;

    $result = $wpdb->update($table_name_products, array( 'product_name' => $product_name,
        'file_ext_name' => $file_ext_name, 'file_url' => $file_url ), 
        array('product_id' => $product_id));
    if ($result == false)
        return false;
    return true;
}

function delete_product($product_id)
{
    global $wpdb, $table_name_products, $table_name_dynamic_links;

    $results = $wpdb->get_results("SELECT * FROM $table_name_dynamic_links WHERE product_id = $product_id;");
    if (count($results) > 0 && delete_dynamic_links($product_id) == false)
        return false;

    if ($wpdb->delete($table_name_products, 
        array('product_id' => $product_id)) == false)
        {
            //var_dump($wpdb->last_error);
            return false;
        }
    return true;
}

function add_package($package_name, $products_included)
{
    global $wpdb, $table_name_packages;

    if ($wpdb->insert($table_name_packages,
        array('package_name' => $package_name, 'products_included' => $products_included)) == false)
        {
            //var_dump($wpdb->last_error);
            return false;
        }
    return true;
}

function edit_package($package_id, $package_name, $products_included)
{
    global $wpdb, $table_name_packages;

    $result = $wpdb->update($table_name_packages, array( 'package_name' => $package_name, 
        'products_included' => $products_included ), array( 'package_id' => $package_id ));
    if ($result == false)
    {
        //var_dump($wpdb->last_error);
        return false;
    }
    return true;
}

function delete_package($package_id)
{
    global $wpdb, $table_name_packages;

    if ($wpdb->delete($table_name_packages,
        array('package_id' => $package_id)) == false)
        {
            //var_dump($wpdb->last_error);
            return false;
        }
    return true;
}

function add_code($serial_code, $package_reference, $expires_at, $status)
{
    global $wpdb, $table_name_codes;

    if ($wpdb->insert($table_name_codes,
        array('serial_code' => $serial_code, 'package_reference' => $package_reference, 'expires_at' => $expires_at, 'status' => $status)) == false)
        {
            //var_dump($wpdb->last_error);
            return false;
        }
    $products = get_package_products($package_reference);
    $products_count = count($products);
    for ($i = 0; $i < $products_count; $i++)
    {
        $product = $products[$i];
        get_dynamic_path($serial_code, $product->product_id); // dodaje dynamiczne sciezki do kazdego produktu z pakietu, jesli nie istnieja
    }
    return true;
}

<<<<<<< HEAD
=======
function check_code_from_file($file_path)
{
    global $wpdb, $table_name_codes;
    $return_array = array();
    $file = fopen($file_path, 'r');
    $lines = 0;
    if ($file == false)
    {
        $return_array['result'] = false;
        $return_array['reason'] = 'cannot_open_file';
        return $return_array;
    }
    while (!feof($file))
    {
        $line = fgets($file);
        $code_params = explode(';', $line);
        if (count($code_params) != 4)
        {
            fclose($file);
            $return_array['result'] = false;
            $return_array['reason'] = 'invalid_format';
            return $return_array;
        }
        $lines++;
    }
    fclose($file);
    $return_array['result'] = true;
    $return_array['lines'] = $lines;
    return $return_array;
}

function add_code_from_file($file_path)
{
    global $wpdb, $table_name_codes;
    $return_array = array();
    $file = fopen($file_path, 'r');
    if ($file == false)
    {
        $return_array['result'] = false;
        $return_array['reason'] = 'cannot_open_file';
        return $return_array;
    }
    
    $n = 1;
    $error_lines = array();
    while (!feof($file))
    {   $single_code_array = array();
        $line = fgets($file);
        $code_params = explode(';', $line);
        if (count($code_params) != 4)
        {
            fclose($file);
            $return_array['result'] = false;
            $return_array['reason'] = 'invalid_format';
        }
        $single_code_array['serial_code'] = intval($code_params[0]);
        $single_code_array['package_reference'] = intval($code_params[1]);
        if ($code_params[2] == 'NULL')
            $single_code_array['expires_at'] = null;
        else $single_code_array['expires_at'] = trim($code_params[2]);
        $single_code_array['status'] = trim($code_params[3]);

        if (add_code($single_code_array['serial_code'], $single_code_array['package_reference'], 
            $single_code_array['expires_at'], $single_code_array['status']) == false)
        {
            array_push($error_lines, array($n, $single_code_array['serial_code'], $single_code_array['package_reference'], 
                $code_params[2], $single_code_array['status']));
        }
        $n++;
    }
    if (count($error_lines) == 0)
    {
        $return_array['result'] = true;
        return $return_array;
    }
    else
    {
        $return_array['result'] = false;
        $return_array['reason'] = 'wrong_records';
        $return_array['error_lines'] = $error_lines;
        return $return_array;
    }
}

>>>>>>> 3b3e8aa (0.5.0)
function edit_code($serial_code, $package_reference, $expires_at, $status)
{
    global $wpdb, $table_name_codes;

    $result = $wpdb->update($table_name_codes, array( 'package_reference' => $package_reference, 
        'expires_at' => $expires_at, 'status' => $status ), array( 'serial_code' => $serial_code ));
    if ($result == false)
        return false;
    return true;
}

function delete_code($serial_code)
{
    global $wpdb, $table_name_codes, $table_name_dynamic_links;

    if ($wpdb->delete($table_name_dynamic_links,
        array('serial_code' => $serial_code)) == false)
        {
            //var_dump($wpdb->last_error);
            return false;
        }

    if ($wpdb->delete($table_name_codes,
        array('serial_code' => $serial_code)) == false)
        {
            //var_dump($wpdb->last_error);
            return false;
        }
    return true;
}