<?php

namespace nzt\interfaces;

interface Savable
{
    public function load(): void;
    public function save(): void;
}