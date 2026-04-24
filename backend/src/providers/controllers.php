<?php

use QuickJDR\controllers\AuthController;
use QuickJDR\controllers\DiceController;
use QuickJDR\controllers\PartyController;
use QuickJDR\controllers\CharacterController;
use QuickJDR\gateways\UserGateway;
use QuickJDR\gateways\RoleGateway;
use QuickJDR\gateways\DiceGateway;
use QuickJDR\gateways\PartyGateway;
use QuickJDR\gateways\CharacterGateway;
use QuickJDR\controllers\UserController;


return [
    AuthController::class => [UserGateway::class, RoleGateway::class],
    DiceController::class => [DiceGateway::class],
    PartyController::class => [PartyGateway::class],
    CharacterController::class => [CharacterGateway::class],
    UserController::class => [UserGateway::class],
];