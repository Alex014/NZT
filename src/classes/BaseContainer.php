<?php
namespace nzt\classes;

use nzt\exceptions\EContainerItemNotFound;

class BaseContainer
{
    private static $container;
    private static $items;

    /**
     * @return BaseContainer
     */
    public static function getInstance(): BaseContainer
    {
        $className = static::class;

        if (empty(self::$container)) {
            self::$container = [];
        }

        if (empty(self::$container[$className])) {
            self::$container[$className] = new $className();
        }

        return self::$container[$className];
    }

    /**
     * @param string $id
     * @return object
     */
    public function get(string $id): object
    {
        return self::get_($id);
    }

    /**
     * @param string $id
     * @param object $object
     * @return void
     */
    public function set(string $id, object $object)
    {
        return self::set_($id, $object);
    }

    /**
     * @param string $id
     * @return void
     */
    public function unset(string $id)
    {
        return self::unset_($id);
    }

    /**
     * @param string $id
     * @return void
     */
    public function remove(string $id)
    {
        $this->unset($id);
    }

    /**
     * @param string $id
     * @return void
     */
    public function delete(string $id)
    {
        $this->unset($id);
    }

    /**
     * @param string $id
     * @return boolean
     */
    public function has(string $id) 
    {
        return self::has_($id);
    }

    /**
     * @param string $id
     * @return object
     */
    public static function get_(string $id): object
    {
        if (!self::has_($id)) {
            throw new EContainerItemNotFound($id, basename(static::class));
        }

        return self::$items[static::class][$id];
    }

    /**
     * @param string $id
     * @param object $object
     * @return void
     */
    public static function set_(string $id, object $object) 
    {
        self::$items[static::class][$id] = $object;
    }

    /**
     * @param string $id
     * @return void
     */
    public static function unset_(string $id)
    {
        unset(self::$items[static::class][$id]);
    }

    /**
     * @param string $id
     * @return void
     */
    public static function remove_(string $id)
    {
        self::unset_($id);
    }

    /**
     * @param string $id
     * @return void
     */
    public static function delete_(string $id)
    {
        self::unset_($id);
    }

    /**
     * @param string $id
     * @return boolean
     */
    public static function has_(string $id) 
    {
        return isset(self::$items[static::class][$id]);
    }
}
