<?php
require_once __DIR__ . '/../src/classes/Loader.php';
use nzt\classes\Loader;

Loader::$baseFileName = __DIR__ . '/../src/interfaces/';
Loader::requireFiles(['StorageInterface', 'Savable', 'Searchable', 'SessionInterface','UserStorageInterface']);

Loader::$baseFileName = __DIR__ . '/../src/traits/';
Loader::requireFiles(['EventDispatcher']);

Loader::$baseFileName = __DIR__ . '/../src/classes/';
Loader::requireFiles(['BaseSession', 'FileStorage', 'SavableSearchableStorage', 'JsonStorage', 'BaseUser']);

use PHPUnit\Framework\TestCase;
use nzt\classes\BaseContainer;
use nzt\classes\FileStorage;
use nzt\classes\JsonStorage;
use nzt\classes\BaseSession;
use nzt\classes\SavableSearchableStorage;
use nzt\classes\BaseUser;

use nzt\interfaces\UserStorageInterface;

use nzt\exceptions\ERouterDefaultRouteNotSet;
use nzt\exceptions\ERouterNotfoundRouteNotSet;

class UserSession extends BaseSession {}

class UserStorage implements UserStorageInterface 
{
    public SavableSearchableStorage $storage;

    public function __construct(SavableSearchableStorage $storage)
    {
        $this->storage = $storage;
    }

    private function genId()
    {
        return md5(time() . 'uusssrrr');
    }

    public function getUser(string $login, string $password)
    {
        $this->storage->load();
        $users = $this->storage->searchByParams(['login' => $login, 'password' => $password]);

        if (!empty($users)) {
            return $users[0];
        } else {
            return false;
        }
    }

    public function getUserById(string $id)
    {
        $this->storage->load();
        $this->storage->getValue($id);
    }

    public function registerUser(string $login, string $password, array $data): bool
    {
        $id = $this->genId();
        $data['id'] = $id;
        $data['login'] = $login;
        $data['password'] = $password;
        $data['activated'] = 0;

        $this->storage->load();
        $this->storage->setValue($id, $data);
        $this->storage->save();

        return true;
    }

    public function activateUser(string $id): bool
    {
        $this->storage->load();
        $data = $this->storage->getValue($id);
        $data['activated'] = 1;
        $this->storage->setValue($id, $data);
        $this->storage->save();

        return true;
    }

    public function isActivated(array $userdata): bool
    {
        return (bool) $userdata['activated'];
    }

    public function getId(array $userdata): string
    {
        return $userdata['id'];
    }
}

class User extends BaseUser {}

class UserContainer extends BaseContainer {
    private static $session;

    public function getStorage(): FileStorage
    {
        return new FileStorage(__DIR__ . '/../storage');
    }

    public function getSession(): UserSession
    {
        if (!isset(self::$session)) {
            self::$session = new UserSession($this->getStorage());
        }

        return self::$session;
    }

    public function getUserSavableStorage(): SavableSearchableStorage
    {
        return new JsonStorage($this->getStorage(), 'users.json');
    }

    public function getUserStorage(): UserStorageInterface
    {
        return new UserStorage($this->getUserSavableStorage());
    }

    public function getUser(): BaseUser
    {
        return new User($this->getSession(), $this->getUserStorage());
    }
}

class storage
{
    public static array $data = [];
}

$usrnum = rand(10000, 90000) . (time() % 10000);
$username = 'Test_'.$usrnum;
$sample = time();

class UserTest extends TestCase
{
    public function testUserCreation()
    {
        global $username, $sample;

        $c = UserContainer::getInstance();
        $user = $c->getUser();

        $user->addListener('AfterRegister', function($userdata)
            {
                storage::$data['sample'] = $userdata['sample']; 
            }
        );

        $user->register($username, $username, ['sample' => $sample]);

        $this->assertTrue( storage::$data['sample'] === $sample );

        $user->addListener('AfterLogin', function($userdata)
            {
                storage::$data['login'] = $userdata['login']; 
                storage::$data['logged-in'] = true;
            }
        );

        $this->assertTrue( $user->login($username, $username) );

        $this->assertTrue( storage::$data['login'] === $username );

        $this->assertFalse( $user->isActivated() );

        $user->addListener('AfterLogout', function()
            {
                storage::$data['logged-in'] = false;
            }
        );

        $user->logout();

        $this->assertFalse( storage::$data['logged-in'] );
    }

    public function testUserLoginActivate()
    {
        global $username;
        
        $c = UserContainer::getInstance();
        $user = $c->getUser();
        $this->assertTrue( $user->login($username, $username) );

        storage::$data['activated'] = false; 

        $user->addListener('AfterActivate', function($id)
            {
                storage::$data['activated'] = true; 
                storage::$data['id'] = $id; 
            }
        );

        $user->activate($user->getId());

        $this->assertTrue( storage::$data['activated'] );
        $this->assertTrue( storage::$data['id'] === storage::$data['id'] );

        $user->logout();
        $user->login($username, $username);
        $this->assertTrue( $user->isActivated() );
    }

}