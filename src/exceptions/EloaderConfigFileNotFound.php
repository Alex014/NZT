<?php
namespace nzt\exceptions;


use Throwable;

class EloaderConfigFileNotFound extends \Error
{
     public function __construct(string $filename, int $code = 0, Throwable $previous = null)
     {
         parent::__construct('Config file "' .  $filename . '" not found', $code, $previous);
     }
}
