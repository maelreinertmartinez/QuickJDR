<?php

namespace QuickJDR\controllers;

use QuickJDR\attributes\RequiresRole;
use QuickJDR\contexts\AuthContext;
use QuickJDR\attributes\RequiresAuth;
use QuickJDR\gateways\CharacterGateway;
use QuickJDR\gateways\DiceGateway;
use QuickJDR\gateways\SkillGateway;

#[RequiresAuth]
class DiceController implements Controller
{
    public function __construct(
        private DiceGateway $dices,
        private CharacterGateway $characters,
        private SkillGateway $skills,
    ) {}

    public function processRequest(
        string $method,
        string $action,
        array $data,
        ?AuthContext $auth = null,
    ): void {
        switch ($action) {
            case "blank":
                if ($method === "POST") {
                    $this->blank((int) $data["max_value"], $auth->userId);
                }
                break;
            case "roll":
                if ($method === "POST") {
                    $this->roll($auth->userId, (int) $data["max_value"]);
                }
                break;
            default:
                http_response_code(404);
                echo json_encode(["message" => "Unknown dice action"]);
        }
    }

    #[RequiresRole("game_master")]
    private function blank(int $maxValue): void
    {
        $value = random_int(1, $maxValue);

        $this->dices->create(null, $value, $maxValue);

        http_response_code(201);
        echo json_encode([
            "message" => "Dice launched",
            "value" => $value,
        ]);
    }

    private function roll(int $userId, int $maxValue): void
    {
        $value = random_int(1, $maxValue);

        $character = $this->characters->getByUserId($userId);

        if (!$character) {
            http_response_code(404);
            echo json_encode(["message" => "Character not found"]);
            return;
        }

        $this->dices->create($character["id"], $value, $maxValue);

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
