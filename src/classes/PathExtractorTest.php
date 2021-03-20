<?php

namespace nzt\classes;

use nzt\interfaces\PathExtractor;

class PathExtractorTest implements PathExtractor
{
    private string $path;

    public function __construct(string $path) 
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
