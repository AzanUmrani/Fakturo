<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Clients Language Lines (Ukrainian)
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for the clients management pages
    | and features throughout the application.
    |
    */

    'title' => 'Клієнти',
    'description' => 'Управління клієнтами',
    'add_client' => 'Додати клієнта',
    'edit_client' => 'Редагувати клієнта',
    'delete_client' => 'Видалити клієнта',
    'search_placeholder' => 'Пошук клієнтів...',
    'no_clients' => 'Клієнтів не знайдено. Додайте свого першого клієнта, натиснувши кнопку "Додати клієнта".',
    'client_created' => 'Клієнт успішно створений.',
    'client_updated' => 'Клієнт успішно оновлений.',
    'client_deleted' => 'Клієнт успішно видалений.',
    'delete_confirm' => 'Ви впевнені, що хочете видалити цього клієнта?',
    'delete_confirm_message' => 'Ви впевнені, що хочете видалити клієнта "{name}"? Цю дію неможливо скасувати.',

    // Table headers
    'table' => [
        'name' => 'Назва',
        'id_number' => 'Ідентифікаційний номер',
        'city' => 'Місто',
        'contact' => 'Контакт',
        'actions' => 'Дії',
    ],

    // Pagination
    'pagination' => [
        'showing' => 'Показано',
        'to' => 'до',
        'of' => 'з',
        'clients' => 'клієнтів',
    ],

    // Form fields
    'form' => [
        'title_add' => 'Додати нового клієнта',
        'title_edit' => 'Редагувати клієнта',
        'description_add' => 'Заповніть інформацію про клієнта нижче, щоб створити нового клієнта.',
        'description_edit' => 'Оновіть інформацію про клієнта нижче.',
        'submit_add' => 'Додати клієнта',
        'submit_edit' => 'Оновити клієнта',

        // Basic info
        'name' => 'Назва',
        'state' => 'Країна/Держава',
        'state_placeholder' => 'Виберіть країну',
        'street' => 'Вулиця',
        'street_extra' => 'Додаткова інформація про адресу',
        'zip' => 'Поштовий індекс',
        'city' => 'Місто',

        // Business identification
        'identification_number' => 'Ідентифікаційний номер',
        'vat_identification_number' => 'ІПН',
        'vat_identification_number_sk' => 'ІПН (SK)',
        'registry_info' => 'Інформація про реєстрацію',

        // Contact information
        'contact_name' => 'Контактна особа',
        'contact_phone' => 'Телефон',
        'contact_email' => 'Електронна пошта',
        'contact_web' => 'Веб-сайт',
        'website_placeholder' => 'https://example.ua',
    ],

    // Buttons
    'buttons' => [
        'edit' => 'Редагувати',
        'delete' => 'Видалити',
        'cancel' => 'Скасувати',
        'save' => 'Зберегти',
    ],
];
