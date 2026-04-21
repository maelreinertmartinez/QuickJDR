<?php

class AuthController
{
    public function __construct(
        private UserGateway $users,
        private RoleGateway $roles,
    ) {}

    /**
     * @param array<int,mixed> $data
     */
    public function processRequest(
        string $method,
        string $path,
        array $data,
    ): void {
        if (
            $method === "POST" &&
            ($path === "login" || $path === "register" || $path === "logout")
        ) {
            $this->{$path}($data);
        } else {
            http_response_code(405);
            header("Allow: POST");
        }
    }

    /**
     * @param array<int,mixed> $data
     */
    private function register(array $data): void
    {
        if (empty($data["username"])) {
            http_response_code(451);
            echo json_encode([
                "message" => "Username is required",
            ]);
            return;
        }

        if (empty($data["password"])) {
            http_response_code(452);
            echo json_encode([
                "message" => "Password is required",
            ]);
            return;
        }

        if (empty($data["confirm_password"])) {
            http_response_code(453);
            echo json_encode([
                "message" => "Confirm password is required",
            ]);
            return;
        }

        if ($data["password"] !== $data["confirm_password"]) {
            http_response_code(454);
            echo json_encode([
                "message" => "Passwords do not match",
            ]);
            return;
        }

        $user = $this->users->getByUsername($data["username"]);

        if ($user !== false) {
            http_response_code(454);
            echo json_encode([
                "message" => "This username is already taken",
            ]);
            return;
        }

        $data["password"] = password_hash($data["password"], PASSWORD_DEFAULT);

        $this->users->create($data);

        $user = $this->users->getByUsername($data["username"]);

        if ($data["as_game_master"] ?? false) {
            $user = $this->users->getByUsername($data["username"]);
            $role = $this->roles->getByLabel("game_master");
            $this->users->addRoleToUser($user["id"], $role["id"]);
        }

        http_response_code(201);
        session_start();
        $_SESSION["user_id"] = $user["id"];
        echo json_encode([
            "message" => "Registration successful",
            "username" => $user["username"],
            "roles" => $this->users->getRolesByUsername($data["username"]),
        ]);
    }
    /**
     * @param array<int,mixed> $data
     */
    private function login(array $data): void
    {
        session_start();
        if (!empty($_SESSION["user_id"])) {
            http_response_code(401);
            echo json_encode([
                "message" => "You are already logged in",
            ]);
            return;
        }

        $user = $this->users->getByUsername($data["username"]);
        if (
            $user["password"] === false ||
            !password_verify($data["password"], $user["password"])
        ) {
            http_response_code(401);
            echo json_encode([
                "message" => "Invalid username or password",
            ]);
            return;
        }
        $_SESSION["user_id"] = $user["id"];
        http_response_code(200);
        echo json_encode([
            "message" => "Login successful",
            "username" => $user["username"],
            "roles" => $this->users->getRolesByUsername($user["username"]),
        ]);
    }

    private function logout(): void
    {
        session_start();
        session_destroy();
        http_response_code(200);
        echo json_encode([
            "message" => "You are now logged out",
        ]);
    }
}
