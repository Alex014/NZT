<?php

namespace nzt\exceptions;

class ERouterNotfoundRouteNotSet extends \Error
{
    public function __construct(int $code = 0, Throwable $previous = null)
    {
        parent::__construct('Notfound route (@notfound => controller.method) is not set', $code, $previous);
    }
}
