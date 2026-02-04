<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Clients Language Lines (Slovak)
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for the clients management pages
    | and features throughout the application.
    |
    */

    'title' => 'Klienti',
    'description' => 'Správa klientov',
    'add_client' => 'Pridať klienta',
    'edit_client' => 'Upraviť klienta',
    'delete_client' => 'Odstrániť klienta',
    'search_placeholder' => 'Vyhľadať klientov...',
    'no_clients' => 'Nenašli sa žiadni klienti. Pridajte svojho prvého klienta kliknutím na tlačidlo "Pridať klienta".',
    'client_created' => 'Klient bol úspešne vytvorený.',
    'client_updated' => 'Klient bol úspešne aktualizovaný.',
    'client_deleted' => 'Klient bol úspešne odstránený.',
    'delete_confirm' => 'Naozaj chcete odstrániť tohto klienta?',
    'delete_confirm_message' => 'Naozaj chcete odstrániť klienta "{name}"? Túto akciu nemožno vrátiť späť.',

    // Table headers
    'table' => [
        'name' => 'Názov',
        'id_number' => 'IČO',
        'city' => 'Mesto',
        'contact' => 'Kontakt',
        'actions' => 'Akcie',
    ],

    // Pagination
    'pagination' => [
        'showing' => 'Zobrazuje sa',
        'to' => 'až',
        'of' => 'z',
        'clients' => 'klientov',
    ],

    // Form fields
    'form' => [
        'title_add' => 'Pridať nového klienta',
        'title_edit' => 'Upraviť klienta',
        'description_add' => 'Vyplňte informácie o klientovi nižšie pre vytvorenie nového klienta.',
        'description_edit' => 'Aktualizujte informácie o klientovi nižšie.',
        'submit_add' => 'Pridať klienta',
        'submit_edit' => 'Aktualizovať klienta',

        // Basic info
        'name' => 'Názov',
        'state' => 'Štát/Krajina',
        'state_placeholder' => 'Vyberte krajinu',
        'street' => 'Ulica',
        'street_extra' => 'Doplňujúce informácie k adrese',
        'zip' => 'PSČ',
        'city' => 'Mesto',

        // Business identification
        'identification_number' => 'IČO',
        'vat_identification_number' => 'DIČ',
        'vat_identification_number_sk' => 'IČ DPH',
        'registry_info' => 'Informácie o registrácii',

        // Contact information
        'contact_name' => 'Kontaktná osoba',
        'contact_phone' => 'Telefón',
        'contact_email' => 'E-mail',
        'contact_web' => 'Webová stránka',
        'website_placeholder' => 'https://priklad.sk',
    ],

    // Buttons
    'buttons' => [
        'edit' => 'Upraviť',
        'delete' => 'Odstrániť',
        'cancel' => 'Zrušiť',
        'save' => 'Uložiť',
    ],
];
