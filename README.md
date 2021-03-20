# NZT WEB framework
NZT is a modular WEB framework.
The NZT has a modular structure.
Each module has it's own container (PSR-11) and infrastructure (controllers, models, views ...)
## Loader
`Loader::$baseFileName` first part of filename used in Loader menthods (baseFileName + filename + baseFileExt)

`Loader::$baseFileExt` last part of filename in Loader menthods (baseFileName + filename + baseFileExt)

`Loader::includeOnce(string $filename)` simular to php `include_once()`

`Loader::include(string $filename)` simular to php `include()`

`Loader::requireOnce(string $filename)` simular to php `require_once()`, raises `EloaderFileNotFound`

`Loader::require(string $filename)` simular to php `require()`, raises `EloaderFileNotFound`

`Loader::includeFiles(array $fileNames): ?array` multiple `include_once()`

`Loader::requireFiles(array $fileNames): ?array` multiple `require_once()`, raises `EloaderFileNotFound`

`Loader::load(string $className): object` create new class with name `$className`, raises `EloaderClassNotFound`

`Loader::execute(string $className, string $methodName, array $methodParams = [])` create new class with name `$className` and run class method `$methodName` with params $methodParams, raises `EloaderClassNotFound` and `EloaderClassMethodNotFound`

`Loader::getFunction($classNameOrObject, string $methodName)` return single callable from class `$classNameOrObject` and method `$methodName`, raises `EloaderClassNotFound` and `EloaderClassMethodNotFound`

### Modules part
`Loader::includeModules(string $path, string $filename): array` run all modules and wont stop on error
`Loader::requireModules(string $path, string $filename): array` run all modules and raise `EloaderModuleNotFound` exception on error
##### Example
Load all modules from `current_dir/modules/*` and run `run.php` from each module root dir.
```
Loader::$baseFileName = __DIR__ . '/';
Loader::requireModules('modules', 'run');
```
### Config part
`Loader::requireConfig(string $filename)` load php file with config array, raises `EloaderConfigFileNotFound`
`Loader::getConfig(string $configPath)` get loaded config, raises `EloaderConfigPathNotFound`
`Loader::getAllConfig() : array` return all config as array
##### Example
```
Loader::$baseFileName = __DIR__ . '/';
$data = Loader::requireConfig('config/config');
```
*config.php*
```
<?php
return [
    'test' => 'dfgsdfgsdfgsdfgdsfg', 
    '123' => 321,
    'sub' => ['sub-sub' => 99999]
];
```
## Router
`Router->__construct(MethodResolver $methodResolver, PathExtractor $pathExtractor)`
`Router->route(array $routes)`
##### Example
```
$method = new MethodResolverHttp();
$path = new PathExtractorGet();
$router = new Router($method, $path);

$router->route([
    'route/x/y/([a-z]+)/([0-9]+)' => [
        'GET' => Loader::getFunction(controllerTest::class, 'testMethod'),
        'POST' => function() { echo 555; }
    ],
    '@default' => Loader::getFunction(controllerTest::class, 'test'),
    '@notfound' => function() { echo 123; }
]);
```
*@default* - if there is empty '' path
*@notfound* - if path is not found
## Container
Container uses a dependency injection pattern and implements PSR-11
Container of each *module* extends `BaseContainer`
### Base functionality
`Container::getInstance(): BaseContainer` get single instance of container
`Container->get(string $id): object` get object from container
`Container->set(string $id, object $object)` put object in container
`Container->has(string $id)` check for object in container
`Container->[remove|delete|unset](string $id)` remove
### Static methods functionality
```
Container::get_(string $id): object
Container::set_(string $id, object $object) 
Container::has_(string $id)
Container::[remove_|delete_|unset_](string $id)
```
##### Example
```
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
```
->
```
$c = UserContainer::getInstance();
$user = $c->getUser();
```
## Event Dispatcher
EventDispatcher is a trait and it uses an Event Dispatcher pattern and implements PSR-14

EventDispatcher is used in BaseUser class and it can be used in other classes ...

`EventDispatcher->[dispath|dispathObject](object $event)` - dispath all listeners using $event class as parameter

`EventDispatcher->dispathFn(string $event, array $params = [])` - dispath all listeners using $event event name and $params as parameters

`EventDispatcher->addListener(string $event, callable $fn)` - add callable object as a new listener

`EventDispatcher->getListenersForEvent(string $event): iterable` - return all listeners
## Storage
Storages is used in `Session` and `User` classes and can be used elsewhere ...
### Storage interface
```
interface StorageInterface
{
    public function getValue(string $name);
    public function setValue(string $name, $value): void;
    public function unsetValue(string $name): void;
    public function removeValue(string $name): void;
    public function deleteValue(string $name): void;
    public function getAll(): array;
}
```
### Storage types
#### JsonStorage
`JsonStorage->__construct(StorageInterface $storage, string $itemID)`
#### FileStorage
`FileStorage->__construct(string $filePath)`
#### Savable interface (can be used with the storage interface)
```
interface Savable
{
    public function load(): void;
    public function save(): void;
}
```
## Session
`BaseSession->__construct(StorageInterface $storage)`
##### Example
```
new Session(new FileStorage(__DIR__ . '/../storage'));
```
## User
User class is used for user management (login, register, logout, ...)
The user object can be implemented by different storage types (files, database, nosql ...)
`BaseUser->__construct(SessionInterface $session, UserStorageInterface $storage)`
```
BaseUser->login(string $login, string $password): bool
BaseUser->loginByID(string $id): bool
BaseUser->logout()
BaseUser->isLogged(): bool
BaseUser->activate(string $id)
BaseUser->isActivated(): bool
BaseUser->getId(): string
BaseUser->register(string $login, string $password, array $data)
```
### User Storage Interface
```
interface UserStorageInterface
{
    public function getUser(string $login, string $password);
    public function getUserById(string $id);
    public function registerUser(string $login, string $password, array $data): bool;
    public function activateUser(string $id): bool;
    public function isActivated(array $userdata): bool;
    public function getId(array $userdata): string;
}
```
##### Example (using container)
```
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
```
## Tests
run `cd tests` and `./phpunit .`
## Uses
PHPUnit from https://phpunit.de/