<?php

add_action('admin_menu', 'test_create_menu');

function test_create_menu()
{
    add_menu_page('Ustawienia Test Plugin', 'Test Plugin', 'administrator', __FILE__,
    'test_plugin_page', '', __FILE__);
    add_action('admin_init', 'register_test_plugin_settings');
}

function register_test_plugin_settings()
{
    register_setting('test-plugin-settings-group', 'comments_file_location');
    register_setting('test-plugin-settings-group', 'code_expiry_time_days');
    register_setting('test-plugin-settings-group', 'product_downloads_amount');
}

function show_products_table()
{
    ?>
        <dialog id="product_add_dialog">
            <a class="close-X" onclick="close_add_product_dialog()">&#10006</a>
            <br><br>
            <form action="" method="POST">
                <input type="text" name="action" value="add_product" style="display:none">
                <a>Nazwa produktu:</a>
                <input type="text" size="32" name="product_name">
                <br><br>
                <a>Nazwa zewnętrzna pliku:</a>
                <input type="text" size="32" name="file_ext_name">
                <br><br>
                <a>Ścieżka do pliku:</a>
                <input type="text" size="128" name="file_url">
                <br><br>
                <input class="own" type="submit" value="Dodaj">
            </form>
        </dialog>
        <dialog id="product_edit_dialog">
            <a class="close-X" onclick="close_edit_product_dialog()">&#10006</a>
            <br><br>
            <form action="" method="POST">
                <input type="text" name="action" value="edit_product" style="display:none">
                <a>ID produktu:</a>
                <span>&nbsp<strong style="font-size: 15px" id="product_id"></strong></span>
                <input type="text" id="product_id_input" name="product_id" value="" style="display:none">
                <br><br>
                <a>Nazwa produktu:</a>
                <input type="text" size="32" id="product_name_input" name="product_name">
                <br><br>
                <a>Nazwa zewnętrzna pliku:</a>
                <input type="text" size="32" id="file_ext_name_input" name="file_ext_name">
                <br><br>
                <a>Ścieżka do pliku:</a>
                <input type="text" size="128" id="file_url_input" name="file_url">
                <br><br>
                <input class="own" type="submit" value="Zatwierdź">
            </form>
        </dialog>
        <div>
            <h2>Produkty</h2>
            <table class="dataTable">
                <tr>
                    <th>ID produktu</th>
                    <th>Nazwa produktu</th>
                    <th>Zewnętrzna nazwa pliku</th>
                    <th>Ścieżka do pliku</th>
                </tr>
                <?php 
                    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                    require_once('globals.php');
                    global $wpdb, $table_name_products;

                    $sql = "SELECT * FROM $table_name_products;";
                    $products = $wpdb->get_results($sql);
                    foreach ($products as $product)
                    {
                        ?>
                            <tr>
                                <td class="row" id="product_productid_<?php echo $product->product_id ?>"><?php echo $product->product_id ?></td>
                                <td class="row" id="product_productname_<?php echo $product->product_id ?>"><?php echo $product->product_name ?></td>
                                <td class="row" id="product_fileextname_<?php echo $product->product_id ?>"><?php echo $product->file_ext_name ?></td>
                                <td class="row" id="product_fileurl_<?php echo $product->product_id ?>"><?php echo $product->file_url ?></td>
                                <td class="td_button"><button type="button" class="own" onclick="edit_product(<?php echo $product->product_id ?>)">Edytuj</button></td>
                                <td class="td_button">
                                    <form action="" method="POST" onsubmit="return confirm('Czy jesteś pewny że chcesz usunąć \'<?php echo $product->product_name ?>\'? Ta operacja jest nieodwracalna!');">
                                        <input type="text" name="action" value="delete_product" style="display:none">
                                        <input type="text" name="product_id" value="<?php echo $product->product_id ?>" style="display:none">
                                        <input class="own delete" type="submit" value="Usuń">
                                    </form>
                                </td>
                            </tr>
                        <?php
                    }
                ?>
            </table>
            <div style="margin-top: 5px"><button class="own" onclick="add_product_dialog()">Dodaj</button></div>
        </div>
    <?php
}

