Na razie skrawek dokumentacji co i jak obsługiwać

Żeby wyświetlić na stronie okienko z inputem, działa to tak samo jak 
w starej wtyczce tylko trzeba wpisać w nawiasy klamrowe [plugin-test]
WAŻNE żeby działały dynamiczne odnośniki, po włączeniu wtyczki należy 
wejść w kokpicie Wordpressa w Ustawienia->Bezpośrednie odnośniki i tam 
na samym dole (bez zmieniania niczego) wcisnąć "Zapisz zmiany" - powoduje to
flush odnośników który z jakiegoś powodu nie dzieje się sam po wywołaniu odpowiednich
funkcji z API

Cała wtyczka ma łącznie 5 różnych baz: produkty (wp_products_p), 
pakiety (wp_packages_p), kody seryjne (wp_serial_codes_p), dynamiczne linki
(wp_dynamic_links_p) i logi pobrań (wp_downloads_p)

jak na razie na panelu administratora wtyczki działają wam opcje dodawania oraz usuwania: produktów, 
pakietów oraz kodów (dodam jeszcze edytowanie, na razie jest przycisk ale nic nie robi)

logi z pobrań są zapisywane do bazy ale jeszcze nie wyświetlam ich na ekranie, również dodam to w jakiejś 
formie w przyszłości

teraz to chyba tyle, jak coś to proszę o feedback itd.