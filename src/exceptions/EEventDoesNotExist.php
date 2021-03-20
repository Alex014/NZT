<?php
namespace nzt\exceptions;


use Throwable;

class EEventDoesNotExist extends \Error
{
    public function __construct(string $eventName, int $code = 0, Throwable $previous = null)
    {
        parent::__construct('Event "' .  $eventName . '" not found', $code, $previous);
    }
}
