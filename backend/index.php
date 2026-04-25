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

use QuickJDR\AuthContext;
use QuickJDR\AuthMiddleware;
use QuickJDR\database\Database;
use QuickJDR\controllers\ControllerFactory;
use QuickJDR\gateways\SessionGateway;
use QuickJDR\gateways\UserGateway;

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

$pdo = $database->getConnection();

$authContext = null;
$authHeader = $_SERVER["HTTP_AUTHORIZATION"] ?? "";
if (str_starts_with($authHeader, "Bearer ")) {
    $token = substr($authHeader, 7);
    $session = (new SessionGateway($pdo))->getValidByToken($token);
    if ($session) {
        $roles = (new UserGateway($pdo))->getRolesByUserId($session["user_id"]);
        $authContext = new AuthContext($session["user_id"], $roles);
    }
}

$controller = ControllerFactory::createController($parts[1] ?? "", $pdo);

if (!$controller) {
    http_response_code(404);
    echo json_encode(["error" => "Not Found"]);
    exit();
}

$action = $parts[2] ?? "";

if (!(new AuthMiddleware())->check($controller, $action, $authContext)) {
    exit();
}

$data = [];
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    $contentType = $_SERVER["CONTENT_TYPE"] ?? "";
    if (str_contains($contentType, "application/json")) {
        $data = json_decode(file_get_contents("php://input"), true) ?? [];
    } else {
        $data = $_POST;
    }
}

$controller->processRequest(
    $_SERVER["REQUEST_METHOD"],
    $action,
    $data,
    $authContext,
);
