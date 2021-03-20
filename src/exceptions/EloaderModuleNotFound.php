<?php
namespace nzt\exceptions;


use Throwable;

class EloaderModuleNotFound extends \Error
{
    public function __construct(string $filename,string $moduleName, string $path, int $code = 0, Throwable $previous = null)
    {
        parent::__construct('Needed file "' .  $filename . '" not found in module "' . $moduleName . '" in path "' . $path . '"', $code, $previous);
    }
}
