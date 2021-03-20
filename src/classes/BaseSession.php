<?php

namespace nzt\classes;

use nzt\interfaces\Session;
use nzt\interfaces\SessionInterface;
use nzt\interfaces\StorageInterface;

class BaseSession implements SessionInterface
{
    private StorageInterface $storage;
    private string $id;
    public static array $_;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function initialize(): void
    {
        if (empty($_COOKIE['SESSIONID'])) {
            $this->id = md5(time() . 'SALT9873575194');
            setcookie('SESSIONID', $this->id);
        } else {
            $this->id = $_COOKIE['SESSIONID'];
        }

        $data = $this->storage->getValue($this->id);
        if ($data === false) {
            self::$_ = [];
        } else {
            self::$_ = unserialize($data);
        }
    }

    public function getValue(string $name)
    {
        return self::$_[$name];
    }

    public function hasValue(string $name)
    {
        return isset(self::$_[$name]);
    }

    public function setValue(string $name, $value): void
    {
        self::$_[$name] = $value;
    }

    public function unsetValue(string $name)
    {
        unset(self::$_[$name]);
    }

    public function removeValue(string $name)
    {
        $this->unsetValue($name);
    }

    public function deleteValue(string $name)
    {
        $this->unsetValue($name);
    }

    public function finalize(): void
    {
        $this->storage->setValue($this->id, serialize(self::$_));
    }
}
