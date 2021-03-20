<?php

namespace nzt\exceptions;

class ERouterDefaultRouteNotSet extends \Error
{
    public function __construct(int $code = 0, Throwable $previous = null)
    {
        parent::__construct('Default route (@default => controller.method) is not set', $code, $previous);
    }
}
