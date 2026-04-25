<?php

namespace QuickJDR\attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class RequiresRole
{
    public function __construct(public readonly string $role) {}
}
