<?php
/**
 * PHP++ Global Entry Point
 * This file boots up the entire engine automatically.
 */

// 1. Load all core components
require_once __DIR__ . '/src/Compiler.php';
require_once __DIR__ . '/src/Router.php';
require_once __DIR__ . '/src/View.php';
require_once __DIR__ . '/src/Helpers.php';

// 2. Trigger the Autonomic Compiler
// It will check folders, create C++ files, and compile them without any intervention.
\PHPPlusPlus\Compiler::build();

// 3. Optional: Alias the Router for easier access
class_alias('\PHPPlusPlus\Router', 'Router');
