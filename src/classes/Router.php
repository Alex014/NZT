<?php

namespace nzt\classes;

use nzt\interfaces\MethodResolver;
use nzt\interfaces\PathExtractor;
use nzt\interfaces\RouterInterface;

use \nzt\exceptions\ERouterDefaultRouteNotSet;
use \nzt\exceptions\ERouterNotfoundRouteNotSet;

class Router implements RouterInterface
{
    private MethodResolver $methodResolver;
    private PathExtractor $pathExtractor;

    /**
     * @param iMethodResolver $methodResolver
     * @param iPathExtractor $pathExtractor
     */
    public function __construct(MethodResolver $methodResolver, PathExtractor $pathExtractor)
    {
        $this->methodResolver = $methodResolver;
        $this->pathExtractor = $pathExtractor;
    }

    /**
     * @param string $path
     * @param string $route
     * @return array
     */
    private function parseRoute(string $path, string $route, array &$matches): bool
    {
        $matches = [];
        $route = str_replace('/', '\/', $route);
        $result = preg_match('/' . $route . '/i', $path, $matches);
        $matches = array_slice($matches, 1, count($matches) - 1);
        return $result;
    }

    /**
     * @param array $routes
     * @return void
     */
    public function route(array $routes)
    {
        $path = $this->pathExtractor->getPath();
        $httpMethod = $this->methodResolver->getMethod();

        if ($path == '') {
            if (isset($routes['@default']) && is_callable($routes['@default'])) {
                return call_user_func_array($routes['@default'], []);
            } else {
                throw new ERouterDefaultRouteNotSet();
            }
        }

        foreach ($routes as $route => $item) {
            if (is_array($item)) {
                foreach ($item as $method => $item) {
                    if (($httpMethod === $method) && is_callable($item)) {
                        $matches = [];
                        if ($this->parseRoute($path, $route, $matches)) {
                            return call_user_func_array($item, $matches);
                        }
                    }
                }
            } elseif(is_callable($item)) {
                if (is_string($route)) {
                    $matches = [];
                    if ($this->parseRoute($path, $route, $matches)) {
                        return call_user_func_array($item, $matches);
                    }
                }
            }
        }

        if (isset($routes['@notfound']) && is_callable($routes['@notfound'])) {
            return call_user_func_array($routes['@notfound'], []);
        } else {
            throw new ERouterNotfoundRouteNotSet();
        }
    }
    
}
