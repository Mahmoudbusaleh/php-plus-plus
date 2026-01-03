<?php
// PHP++ Identity: Quick and recognizable
include 'pp.php';

get('/', function() {
    return view('welcome');
});

get('/hello', function() {
    return "<h1>Hello from P++</h1>";
});

dispatch();
