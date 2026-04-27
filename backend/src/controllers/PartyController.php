<?php

namespace QuickJDR\controllers;

use QuickJDR\contexts\AuthContext;
use QuickJDR\attributes\RequiresAuth;
use QuickJDR\attributes\RequiresRole;
use QuickJDR\gateways\PartyGateway;
use QuickJDR\gateways\UserGateway;

#[RequiresAuth]
class PartyController implements Controller
{
    public function __construct(
        private PartyGateway $parties,
        private UserGateway $users,
    ) {}

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
                    $this->players(
                        isset($_GET["id"]) ? (int) $_GET["id"] : null,
                    );
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
        $id = $this->parties->create($auth->userId);

        http_response_code(201);
        echo json_encode([
            "message" => "Party created",
            "id" => $id,
        ]);
    }

    private function list(): void
    {
        $parties = $this->parties->getAll();

        for ($i = 0; $i < count($parties); $i++) {
            $parties[$i]["game_master"] = $this->users->getById(
                $parties[$i]["mj_id"],
            )["username"];
            unset($parties[$i]["mj_id"]);

            $parties[$i]["nb_players"] = $this->parties->countPlayers(
                $parties[$i]["id"],
            );
        }

        echo json_encode($parties);
    }

    private function players(?int $partyId): void
    {
        if (!$partyId) {
            http_response_code(400);
            echo json_encode(["message" => "Missing party id"]);
            return;
        }

        echo json_encode($this->parties->getPlayers($partyId));
    }

    public static function getBasePath(): string
    {
        return "party";
    }
}
