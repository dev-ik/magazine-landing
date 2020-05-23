<?php

namespace components;

use helpers\SecurityHelper;

class SessionComponent
{
    /**
     * @throws \Exception
     */
    public function open(): void
    {
        if ($this->getIsActive()) {
            return;
        }

        session_start();
    }
    

    public function destroy(): void
    {
        if ($this->getIsActive()) {
            session_unset();
            session_destroy();
        }
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * @param $key
     * @param null $defaultValue
     * @return mixed|null
     * @throws \Exception
     */
    public function get($key, $defaultValue = null)
    {
        $this->open();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $defaultValue;
    }

    /**
     * @param $key
     * @param $value
     * @throws \Exception
     */
    public function set($key, $value): void
    {
        $this->open();
        $_SESSION[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed|null
     * @throws \Exception
     */
    public function remove($key)
    {
        $this->open();
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
            unset($_SESSION[$key]);

            return $value;
        }

        return null;
    }

    /**
     * @param $key
     * @return bool
     * @throws \Exception
     */
    public function has($key): bool
    {
        $this->open();
        return isset($_SESSION[$key]);
    }

    /**
     * @throws \Exception
     */
    public function setCSRF()
    {
        if(!$this->has('_csrf')){
            $this->set('_csrf', SecurityHelper::generateCSRF());
        }
    }

    /**
     * @return mixed|null
     * @throws \Exception
     */
    public function getCSRF()
    {
        return $this->get('_csrf');
    }

}