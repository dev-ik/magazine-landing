<?php

namespace controllers;

use classes\Application;
use interfaces\ViewInterface;

/**
 * Class Controller
 * @package controllers
 */
abstract class Controller extends Application
{

    /**
     * @var ViewInterface
     */
    protected $view;

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->view->render('index', []);
    }


    /**
     * @param ViewInterface $view
     */
    public function setViewComponent(ViewInterface $view)
    {
        $this->view = $view;
    }

}