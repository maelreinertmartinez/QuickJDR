<?php

class CharacterGateway
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO characters
            (user_id, party_id, name, health, max_health, max_mana)
            VALUES (:user_id, :party_id, :name, :health, :max_health, :max_mana)
            RETURNING id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            "user_id" => $data["user_id"],
            "party_id" => $data["party_id"],
            "name" => $data["name"],
            "health" => $data["health"],
            "max_health" => $data["max_health"],
            "max_mana" => $data["max_mana"],
        ]);

        return $stmt->fetchColumn();
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->conn->prepare("SELECT * FROM characters WHERE id = :id");
        $stmt->execute(["id" => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByParty(int $partyId): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM characters WHERE party_id = :id");
        $stmt->execute(["id" => $partyId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}