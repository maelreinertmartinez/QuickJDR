<?php

class RoleGateway
{
    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAll(): array
    {
        $sql = "SELECT id, username
                FROM users";

        $stmt = $this->conn->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByLabel(string $label): array|false
    {
        $sql = "SELECT * FROM roles
                WHERE label = :label";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":label", $label, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data;
    }
}
