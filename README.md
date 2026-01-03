# PHP++ (P++) Web Engine
**A High-Performance PHP Framework with an Integrated C++ Core**

PHP++ (P++) is a next-generation PHP framework designed for speed and efficiency. Unlike traditional frameworks, P++ leverages the power of **C++ via PHP FFI** (Foreign Function Interface) to handle core routing operations, ensuring lightning-fast performance for modern web applications.

---

## 🚀 Key Features
- **C++ Hybrid Core:** Static route matching is handled by a compiled C++ shared library for maximum speed.
- **Auto-Compiler:** The engine automatically manages directories, generates `.htaccess`, and compiles the C++ source if changes are detected.
- **Clean Routing:** Elegant API for defining routes (similar to modern standards but faster).
- **Built-in Boilerplate:** Automatically sets up your project structure on the first run.
- **Native Security:** Integrated `Request` and `Response` handlers for safe data processing.

---

## 🛠 Prerequisites
To run P++, ensure your server environment has:
1. **PHP 7.4+** or **PHP 8.x**
2. **PHP FFI Extension** enabled (`ffi.enable=true` in `php.ini`)
3. **g++ (GCC)** compiler installed (for the Auto-Compiler to build the C++ shared library)
4. **Apache** with `mod_rewrite` enabled.

---

## 📂 Project Structure
```text
├── cache/          # Compiled routes and temporary data
├── engine/         # C++ Source (router.cpp) and Shared Object (router.so)
├── public/         # Publicly accessible files
├── src/            # Core P++ PHP Classes (Router, Compiler, Request, etc.)
├── views/          # Your UI templates
├── pp.php          # Framework Bootstrap
└── index.php       # Entry point

## 🚦 Quick Start

1. Define Routes
Edit your index.php to start building:

```
require_once __DIR__ . '/pp.php';
// Simple GET route
get('/', function() {
    return "<h1>Hello from P++!</h1>";
});
// Route with View and Data
get('/profile', function() {
    return view('welcome', ['name' => 'Mahmoud']);
});
// JSON API Response
get('/api/status', function() {
    return \PHPPlusPlus\Response::json(['status' => 'Running', 'core' => 'C++']);
});
\PHPPlusPlus\Router::dispatch();

```

* 2. Run
Just point your browser to your project folder. The P++ Compiler will automatically:

Create missing folders.

Compile the engine/router.cpp into a shared library.

Generate the .htaccess for clean URLs.
## ⚡ Performance
The core advantage of P++ is the C++ Router Bridge. By offloading string matching to a compiled shared object (.so), we reduce the overhead of PHP's runtime for static route lookups, making it ideal for high-traffic applications.

## 🤝 Contribution
Developed with ❤️ by Mahmoud Busaleh.
Feel free to fork, report issues, and submit pull requests!

