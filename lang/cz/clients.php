<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Clients Language Lines (Czech)
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for the clients management pages
    | and features throughout the application.
    |
    */

    'title' => 'Klienti',
    'description' => 'Správa klientů',
    'add_client' => 'Přidat klienta',
    'edit_client' => 'Upravit klienta',
    'delete_client' => 'Odstranit klienta',
    'search_placeholder' => 'Vyhledat klienty...',
    'no_clients' => 'Nenalezeni žádní klienti. Přidejte svého prvního klienta kliknutím na tlačítko "Přidat klienta".',
    'client_created' => 'Klient byl úspěšně vytvořen.',
    'client_updated' => 'Klient byl úspěšně aktualizován.',
    'client_deleted' => 'Klient byl úspěšně odstraněn.',
    'delete_confirm' => 'Opravdu chcete odstranit tohoto klienta?',
    'delete_confirm_message' => 'Opravdu chcete odstranit klienta "{name}"? Tuto akci nelze vrátit zpět.',

    // Table headers
    'table' => [
        'name' => 'Název',
        'id_number' => 'IČO',
        'city' => 'Město',
        'contact' => 'Kontakt',
        'actions' => 'Akce',
    ],

    // Pagination
    'pagination' => [
        'showing' => 'Zobrazuje se',
        'to' => 'až',
        'of' => 'z',
        'clients' => 'klientů',
    ],

    // Form fields
    'form' => [
        'title_add' => 'Přidat nového klienta',
        'title_edit' => 'Upravit klienta',
        'description_add' => 'Vyplňte informace o klientovi níže pro vytvoření nového klienta.',
        'description_edit' => 'Aktualizujte informace o klientovi níže.',
        'submit_add' => 'Přidat klienta',
        'submit_edit' => 'Aktualizovat klienta',

        // Basic info
        'name' => 'Název',
        'state' => 'Stát/Země',
        'state_placeholder' => 'Vyberte zemi',
        'street' => 'Ulice',
        'street_extra' => 'Doplňující informace k adrese',
        'zip' => 'PSČ',
        'city' => 'Město',

        // Business identification
        'identification_number' => 'IČO',
        'vat_identification_number' => 'DIČ',
        'vat_identification_number_sk' => 'IČ DPH',
        'registry_info' => 'Informace o registraci',

        // Contact information
        'contact_name' => 'Kontaktní osoba',
        'contact_phone' => 'Telefon',
        'contact_email' => 'E-mail',
        'contact_web' => 'Webová stránka',
        'website_placeholder' => 'https://priklad.cz',
    ],

    // Buttons
    'buttons' => [
        'edit' => 'Upravit',
        'delete' => 'Odstranit',
        'cancel' => 'Zrušit',
        'save' => 'Uložit',
    ],
];
