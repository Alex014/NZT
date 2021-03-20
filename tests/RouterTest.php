<?php
require_once __DIR__ . '/../src/classes/Loader.php';
use nzt\classes\Loader;

Loader::$baseFileName = __DIR__ . '/../src/interfaces/';
Loader::requireFiles(['RouterInterface','MethodResolver','PathExtractor']);

Loader::$baseFileName = __DIR__ . '/../src/classes/';
Loader::requireFiles(['Router','MethodResolverTest','PathExtractorTest']);

Loader::$baseFileName = __DIR__ . '/../src/exceptions/';
Loader::requireFiles(['ERouterDefaultRouteNotSet','ERouterNotfoundRouteNotSet']);

use PHPUnit\Framework\TestCase;
use nzt\classes\Router;
use nzt\classes\MethodResolverTest;
use nzt\classes\PathExtractorTest;

use nzt\exceptions\ERouterDefaultRouteNotSet;
use nzt\exceptions\ERouterNotfoundRouteNotSet;

class controllerTest {
    public function test()
    {
        echo '789';
    }

    public function testMethod(string $param1, int $param2) 
    { 
        echo $param1 . '+' . $param2; 
    }
}

class RouterTest extends TestCase
{
    public function testNoDefaultRoute()
    {
        $method = new MethodResolverTest('GET');
        $path = new PathExtractorTest('');
        $router = new Router($method, $path);

        $this->expectException(ERouterDefaultRouteNotSet::class);
        
        $router->route([

        ]);
    }

    public function testNotfoundNotFoundRoute()
    {
        $method = new MethodResolverTest('GET');
        $path = new PathExtractorTest('jsfdjhsbdfsjdbh');
        $router = new Router($method, $path);

        $this->expectException(ERouterNotfoundRouteNotSet::class);
        
        $router->route([

        ]);
    }

    public function testNotFoundRoute()
    {        
        $method = new MethodResolverTest('GET');
        $path = new PathExtractorTest('jsfdjhsbdfsjdbh');
        $router = new Router($method, $path);

        $this->expectOutputString('123');

        $router->route([
            'xxx' => function() { echo 111; },
            'yyy' => function() { echo 222; },
            'zzz' => function() { echo 333; },
            '@default' => function() { echo 111; },
            '@notfound' => function() { echo 123; }
        ]);
    }

    public function testNotFoundRouteLoadMethod()
    {        
        $method = new MethodResolverTest('GET');
        $path = new PathExtractorTest('jsfdjhsbdfsjdbh');
        $router = new Router($method, $path);

        $this->expectOutputString('789');

        $router->route([
            'xxx' => function() { echo 111; },
            'yyy' => function() { echo 222; },
            'zzz' => function() { echo 333; },
            '@default' => function() { echo 123; },
            '@notfound' => Loader::getFunction(controllerTest::class, 'test')
        ]);
    }

    public function testDefaultRouteLoadMethod()
    {        
        $method = new MethodResolverTest('GET');
        $path = new PathExtractorTest('');
        $router = new Router($method, $path);

        $this->expectOutputString('789');

        $router->route([
            'xxx' => function() { echo 111; },
            'yyy' => function() { echo 222; },
            'zzz' => function() { echo 333; },
            '@default' => Loader::getFunction(controllerTest::class, 'test'),
            '@notfound' => function() { echo 123; }
        ]);
    }

    public function testRoute()
    {
        $method = new MethodResolverTest('GET');
        $path = new PathExtractorTest('route/x/y/param/123');
        $router = new Router($method, $path);

        $this->expectOutputString('param-123');

        $router->route([
            'route/x/y/([a-z]+)/([0-9]+)' => function(string $param1, int $param2) { echo $param1 . '-' . $param2; },
            '@default' => Loader::getFunction(controllerTest::class, 'test'),
            '@notfound' => function() { echo 123; }
        ]);
    }

    public function testRouteMethod()
    {
        $method = new MethodResolverTest('GET');
        $path = new PathExtractorTest('route/x/y/qwertyuiop/684654');
        $router = new Router($method, $path);

        $this->expectOutputString('qwertyuiop+684654');

        $router->route([
            'route/x/y/([a-z]+)/([0-9]+)' => [
                'GET' => Loader::getFunction(controllerTest::class, 'testMethod'),
                'POST' => function() { echo 555; }
            ],
            '@default' => Loader::getFunction(controllerTest::class, 'test'),
            '@notfound' => function() { echo 123; }
        ]);
    }
}