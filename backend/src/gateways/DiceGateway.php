<?php

namespace QuickJDR\gateways;

use PDO;

class DiceGateway extends Gateway
{
    public function create(?int $character_id, int $value, int $max_value)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO dice (character_id, value, max_value)
            VALUES (:character_id, :value, :max_value)",
        );

        $stmt->execute([
            ":character_id" => $character_id,
            ":value" => $value,
            ":max_value" => $max_value,
        ]);

        return $stmt->fetch();
    }

    public function getAll(): array
    {
        $stmt = $this->conn->query("SELECT * FROM dice");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
