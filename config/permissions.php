<?php

/**
 * Application permissions - used for seeding and role management UI.
 * Format: 'permission_name' => 'Human readable label'
 */
return [
    'products' => [
        'products.view' => 'View',
        'products.create' => 'Create',
        'products.edit' => 'Edit',
        'products.delete' => 'Delete',
    ],
    'users' => [
        'users.view' => 'View',
        'users.create' => 'Create',
        'users.edit' => 'Edit',
        'users.delete' => 'Delete',
    ],
    'site_settings' => [
        'site_settings.view' => 'View',
        'site_settings.create' => 'Create',
        'site_settings.edit' => 'Edit',
    ],
    'pages' => [
        'pages.view' => 'View',
        'pages.edit' => 'Edit',
    ],
    'roles' => [
        'roles.view' => 'View',
        'roles.edit' => 'Edit',
    ],
    'audit_logs' => [
        'audit_logs.view' => 'View',
    ],
];