function show_packages_table()
{
    ?>
        <dialog id="package_add_dialog">
            <a class="close-X" onclick="close_add_package_dialog()">&#10006</a>
            <br><br>
            <form action="" method="POST">
                <input type="text" name="action" value="add_package" style="display:none">
                <a>Nazwa pakietu:</a>
                <input type="text" size="32" name="package_name">
                <br><br>
                <a>Zawarte produkty</a>
                <input type="text" size="32" name="products_included">
                <br><br>
                <input class="own" type="submit" value="Dodaj">
            </form>
        </dialog>
        <dialog id="package_edit_dialog">
            <a class="close-X" onclick="close_edit_package_dialog()">&#10006</a>
            <br><br>
            <form action="" method="POST">
                <input type="text" name="action" value="edit_package" style="display:none">
                <a>ID pakietu:</a>
                <span>&nbsp<strong style="font-size: 15px" id="package_id"></strong></span>
                <input type="text" id="package_id_input" name="package_id" value="" style="display:none">
                <br><br>
                <a>Nazwa pakietu:</a>
                <input type="text" size="32" id="package_name_input" name="package_name">
                <br><br>
                <a>Zawarte produkty</a>
                <input type="text" size="32" id="products_included_input" name="products_included">
                <br><br>
                <input class="own" type="submit" value="Zatwierdź">
            </form>
        </dialog>
        <div>
            <h2>Pakiety</h2>
            <table class="dataTable">
                <tr>
                    <th>ID pakietu</th>
                    <th>Nazwa pakietu</th>
                    <th>Zawarte produkty</th>
                </tr>
                <?php 
                    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                    require_once('globals.php');
                    global $wpdb, $table_name_packages;

                    $sql = "SELECT * FROM $table_name_packages;";
                    $packages = $wpdb->get_results($sql);
                    foreach ($packages as $package)
                    {
                        ?>
                            <tr>
                                <td class="row" id="package_packageid_<?php echo $package->package_id ?>"><?php echo $package->package_id ?></td>
                                <td class="row" id="package_packagename_<?php echo $package->package_id ?>"><?php echo $package->package_name ?></td>
                                <td class="row" id="package_productsincluded_<?php echo $package->package_id ?>"><?php echo $package->products_included ?></td>
                                <td class="td_button"><button class="own" onclick="edit_package(<?php echo $package->package_id ?>)">Edytuj</button></td>
                                <td class="td_button">
                                    <form action="" method="POST" onsubmit="return confirm('Czy jesteś pewny że chcesz usunąć \'<?php echo $package->package_name ?>\'? Ta operacja jest nieodwracalna!');">
                                        <input type="text" name="action" value="delete_package" style="display:none">
                                        <input type="text" name="package_id" value="<?php echo $package->package_id ?>" style="display:none">
                                        <input class="own delete" type="submit" value="Usuń">
                                    </form>
                                </td>
                            </tr>
                        <?php
                    }
                ?>
            </table>
            <div style="margin-top: 5px"><button class="own" onclick="add_package_dialog()">Dodaj</button></div>
        </div>
    <?php
}

