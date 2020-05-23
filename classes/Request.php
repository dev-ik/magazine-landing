<?php

namespace classes;


/**
 * Class Request
 * @package classes
 */
class Request
{

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var array
     */
    protected $request;

    /**
     * @var array
     */
    protected $server;

    /**
     * @var array
     */
    protected $post;

    /**
     * @var array
     */
    protected $get;

    public function __construct()
    {
        $this->request = $_REQUEST;
        $this->server = $_SERVER;
        $this->post = $_POST;
        $this->get = $_GET;
        $this->findHeaders();
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        if (isset($this->server['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
            if ($requestUri !== '' && $requestUri[0] !== '/') {
                $requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $requestUri);
            }
        }
        return $requestUri ?? '';
    }

    public function isTest()
    {
        $cookie = $this->headers['cookie'] ?? null;
        if ($cookie) {
           if (mb_stripos($cookie, 'IS_TEST=1') !== false){
               return true;
           }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getResolvePath(): string
    {
        $pathInfo = $this->getUrl();

        if (($pos = strpos($pathInfo, '?')) !== false) {
            $pathInfo = substr($pathInfo, 0, $pos);
        }

        $pathInfo = urldecode($pathInfo);

        if (substr($pathInfo, 0, 1) === '/') {
            $pathInfo = substr($pathInfo, 1);
        }

        return (string)$pathInfo;
    }

    /**
     * @return mixed
     */
    public function getHostName()
    {
        return $this->server['HTTP_HOST'];
    }

    /**
     * @return string
     */
    public function getRemoteAddress(): string
    {
        return $this->server['REMOTE_ADDR'] ?? null;
    }

    /**
     * @param string $type
     * @return array|null
     */
    public function get(string $type): ?array
    {
        return $this->$type ?? null;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return bool
     */
    public function isAjax(): bool
    {
        if (isset($this->headers['x-requested-with'])) {
            return $this->headers['x-requested-with'] === 'XMLHttpRequest';
        }
        return false;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function getPost($name)
    {
        return $this->get('post')[$name] ?? null;
    }

    protected function findHeaders(): void
    {
        if ($this->headers === null) {
            if (function_exists('getallheaders')) {
                $headers = getallheaders();
                foreach ($headers as $name => $value) {
                    $name = strtolower($name);
                    $this->headers[$name] = $value;
                }
            } elseif (function_exists('http_get_request_headers')) {
                $headers = http_get_request_headers();
                foreach ($headers as $name => $value) {
                    $name = strtolower($name);
                    $this->headers[$name] = $value;
                }
            } else {
                foreach ($_SERVER as $name => $value) {
                    if (strncmp($name, 'HTTP_', 5) === 0) {
                        $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                        $name = strtolower($name);
                        $this->headers[$name] = $value;
                    }
                }
            }
        }
    }

    public function isPost()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
        }
        return false;
    }

}