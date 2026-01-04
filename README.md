<p align="center">
  <img src="images/phpplusplus.png" alt="PHP++ Logo" width="250">
  <br>
</p>
# ⚡ PHP++ Web Engine
**The World's First Hybrid PHP Framework with a Compiled C++ Core.**

[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-8892bf.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Engine](https://img.shields.io/badge/Core-C++%20%2F%20FFI-blue.svg)]()

PHP++ (P++) is a revolutionary web engine designed to break the performance limits of traditional PHP. By leveraging **PHP FFI (Foreign Function Interface)**, P++ offloads heavy string matching and routing logic to a **highly optimized C++ binary**, delivering near-native execution speeds.

---

## 🚀 Why P++?

Most frameworks struggle with routing overhead as the number of routes grows. P++ solves this by using a **Hybrid Execution Model**:
1. **Static Routes:** Processed via **C++ `strcmp`** at the machine level.
2. **Dynamic Routes:** Processed via optimized PHP Regex.
3. **Self-Healing Core:** The engine detects changes in C++ source code and re-compiles the binary automatically.



---

## ✨ Key Features
- **C++ Hybrid Core:** Blazing fast static route matching using a shared object (`.so`).
- **Zero-Config Auto-Compiler:** No need for complex build tools; the engine sets itself up on the first hit.
- **Built-in Security:** Native `Request` class with recursive XSS filtering and `Response` handler for clean APIs.
- **Minimalist API:** Developer-friendly syntax inspired by modern standards but built for extreme performance.
- **Smart Boilerplate:** Automatically generates `.htaccess`, `cache`, and `views` directories.

---

## 🛠 Prerequisites
To unleash the power of P++, you need:
* **PHP 7.4+** (PHP 8.1+ recommended for better FFI stability).
* **FFI Extension** enabled (`ffi.enable=true` in your `php.ini`).
* **g++ compiler** (for the auto-compilation feature).
* **Apache/Nginx** with rewrite rules enabled.

---

## 🚦 Quick Start

### 1. Installation
Clone the repository and P++ will handle the rest on the first run:
```bash
git clone [https://github.com/Mahmoudbusaleh/php-plus-plus.git](https://github.com/Mahmoudbusaleh/php-plus-plus.git)
```
## 2. Define Your Routes
Edit index.php to start building:
```
require_once __DIR__ . '/pp.php';

// 1. Simple Text Response
get('/', fn() => "<h1>P++ is Running!</h1>");

// 2. Render a View with Data
get('/profile', function() {
    return view('welcome', ['name' => 'Mahmoud']);
});

// 3. High-Speed JSON API
get('/api/v1/status', function() {
    return \PHPPlusPlus\Response::json([
        'status' => 'Stable',
        'engine' => 'C++ Core Active'
    ]);
});

dispatch();
```
## 📂 Project Structure

```
├── cache/          # High-speed route caching
├── engine/         # C++ Source (router.cpp) & Shared Binary (router.so)
├── src/            # Core PHP Classes (The Brain)
├── views/          # UI Templates
├── pp.php          # Framework Bootstrap
└── index.php       # Entry Point
```

## ⚡ Performance Benchmark (Conceptual)
```
Operation,PHP Standard,P++ (C++ Core),Efficiency Gain
Static Route Match,~0.02ms,~0.005ms,400% Faster
Bootstrapping,Moderate,Instant,Self-Optimizing
```

## 🤝 Contribution & Support

Developed with ❤️ by Mahmoud Busaleh.

If you like this project, give it a ⭐ on GitHub! Feel free to fork and submit pull requests to make PHP++ even faster.


