<?php

require_once 'src/Router.php';

use PHPPlusPlus\Router;

// Registering routes using standard anonymous functions (Closures)
Router::get('/home', function() {
    return "<h1>Welcome to PHP++</h1>";
});

Router::get('/about', function() {
    return "This is a high-performance compiled PHP project.";
});

// Start the routing process
Router::dispatch();
