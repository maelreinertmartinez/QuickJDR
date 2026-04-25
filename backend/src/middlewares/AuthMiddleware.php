<?php

namespace QuickJDR\middlewares;

use QuickJDR\contexts\AuthContext;
use QuickJDR\attributes\RequiresAuth;
use QuickJDR\attributes\RequiresRole;
use QuickJDR\controllers\Controller;

class AuthMiddleware
{
    /**
     * Checks class-level #[RequiresAuth], then method-level #[RequiresAuth]
     * and #[RequiresRole], writing the HTTP error response when access is denied.
     *
     * Returns false if the request must be stopped, true otherwise.
     */
    public function check(
        Controller $controller,
        string $action,
        ?AuthContext $auth,
    ): bool {
        $class = new \ReflectionClass($controller);

        if ($class->getAttributes(RequiresAuth::class)) {
            if ($auth === null) {
                http_response_code(401);
                echo json_encode(["error" => "Not logged in"]);
                return false;
            }
        }

        try {
            $method = $class->getMethod($action);
        } catch (\ReflectionException) {
            return true;
        }

        if ($method->getAttributes(RequiresAuth::class)) {
            if ($auth === null) {
                http_response_code(401);
                echo json_encode(["error" => "Not logged in"]);
                return false;
            }
        }

        foreach ($method->getAttributes(RequiresRole::class) as $attribute) {
            $role = $attribute->newInstance()->role;
            if ($auth === null || !$auth->hasRole($role)) {
                http_response_code(403);
                echo json_encode([
                    "error" => "Forbidden: requires role '$role'",
                ]);
                return false;
            }
        }

        return true;
    }
}
