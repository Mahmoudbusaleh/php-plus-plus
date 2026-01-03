require_once 'src/Router.php';
use PHPPlusPlus\Router;

// Static route
Router::get('/home', function() {
    return "Welcome Home";
});

// Dynamic route with ID
Router::get('/user/{id}', function($id) {
    return "User Profile. The ID is: " . $id;
});

// Dynamic route with multiple params
Router::get('/post/{category}/{slug}', function($category, $slug) {
    return "Category: $category | Post: $slug";
});

Router::dispatch();
