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

    'title' => 'Faktury',
    'search_placeholder' => 'Vyhledat faktury...',
    'add_invoice' => 'Přidat fakturu',
    'edit_invoice' => 'Upravit fakturu',

    'delete_confirmation_title' => 'Smazat fakturu',
    'delete_confirmation_description' => 'Tuto akci nelze vrátit zpět.',
    'delete_confirmation_message' => 'Opravdu chcete smazat fakturu #{number}?',

    'form' => [
        'title_add' => 'Přidat novou fakturu',
        'description_add' => 'Vyplňte údaje faktury níže pro vytvoření nové faktury.',
        'title_edit' => 'Upravit fakturu',
        'description_edit' => 'Aktualizujte údaje faktury níže.',
        'number' => 'Číslo faktury',
        'client' => 'Klient',
        'select_client' => 'Vyberte klienta',
        'search_clients' => 'Vyhledat klienty...',
        'no_clients_found' => 'Nenalezeni žádní klienti',
        'billed_date' => 'Datum vystavení',
        'due_date' => 'Datum splatnosti',
        'send_date' => 'Datum odeslání',
        'variable_symbol' => 'Variabilní symbol',
        'constant_symbol' => 'Konstantní symbol',
        'specific_symbol' => 'Specifický symbol',
        'order_id' => 'Číslo objednávky',
        'items' => 'Položky faktury',
        'item_name' => 'Název položky',
        'quantity' => 'Množství',
        'price' => 'Cena',
        'tax' => 'DPH (%)',
        'item_name_placeholder' => 'Zadejte název položky',
        'add_item' => 'Přidat položku',
        'total_price' => 'Celková cena',
        'total_tax' => 'Celková DPH',
        'total_with_tax' => 'Celková cena s DPH',
        'note' => 'Poznámka',
        'submit_add' => 'Vytvořit fakturu',
        'submit_edit' => 'Aktualizovat fakturu',
    ],

    'table' => [
        'number' => 'Číslo',
        'billed_date' => 'Datum vystavení',
        'due_date' => 'Datum splatnosti',
        'client' => 'Klient',
        'total' => 'Celkem',
        'status' => 'Stav',
        'actions' => 'Akce',
        'no_invoices' => 'Nenalezeny žádné faktury. Přidejte svou první fakturu kliknutím na tlačítko "Přidat fakturu".',
    ],

    'status' => [
        'paid' => 'Zaplacená',
        'sent' => 'Odeslaná',
        'draft' => 'Koncept',
    ],

    'actions' => [
        'download' => 'Stáhnout',
        'edit' => 'Upravit',
        'delete' => 'Smazat',
    ],

    'pagination' => [
        'invoices' => 'faktur',
    ],
];
