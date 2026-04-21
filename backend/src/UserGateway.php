<?php

class UserGateway
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

    public function create(array $data)
    {
        $sql = "INSERT INTO users (username, password)
                VALUES (:username, :password)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":username", $data["username"], PDO::PARAM_STR);
        $stmt->bindValue(":password", $data["password"], PDO::PARAM_STR);
        $stmt->execute();
    }

    public function addRoleToUser(int $userId, int $roleId)
    {
        $sql = "INSERT INTO user_role (user_id, role_id)
                VALUES (:user_id, :role_id)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":user_id", $userId, PDO::PARAM_INT);
        $stmt->bindValue(":role_id", $roleId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getByUsername(string $username): array|false
    {
        $sql = "SELECT *
                FROM users
                WHERE username = :username";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data;
    }

    public function getRolesByUsername(string $username): array|false
    {
        $sql = "SELECT roles.label
                FROM users
                JOIN user_role ON users.id = user_role.user_id
                JOIN roles ON user_role.role_id = roles.id
                WHERE users.username = :username";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
