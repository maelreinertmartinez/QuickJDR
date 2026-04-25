<?php

namespace QuickJDR\controllers;

use QuickJDR\AuthContext;
use QuickJDR\attributes\RequiresAuth;
use QuickJDR\attributes\RequiresRole;
use QuickJDR\gateways\PartyGateway;

#[RequiresAuth]
class PartyController implements Controller
{
    public function __construct(private PartyGateway $gateway) {}

    public function processRequest(
        string $method,
        string $action,
        array $data,
        ?AuthContext $auth = null,
    ): void {
        switch ($action) {
            case "create":
                if ($method === "POST") {
                    $this->create($auth);
                }
                break;

            case "list":
                if ($method === "GET") {
                    $this->list();
                }
                break;

            case "players":
                if ($method === "GET") {
                    $this->players(isset($_GET["id"]) ? (int) $_GET["id"] : null);
                }
                break;

            default:
                http_response_code(404);
                echo json_encode(["message" => "Unknown party action"]);
        }
    }

    #[RequiresRole("game_master")]
    private function create(?AuthContext $auth): void
    {
        $id = $this->gateway->create($auth->userId);

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

    private function players(?int $partyId): void
    {
        if (!$partyId) {
            http_response_code(400);
            echo json_encode(["message" => "Missing party id"]);
            return;
        }

        echo json_encode($this->gateway->getPlayers($partyId));
    }

    public static function getBasePath(): string
    {
        return "party";
    }
}
