<?php

$parts = explode('/', $_SERVER['REQUEST_URI']);

echo $parts[1] ?? 'Hello World';
