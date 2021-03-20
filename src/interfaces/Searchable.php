<?php

namespace nzt\interfaces;

interface Searchable
{
    public function searchBy(string $name, string $value): array;
    public function searchByParams(array $params): array;
}