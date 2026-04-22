<?php

class PartyGateway
{
    public function __construct(private PDO $conn) {}

    public function create(int $mjId): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO party (mj_id) VALUES (:mj_id) RETURNING id"
        );

        $stmt->execute([":mj_id" => $mjId]);

        return (int) $stmt->fetchColumn();
    }

    public function getAll(): array
    {
        $stmt = $this->conn->query("SELECT * FROM party");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPlayers(int $partyId): array
    {
        $stmt = $this->conn->prepare(
        "SELECT id, user_id, name 
         FROM characters 
         WHERE party_id = :party_id"
        );

        $stmt->execute([":party_id" => $partyId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}