<?php

namespace nzt\classes;

use nzt\interfaces\StorageInterface;
use nzt\interfaces\Savable;
use nzt\interfaces\Searchable;

abstract class SavableSearchableStorage implements Savable, Searchable, StorageInterface 
{
    
}