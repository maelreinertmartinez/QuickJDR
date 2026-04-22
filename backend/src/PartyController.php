<?php

class PartyController
{
    public function __construct(private PartyGateway $gateway) {}

    public function processRequest(string $method, string $action, array $data): void
    {
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

            case "players":
                if ($method === "GET") {
                    $this->players(isset($_GET["id"]) ? (int)$_GET["id"] : null);
                }
                break;

            default:
                http_response_code(404);
                echo json_encode(["message" => "Unknown party action"]);
        }
    }

    private function create(int $userId): void
    {
        if (!$this->gateway->isGameMaster($userId)) {
            http_response_code(403);
            echo json_encode(["message" => "You must be a game master to create a party"]);
            return;
        }

        $id = $this->gateway->create($userId);

        http_response_code(201);
        echo json_encode([
            "message" => "Party created",
            "id" => $id
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

        $players = $this->gateway->getPlayers($partyId);

        echo json_encode($players);
    }
}