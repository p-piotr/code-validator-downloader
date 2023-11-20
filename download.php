<?php

//$root = realpath($_SERVER["DOCUMENT_ROOT"]);
//require_once("wp-blog-header.php");

function redirect($url, $status_code = 303)
{
    header('Location: ' . $url, true, $status_code);
    die();
}

function send_file($file_path, $file_ext_name)
{
    ob_clean();
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    header('Content-Type: ' . finfo_file($finfo, $file_path));
    finfo_close($finfo);
    //Use Content-Disposition: attachment to specify the filename
    header('Content-Disposition: attachment; filename=' . $file_ext_name);
    //No cache
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    //Define file size
    header('Content-Length: ' . filesize($file_path));
    flush();
    //readfile($file_path);
    $file = fopen($file_path, "r");
    session_write_close(); // MUSI BYC!!! inaczej podczas pobierania wiekszych plikow strona zawiesza sie na czas pobierania
    while (!feof($file))
    {
        print(fread($file, 1024 * 1024));
        flush();
    }
    fclose($file);
}

function check_path($dynamic_path, $redirect)
{
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    require_once('tables.php');
    require_once('serial-code-status.php');
    global $wpdb, $wp_query, $table_name_products, $table_name_dynamic_links, $table_name_downloads, $table_name_codes, $product_downloads_amount, 
        $shortcode_rendered_url;
    
    $sql = "SELECT * FROM $table_name_products
    INNER JOIN $table_name_dynamic_links
    ON $table_name_products.product_id = $table_name_dynamic_links.product_id
    INNER JOIN $table_name_codes
    ON $table_name_dynamic_links.serial_code = $table_name_codes.serial_code
    WHERE $table_name_dynamic_links.dynamic_path = \"$dynamic_path\";";    
    $result = $wpdb->get_results($sql);
    if ($result == null)
    {
        if ($redirect)
        {
            $wp_query->set_404();
            status_header(404);
            get_template_part(404);
            exit();
        }
        return array('status' => 'invalid_url', 'description' => 'invalid url');
    }
    $file_path = $result[0]->file_url;
    $serial_code = $result[0]->serial_code;
    $product_id = $result[0]->product_id;
    $package_id = $result[0]->package_reference;
    $file_ext_name = $result[0]->file_ext_name;
    if (is_serial_code_valid($serial_code)['status'] == CODE_RESULT_EXPIRED)
    {
        if ($redirect)
            redirect($_SESSION['shortcode_rendered_url'] . "?serial_code=" . $serial_code);
        return array('status' => 'expired', 'description' => 'serial code expired');
    }
    if (!file_exists($file_path))
        return array('status' => 'internal_error', 'description' => 'file does not exist'); // błąd wewnętrzny, plik powinien istnieć

    $sql = "SELECT * FROM $table_name_downloads WHERE serial_code = $serial_code AND product_id = $product_id AND ctd = 1;";
    $result = $wpdb->get_results($sql);
    //echo 'count($result) = ' . count($result) . ' $product_downloads_amount = ' . $product_downloads_amount;
    if (count($result) >= $product_downloads_amount)
    {
        if ($redirect)
            redirect($_SESSION['shortcode_rendered_url'] . "?serial_code=" . $serial_code . "&cpe=" . $product_id);
        return array('status' => 'expired', 'description' => 'product download limit exceeded');
    }

    // plik istnieje oraz kod nadal jest ważny, można przesłać plik
    return array('status' => 'valid', 'serial_code' => $serial_code, 'product_id' => $product_id, 'package_id' => $package_id, 
        'file_path' => $file_path, 'file_ext_name' => $file_ext_name);
}

?>