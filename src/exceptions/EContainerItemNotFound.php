<?php
namespace nzt\exceptions;


use Throwable;

class EContainerItemNotFound extends \Exception
{
     public function __construct(string $item, string $container, int $code = 0, Throwable $previous = null)
     {
         parent::__construct('Item "' .  $item . '" not found in container "' . $container . '"', $code, $previous);
     }
}
