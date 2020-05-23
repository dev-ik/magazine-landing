<?php

namespace classes;

use components\SessionComponent;
use components\ViewComponent;
use controllers\Controller;
use helpers\SecurityHelper;
use interfaces\DbInterface;
use interfaces\ViewInterface;
use Psr\Log\LoggerInterface;


class Application
{
    /**
     * @Description path to controllers
     */
    const CONTROLLER_DIR = '\controllers\\';

    /**
     * @var Configuration
     */
    public static $configuration;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var DbInterface
     */
    protected $db;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SessionComponent
     */
    protected $session;

    /**
     * Application constructor.
     * @throws \Exception
     */
    private function __construct()
    {
        self::$configuration = Configuration::getInstance();
        $this->request = new Request();
        $this->logger = new FileLogger();
        $dbConfig = $this->request->isTest()
            ? self::$configuration->getConfigTestDb()
            : self::$configuration->getConfigDb();
        $this->db = MySql::getInstance($dbConfig);
        $this->session = new SessionComponent();
        $this->session->open();
        $this->session->setCSRF();
    }

    /**
     * @throws \Exception
     */
    public function run(): void
    {
        $this->checkRequest();

        $entryPoint = $this->request->getResolvePath();
        $explodePath = explode('/', $entryPoint);

        if (empty($explodePath[0])) {
            $explodePath[0] = self::$configuration->getDefaultController();
        }
        if (empty($explodePath[1])) {
            $explodePath[1] = self::$configuration->getDefaultAction();
        }

        $controller = self::CONTROLLER_DIR . ucfirst($explodePath[0]) . 'Controller';
        if (file_exists(__DIR__ . '/..' . str_replace('\\', '/', $controller . '.php'))) {
            $action = 'action' . ucwords($explodePath[1], '-');
            $action = str_replace('-', '', $action);

            /** @var Controller $controller */
            $controller = new $controller();
            $controller->setViewComponent(self::getView());

            $content = $controller->$action();

            self::showDisplay($content);
        } else {
            throw new \Exception('404 Page Not Found', 404);
        }
    }

    /**
     * @param string $content
     */
    public static function showDisplay(string $content): void
    {
        echo $content;
    }

    /**
     * @throws \Exception
     */
    private function checkRequest()
    {
        if ($this->request->isPost()) {
            /** @var SessionComponent $session */
            $session = new SessionComponent();
            $checkCSRF = SecurityHelper::checkCSRF((string)$session->getCSRF() ?? '',
                (string)$this->request->getPost('_csrf'));
            if (!$checkCSRF) {
                throw new \Exception('400 Bad Request', 400);
            }
        }
    }

    /**
     * @return ViewInterface
     */
    public static function getView(): ViewInterface
    {
        static $view;
        if (null === $view) {
            $view = new ViewComponent(self::$configuration->getTwigConfiguration());
        }

        return $view;
    }

    public static function getSentrySingleton(): \Raven_Client
    {
        static $client;
        if (null === $client) {
            $client = new \Raven_Client(getenv('SENTRY_DSN'), [
                'environment' => getenv('SENTRY_ENV') ?: null,
            ]);
        }

        return $client;
    }

    /**
     * @return bool
     */
    public static function isDevMode(): bool
    {
        $devMode = (int)getenv('DEV_MODE');
        return $devMode === 1;
    }

    /**
     * @param \Exception $exception
     */
    public static function captureToSentry(\Exception $exception): void
    {
        $sentryClient = self::getSentrySingleton();
        $sentryClient->captureException($exception);
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @return DbInterface
     */
    public function getDb(): DbInterface
    {
        return $this->db;
    }

    /**
     * @param bool $isTest
     * @return Application
     * @throws \Exception
     */
    public static function getInstance()
    {
        static $app;
        if ($app === null) {
            $app = new static();
        }

        return $app;
    }
}