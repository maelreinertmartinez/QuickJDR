<?php

class AuthController
{
    public function __construct(private UserGateway $gateway) {}

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

        $user = $this->gateway->getByUsername($data["username"]);

        if ($user !== false) {
            http_response_code(454);
            echo json_encode([
                "message" => "This username is already taken",
            ]);
            return;
        }

        $data["password"] = password_hash($data["password"], PASSWORD_DEFAULT);

        $this->gateway->create($data);
        http_response_code(201);
        echo json_encode([
            "message" => "Registration successful",
            "roles" => $this->gateway->getRolesByUsername($data["username"]),
        ]);
    }
    /**
     * @param array<int,mixed> $data
     */
    private function login(array $data): void
    {
        if (empty($_SESSION["user_id"])) {
            http_response_code(401);
            echo json_encode([
                "message" => "You are already logged in",
            ]);
            return;
        }

        $user = $this->gateway->getByUsername($data["username"]);
        if (
            $user["password"] === false ||
            !password_verify($data["password"], $user["password"])
        ) {
            http_response_code(401);
            echo json_encode([
                "message" => "Invalid username or password",
                "roles" => $this->gateway->getRolesByUsername(
                    $data["username"],
                ),
            ]);
            return;
        }
        session_start();
        $_SESSION["user_id"] = $user["id"];
        http_response_code(200);
        echo json_encode([
            "message" => "Login successful",
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
