<?php

use PHPPlusPlus\View;

/**
 * Global helper to render views without calling the class directly.
 */
if (!function_exists('view')) {
    function view($name, $data = []) {
        return View::render($name, $data);
    }
}
