<?php
// PHP++ Identity: Quick and recognizable
include 'pp.php';

get('/', function() {
    view('home'); 
});

get('/hello', function() {
    return "<h1>Hello from P++</h1>";
});

dispatch();
