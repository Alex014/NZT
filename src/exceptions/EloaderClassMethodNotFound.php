<?php
namespace nzt\exceptions;


use Throwable;

class EloaderClassMethodNotFound extends \Error
{
    public function __construct(string $className,string $methodName, int $code = 0, Throwable $previous = null)
    {
        parent::__construct('Class method with name "' .  $className . '" -> "' . $methodName . '" not found', $code, $previous);
    }
}
