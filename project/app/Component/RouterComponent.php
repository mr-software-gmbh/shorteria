<?php

/**
 * This file is part of Shorteria by MR Software GmbH.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Component;

use Exception;
use JetBrains\PhpStorm\ArrayShape;

class RouterComponent
{
    protected mixed $requestUri;
    protected mixed $requestMethod;
    protected array $routeCollection = [];

    public function __construct()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $findQueryPos = strpos($uri, '?');
        $this->requestUri = $findQueryPos ? substr($uri, 0, $findQueryPos) : $uri;
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @throws Exception
     */
    #[ArrayShape([
        'controller' => 'string',
        'method'     => 'string',
    ])]
    final protected function getControllerMethod($callback): array
    {
        $separate = explode('@', $callback);
        if (count($separate)) {
            return [
                'controller' => 'App\\Controller\\' . $separate[0],
                'method'     => $separate[1],
            ];
        }
        throw new \Exception("$callback is not a valid callback.");
    }

    /**
     * @throws Exception
     */
    final protected function mount($callback)
    {
        if (is_callable($callback)) {
            return $callback;
        } else {
            $get = $this->getControllerMethod($callback);
            $controller = $get['controller'];
            $method = $get['method'];

            if (class_exists($controller)) {
                $controllerInstance = new $controller();

                return $controllerInstance->$method();
            }

            throw new \Exception("$controller class does not exist.");
        }
    }

    /**
     * @throws Exception
     */
    public function request($method, $route, $callback)
    {
        if ($this->requestMethod === strtoupper($method) && $this->requestUri === $route) {
            $this->mount($callback);
        }
    }

    /**
     * @throws Exception
     */
    public function get($route, $callback)
    {
        $this->routeCollection[] = [
            'route'    => $route,
            'method'   => 'GET',
            'callback' => $callback,
        ];
    }

    /**
     * @throws Exception
     */
    public function post($route, $callback)
    {
        $this->routeCollection[] = [
            'route'    => $route,
            'method'   => 'POST',
            'callback' => $callback,
        ];
    }

    /**
     * @throws Exception
     */
    public function put($route, $callback)
    {
        $this->routeCollection[] = [
            'route'    => $route,
            'method'   => 'PUT',
            'callback' => $callback,
        ];
    }

    /**
     * @throws Exception
     */
    public function delete($route, $callback)
    {
        $this->routeCollection[] = [
            'route'    => $route,
            'method'   => 'DELETE',
            'callback' => $callback,
        ];
    }

    /**
     * @throws Exception
     */
    public function patch($route, $callback)
    {
        $this->routeCollection[] = [
            'route'    => $route,
            'method'   => 'PATCH',
            'callback' => $callback,
        ];
    }

    public function array_key_search_recursive($needle, $haystack, $currentKey = ''): bool|string
    {
        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                $nextKey = $this->array_key_search_recursive($needle, $value, $currentKey . $key);
                if ($nextKey) {
                    return $nextKey;
                }
            } elseif ($value === $needle) {
                return is_numeric($key) ? $currentKey . $key : $currentKey;
            }
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        array_unshift($this->routeCollection, null);
        unset($this->routeCollection[0]);
        if ($this->requestMethod === 'GET' && !$this->array_key_search_recursive($this->requestUri, $this->routeCollection)) {
            $wildcardIndex = $this->array_key_search_recursive('/*', $this->routeCollection);
            if ($wildcardIndex) {
                $this->mount($this->routeCollection[$wildcardIndex]['callback']);
            }
        } else {
            foreach ($this->routeCollection as $data) {
                $this->request($data['method'], $data['route'], $data['callback']);
            }
        }
    }
}
