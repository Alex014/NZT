<?php
require_once __DIR__ . '/../src/classes/Loader.php';
require_once __DIR__ . '/../src/exceptions/EloaderFileNotFound.php';
require_once __DIR__ . '/../src/exceptions/EloaderConfigFileNotFound.php';
require_once __DIR__ . '/../src/exceptions/EloaderConfigPathNotFound.php';
require_once __DIR__ . '/../src/exceptions/EloaderClassNotFound.php';
require_once __DIR__ . '/../src/exceptions/EloaderClassMethodNotFound.php';
require_once __DIR__ . '/../src/exceptions/EloaderModuleNotFound.php';

use PHPUnit\Framework\TestCase;
use \nzt\classes\Loader;

use nzt\exceptions\EloaderFileNotFound;
use nzt\exceptions\EloaderConfigFileNotFound;
use nzt\exceptions\EloaderConfigPathNotFound;
use nzt\exceptions\EloaderClassNotFound;
use nzt\exceptions\EloaderClassMethodNotFound;
use nzt\exceptions\EloaderModuleNotFound;

class testClass 
{
    public function test($a = 0, $b = 0, $c = 0) {
        return $a + $b + $c;
    }
}

Loader::$baseFileName = __DIR__ . '/';

class LoaderTest extends TestCase
{
    public function testFileNotFound()
    {
        Loader::$baseFileName = __DIR__ . '/';
        $this->expectException(EloaderFileNotFound::class);
        Loader::require('non-existing-file');
    }

    public function testFileNotFoundMany()
    {
        Loader::$baseFileName = __DIR__ . '/';
        $this->expectException(EloaderFileNotFound::class);
        Loader::requireFiles(['non-existing-file']);
    }

    public function testModuleNotFound()
    {
        Loader::$baseFileName = __DIR__ . '/';
        $this->expectException(EloaderModuleNotFound::class);
        Loader::requireModules('modules', 'run');
    }

    public function testModuleIncludeDataFile()
    {
        Loader::$baseFileName = __DIR__ . '/';
        $data = Loader::include('config/config');
        $this->assertTrue(is_array($data));
    }

    public function testModuleIncludeNullFile()
    {
        Loader::$baseFileName = __DIR__ . '/';
        $data = Loader::include('config/test');
        $this->assertTrue($data === 1);
    }

    public function testModuleRequireDataFile()
    {
        Loader::$baseFileName = __DIR__ . '/';
        $data = Loader::require('config/config');
        $this->assertTrue(is_array($data));
    }

    public function testModuleRequireNullFile()
    {
        Loader::$baseFileName = __DIR__ . '/';
        $data = Loader::require('config/test');
        $this->assertTrue($data === 1);
    }

    public function testRequireModules()
    {
        Loader::$baseFileName = __DIR__ . '/';
        $this->expectException(EloaderModuleNotFound::class);
        Loader::requireModules('modules', 'run');
    }

    public function testIncludeModules()
    {
        Loader::$baseFileName = __DIR__ . '/';
        $modules = Loader::includeModules('modules', 'run');
        $this->assertTrue(isset($modules['module1']) && isset($modules['module2']));
    }

    public function testLoaderClassNotFound()
    {
        Loader::$baseFileName = __DIR__ . '/';
        $class = Loader::load(testClass::class);
        $this->assertTrue(is_object($class));
    }

    public function testLoaderClassMethodNotFound()
    {
        Loader::$baseFileName = __DIR__ . '/';
        $result = Loader::execute(testClass::class, 'test');
        $this->assertTrue($result === 0);

        $result = Loader::execute(testClass::class, 'test', [1, 2, 3]);
        $this->assertTrue($result === 6);
    }

    public function testNoConfigFile()
    {
        Loader::$baseFileName = __DIR__ . '/';
        $this->expectException(EloaderConfigFileNotFound::class);
        Loader::requireConfig('config/no-config-file');
    }

    public function testConfig()
    {
        Loader::$baseFileName = __DIR__ . '/';
        $data = Loader::requireConfig('config/config');
        $this->assertTrue(Loader::getConfig('123') === 321);
        $this->assertTrue(Loader::getAllConfig()['123'] === 321);
        $this->assertTrue(Loader::getConfig('sub.sub-sub') === 99999);
        $this->expectException(EloaderConfigPathNotFound::class);
        $this->assertTrue(Loader::getConfig('xxx') === 'yyy');
    }
}