function show_codes_table()
{
    ?>
        <dialog id="code_add_dialog">
            <a class="close-X" onclick="close_add_code_dialog()">&#10006</a>
            <br><br>
            <form action="" method="POST">
                <input type="text" name="action" value="add_code" style="display:none">
                <a>Kod seryjny</a>
                <input type="text" size="32" name="serial_code">
                <br><br>
                <a>Odwołanie do pakietu</a>
                <input type="text" size="2" name="package_reference">
                <br><br>
                <a>Wygasa za (domyślnie NULL)</a>
                <input type="text" size="20" name="expires_at" value="NULL">
                <a>Status (domyślnie active)</a>
                <input type="text" size="10" name="status" value="active">
                <input class="own" type="submit" value="Dodaj">
            </form>
        </dialog>
        <dialog id="code_edit_dialog">
            <a class="close-X" onclick="close_edit_code_dialog()">&#10006</a>
            <br><br>
            <form action="" method="POST">
                <input type="text" name="action" value="edit_code" style="display:none">
                <a>Kod seryjny</a>
                <span>&nbsp<strong style="font-size: 15px" id="serial_code"></strong></span>
                <input type="text" id="serial_code_input" name="serial_code" value="" style="display:none">
                <br><br>
                <a>Odwołanie do pakietu</a>
                <input type="text" size="2" id = "package_reference_input" name="package_reference">
                <br><br>
                <a>Wygasa za (domyślnie NULL)</a>
                <input type="text" size="20" id = "expires_at_input" name="expires_at" value="NULL">
                <a>Status (domyślnie active)</a>
                <input type="text" size="10" id = "status_input" name="status" value="active">
                <input class="own" type="submit" value="Zatwierdź">
            </form>
        </dialog>
        <div>
            <h2>Kody seryjne</h2>
            <table class="dataTable">
                <tr>
                    <th>Kod seryjny</th>
                    <th>Odwołanie do pakietu</th>
                    <th>Wygasa za</th>
                    <th>Status</th>
                </tr>
                <?php 
                    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                    require_once('globals.php');
                    global $wpdb, $table_name_codes;

                    $sql = "SELECT * FROM $table_name_codes;";
                    $codes = $wpdb->get_results($sql);
                    foreach ($codes as $code)
                    {
                        ?>
                            <tr>
                                <td class="row" id="code_serialcode_<?php echo $code->serial_code ?>"><?php echo $code->serial_code ?></td>
                                <td class="row" id="code_packagereference_<?php echo $code->serial_code ?>"><?php echo $code->package_reference ?></td>
                                <td class="row" id="code_expiresat_<?php echo $code->serial_code ?>"><?php echo $code->expires_at ?></td>
                                <td class="row" id="code_status_<?php echo $code->serial_code ?>"><?php echo $code->status ?></td>
                                <td class="td_button"><button class="own" onclick="edit_code(<?php echo $code->serial_code ?>)">Edytuj</button></td>
                                <td class="td_button">
                                    <form action="" method="POST" onsubmit="return confirm('Czy jesteś pewny że chcesz usunąć \'<?php echo $code->serial_code ?>\'? Ta operacja jest nieodwracalna!');">
                                        <input type="text" name="action" value="delete_code" style="display:none">
                                        <input type="text" name="serial_code" value="<?php echo $code->serial_code ?>" style="display:none">
                                        <input class="own delete" type="submit" value="Usuń">
                                    </form>
                                </td>
                            </tr>
                        <?php
                    }
                ?>
            </table>
            <div style="margin-top: 5px"><button class="own" onclick="add_code_dialog()">Dodaj</button></div>
            <h4>Jeżeli status kodu jest aktywny mimo minięcia daty wygaśnięcia - nic nieporządanego się nie dzieje.<br>
                Status kodu zostanie zmieniony na wygaśnięty w następnej próbie wykorzystania kodu, a klient<br>
                otrzyma informację o jego wygaśnięciu.
            </h4>
        </div>
    <?php
}

function show_downloads_table()
{
    ?>
        <table class="dataTable">
            <tr>
                <th>ID pobrania</th>
                <th>Kod seryjny</th>
                <th>ID produktu</th>
                <th>IP gościa</th>
                <th>Data</th>
                <th>User Agent</th>
                <th>Flaga CTD</th>
            </tr>
            <?php 
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                require_once('globals.php');
                global $wpdb, $table_name_downloads;
                $sql = "SELECT * FROM $table_name_downloads;";
                $downloads = $wpdb->get_results($sql);
                foreach ($downloads as $download)
                {
                    ?>
                        <tr>
                            <td class="row"><?php echo $download->id ?></td>
                            <td class="row"><?php echo $download->serial_code ?></td>
                            <td class="row"><?php echo $download->product_id ?></td>
                            <td class="row"><?php echo $download->visitor_ip ?></td>
                            <td class="row"><?php echo $download->date_time ?></td>
                            <td class="row"><?php echo $download->user_agent ?></td>
                            <td class="row"><?php echo $download->ctd ?></td>
                        </tr>
                    <?php
                }
            ?>
        </table>
        <div style="margin-top:15px;">
            <span>
                <strong>Flaga CTD</strong> - wewnętrzna flaga skryptu umożliwiająca filtrowanie rekordów pobrań pod kątem zaliczania ich do "poprawnych" pobrań
                <br>W przypadku sprawdzania przekroczenia limitu pobrań przez klienta, liczone są jedynie rekordy z flagą CTD o wartości 1; zmiana jej wartości na 0
                    de facto umożliwia dalsze pobieranie pliku do ustalonego limitu, bez konieczności usuwania rekordów pobrań.
            </span>
        </div>
    <?php
}

