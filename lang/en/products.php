<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Products Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for the products management pages
    | and features throughout the application.
    |
    */

    'title' => 'Products',
    'description' => 'Manage your products',
    'add_product' => 'Add Product',
    'edit_product' => 'Edit Product',
    'delete_product' => 'Delete Product',
    'search_placeholder' => 'Search products...',
    'no_products' => 'No products found. Add your first product by clicking the "Add Product" button.',
    'product_created' => 'Product created successfully.',
    'product_updated' => 'Product updated successfully.',
    'product_deleted' => 'Product deleted successfully.',
    'delete_confirm' => 'Are you sure you want to delete this product?',
    'delete_confirm_message' => 'Are you sure you want to delete the product "{name}"? This action cannot be undone.',

    // Table headers
    'table' => [
        'image' => 'Image',
        'name' => 'Name',
        'type' => 'Type',
        'price' => 'Price',
        'sku' => 'SKU',
        'actions' => 'Actions',
    ],

    // Pagination
    'pagination' => [
        'showing' => 'Showing',
        'to' => 'to',
        'of' => 'of',
        'products' => 'products',
    ],

    // Form fields
    'form' => [
        'title_add' => 'Add New Product',
        'title_edit' => 'Edit Product',
        'description_add' => 'Fill in the product information below to create a new product.',
        'description_edit' => 'Update the product information below.',
        'submit_add' => 'Add Product',
        'submit_edit' => 'Update Product',

        // Basic info
        'name' => 'Name',
        'type' => 'Type',
        'description' => 'Description',
        'price' => 'Price',
        'taxRate' => 'Tax Rate (%)',
        'discount' => 'Discount (%)',
        'unit' => 'Unit',
        'sku' => 'SKU',
        'weight' => 'Weight',
        'has_image' => 'Product Image',
        'upload_image' => 'Upload Image',
        'remove_image' => 'Remove Image',

        // Product types
        'types' => [
            'product' => 'Product',
            'service' => 'Service',
        ],
    ],

    // Buttons
    'buttons' => [
        'edit' => 'Edit',
        'delete' => 'Delete',
        'cancel' => 'Cancel',
        'save' => 'Save',
    ],
];
