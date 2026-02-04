<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Products Language Lines (Czech)
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for the products management pages
    | and features throughout the application.
    |
    */

    'title' => 'Produkty',
    'description' => 'Správa produktů',
    'add_product' => 'Přidat produkt',
    'edit_product' => 'Upravit produkt',
    'delete_product' => 'Odstranit produkt',
    'search_placeholder' => 'Vyhledat produkty...',
    'no_products' => 'Nebyly nalezeny žádné produkty. Přidejte svůj první produkt kliknutím na tlačítko "Přidat produkt".',
    'product_created' => 'Produkt byl úspěšně vytvořen.',
    'product_updated' => 'Produkt byl úspěšně aktualizován.',
    'product_deleted' => 'Produkt byl úspěšně odstraněn.',
    'delete_confirm' => 'Opravdu chcete odstranit tento produkt?',
    'delete_confirm_message' => 'Opravdu chcete odstranit produkt "{name}"? Tuto akci nelze vrátit zpět.',

    // Table headers
    'table' => [
        'image' => 'Obrázek',
        'name' => 'Název',
        'type' => 'Typ',
        'price' => 'Cena',
        'sku' => 'SKU',
        'actions' => 'Akce',
    ],

    // Pagination
    'pagination' => [
        'showing' => 'Zobrazuje se',
        'to' => 'až',
        'of' => 'z',
        'products' => 'produktů',
    ],

    // Form fields
    'form' => [
        'title_add' => 'Přidat nový produkt',
        'title_edit' => 'Upravit produkt',
        'description_add' => 'Vyplňte informace o produktu níže pro vytvoření nového produktu.',
        'description_edit' => 'Aktualizujte informace o produktu níže.',
        'submit_add' => 'Přidat produkt',
        'submit_edit' => 'Aktualizovat produkt',

        // Basic info
        'name' => 'Název',
        'type' => 'Typ',
        'description' => 'Popis',
        'price' => 'Cena',
        'taxRate' => 'Sazba daně (%)',
        'discount' => 'Sleva (%)',
        'unit' => 'Jednotka',
        'sku' => 'SKU',
        'weight' => 'Hmotnost',
        'has_image' => 'Obrázek produktu',
        'upload_image' => 'Nahrát obrázek',
        'remove_image' => 'Odstranit obrázek',

        // Product types
        'types' => [
            'product' => 'Produkt',
            'service' => 'Služba',
        ],
    ],

    // Buttons
    'buttons' => [
        'edit' => 'Upravit',
        'delete' => 'Odstranit',
        'cancel' => 'Zrušit',
        'save' => 'Uložit',
    ],
];
