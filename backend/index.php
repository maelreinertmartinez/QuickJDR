<?php

declare(strict_types=1);

spl_autoload_register(function ($class_name) {
    $path = str_replace(
        "\\",
        "/",
        preg_replace("/^QuickJDR\//", "", str_replace("\\", "/", $class_name)),
    );
    include __DIR__ . "/src/" . $path . ".php";
});

use QuickJDR\database\Database;
use QuickJDR\controllers\ControllerFactory;

// CORS : autoriser le frontend sur localhost:5173
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit();
}

$url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$parts = explode("/", $url);

$database = new Database(
    $_ENV["DB_HOST"],
    $_ENV["DB_DATABASE"],
    $_ENV["DB_USERNAME"],
    $_ENV["DB_PASSWORD"],
);

$controller = ControllerFactory::createController(
    $parts[1] ?? "",
    $database->getConnection(),
);

if (!$controller) {
    http_response_code(404);
    echo json_encode(["error" => "Not Found"]);
    exit();
}

$controller->processRequest(
    $_SERVER["REQUEST_METHOD"],
    $parts[2] ?? "",
    $_POST,
);
