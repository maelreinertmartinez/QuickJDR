<?php

namespace QuickJDR\controllers;

use QuickJDR\gateways\PartyGateway;

class PartyController implements Controller
{
    public function __construct(private PartyGateway $gateway) {}

    public function processRequest(
        string $method,
        string $action,
        array $data,
    ): void {
        session_start();

        if (!isset($_SESSION["user_id"])) {
            http_response_code(401);
            echo json_encode(["message" => "Not logged in"]);
            return;
        }

        $userId = $_SESSION["user_id"];

        switch ($action) {
            case "create":
                if ($method === "POST") {
                    $this->create($userId);
                }
                break;

            case "list":
                if ($method === "GET") {
                    $this->list();
                }
                break;

            default:
                http_response_code(404);
                echo json_encode(["message" => "Unknown party action"]);
        }
    }

    private function create(int $userId): void
    {
        $id = $this->gateway->create($userId);

        http_response_code(201);
        echo json_encode([
            "message" => "Party created",
            "id" => $id,
        ]);
    }

    private function list(): void
    {
        echo json_encode($this->gateway->getAll());
    }

    public static function getBasePath(): string
    {
        return "dice";
    }
}
