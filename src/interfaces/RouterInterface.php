<?php

use nzt\interfaces\MethodResolver;
use nzt\interfaces\PathExtractor;

namespace nzt\interfaces;

interface RouterInterface
{
    public function __construct(MethodResolver $methodResolver, PathExtractor $pathExtractor);
    public function route(array $routes);
}
