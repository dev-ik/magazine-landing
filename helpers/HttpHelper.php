<?php


namespace helpers;

class HttpHelper
{

    /**
     * @param null $url
     * @param bool $post
     * @param null $arg
     * @param bool $ssl
     * @return array
     */
    public static function sendRequest($url = 'https://ya.ru', $post = false, $arg = null, $ssl = true): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($arg) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $arg);
            }
        }
        if (self::hasSSL($url)) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $ssl ? 2 : 0);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        return [
            'content' => curl_exec($ch),
            'err' => curl_errno($ch),
            'errmsg' => curl_error($ch),
            'header' => curl_getinfo($ch)];
    }

    /**
     * @param $url
     * @return bool
     */
    public static function hasSSL($url)
    {
        return (bool)preg_match('/^https/is', trim($url));
    }
}