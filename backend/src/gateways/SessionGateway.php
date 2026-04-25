<?php

namespace QuickJDR\gateways;

class SessionGateway extends Gateway
{
    public function create(int $userId, string $token): void
    {
        $sql = "INSERT INTO session (user_id, token, ended_at) VALUES (?, ?, NOW() + interval '24 hours')";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$userId, $token]);
    }

    public function getValidByToken(string $token): array|false
    {
        $sql = "SELECT * FROM session WHERE token = ? AND ended_at > NOW()";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$token]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function invalidate(string $token): void
    {
        $sql = "UPDATE session SET ended_at = NOW() WHERE token = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$token]);
    }
}
