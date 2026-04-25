<?php

namespace QuickJDR\controllers;

use QuickJDR\contexts\AuthContext;
use QuickJDR\attributes\RequiresAuth;
use QuickJDR\gateways\UserGateway;

#[RequiresAuth]
class UserController implements Controller
{
    public function __construct(private UserGateway $gateway) {}

    public function processRequest(
        string $method,
        string $action,
        array $data,
        ?AuthContext $auth = null,
    ): void {
        switch ($action) {
            case "list":
                if ($method === "GET") {
                    $this->list();
                }
                break;

            default:
                http_response_code(404);
                echo json_encode(["message" => "Unknown user action"]);
        }
    }

    private function list(): void
    {
        echo json_encode($this->gateway->getAll());
    }

    public static function getBasePath(): string
    {
        return "users";
    }
}
