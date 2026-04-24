<?php

namespace QuickJDR\controllers;

use QuickJDR\gateways\CharacterGateway;

class CharacterController implements Controller
{
    public function __construct(private CharacterGateway $gateway) {}

    public function processRequest(
        string $method,
        string $action,
        array $data,
    ): void {
        session_start();

        if (!isset($_SESSION["user_id"])) {
            http_response_code(401);
            echo json_encode(["error" => "Not logged in"]);
            return;
        }

        switch ($method) {
            case "POST":
                $this->create($data, $_SESSION["user_id"]);
                break;

            case "GET":
                if ($action) {
                    $this->getOne((int)$action);
                } else {
                    $this->getByParty((int)($_GET["party_id"] ?? 0));
                }
                break;

            default:
                http_response_code(405);
                echo json_encode(["error" => "Method not allowed"]);
        }
    }

    private function create(array $data, int $userId): void
    {
        $data["user_id"] = $userId;
        $id = $this->gateway->create($data);
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

    public static function getBasePath(): string
    {
        return "characters";
    }
}