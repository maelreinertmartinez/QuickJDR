<?php

namespace QuickJDR\controllers;

use QuickJDR\AuthContext;

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
