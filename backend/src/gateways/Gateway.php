<?php

namespace QuickJDR\gateways;

use PDO;

class Gateway
{
    public function __construct(protected PDO $conn) {}
}
