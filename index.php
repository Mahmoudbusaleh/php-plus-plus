<?php

require_once 'src/Router.php';
use PHPPlusPlus\Router;

// Simple GET route returning HTML
Router::get('/', function() {
    return "<h1>PHP++ Home</h1><p>Simpler than Laravel, faster than Native.</p>";
});

// Dynamic route handling user IDs
Router::get('/profile/{id}', function($id) {
    return "User Profile ID: " . $id;
});

// Simple POST route using native $_POST
Router::post('/save', function() {
    $data = $_POST['username'] ?? 'Anonymous';
    return "Saved user: " . htmlspecialchars($data);
});

// One-line redirection
Router::redirect('/old-page', '/');

// Execute the routing engine
Router::dispatch();
