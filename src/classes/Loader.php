<?php

namespace nzt\classes;

use \nzt\exceptions\EloaderFileNotFound;
use \nzt\exceptions\EloaderConfigFileNotFound;
use \nzt\exceptions\EloaderConfigPathNotFound;
use \nzt\exceptions\EloaderClassNotFound;
use \nzt\exceptions\EloaderClassMethodNotFound;
use \nzt\exceptions\EloaderModuleNotFound;

class Loader
{
    public static string $baseFileName = '';
    public static string $baseFileExt = '.php';
    private static array $config = [];

    /**
     * @param string $filename
     * @return boolean
     */
    private static function fileExists(string $filename): bool
    {
        return file_exists($filename) && !is_dir($filename);
    }

    /**
     * @param string $filename
     * @return mixed
     */
    public static function includeOnce(string $filename)
    {
        return include_once(self::$baseFileName . $filename . self::$baseFileExt);
    }

    /**
     * @param string $filename
     * @return mixed
     */
    public static function include(string $filename)
    {
        return include(self::$baseFileName . $filename . self::$baseFileExt);
    }

    /**
     * @param string $filename
     * @return mixed
     */
    public static function requireOnce(string $filename)
    {

        if (self::fileExists(self::$baseFileName . $filename . self::$baseFileExt)) {
            return require_once(self::$baseFileName . $filename . self::$baseFileExt);
        } else {
            throw new EloaderFileNotFound(self::$baseFileName . $filename . self::$baseFileExt);
        }
    }

    /**
     * @param string $filename
     * @return mixed
     */
    public static function require(string $filename)
    {

        if (self::fileExists(self::$baseFileName . $filename . self::$baseFileExt)) {
            return require(self::$baseFileName . $filename . self::$baseFileExt);
        } else {
            throw new EloaderFileNotFound(self::$baseFileName . $filename . self::$baseFileExt);
        }
    }

    /**
     * @param array $fileNames<string>
     * @return array|null
     */
    public static function includeFiles(array $fileNames): ?array
    {
        $result = [];

        foreach($fileNames as $filename) {
            $result[] = include_once(self::$baseFileName . $filename . self::$baseFileExt);
        }
        return $result;
    }

    /**
     * @param array $fileNames
     * @return array|null
     */
    public static function requireFiles(array $fileNames): ?array
    {
        $result = [];

        foreach($fileNames as $filename) {
            if (self::fileExists(self::$baseFileName . $filename . self::$baseFileExt)) {
                $result[] = require_once(self::$baseFileName . $filename . self::$baseFileExt);
            } else {
                throw new EloaderFileNotFound(self::$baseFileName . $filename . self::$baseFileExt);
            }
        }
        return $result;
    }

    /**
     * @param string $className
     * @return object
     */
    public static function load(string $className): object
    {
        if (! class_exists($className)) {
            throw new EloaderClassNotFound($className);
        }

        return new $className();
    }

    /**
     * @param string $className
     * @param string $methodName
     * @param array $methodParams
     * @return mixed
     */
    public static function execute(string $className, string $methodName, array $methodParams = [])
    {
        if (! class_exists($className)) {
            throw new EloaderClassNotFound($className);
        }

        $class = new $className();

        if (! method_exists($class, $methodName)) {
            throw new EloaderClassMethodNotFound($className, $methodName);
        }

        return call_user_func_array([$class, $methodName], $methodParams);
    }

    /**
     * @param string|object $classNameOrObject
     * @param string $methodName
     * @return void
     */
    public static function getFunction($classNameOrObject, string $methodName)
    {
        if (is_string($classNameOrObject) && ! class_exists($classNameOrObject)) {
            throw new EloaderClassNotFound($classNameOrObject);
        }

        if (is_string($classNameOrObject)) {
            $class = new $classNameOrObject();
        } else {
            $class = $classNameOrObject;
        }

        if (! method_exists($class, $methodName)) {
            throw new EloaderClassMethodNotFound($classNameOrObject, $methodName);
        }

        return fn() => call_user_func_array([$class, $methodName], func_get_args());
    }

    /**
     * @param string $path
     * @param string $filename
     * @return array
     */
    public static function includeModules(string $path, string $filename): array
    {
        $result = [];

        $path = Loader::$baseFileName . $path;

        if ($path[-1] !== '/') {
            $path .= '/';
        }

        $dirs = glob($path . '*');

        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                $filepath = $dir . '/' . $filename . self::$baseFileExt;

                if (file_exists($filepath)) {
                    $result[basename($dir)] = include_once($filepath);
                }
            }
        }

        return $result;
    }


    /**
     * @param string $path
     * @param string $filename
     * @return array
     */
    public static function requireModules(string $path, string $filename): array
    {
        $result = [];

        $path = Loader::$baseFileName . $path;

        if ($path[-1] !== '/') {
            $path .= '/';
        }

        $dirs = glob($path . '*');

        foreach ($dirs as $dir) {
            if (is_dir($dir)) {
                $filepath = $dir . '/' .$filename . self::$baseFileExt;

                if (file_exists($filepath)) {
                    $result[basename($dir)] = include_once($filepath);
                } else {
                    throw new EloaderModuleNotFound($filename, basename($dir), $dir);
                }
            }
        }

        return $result;
    }

    /**
     * @param string $filename
     * @throws \Exception
     */
    public static function requireConfig(string $filename)
    {        
        try {
            $config = self::require($filename);
        }
        catch (EloaderFileNotFound $e) {
            throw new EloaderConfigFileNotFound($filename);
        }

        if (!is_array($config)) {
            throw new EloaderConfigFileNotFound($filename);
        }

        self::$config = self::$config + $config;
    }

    /**
     * @param string $configPath
     * @return mixed
     */
    public static function getConfig(string $configPath)
    {
        $config = self::$config;
        $key_list = explode('.', $configPath);

        foreach ($key_list as $key) {
            if(isset($config[$key])) {
                $config = $config[$key];
            } else {
                throw new EloaderConfigPathNotFound($configPath);
            }
        }

        return $config;
    }

    /**
     * @return array
     */
    public static function getAllConfig() : array
    {
        return self::$config;
    }
}
