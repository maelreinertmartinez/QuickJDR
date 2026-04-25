<?php

namespace QuickJDR\controllers;

use QuickJDR\AuthContext;
use QuickJDR\attributes\RequiresAuth;
use QuickJDR\gateways\DiceGateway;

#[RequiresAuth]
class DiceController implements Controller
{
    public function __construct(private DiceGateway $gateway) {}

    public function processRequest(
        string $method,
        string $action,
        array $data,
        ?AuthContext $auth = null,
    ): void {
        switch ($action) {
            case "launch":
                if ($method === "POST") {
                    $this->launch((int) $data["character_id"], (int) $data["max_value"]);
                }
                break;

            default:
                http_response_code(404);
                echo json_encode(["message" => "Unknown dice action"]);
        }
    }

    private function launch(int $characterId, int $maxValue): void
    {
        $value = random_int(1, $maxValue);

        $this->gateway->create($characterId, $value, $maxValue);

        http_response_code(201);
        echo json_encode([
            "message" => "Dice launched",
            "value" => $value,
        ]);
    }

    public static function getBasePath(): string
    {
        return "dice";
    }
}
