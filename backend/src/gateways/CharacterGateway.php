<?php

namespace QuickJDR\gateways;

use PDO;

class CharacterGateway extends Gateway
{
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

        return (int) $stmt->fetchColumn();
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

    public function updateHealth(int $characterId, int $value): array|false
    {
        $stmt = $this->conn->prepare(
            "UPDATE characters 
            SET health = GREATEST(0, LEAST(max_health, health + :value))
            WHERE id = :id
            RETURNING id AS character_id, health AS new_health"
        );

        $stmt->execute([":value" => $value, ":id" => $characterId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function updateMana(int $characterId, int $value): array|false
    {
        $stmt = $this->conn->prepare(
            "UPDATE characters 
            SET mana = GREATEST(0, LEAST(max_mana, mana + :value))
            WHERE id = :id
            RETURNING id AS character_id, mana AS new_mana"
    );

        $stmt->execute([":value" => $value, ":id" => $characterId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function updateArmor(int $characterId, int $value): array|false
    {
    $stmt = $this->conn->prepare(
        "UPDATE characters 
         SET armor = GREATEST(0, armor + :value)
         WHERE id = :id
         RETURNING id AS character_id, armor AS new_armor"
    );

    $stmt->execute([":value" => $value, ":id" => $characterId]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}