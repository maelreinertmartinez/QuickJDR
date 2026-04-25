<?php

namespace QuickJDR\controllers;

use QuickJDR\contexts\AuthContext;
use QuickJDR\attributes\RequiresAuth;
use QuickJDR\gateways\SkillGateway;

#[RequiresAuth]
class SkillController implements Controller
{
    public function __construct(private SkillGateway $gateway) {}

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

            case "create":
                if ($method === "POST") {
                    $this->create($data);
                }
                break;

            case "assign":
                if ($method === "POST") {
                    $this->assign($data);
                }
                break;

            case "character":
                if ($method === "GET") {
                    $this->getByCharacter(isset($_GET["character_id"]) ? (int)$_GET["character_id"] : null);
                }
                break;

            default:
                http_response_code(404);
                echo json_encode(["message" => "Unknown skill action"]);
        }
    }

    private function list(): void
    {
        echo json_encode($this->gateway->getAll());
    }

    private function create(array $data): void
    {
        $id = $this->gateway->create($data);
        http_response_code(201);
        echo json_encode(["id" => $id]);
    }

    private function assign(array $data): void
    {
        if (empty($data["character_id"]) || empty($data["skill_id"])) {
            http_response_code(400);
            echo json_encode(["message" => "character_id and skill_id are required"]);
            return;
        }

        $id = $this->gateway->assignToCharacter(
            (int)$data["character_id"],
            (int)$data["skill_id"]
        );

        http_response_code(201);
        echo json_encode([
            "message" => "Skill assigned to character",
            "id" => $id
        ]);
    }

    private function getByCharacter(?int $characterId): void
    {
        if (!$characterId) {
            http_response_code(400);
            echo json_encode(["message" => "Missing character_id"]);
            return;
        }

        echo json_encode($this->gateway->getByCharacter($characterId));
    }

    public static function getBasePath(): string
    {
        return "skills";
    }
}