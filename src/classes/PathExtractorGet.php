<?php

namespace nzt\classes;

use nzt\interfaces\PathExtractor;

class PathExtractorGet implements PathExtractor
{
    public function getPath(): string
    {
        foreach ($_GET as $key => $value) {
            return $key;
        }

        return '';
    }
}
