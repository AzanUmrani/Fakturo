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

    'title' => 'Рахунки',
    'search_placeholder' => 'Пошук рахунків...',
    'add_invoice' => 'Додати рахунок',
    'edit_invoice' => 'Редагувати рахунок',

    'delete_confirmation_title' => 'Видалити рахунок',
    'delete_confirmation_description' => 'Цю дію неможливо скасувати.',
    'delete_confirmation_message' => 'Ви впевнені, що хочете видалити рахунок #{number}?',

    'form' => [
        'title_add' => 'Додати новий рахунок',
        'description_add' => 'Заповніть інформацію про рахунок нижче, щоб створити новий рахунок.',
        'title_edit' => 'Редагувати рахунок',
        'description_edit' => 'Оновіть інформацію про рахунок нижче.',
        'number' => 'Номер рахунку',
        'client' => 'Клієнт',
        'select_client' => 'Виберіть клієнта',
        'search_clients' => 'Пошук клієнтів...',
        'no_clients_found' => 'Клієнтів не знайдено',
        'billed_date' => 'Дата виставлення',
        'due_date' => 'Дата оплати',
        'send_date' => 'Дата відправлення',
        'variable_symbol' => 'Змінний символ',
        'constant_symbol' => 'Постійний символ',
        'specific_symbol' => 'Специфічний символ',
        'order_id' => 'Номер замовлення',
        'items' => 'Позиції рахунку',
        'item_name' => 'Назва позиції',
        'quantity' => 'Кількість',
        'price' => 'Ціна',
        'tax' => 'ПДВ (%)',
        'item_name_placeholder' => 'Введіть назву позиції',
        'add_item' => 'Додати позицію',
        'total_price' => 'Загальна ціна',
        'total_tax' => 'Загальний ПДВ',
        'total_with_tax' => 'Загальна ціна з ПДВ',
        'note' => 'Примітка',
        'submit_add' => 'Створити рахунок',
        'submit_edit' => 'Оновити рахунок',
    ],

    'table' => [
        'number' => 'Номер',
        'billed_date' => 'Дата виставлення',
        'due_date' => 'Дата оплати',
        'client' => 'Клієнт',
        'total' => 'Всього',
        'status' => 'Статус',
        'actions' => 'Дії',
        'no_invoices' => 'Рахунків не знайдено. Додайте свій перший рахунок, натиснувши кнопку "Додати рахунок".',
    ],

    'status' => [
        'paid' => 'Оплачено',
        'sent' => 'Відправлено',
        'draft' => 'Чернетка',
    ],

    'actions' => [
        'download' => 'Завантажити',
        'edit' => 'Редагувати',
        'delete' => 'Видалити',
    ],

    'pagination' => [
        'invoices' => 'рахунків',
    ],
];
