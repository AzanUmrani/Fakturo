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

    'title' => 'Společnosti',
    'description' => 'Spravujte své společnosti',
    'add_company' => 'Přidat společnost',
    'edit_company' => 'Upravit společnost',
    'delete_company' => 'Smazat společnost',
    'search_placeholder' => 'Vyhledat společnosti...',
    'no_companies' => 'Nebyly nalezeny žádné společnosti. Přidejte svou první společnost kliknutím na tlačítko "Přidat společnost".',
    'company_created' => 'Společnost byla úspěšně vytvořena.',
    'company_updated' => 'Společnost byla úspěšně aktualizována.',
    'company_deleted' => 'Společnost byla úspěšně smazána.',
    'delete_confirm' => 'Opravdu chcete smazat tuto společnost?',
    'delete_confirm_message' => 'Opravdu chcete smazat společnost "{name}"? Tuto akci nelze vrátit zpět.',

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
        'companies' => 'společností',
    ],

    // Form fields
    'form' => [
        'title_add' => 'Přidat novou společnost',
        'title_edit' => 'Upravit společnost',
        'description_add' => 'Vyplňte informace o společnosti níže pro vytvoření nové společnosti.',
        'description_edit' => 'Aktualizujte informace o společnosti níže.',
        'submit_add' => 'Přidat společnost',
        'submit_edit' => 'Aktualizovat společnost',

        // Basic info
        'name' => 'Název',
        'state' => 'Stát/Země',
        'state_placeholder' => 'Vyberte zemi',
        'street' => 'Ulice',
        'street_extra' => 'Doplňující informace k adrese',
        'zip' => 'PSČ',
        'city' => 'Město',

        // Business identification
        'tax_type' => 'Typ daně',
        'identification_number' => 'IČO',
        'vat_identification_number' => 'DIČ',
        'vat_identification_number_sk' => 'IČ DPH',
        'registry_info' => 'Informace o registraci',

        // Contact information
        'contact_name' => 'Kontaktní osoba',
        'contact_phone' => 'Telefon',
        'contact_email' => 'Email',
        'contact_web' => 'Webová stránka',
        'website_placeholder' => 'https://example.com',
    ],

    // Buttons
    'buttons' => [
        'edit' => 'Upravit',
        'delete' => 'Smazat',
        'cancel' => 'Zrušit',
        'save' => 'Uložit',
    ],
];
