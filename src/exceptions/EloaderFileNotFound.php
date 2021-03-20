<?php
namespace nzt\exceptions;


use Throwable;

class EloaderFileNotFound extends \Error
{
     public function __construct(string $filename, int $code = 0, Throwable $previous = null)
     {
         parent::__construct('File "' .  $filename . '" not found', $code, $previous);
     }
}
