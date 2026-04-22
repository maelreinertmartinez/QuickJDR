<?php

class CharacterController
{
    public function __construct(
        private CharacterGateway $gateway
    ) {}

    public function processRequest(string $method, string $id, array $data): void
    {
        switch ($method) {
            case "POST":
                $this->create($data);
                break;

            case "GET":
                if ($id) {
                    $this->getOne((int)$id);
                } else {
                    $this->getByParty($_GET["party_id"] ?? 0);
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
}