<?php
namespace nzt\exceptions;


use Throwable;

class EloaderClassNotFound extends \Error
{
    public function __construct(string $className, int $code = 0, Throwable $previous = null)
    {
        parent::__construct('Class with name "' .  $className . '" not found', $code, $previous);
    }
}
