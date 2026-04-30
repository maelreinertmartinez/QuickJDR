<?php

namespace QuickJDR\controllers;

use QuickJDR\contexts\AuthContext;
use QuickJDR\attributes\RequiresAuth;
use QuickJDR\gateways\CharacterGateway;

#[RequiresAuth]
class CharacterController implements Controller
{
    public function __construct(private CharacterGateway $gateway) {}

    public function processRequest(
        string $method,
        string $action,
        array $data,
        ?AuthContext $auth = null,
    ): void {
        switch ($method) {
            case "POST":
                $this->create($data);
                break;

            case "GET":
                if ($action) {
                    $this->getOne((int) $action);
                } else {
                    $this->getByParty((int) ($_GET["party_id"] ?? 0));
                }
                break;

            case "PATCH":
                switch ($action) {
                    case "health":
                        $this->updateHealth($data);
                        break;
                    case "mana":
                        $this->updateMana($data);
                        break;
                    case "armor":
                        $this->updateArmor($data);
                        break;
                    default:
                        http_response_code(404);
                        echo json_encode(["error" => "Unknown action"]);
                }
                break;

            default:
                http_response_code(405);
                echo json_encode(["error" => "Method not allowed"]);
        }
    }

    private function create(array $data): void
    {
        $id = $this->gateway->create($data);
        if ($id == null) {
            http_response_code(500);
            echo json_encode(["error" => "Error while inserting."]);
            return;
        }
        http_response_code(201);
        echo json_encode(["id" => $id]);
    }

    private function getOne(int $id): void
    {
        $char = $this->gateway->getById($id);

        if (!$char) {
            http_response_code(404);
            echo json_encode(["error" => "Not found"]);
            return;
        }

        echo json_encode($char);
    }

    private function getByParty(int $partyId): void
    {
        echo json_encode($this->gateway->getByParty($partyId));
    }

    private function updateHealth(array $data): void
    {
        if (empty($data["character_id"]) || !isset($data["value"])) {
            http_response_code(400);
            echo json_encode([
                "error" => "character_id and value are required",
            ]);
            return;
        }

        $result = $this->gateway->updateHealth(
            (int) $data["character_id"],
            (int) $data["value"],
        );
        echo json_encode($result);
    }

    private function updateMana(array $data): void
    {
        if (empty($data["character_id"]) || !isset($data["value"])) {
            http_response_code(400);
            echo json_encode([
                "error" => "character_id and value are required",
            ]);
            return;
        }

        $result = $this->gateway->updateMana(
            (int) $data["character_id"],
            (int) $data["value"],
        );
        echo json_encode($result);
    }

    private function updateArmor(array $data): void
    {
        if (empty($data["character_id"]) || !isset($data["value"])) {
            http_response_code(400);
            echo json_encode([
                "error" => "character_id and value are required",
            ]);
            return;
        }

        $result = $this->gateway->updateArmor(
            (int) $data["character_id"],
            (int) $data["value"],
        );
        echo json_encode($result);
    }

    public static function getBasePath(): string
    {
        return "characters";
    }
}
