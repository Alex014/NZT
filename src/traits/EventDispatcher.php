<?php

namespace nzt\traits;

use nzt\exceptions\EEventDoesNotExist;


trait EventDispatcher
{
    public static array $events;        

    /**
     * @param string $event
     * @param array $params
     * @return void
     */
    public function dispathFn(string $event, array $params = [])
    {
        if (empty(self::$events[static::class . '.' . $event])) {
            return false;
        }

        foreach (self::$events[static::class . '.' . $event] as $fn) {
            call_user_func_array($fn, $params);
        }

        return true;
    }

    /**
     * @param object $event
     * @return void
     */
    public function dispathObject(object $event)
    {
        if (empty(self::$events[static::class . '.' . get_class($event)])) {
            return false;
        }

        foreach (self::$events[static::class . '.' . get_class($event)] as $fn) {
            call_user_func($fn, $event);
        }

        return true;
    }

    /**
     * @param object $event
     * @return void
     */
    public function dispath(object $event)
    {
        return $this->dispathObject($event);
    }

    /**
     * @param string $event
     * @param callable $fn
     * @return void
     */
    public function addListener(string $event, callable $fn)
    {
        self::$events[static::class . '.' . $event][] = $fn;
    }

    /**
     * @param string $event
     * @return iterable<callable>
     */
    public function getListenersForEvent(string $event): iterable
    {
        return self::$events[static::class . '.' . $event];
    }
}
