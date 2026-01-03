# 🚀 PHP++ (P++)
**The World's First Self-Compiling Hybrid PHP & C++ Framework.**

PHP++ is not just another framework; it’s a high-performance engine designed to bridge the gap between the productivity of **PHP** and the raw power of **C++**. 



---

## 🌟 Why PHP++?

Most frameworks are slow because they are built 100% on interpreted PHP. **PHP++** changes the game by offloading heavy logic (like Routing) to a compiled **C++ Turbo Core** automatically.

### 🔥 Key Features:
* **Zero-Config Auto-Build:** Just include `pp.php`, and the engine builds your folders and configures itself.
* **C++ Turbo Routing:** Automatically detects, writes, and compiles a C++ library to handle URL matching at lightning speed.
* **Hybrid Power:** Use standard PHP for your logic while enjoying C++ performance for the core.
* **Minimalist Syntax:** No complex classes. Just `get()`, `post()`, and `view()`.
* **AOT Compilation:** Pre-compiles routes to reduce overhead on every request.

---

## ⚡ Quick Start (30 Seconds)

1. **Clone the repo** into your local server.
2. Create an `index.php` and write:

```php
<?php
require_once 'pp.php';

get('/', function() {
    return view('welcome');
});

get('/user/{id}', function($id) {
    return "User Profile: " . $id;
});

dispatch();

```
## Open your browser. The engine will automatically:

Create views/, cache/, and engine/ folders.

Generate and compile the router.cpp into a shared object (.so).

Set up your .htaccess for clean URLs.

## 🛠 Under the Hood (The C++ Bridge)
PHP++ uses FFI (Foreign Function Interface) to call compiled C++ functions directly.
When the engine runs for the first time, our Smart Compiler executes:
```
g++ -fPIC -shared -o engine/router.so engine/router.cpp
```
* This turns your routing logic into machine code, making it thousands of times faster than traditional regex-based routers.


## 🛡 Security
    *Built-in protection for your routes and views, with an automated compiler that ensures your production environment is always optimized and locked down.
## 🤝 Contributing
    We are building the future of the web. If you are a C++ wizard or a PHP ninja, join us!

##  How to Contribute
We love your input! We want to make contributing to this project as easy and transparent as possible.

### Documentation Update:
If you add a new feature or make any significant changes, please:
1.  **Update the README.md**: Add your name and a brief description of your contribution to the **Contributors Table**.
2.  **Maintain the Style**: Ensure your entry matches the existing format in the table.

Thank you for your contribution!

Developed with ❤️ by Mahmoud Busaleh
