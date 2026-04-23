<?php

namespace QuickJDR\controllers;

use QuickJDR\gateways\DiceGateway;

class DiceController implements Controller
{
    public function __construct(private DiceGateway $gateway) {}

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
            case "launch":
                if ($method === "POST") {
                    $this->launch($data["character_id"], $data["max_value"]);
                }
                break;

            default:
                http_response_code(404);
                echo json_encode(["message" => "Unknown party action"]);
        }
    }

    private function launch(int $character_id, int $max_value): void
    {
        $value = random_int(0, $max_value);

        $id = $this->gateway->create($character_id, $value, $max_value);

        http_response_code(201);
        echo json_encode([
            "message" => "Dice launched",
            "value" => $value,
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
