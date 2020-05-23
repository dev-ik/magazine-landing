<?php

namespace helpers;


class SecurityHelper
{
    /**
     * @return string
     * @throws \Exception
     */
    public static function generateCSRF(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * @param string $actual
     * @param string $expected
     * @return bool
     */
    public static function checkCSRF(string $actual, string $expected): bool
    {
        return hash_equals($actual, $expected);
    }

}