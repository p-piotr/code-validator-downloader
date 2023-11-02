<?php

/**
 * Plugin Name:     Serial Code & Download Manager
 * Version:         0.7.0
 * Author:          Piotr Pazdan
 * Description:     Menedżer kodów seryjnych wraz z pobieraniem
 */

session_start();

require_once('tables.php');
require_once('serial-code-status.php');
require_once('serial-code-response.php');
require_once('admin-page.php');
require_once('globals.php');

function test_activation()
{
    create_code_table();
}

function add_filter_download($query_vars)
{
    $query_vars[] = 'download';
    return $query_vars;
}

function add_filter_check($query_vars)
{
    $query_vars[] = 'check';
    return $query_vars;
}

function add_filter_add_download_log($query_vars)
{
    $query_vars[] = 'add-download-log';
    return $query_vars;
}

function add_templates($template)
{
    $var_download = get_query_var('download');
    $var_check = get_query_var('check');
    $var_add_download_log = get_query_var('add-download-log');
    if ($var_download == false && $var_check == false && $var_add_download_log == false)
        return $template;
    if ($var_download != false)
        return WP_PLUGIN_DIR . '/scdm/download-page-template.php';
    if ($var_check != false)
        return WP_PLUGIN_DIR . '/scdm/check-code-template.php';
    return WP_PLUGIN_DIR . '/scdm/add-download-log.php';
}

function test_uninstall()
{
    remove_action('template_include', 'add_templates');
    remove_filter('query_vars', 'add_filter_download');
    remove_filter('query_vars', 'add_filter_check');
    remove_filter('query_vars', 'add_filter_add_download_log');
    flush_rewrite_rules();
    remove_shortcode('plugin-test');
}

function add_serial_code_input($attr = [], $content = null, $tag = '')
{
    global $wp;
    $_SESSION['shortcode_rendered_url'] = home_url($wp->request);
    $serial_code = $_REQUEST['serial_code'];
    $cpe = $_REQUEST['cpe'];
    $output = //'<style type="text/css"> input { font-size: 17px; margin: 0 auto; height: 100%;} input[type="submit"] { font-size: 17px; }</style>
            '<div style="display: flex;justify-content: center;">
                <form action="?">
                    <input style="border-width: 0; width: 230px; height: 55px;" type="text" id="serial_code_box" name="serial_code" placeholder="XXXXXXXXX">
                    <input style="height: 100%;" type="submit" value="SPRAWDŹ">
                </form>
            </div>';
    if (!isset($serial_code))
        return $output;
    if (!isset($cpe))
        $cpe = 0;
    $resp = is_serial_code_valid($serial_code);
    switch($resp['status'])
    {
        case CODE_RESULT_EXPIRED:
            $output .= '<br>' . invalid_serial_code_expired();
            break;
        case CODE_RESULT_ILLEGAL_CHARACTERS:
            $output .= '<br>' . invalid_serial_code_illegal_characters();
            break;
        case CODE_RESULT_NOT_FOUND:
            $output .= '<br>' . invalid_serial_code_not_found();
            break;
        case CODE_RESULT_VALID:
            $package = $resp['package'];
            $output .= '<script>';
            $js_array = file(WP_PLUGIN_DIR . '/scdm/js/client-dialog-handler.js');
            foreach ($js_array as $line)
                $output .= $line;
            $output .= '</script>
                        <dialog style="left: 0; right: 0; top: 0; bottom: 0; margin: auto; position: absolute;" id="dialog">' 
                        . valid_serial_code($serial_code, $package, $cpe) . 
                            '<br><div style="text-align:center;"><button type="button" onclick="performClose()">ANULUJ</button></div>
                        </dialog>';
            break;
        default:
            break;
    }

    return $output;
}

register_activation_hook(__FILE__, 'test_activation');
register_uninstall_hook(__FILE__, 'test_uninstall');
add_shortcode('scdm', 'add_serial_code_input');

add_action( 'init',  function() {
    add_rewrite_rule('download/([^/]*)/?$', 'index.php?download=$matches[1]', 'top');
    add_rewrite_rule('check?([^/]*)/?$', 'index.php?check=check/$matches[1]', 'top');
    add_rewrite_rule('add-download-log?([^/]*)/?$', 'index.php?add-download-log=add-download-log/$matches[1]', 'top');
    flush_rewrite_rules();
} );

add_filter('query_vars', 'add_filter_download');
add_filter('query_vars', 'add_filter_check');
add_filter('query_vars', 'add_filter_add_download_log');

add_action('template_include', 'add_templates');