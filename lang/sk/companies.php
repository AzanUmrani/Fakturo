<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Companies Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for the companies management pages
    | and features throughout the application.
    |
    */

    'title' => 'Spoločnosti',
    'description' => 'Spravujte svoje spoločnosti',
    'add_company' => 'Pridať spoločnosť',
    'edit_company' => 'Upraviť spoločnosť',
    'delete_company' => 'Vymazať spoločnosť',
    'search_placeholder' => 'Vyhľadať spoločnosti...',
    'no_companies' => 'Neboli nájdené žiadne spoločnosti. Pridajte svoju prvú spoločnosť kliknutím na tlačidlo "Pridať spoločnosť".',
    'company_created' => 'Spoločnosť bola úspešne vytvorená.',
    'company_updated' => 'Spoločnosť bola úspešne aktualizovaná.',
    'company_deleted' => 'Spoločnosť bola úspešne vymazaná.',
    'delete_confirm' => 'Naozaj chcete vymazať túto spoločnosť?',
    'delete_confirm_message' => 'Naozaj chcete vymazať spoločnosť "{name}"? Túto akciu nemožno vrátiť späť.',

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
        'companies' => 'spoločností',
    ],

    // Form fields
    'form' => [
        'title_add' => 'Pridať novú spoločnosť',
        'title_edit' => 'Upraviť spoločnosť',
        'description_add' => 'Vyplňte informácie o spoločnosti nižšie pre vytvorenie novej spoločnosti.',
        'description_edit' => 'Aktualizujte informácie o spoločnosti nižšie.',
        'submit_add' => 'Pridať spoločnosť',
        'submit_edit' => 'Aktualizovať spoločnosť',

        // Basic info
        'name' => 'Názov',
        'state' => 'Štát/Krajina',
        'state_placeholder' => 'Vyberte krajinu',
        'street' => 'Ulica',
        'street_extra' => 'Doplňujúce informácie k adrese',
        'zip' => 'PSČ',
        'city' => 'Mesto',

        // Business identification
        'tax_type' => 'Typ dane',
        'identification_number' => 'IČO',
        'vat_identification_number' => 'DIČ',
        'vat_identification_number_sk' => 'IČ DPH',
        'registry_info' => 'Informácie o registrácii',

        // Contact information
        'contact_name' => 'Kontaktná osoba',
        'contact_phone' => 'Telefón',
        'contact_email' => 'Email',
        'contact_web' => 'Webová stránka',
        'website_placeholder' => 'https://example.com',
    ],

    // Buttons
    'buttons' => [
        'edit' => 'Upraviť',
        'delete' => 'Vymazať',
        'cancel' => 'Zrušiť',
        'save' => 'Uložiť',
    ],
];
