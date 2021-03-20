<?php
require_once __DIR__ . '/../src/classes/Loader.php';
use nzt\classes\Loader;

Loader::$baseFileName = __DIR__ . '/../src/interfaces/';
Loader::requireFiles(['StorageInterface','SessionInterface']);

Loader::$baseFileName = __DIR__ . '/../src/classes/';
Loader::requireFiles(['BaseSession','FileStorage']);

use PHPUnit\Framework\TestCase;
use nzt\classes\BaseContainer;
use nzt\classes\FileStorage;
use nzt\classes\BaseSession;

use nzt\exceptions\ERouterDefaultRouteNotSet;
use nzt\exceptions\ERouterNotfoundRouteNotSet;

class Session extends BaseSession {}

class Container extends BaseContainer {
    private static $session;

    public function getStorage(): FileStorage
    {
        return new FileStorage(__DIR__ . '/../storage');
    }

    public function getSession(): Session
    {
        if (!isset(self::$session)) {
            self::$session = new Session($this->getStorage());
        }

        return self::$session;
    }
}

$c = new Container();
$s = $c->getSession();
$s->initialize();

class SessionTest extends TestCase {
    public function testStorage()
    {
        $c = new Container();
        $storage = $c->getStorage();
        $storage->setValue('xxx', 'yyy');
        $this->assertTrue( $storage->getValue('xxx') === 'yyy' );
    }

    public function testStorageDelete()
    {
        $c = new Container();
        $storage = $c->getStorage();
        $storage->setValue('zzz', '123');
        $storage->removeValue('zzz');
        $this->assertTrue( $storage->getValue('zzz') === false );
    }

    public function testSession()
    {
        $c = new Container();
        $s = $c->getSession();
        $s->setValue('asdfgh', 'zxcvb');
        $this->assertTrue( $s->getValue('asdfgh') === 'zxcvb' );
        $s->finalize();
    }

    public function testSessionSave()
    {
        $c = new Container();
        $s = $c->getSession();
        $this->assertTrue( $s->getValue('asdfgh') === 'zxcvb' );
        $s->setValue('asdfgh', '');
        $s->finalize();
    }

    public function testSessionDelete()
    {
        $c = new Container();
        $s = $c->getSession();
        $this->assertTrue( $s->getValue('asdfgh') === '' );
        $s->finalize();
    }
}