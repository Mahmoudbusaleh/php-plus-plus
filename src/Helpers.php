<?php

use PHPPlusPlus\View;

/**
 * Global helper function to render views easily
 */
if (!function_exists('view')) {
    function view($name, $data = []) {
        return View::render($name, $data);
    }
}
