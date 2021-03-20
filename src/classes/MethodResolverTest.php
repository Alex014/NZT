<?php

namespace nzt\classes;

use nzt\interfaces\MethodResolver;

class MethodResolverTest implements MethodResolver
{
    private string $method;

    public function __construct(string $method) 
    {
        $this->method = $method;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}