function test_plugin_page_default()
{
    ?>
    <style>
        <?php 
            $array = file(WP_PLUGIN_DIR . '/test-plugin/css/admin-page.css');
            foreach ($array as $line)
                echo $line;
        ?>
    </style>
    <script>
        <?php 
            $array = file(WP_PLUGIN_DIR . '/test-plugin/js/admin-page.js');
            foreach ($array as $line)
                echo $line;
        ?>
    </script>
    <div>
        <?php
            require_once('tables.php');
            $action = $_POST['action'];
            if ($action == 'show_downloads_table')
            {
                show_downloads_table();
                die();
            }
            else if ($action == 'add_product')
            {
                $product_name = $_POST['product_name'];
                $file_ext_name = $_POST['file_ext_name'];
                $file_url = $_POST['file_url'];
                if ($product_name !== null || $file_url !== null)
                {
                    if ($product_name === '' || $file_url === '')
                    {
                        ?><div><strong style="color:red; font-size: 17px;">Błędne dane - nie można dodać produktu</strong></div><?php
                    }
                    else if (add_product($product_name, $file_ext_name, $file_url))
                    {
                        ?><div><strong style="color:green; font-size: 17px">Dodawanie produktu powiodło się</strong></div><?php
                    }
                    else
                    {
                        ?><div><strong style="color:red; font-size: 17px">Nie udało się dodać produktu</strong></div><?php
                    }
                }
            }
            else if ($action == 'edit_product')
            {
                $product_id = $_POST['product_id'];
                $product_name = $_POST['product_name'];
                $file_ext_name = $_POST['file_ext_name'];
                $file_url = $_POST['file_url'];
                if ($product_name !== null || $file_url !== null)
                {
                    if ($product_name === '' || $file_ext_name === '' || $file_url === '')
                    {
                        ?><div><strong style="color:red; font-size: 17px;">Błędne dane - nie można edytować produktu</strong></div><?php
                    }
                    else if (edit_product($product_id, $product_name, $file_ext_name, $file_url))
                    {
                        ?><div><strong style="color:green; font-size: 17px">Edytowanie produktu powiodło się</strong></div><?php
                    }
                    else
                    {
                        ?><div><strong style="color:red; font-size: 17px">Błąd edycji produktu - żaden parametr nie został zmieniony lub wystąpił nieznay błąd</strong></div><?php
                    }
                }
            }
            else if ($action == 'delete_product')
            {
                $product_id = $_POST['product_id'];
                if ($product_id == null)
                {
                    ?><div><strong style="color:red; font-size: 17px;">Nie można usunąć produktu - błędne ID</strong></div><?php
                }
                else if (delete_product($product_id))
                {
                    ?><div><strong style="color:green; font-size: 17px">Usuwanie produktu powiodło się</strong></div><?php
                }
                else
                {
                    ?><div><strong style="color:red; font-size: 17px">Nie udało się usunąć produktu</strong></div><?php
                }
            }
            else if ($action == 'add_package')
            {
                $package_name = $_POST['package_name'];
                $products_included = $_POST['products_included'];
                if ($package_name !== null || $products_included !== null)
                {
                    if ($package_name === '' || $products_included === '')
                    {
                        ?><div><strong style="color:red; font-size: 17px;">Błędne dane - nie można dodać pakietu</strong></div><?php
                    }
                    else if (add_package($package_name, $products_included))
                    {
                        ?><div><strong style="color:green; font-size: 17px">Dodawanie pakietu powiodło się</strong></div><?php
                    }
                    else
                    {
                        ?><div><strong style="color:red; font-size: 17px">Nie udało się dodać pakietu</strong></div><?php
                    }
                }
            }
            else if ($action == 'edit_package')
            {
                $package_id = $_POST['package_id'];
                $package_name = $_POST['package_name'];
                $products_included = $_POST['products_included'];
                if ($package_name === '' || $products_included === '')
                {
                    ?><div><strong style="color:red; font-size: 17px;">Błędne dane - nie można edytować pakietu</strong></div><?php
                }
                else if (edit_package($package_id, $package_name, $products_included))
                {
                    ?><div><strong style="color:green; font-size: 17px">Edytowanie pakietu powiodło się</strong></div><?php
                }
                else
                {
                    ?><div><strong style="color:red; font-size: 17px">Błąd edycji pakietu - żaden parametr nie został zmieniony lub wystąpił nieznay błąd</strong></div><?php
                }
            }
            else if ($action == 'delete_package')
            {
                $package_id = $_POST['package_id'];
                if ($package_id == null)
                {
                    ?><div><strong style="color:red; font-size: 17px;">Nie można usunąć pakietu - błędne ID</strong></div><?php
                }
                else if (delete_package($package_id))
                {
                    ?><div><strong style="color:green; font-size: 17px">Usuwanie pakietu powiodło się</strong></div><?php
                }
                else
                {
                    ?><div><strong style="color:red; font-size: 17px">Nie udało się usunąć pakietu</strong></div><?php
                }
            }
            else if ($action == 'add_code')
            {
                $serial_code = $_POST['serial_code'];
                $package_reference = $_POST['package_reference'];
                $expires_at = $_POST['expires_at'];
                $status = $_POST['status'];
                if ($serial_code !== null || $package_reference !== null || $expires_at !== null || $status !== null)
                {
                    if (isset($expires_at) && $expires_at == 'NULL')
                        $expires_at = null;
                    if ($serial_code === '' || $package_reference === '' || $expires_at === '' || $status === '')
                    {
                        ?><div><strong style="color:red; font-size: 17px;">Błędne dane - nie można dodać kodu</strong></div><?php
                    }
                    else if (add_code($serial_code, $package_reference, $expires_at, $status))
                    {
                        ?><div><strong style="color:green; font-size: 17px">Dodawanie kodu powiodło się</strong></div><?php
                    }
                    else
                    {
                        ?><div><strong style="color:red; font-size: 17px">Nie udało się dodać kodu</strong></div><?php
                    }
                }
            }
            else if ($action == 'edit_code')
            {
                $serial_code = $_POST['serial_code'];
                $package_reference = $_POST['package_reference'];
                $expires_at = $_POST['expires_at'];
                $status = $_POST['status'];
                if ($expires_at === '')
                    $expires_at = null;
                if ($serial_code === '' || $package_reference === '' || $status === '')
                {
                    ?><div><strong style="color:red; font-size: 17px;">Błędne dane - nie można edytować kodu</strong></div><?php
                }
                else if (edit_code($serial_code, $package_reference, $expires_at, $status))
                {
                    ?><div><strong style="color:green; font-size: 17px">Edytowanie kodu powiodło się</strong></div><?php
                }
                else
                {
                    ?><div><strong style="color:red; font-size: 17px">Błąd edycji kodu - żaden parametr nie został zmieniony lub wystąpił nieznay błąd</strong></div><?php
                }
            }
            else if ($action == 'delete_code')
            {
                $serial_code = $_POST['serial_code'];
                if ($serial_code == null)
                {
                    ?><div><strong style="color:red; font-size: 17px;">Nie można usunąć kodu - błędny kod</strong></div><?php
                }
                else if (delete_code($serial_code))
                {
                    ?><div><strong style="color:green; font-size: 17px">Usuwanie kodu powiodło się</strong></div><?php
                }
                else
                {
                    ?><div><strong style="color:red; font-size: 17px">Nie udało się usunąć kodu</strong></div><?php
                }
            }
        ?>
        <h1>Ustawienia Test Plugin</h1>
        </div>
        <form method="POST" action="options.php">
            <?php settings_fields('test-plugin-settings-group')?>
            <?php do_settings_sections('test-plugin-settings-group')?>
            <div style="margin-bottom: 15px; margin-right: 15px;">
                Lokalizacja pliku z komentarzami (absolutna ścieżka)
                <br>
                <input size="128" type="text" name="comments_file_location" value="<?php echo esc_attr(get_option('comments_file_location')); ?>"/>
                <br>
                <i>Plik z komentarzami to plik zawierający komentarze które może otrzymać klient w różnych okolicznościach (np. kod wygasł)</i>
                <a href="#comments-help">Zobacz wyjaśnienie</a>
            </div>
            <div style="margin-bottom: 15px; margin-right: 15px;">
                Ważność kodu od pierwszego wpisania, <strong>w dobach</strong>
                <br>
                <input size="8" type="text" name="code_expiry_time_days" value="<?php echo esc_attr(get_option('code_expiry_time_days')); ?>"/>
                <br>
                <i>Domyślnie: <?php require_once('globals.php'); echo CODE_EXIPRY_TIME_DAYS_DEFAULT ?></i>
            </div>
            <div style="margin-bottom: 15px; margin-right: 15px;">
                Ilość pobrań każdego produktu z osobna z jednego kodu
                <br>
                <input size="5" type="text" name="product_downloads_amount" value="<?php echo esc_attr(get_option('product_downloads_amount')); ?>"/>
                <br>
                <i>Domyślnie: <?php require_once('globals.php'); echo PRODUCT_DOWNLOADS_AMOUNT_DEFAULT ?></i>
            </div>

            <?php submit_button(); ?>
        </form>
        <h1>Tabele</h1>
        <div id="products_table">
            <?php show_products_table() ?>
        </div>
        <div id="packages_table">
            <?php show_packages_table() ?>
        </div>
        <div id="codes_table">
            <?php show_codes_table() ?>
        </div>
        <div id="show_downloads_table">
            <div style="margin-top: 5px">
                <form action="" method="POST">
                    <input type="text" name="action" value="show_downloads_table" style="display:none">
                    <input type="submit" name="submit" class="button button-primary" value="Zobacz tabelę pobrań">
                </form>
            </div>
        </div>
        <div id="comments-help">
            <h2>Plik z komentarzami - wyjaśnienie</h2>
            Plik z komentarzami, jak zostało napisane wyżej, jest to plik (tekstowy .txt) zawierający komentarze do wyświetlenia klientowi.
            <br>
            Struktura tego pliku wygląda następująco: każda linia zawiera oddzielny komentarz, sformatowany w postaci ID komentarza oraz 
            jego treści (razem ze stylami) oddzielonych tyldą, oto przykład:
            <br><br>
            CODE_RESULT_NOT_FOUND`&lt;a style="font-size: 20px; color: #FFFFFF"&gt;Twój numer seryjny nie znajduje się w bazie&lt;/a&gt;
            <br><br>
            Bez żadnych cudzysłowów, spacji, innych znaków - po prostu. W przypadku braku podania pliku wyświetlane będą domyślne komentarze
            (jeżeli w samym pliku zostaną zdefiniowane tylko niektóre ID komentarzy, wtedy te zostaną podmienione, a reszta dalej będzie domyślna)
            <br><br>
            <h4>Możliwe ID komentarzy:</h4>
            <h3>CODE_RESULT_NOT_FOUND</h3> - wyświetlany w przypadku nieodnalezienia kodu w bazie<br><br>
            <h3>CODE_RESULT_ILLEGAL_CHARACTERS</h3> - wyświetlany, gdy wprowadzony kod zawiera niepoprawne znaki (np. cyfry, kropki etc.)<br><br>
            <h3>CODE_RESULT_EXPIRED</h3> - wyświetlany, gdy kod wygasł<br><br>
            <h3>CODE_RESULT_CPE</h3> - wyświetlany w oknie dialogowym, gdy przekroczono dozwoloną liczbę pobrań produktu z danego kodu
        </div>
        <br><br>
        <i style="font-size: 16px;">Wersja wtyczki: <strong><?php echo PLUGIN_VERSION; ?></strong></i>
    <?php
}

function test_plugin_page()
{
    ?>
        <?php test_plugin_page_default() ?>
    <?php
}

?>