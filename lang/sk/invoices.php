<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Invoices Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for the invoices management pages
    | and features throughout the application.
    |
    */

    'title' => 'Faktúry',
    'search_placeholder' => 'Vyhľadať faktúry...',
    'add_invoice' => 'Pridať faktúru',
    'edit_invoice' => 'Upraviť faktúru',

    'delete_confirmation_title' => 'Vymazať faktúru',
    'delete_confirmation_description' => 'Túto akciu nie je možné vrátiť späť.',
    'delete_confirmation_message' => 'Naozaj chcete vymazať faktúru #{number}?',

    'form' => [
        'title_add' => 'Pridať novú faktúru',
        'description_add' => 'Vyplňte údaje faktúry nižšie pre vytvorenie novej faktúry.',
        'title_edit' => 'Upraviť faktúru',
        'description_edit' => 'Aktualizujte údaje faktúry nižšie.',
        'number' => 'Číslo faktúry',
        'client' => 'Klient',
        'select_client' => 'Vyberte klienta',
        'search_clients' => 'Vyhľadať klientov...',
        'no_clients_found' => 'Nenašli sa žiadni klienti',
        'billed_date' => 'Dátum vystavenia',
        'due_date' => 'Dátum splatnosti',
        'send_date' => 'Dátum odoslania',
        'variable_symbol' => 'Variabilný symbol',
        'constant_symbol' => 'Konštantný symbol',
        'specific_symbol' => 'Špecifický symbol',
        'order_id' => 'Číslo objednávky',
        'items' => 'Položky faktúry',
        'item_name' => 'Názov položky',
        'quantity' => 'Množstvo',
        'price' => 'Cena',
        'tax' => 'DPH (%)',
        'item_name_placeholder' => 'Zadajte názov položky',
        'add_item' => 'Pridať položku',
        'total_price' => 'Celková cena',
        'total_tax' => 'Celková DPH',
        'total_with_tax' => 'Celková cena s DPH',
        'note' => 'Poznámka',
        'submit_add' => 'Vytvoriť faktúru',
        'submit_edit' => 'Aktualizovať faktúru',
    ],

    'table' => [
        'number' => 'Číslo',
        'billed_date' => 'Dátum vystavenia',
        'due_date' => 'Dátum splatnosti',
        'client' => 'Klient',
        'total' => 'Celkom',
        'status' => 'Stav',
        'actions' => 'Akcie',
        'no_invoices' => 'Nenašli sa žiadne faktúry. Pridajte svoju prvú faktúru kliknutím na tlačidlo "Pridať faktúru".',
    ],

    'status' => [
        'paid' => 'Zaplatená',
        'sent' => 'Odoslaná',
        'draft' => 'Koncept',
    ],

    'actions' => [
        'download' => 'Stiahnuť',
        'edit' => 'Upraviť',
        'delete' => 'Vymazať',
    ],

    'pagination' => [
        'invoices' => 'faktúr',
    ],
];
