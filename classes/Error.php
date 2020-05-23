<?php

namespace classes;

/**
 * Class ErrorClass
 * @package classes
 */
class Error extends Application
{
    /**
     * @param $errNo
     * @param $errorMsg
     * @param $file
     * @param $line
     * @throws \Exception
     */
    public static function myErrorHandler($errno, $errorMsg, $file, $line, $context): void
    {
        $errorDevMessage = '<b>Exception time</b>: ' . date('Y-m-d H:i:s', time()) . " \n";
        $errorDevMessage .= '<b>Level</b>: ' . $errno . " \n";
        $errorDevMessage .= '<b>File</b>: ' . $file . " \n";
        $errorDevMessage .= '<b>In line</b>: ' . $line . " \n";
        $errorDevMessage .= '<b>Message</b>: ' . $errorMsg . " \n";
        $errorDevMessage .= '<b>Context</b>: ' . var_export($context, true) . " \n";

        if (parent::isDevMode()) {
            echo (parent::getView())->render('error', ['errorMessage' => $errorDevMessage]);
        }

        self::captureToSentry(new \Exception($errorDevMessage));

        throw new \Exception('404 Page Not Found', 404);
    }

    /**
     * @param \Throwable $exception
     * @throws \Exception
     */
    public static function myExceptionHandler(\Throwable $exception): void
    {
        $errorMessage = $exception->getMessage();
        $errorDevMessage = '<b>Exception time</b>: ' . date('Y-m-d H:i:s', time()) . " \n";
        $errorDevMessage .= '<b>File</b>: ' . $exception->getFile() . " \n";
        $errorDevMessage .= '<b>In line</b>: ' . $exception->getLine() . " \n";
        $errorDevMessage .= '<b>Code</b>: ' . $exception->getCode() . " \n";
        $errorDevMessage .= '<b>Message</b>: ' . $errorMessage . " \n";
        $errorDevMessage .= "<b>Stack-trace:</b>\n" . $exception->getTraceAsString() . " \n";

        $request = new Request();
        if ($request->isAjax()) {
            echo parent::isDevMode() ? $exception : 'Internal Server Error';
            throw new \Exception($exception);
        } else {
            echo (parent::getView())->render('error', ['errorMessage' => parent::isDevMode() ? $errorDevMessage : 'Internal Server Error']);
        }

        self::captureToSentry($exception);
    }
}