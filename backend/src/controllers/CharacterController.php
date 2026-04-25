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
                $this->create($data, $auth->userId);
                break;

            case "GET":
                if ($action) {
                    $this->getOne((int) $action);
                } else {
                    $this->getByParty((int) ($_GET["party_id"] ?? 0));
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
