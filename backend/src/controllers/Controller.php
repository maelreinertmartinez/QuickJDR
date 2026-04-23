<?php

namespace QuickJDR\controllers;

interface Controller
{
    /**
     * Process a request.
     *
     * @param string $method The HTTP method of the request.
     * @param string $action The action to perform.
     * @param array $data The request data.
     *
     * Example:
     * ```php
     * $controller->processRequest('POST', 'create', ['name' => 'John']);
     * ```
     */
    public function processRequest(
        string $method,
        string $action,
        array $data,
    ): void;

    /**
     * Get the base path of the controller.
     *
     * @return string The base path of the controller.
     *
     * Example:
     * ```php
     * $basePath = $controller->getBasePath();
     * ```
     */
    public static function getBasePath(): string;
}
