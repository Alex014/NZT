<?php

namespace nzt\classes;

use nzt\interfaces\StorageInterface;

class FileStorage implements StorageInterface
{
    public string $filePath;

    /**
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        if ($filePath[-1] !== '/') {
            $filePath .= '/';
        }

        $this->filePath = $filePath;
    }

    /**
     * @param string $name
     * @return string|bool
     */
    public function getValue(string $name)
    {
        if (file_exists($this->filePath . $name)) {
            return file_get_contents($this->filePath . $name);
        } else {
            return false;
        }
    }

    /**
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setValue(string $name, $value): void
    {
        file_put_contents($this->filePath . $name, $value);
    }
    
    /**
     * @param string $name
     * @return void
     */
    public function unsetValue(string $name): void
    {
        unlink($this->filePath . $name);
    }
    
    /**
     * @param string $name
     * @return void
     */
    public function removeValue(string $name): void
    {
        $this->unsetValue($name);
    }
    
    /**
     * @param string $name
     * @return void
     */
    public function deleteValue(string $name): void
    {
        $this->unsetValue($name);
    }

    public function getAll(): array
    {
        return glob($this->filePath . '*');
    }
    
}
