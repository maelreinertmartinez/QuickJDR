<?php

class Database
{
    public function __construct(
        private string $host,
        private string $database,
        private string $username,
        private string $password,
    ) {}

    public function getConnection(): PDO
    {
        return new PDO(
            "pgsql:host={$this->host};dbname={$this->database}",
            $this->username,
            $this->password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ],
        );
    }
}
