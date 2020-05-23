<?php

namespace components;

use interfaces\ViewInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ViewComponent implements ViewInterface
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(array $configuration)
    {
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader, $configuration);
        $this->twig->addFunction(new \Twig_SimpleFunction('_csrf', function () {
                /** @var SessionComponent $session */
                $session = new SessionComponent();
                return $session->getCSRF() ?? '';
            }));
    }

    /**
     * @param string $templateName
     * @param array $params
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render(string $templateName, array $params): string
    {
        $layout = $this->twig->load('layouts\main.twig');
        $template = $this->twig->load($templateName . '.twig');
        return $template->render(array_merge(['layout' => $layout], $params));
    }

    /**
     * @param string $templateName
     * @param array $params
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderPartial(string $templateName, array $params): string
    {
        $layout = $this->twig->load('layouts\content.twig');
        $template = $this->twig->load($templateName . '.twig');
        return $template->render(array_merge(['layout' => $layout], $params));
    }
}