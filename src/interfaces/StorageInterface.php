<?php

namespace nzt\interfaces;

interface StorageInterface
{
    /**
     * @param string $name
     * @return void
     */
    public function getValue(string $name);

    /**
     * @param string $name
     * @param [type] $value
     * @return void
     */
    public function setValue(string $name, $value): void;

    /**
     * @param string $name
     * @return void
     */
    public function unsetValue(string $name): void;

    /**
     * @param string $name
     * @return void
     */
    public function removeValue(string $name): void;

    /**
     * @param string $name
     * @return void
     */
    public function deleteValue(string $name): void;

    /**
     * @return array
     */
    public function getAll(): array;
}