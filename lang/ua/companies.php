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

    'title' => 'Компанії',
    'description' => 'Керуйте своїми компаніями',
    'add_company' => 'Додати компанію',
    'edit_company' => 'Редагувати компанію',
    'delete_company' => 'Видалити компанію',
    'search_placeholder' => 'Пошук компаній...',
    'no_companies' => 'Компанії не знайдено. Додайте свою першу компанію, натиснувши кнопку "Додати компанію".',
    'company_created' => 'Компанія успішно створена.',
    'company_updated' => 'Компанія успішно оновлена.',
    'company_deleted' => 'Компанія успішно видалена.',
    'delete_confirm' => 'Ви впевнені, що хочете видалити цю компанію?',
    'delete_confirm_message' => 'Ви впевнені, що хочете видалити компанію "{name}"? Цю дію неможливо скасувати.',

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
        'companies' => 'компаній',
    ],

    // Form fields
    'form' => [
        'title_add' => 'Додати нову компанію',
        'title_edit' => 'Редагувати компанію',
        'description_add' => 'Заповніть інформацію про компанію нижче, щоб створити нову компанію.',
        'description_edit' => 'Оновіть інформацію про компанію нижче.',
        'submit_add' => 'Додати компанію',
        'submit_edit' => 'Оновити компанію',

        // Basic info
        'name' => 'Назва',
        'state' => 'Держава/Країна',
        'state_placeholder' => 'Виберіть країну',
        'street' => 'Вулиця',
        'street_extra' => 'Додаткова інформація про адресу',
        'zip' => 'Поштовий індекс',
        'city' => 'Місто',

        // Business identification
        'tax_type' => 'Тип податку',
        'identification_number' => 'Ідентифікаційний номер',
        'vat_identification_number' => 'ІПН',
        'vat_identification_number_sk' => 'ІПН (SK)',
        'registry_info' => 'Інформація про реєстрацію',

        // Contact information
        'contact_name' => 'Контактна особа',
        'contact_phone' => 'Телефон',
        'contact_email' => 'Електронна пошта',
        'contact_web' => 'Веб-сайт',
        'website_placeholder' => 'https://example.com',
    ],

    // Buttons
    'buttons' => [
        'edit' => 'Редагувати',
        'delete' => 'Видалити',
        'cancel' => 'Скасувати',
        'save' => 'Зберегти',
    ],
];
