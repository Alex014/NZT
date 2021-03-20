<?php

namespace nzt\classes;

use nzt\interfaces\StorageInterface;
use nzt\classes\SavableSearchableStorage;

class JsonStorage extends SavableSearchableStorage
{
    public FileStorage $storage;
    public string $itemID;
    private array $data;

    /**
     * @param StorageInterface $storage
     * @param string $itemID
     */
    public function __construct(StorageInterface $storage, string $itemID)
    {
        $this->storage = $storage;
        $this->itemID = $itemID;
        $this->data = [];
    }

    public function load(): void
    {
        $data = json_decode($this->storage->getValue($this->itemID), true);

        if (!empty($data) && is_array($data)) {
            $this->data = $data;
        } else {
            $this->data = [];
        }
    }

    public function save(): void
    {
        $this->storage->setValue($this->itemID, json_encode($this->data));
    }

    /**
     * @param string $name
     * @return string|bool
     */
    public function getValue(string $name)
    {
        return $this->data[$name];
    }

    /**
     * @param string $name
     * @param string $value
     * @return void
     */
    public function setValue(string $name, $value): void
    {
        $this->data[$name] = $value;
    }
    
    /**
     * @param string $name
     * @return void
     */
    public function unsetValue(string $name): void
    {
        unset($this->data[$name]);
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

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->data;
    }
    
    public function searchBy(string $name, string $value): array
    {
        $result = [];

        foreach ($this->data as $key => $row) {
            foreach ($row as $field => $value) {
                if ($field === $name && $value === $value) {
                    $result[] = $row;
                }
            }
        }

        return $result;
    }

    public function searchByParams(array $params): array
    {
        $result = [];

        foreach ($this->data as $key => $row) {
            $found = true;
            foreach ($params as $param => $paramValue) {
                foreach ($row as $field => $value) {
                    if ($param === $field && $paramValue !== $value) {
                        $found = false;
                        break;
                    }
                }
                if (!$found) {
                    break;
                }
            }
            if ($found) {
                $result[] = $row;
            }
        }

        return $result;
    }
    
}
