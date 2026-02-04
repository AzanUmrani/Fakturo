<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Clients Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for the clients management pages
    | and features throughout the application.
    |
    */

    'title' => 'Clients',
    'description' => 'Manage your clients',
    'add_client' => 'Add Client',
    'edit_client' => 'Edit Client',
    'delete_client' => 'Delete Client',
    'search_placeholder' => 'Search clients...',
    'no_clients' => 'No clients found. Add your first client by clicking the "Add Client" button.',
    'client_created' => 'Client created successfully.',
    'client_updated' => 'Client updated successfully.',
    'client_deleted' => 'Client deleted successfully.',
    'delete_confirm' => 'Are you sure you want to delete this client?',
    'delete_confirm_message' => 'Are you sure you want to delete the client "{name}"? This action cannot be undone.',

    // Table headers
    'table' => [
        'name' => 'Name',
        'id_number' => 'ID Number',
        'city' => 'City',
        'contact' => 'Contact',
        'actions' => 'Actions',
    ],

    // Pagination
    'pagination' => [
        'showing' => 'Showing',
        'to' => 'to',
        'of' => 'of',
        'clients' => 'clients',
    ],

    // Form fields
    'form' => [
        'title_add' => 'Add New Client',
        'title_edit' => 'Edit Client',
        'description_add' => 'Fill in the client information below to create a new client.',
        'description_edit' => 'Update the client information below.',
        'submit_add' => 'Add Client',
        'submit_edit' => 'Update Client',

        // Basic info
        'name' => 'Name',
        'state' => 'State/Country',
        'state_placeholder' => 'Select a country',
        'street' => 'Street Address',
        'street_extra' => 'Additional Address Info',
        'zip' => 'ZIP/Postal Code',
        'city' => 'City',

        // Business identification
        'identification_number' => 'Identification Number (ICO)',
        'vat_identification_number' => 'VAT ID (DIC)',
        'vat_identification_number_sk' => 'VAT ID SK (ICDPH)',
        'registry_info' => 'Registry Info',

        // Contact information
        'contact_name' => 'Contact Name',
        'contact_phone' => 'Contact Phone',
        'contact_email' => 'Contact Email',
        'contact_web' => 'Website',
        'website_placeholder' => 'https://example.com',
    ],

    // Buttons
    'buttons' => [
        'edit' => 'Edit',
        'delete' => 'Delete',
        'cancel' => 'Cancel',
        'save' => 'Save',
    ],
];
