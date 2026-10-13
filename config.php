<?php
return [
    'name' => 'Stockify', 'theme' => 'stockify', 'table' => 'items',
    'session_name' => 'stockify_session',
    'database' => ['host' => '127.0.0.1', 'port' => 3306, 'name' => 'stockify', 'user' => 'root', 'password' => ''],
    'fields' => ['code' => ['text', 30], 'name' => ['text', 120], 'category' => ['text', 80], 'quantity' => ['number', 1000000], 'unit' => ['text', 30]],
];
