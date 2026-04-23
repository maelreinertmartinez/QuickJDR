<?php

namespace QuickJDR\controllers;

use PDO;
use QuickJDR\controllers\Controller;

class ControllerFactory
{
    public static function createController(string $path, PDO $pdo): Controller
    {
        $controllers = require __DIR__ . "/../providers/controllers.php";
        foreach ($controllers as $controllerClass => $gateways) {
            if ($controllerClass::getBasePath() === $path) {
                $gatewaysInstances = array_map(
                    fn($gatewayClass) => new $gatewayClass($pdo),
                    $gateways,
                );
                return new $controllerClass(...$gatewaysInstances);
            }
        }
        echo json_encode(["error" => "Controller not found for path: $path"]);
    }
}
