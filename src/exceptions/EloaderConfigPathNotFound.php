<?php
namespace nzt\exceptions;


use Throwable;

class EloaderConfigPathNotFound extends \Error
{
     public function __construct(string $configpath, int $code = 0, Throwable $previous = null)
     {
         parent::__construct('Config path "' .  $configpath . '" not found', $code, $previous);
     }
}
