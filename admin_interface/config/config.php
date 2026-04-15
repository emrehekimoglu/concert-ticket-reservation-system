<?php
// Configuration for Concert Ticket Reservation System Admin Interface

return [
    'db' => [
        // Update these credentials with your local Oracle configuration
        'host' => 'localhost',
        'port' => '1521',
        'service_name' => 'XE',
        'username' => 'your_username',
        'password' => 'your_password', // CHANGE THIS
        'charset' => 'AL32UTF8'
    ],
    'app' => [
        'name' => 'ConcertSys Admin',
        'base_url' => '/admin_interface',
        'admin_user' => 'admin',
        'admin_pass' => 'change_this_password'
    ]
];
