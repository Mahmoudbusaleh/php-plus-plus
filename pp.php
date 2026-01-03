<?php
/**
 * PHP++ Engine (P++) - Entry Point
 * Fast, Compiled, and Minimalist.
 */

require_once __DIR__ . '/src/Compiler.php';
require_once __DIR__ . '/src/Router.php';
require_once __DIR__ . '/src/View.php';
require_once __DIR__ . '/src/Helpers.php';

// Launch the Automatic C++ Compiler & Setup
\PHPPlusPlus\Compiler::build();

// Global Aliases to match the new identity
class_alias('\PHPPlusPlus\Router', 'P'); 

/**
 * Super Shortcuts for PHP++
 */
function get($path, $callback) { \PHPPlusPlus\Router::get($path, $callback); }
function post($path, $callback) { \PHPPlusPlus\Router::post($path, $callback); }
function dispatch() { \PHPPlusPlus\Router::dispatch(); }
