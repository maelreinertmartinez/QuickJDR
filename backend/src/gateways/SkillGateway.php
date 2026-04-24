<?php

namespace QuickJDR\gateways;

use PDO;

class SkillGateway extends Gateway
{
    public function create(array $data): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO skills (label, description, mana_cost, healing, damage, dice_cost)
             VALUES (:label, :description, :mana_cost, :healing, :damage, :dice_cost)
             RETURNING id"
        );

        $stmt->execute([
            "label" => $data["label"],
            "description" => $data["description"],
            "mana_cost" => $data["mana_cost"],
            "healing" => $data["healing"] ?? null,
            "damage" => $data["damage"] ?? null,
            "dice_cost" => $data["dice_cost"],
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function getAll(): array
    {
        $stmt = $this->conn->query("SELECT * FROM skills");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): array|false
    {
        $stmt = $this->conn->prepare("SELECT * FROM skills WHERE id = :id");
        $stmt->execute(["id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function assignToCharacter(int $characterId, int $skillId): int
{
    $stmt = $this->conn->prepare(
        "INSERT INTO character_skills (character_id, skill_id, level, experience)
         VALUES (:character_id, :skill_id, 1, 0)
         RETURNING id"
    );

    $stmt->execute([
        ":character_id" => $characterId,
        ":skill_id" => $skillId
    ]);

    return (int) $stmt->fetchColumn();
}

    public function getByCharacter(int $characterId): array
{
    $stmt = $this->conn->prepare(
        "SELECT skills.* 
         FROM skills
         JOIN character_skills ON skills.id = character_skills.skill_id
         WHERE character_skills.character_id = :character_id"
    );

    $stmt->execute([":character_id" => $characterId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}