<?php

namespace QuickJDR\controllers;

use QuickJDR\contexts\AuthContext;
use QuickJDR\gateways\RoleGateway;
use QuickJDR\gateways\SessionGateway;
use QuickJDR\gateways\UserGateway;

class AuthController implements Controller
{
    public function __construct(
        private UserGateway $users,
        private RoleGateway $roles,
        private SessionGateway $sessions,
    ) {}

    public function processRequest(
        string $method,
        string $path,
        array $data,
        ?AuthContext $auth = null,
    ): void {
        if (
            $method === "POST" &&
            ($path === "login" || $path === "register" || $path === "logout")
        ) {
            $this->{$path}($data, $auth);
        } else {
            http_response_code(405);
            header("Allow: POST");
        }
    }

    private function register(array $data, ?AuthContext $auth): void
    {
        if ($auth !== null) {
            http_response_code(400);
            echo json_encode(["message" => "You are already logged in"]);
            return;
        }

        if (empty($data["username"])) {
            http_response_code(422);
            echo json_encode(["message" => "Username is required"]);
            return;
        }

        if (empty($data["password"])) {
            http_response_code(422);
            echo json_encode(["message" => "Password is required"]);
            return;
        }

        if (empty($data["confirm_password"])) {
            http_response_code(422);
            echo json_encode(["message" => "Confirm password is required"]);
            return;
        }

        if ($data["password"] !== $data["confirm_password"]) {
            http_response_code(422);
            echo json_encode(["message" => "Passwords do not match"]);
            return;
        }

        if ($this->users->getByUsername($data["username"]) !== false) {
            http_response_code(409);
            echo json_encode(["message" => "This username is already taken"]);
            return;
        }

        $data["password"] = password_hash($data["password"], PASSWORD_DEFAULT);
        $this->users->create($data);

        $user = $this->users->getByUsername($data["username"]);

        if ($data["as_game_master"] ?? false) {
            $role = $this->roles->getByLabel("game_master");
            $this->users->addRoleToUser($user["id"], $role["id"]);
        }

        $token = bin2hex(random_bytes(32));
        $this->sessions->create($user["id"], $token);

        $roles = $this->users->getRolesByUsername($data["username"]);

        http_response_code(201);
        echo json_encode([
            "message" => "Registration successful",
            "username" => $user["username"],
            "session" => $token,
            "roles" => array_map(function ($role) {
                return $role["label"];
            }, $roles),
        ]);
    }

    private function login(array $data, ?AuthContext $auth): void
    {
        if ($auth !== null) {
            http_response_code(400);
            echo json_encode(["message" => "You are already logged in"]);
            return;
        }

        $user = $this->users->getByUsername($data["username"] ?? "");
        if (
            $user === false ||
            !password_verify($data["password"] ?? "", $user["password"])
        ) {
            http_response_code(401);
            echo json_encode(["message" => "Invalid username or password"]);
            return;
        }

        $token = bin2hex(random_bytes(32));
        $this->sessions->create($user["id"], $token);

        http_response_code(200);
        echo json_encode([
            "message" => "Login successful",
            "username" => $user["username"],
            "roles" => $this->users->getRolesByUsername($user["username"]),
            "session" => $token,
        ]);
    }

    private function logout(array $data, ?AuthContext $auth): void
    {
        $authHeader = $_SERVER["HTTP_AUTHORIZATION"] ?? "";
        if (!str_starts_with($authHeader, "Bearer ")) {
            http_response_code(401);
            echo json_encode(["message" => "Not logged in"]);
            return;
        }

        $this->sessions->invalidate(substr($authHeader, 7));

        http_response_code(200);
        echo json_encode(["message" => "You are now logged out"]);
    }

    public static function getBasePath(): string
    {
        return "auth";
    }
}
