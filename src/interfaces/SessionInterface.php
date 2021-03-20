<?php

namespace nzt\interfaces;

interface SessionInterface
{
    /**
     * @return void
     */
    public function initialize(): void;

    /**
     * @param string $name
     * @return array|mixed
     */
    public function getValue(string $name);

    /**
     * @param string $name
     * @return boolean
     */
    public function hasValue(string $name);

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setValue(string $name, $value): void;

    /**
     * @param string $name
     * @return void
     */
    public function unsetValue(string $name);

    /**
     * @param string $name
     * @return void
     */
    public function removeValue(string $name);

    /**
     * @param string $name
     * @return void
     */
    public function deleteValue(string $name);
}
