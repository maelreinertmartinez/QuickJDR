<?php

namespace QuickJDR\controllers;

use QuickJDR\contexts\AuthContext;

interface Controller
{
    public function processRequest(
        string $method,
        string $action,
        array $data,
        ?AuthContext $auth = null,
    ): void;

    public static function getBasePath(): string;
}
