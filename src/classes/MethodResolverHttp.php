<?php

namespace nzt\classes;

use nzt\interfaces\MethodResolver;

class MethodResolverHttp implements MethodResolver
{
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }
}