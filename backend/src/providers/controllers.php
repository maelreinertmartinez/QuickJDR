<?php

use QuickJDR\controllers\AuthController;
use QuickJDR\controllers\DiceController;
use QuickJDR\controllers\PartyController;
use QuickJDR\gateways\UserGateway;
use QuickJDR\gateways\RoleGateway;
use QuickJDR\gateways\DiceGateway;
use QuickJDR\gateways\PartyGateway;

return [
    AuthController::class => [UserGateway::class, RoleGateway::class],
    DiceController::class => [DiceGateway::class],
    PartyController::class => [PartyGateway::class],
];
