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

    'title' => 'Companies',
    'description' => 'Manage your companies',
    'add_company' => 'Add Company',
    'edit_company' => 'Edit Company',
    'delete_company' => 'Delete Company',
    'search_placeholder' => 'Search companies...',
    'no_companies' => 'No companies found. Add your first company by clicking the "Add Company" button.',
    'company_created' => 'Company created successfully.',
    'company_updated' => 'Company updated successfully.',
    'company_deleted' => 'Company deleted successfully.',
    'delete_confirm' => 'Are you sure you want to delete this company?',
    'delete_confirm_message' => 'Are you sure you want to delete the company "{name}"? This action cannot be undone.',

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
        'companies' => 'companies',
    ],

    // Form fields
    'form' => [
        'title_add' => 'Add New Company',
        'title_edit' => 'Edit Company',
        'description_add' => 'Fill in the company information below to create a new company.',
        'description_edit' => 'Update the company information below.',
        'submit_add' => 'Add Company',
        'submit_edit' => 'Update Company',

        // Basic info
        'name' => 'Name',
        'state' => 'State/Country',
        'state_placeholder' => 'Select a country',
        'street' => 'Street Address',
        'street_extra' => 'Additional Address Info',
        'zip' => 'ZIP/Postal Code',
        'city' => 'City',

        // Business identification
        'tax_type' => 'Tax Type',
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
