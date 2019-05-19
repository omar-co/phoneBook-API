<?php

namespace src\Services;

/**
 * Class Router.
 */
class Router
{
    /**
     * @var array
     */
    private $afterRoutes = [];

    /**
     * @var object|callable
     */
    protected $notFoundCallback;

    /**
     * @var string
     */
    private $baseRoute = '';

    /**
     * @var string
     */
    private $requestedMethod = '';

    /**
     * @var string
     */
    private $serverBasePath;

    /**
     * @var string
     */
    private $namespace = '';


    /**
     * Store a route and a handling function to be executed when accessed using one of the specified methods.
     *
     * @param string $methods Allowed methods, | delimited
     * @param string $pattern A route pattern such as /about/system
     * @param object|callable $fn The handling function to be executed
     */
    public function match($methods, $pattern, $fn)
    {
        $pattern = $this->baseRoute . '/' . trim($pattern, '/');
        $pattern = $this->baseRoute ? rtrim($pattern, '/') : $pattern;

        foreach (explode('|', $methods) as $method) {
            $this->afterRoutes[$method][] = [
                'pattern' => $pattern,
                'fn' => $fn,
            ];
        }
    }

    /**
     * Shorthand for a route accessed using any method.
     *
     * @param string $pattern A route pattern such as /about/system
     * @param object|callable $fn The handling function to be executed
     */
    public function all($pattern, $fn)
    {
        $this->match('GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using GET.
     *
     * @param string $pattern A route pattern such as /about/system
     * @param object|callable $fn The handling function to be executed
     */
    public function get($pattern, $fn)
    {
        $this->match('GET', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using POST.
     *
     * @param string $pattern A route pattern such as /about/system
     * @param object|callable $fn The handling function to be executed
     */
    public function post($pattern, $fn)
    {
        $this->match('POST', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using PATCH.
     *
     * @param string $pattern A route pattern such as /about/system
     * @param object|callable $fn The handling function to be executed
     */
    public function patch($pattern, $fn)
    {
        $this->match('PATCH', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using DELETE.
     *
     * @param string $pattern A route pattern such as /about/system
     * @param object|callable $fn The handling function to be executed
     */
    public function delete($pattern, $fn)
    {
        $this->match('DELETE', $pattern, $fn);
    }

    /**
     * Shorthand for a route accessed using PUT.
     *
     * @param string $pattern A route pattern such as /about/system
     * @param object|callable $fn The handling function to be executed
     */
    public function put($pattern, $fn)
    {
        $this->match('PUT', $pattern, $fn);
    }

    /**
     * Mounts a collection of callbacks onto a base route.
     *
     * @param string $baseRoute The route sub pattern to mount the callbacks on
     * @param callable $fn The callback method
     */
    public function mount($baseRoute, $fn)
    {
        $curBaseRoute = $this->baseRoute;
        $this->baseRoute .= $baseRoute;
        $fn();

        $this->baseRoute = $curBaseRoute;
    }

    /**
     * Get all request headers.
     *
     * @return array The request headers
     */
    public function getRequestHeaders()
    {
        $headers = [];

        if (function_exists('getallheaders')) {
            $headers = getallheaders();

            if ($headers !== false) {
                return $headers;
            }
        }

        foreach ($_SERVER as $name => $value) {
            if (($name === 'CONTENT_TYPE') || ($name === 'CONTENT_LENGTH') || strpos($name, 'HTTP_') === 0) {
                $headers[str_replace([' ', 'Http'], ['-', 'HTTP'], ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }

    /**
     * Get the request method used, taking overrides into account.
     *
     * @return string The Request method to handle
     */
    public function getRequestMethod()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
            ob_start();
            $method = 'GET';
        }
        elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $headers = $this->getRequestHeaders();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], ['PUT', 'DELETE', 'PATCH'])) {
                $method = $headers['X-HTTP-Method-Override'];
            }
        }

        return $method;
    }

    /**
     * Set a Default Lookup Namespace for Callable methods.
     *
     * @param string $namespace A given namespace
     */
    public function setNamespace($namespace)
    {
        if (is_string($namespace)) {
            $this->namespace = $namespace;
        }
    }

    /**
     * Get the given Namespace before.
     *
     * @return string The given Namespace if exists
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Execute the router.
     *
     * @param object|callable $callback Function to be executed after a matching route was handled (= after router middleware)
     *
     * @return bool
     */
    public function start($callback = null)
    {
        $this->requestedMethod = $this->getRequestMethod();
        $numHandled = 0;

        if (isset($this->afterRoutes[$this->requestedMethod])) {
            $numHandled = $this->handle($this->afterRoutes[$this->requestedMethod], true);
        }

        if ($numHandled === 0) {
            if ($this->notFoundCallback) {
                $this->invoke($this->notFoundCallback);
            } else {
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            }
        }
        else if ($callback && is_callable($callback)) {
            $callback();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
            ob_end_clean();
        }
        return $numHandled !== 0;
    }

    /**
     * Set the 404.
     *
     * @param object|callable $fn The function to be executed
     */
    public function set404($fn)
    {
        $this->notFoundCallback = $fn;
    }

    /**
     * Handle a a set of routes: if a match is found, execute the relating handling function.
     *
     * @param array $routes Collection of route patterns and their handling functions
     * @param bool $quitAfterRun Does the handle function need to quit after one route was matched?
     *
     * @return int The number of routes handled
     */
    private function handle($routes, $quitAfterRun = false)
    {
        $numHandled = 0;

        $uri = $this->getCurrentUri();

        foreach ($routes as $route) {
            $route['pattern'] = preg_replace('/\/{(.*?)}/', '/(.*?)', $route['pattern']);

            if (preg_match_all('#^' . $route['pattern'] . '$#', $uri, $matches, PREG_OFFSET_CAPTURE)) {
                $matches = array_slice($matches, 1);
                $params = array_map(function ($match, $index) use ($matches) {

                    if (isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                        return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
                    }

                    return isset($match[0][0]) ? trim($match[0][0], '/') : null;
                }, $matches, array_keys($matches));
                $this->invoke($route['fn'], $params);
                ++$numHandled;

                if ($quitAfterRun) {
                    break;
                }
            }
        }
        return $numHandled;
    }

    private function invoke($fn, $params = [])
    {
        if (is_callable($fn)) {
            call_user_func_array($fn, $params);
        }
        elseif (strpos($fn, '@') !== false) {

            list($controller, $method) = explode('@', $fn);

            if ($this->getNamespace() !== '') {
                $controller = $this->getNamespace() . '\\' . $controller;
            }

            if (class_exists($controller) && call_user_func_array([new $controller(), $method], $params) === false &&
                forward_static_call_array([$controller, $method], $params) === false) {
                return;
            }
        }
    }

    /**
     * Define the current relative URI.
     *
     * @return string
     */
    public function getCurrentUri()
    {
        $uri = substr(rawurldecode($_SERVER['REQUEST_URI']), strlen($this->getBasePath()));
        if (false !== strpos($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        return '/' . trim($uri, '/');
    }

    /**
     * Return server base Path, and define it if isn't defined.
     *
     * @return string
     */
    public function getBasePath()
    {
        if ($this->serverBasePath === null) {
            $this->serverBasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        }
        return $this->serverBasePath;
    }
}