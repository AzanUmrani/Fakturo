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

    'title' => 'Invoices',
    'search_placeholder' => 'Search invoices...',
    'add_invoice' => 'Add Invoice',
    'bulk_download' => 'Bulk Download',
    'edit_invoice' => 'Edit Invoice',

    'delete_confirmation_title' => 'Delete Invoice',
    'delete_confirmation_description' => 'This action cannot be undone.',
    'delete_confirmation_message' => 'Are you sure you want to delete invoice #{number}?',

    'form' => [
        'title_add' => 'Add New Invoice',
        'description_add' => 'Fill in the invoice information below to create a new invoice.',
        'title_edit' => 'Edit Invoice',
        'description_edit' => 'Update the invoice information below.',
        'number' => 'Invoice Number',
        'client' => 'Client',
        'select_client' => 'Select a client',
        'search_clients' => 'Search clients...',
        'no_clients_found' => 'No clients found',
        'billed_date' => 'Billed Date',
        'due_date' => 'Due Date',
        'send_date' => 'Send Date',
        'variable_symbol' => 'Variable Symbol',
        'constant_symbol' => 'Constant Symbol',
        'specific_symbol' => 'Specific Symbol',
        'order_id' => 'Order ID',
        'items' => 'Invoice Items',
        'item_name' => 'Item Name',
        'quantity' => 'Quantity',
        'price' => 'Price',
        'tax' => 'Tax (%)',
        'item_name_placeholder' => 'Enter item name',
        'add_item' => 'Add Item',
        'total_price' => 'Total Price',
        'total_tax' => 'Total Tax',
        'total_with_tax' => 'Total with Tax',
        'note' => 'Note',
        'submit_add' => 'Create Invoice',
        'submit_edit' => 'Update Invoice',
    ],

    'table' => [
        'number' => 'Number',
        'billed_date' => 'Billed Date',
        'due_date' => 'Due Date',
        'client' => 'Client',
        'total' => 'Total',
        'status' => 'Status',
        'actions' => 'Actions',
        'no_invoices' => 'No invoices found. Add your first invoice by clicking the "Add Invoice" button.',
    ],

    'status' => [
        'paid' => 'Paid',
        'sent' => 'Sent',
        'draft' => 'Draft',
    ],

    'actions' => [
        'download' => 'Download',
        'edit' => 'Edit',
        'delete' => 'Delete',
    ],

    'pagination' => [
        'invoices' => 'invoices',
    ],
];
