<?php

// CORS : autoriser le frontend sur localhost:5173
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$parts = explode('/', $_SERVER['REQUEST_URI']);

echo $parts[1] ?? 'Hello World';
