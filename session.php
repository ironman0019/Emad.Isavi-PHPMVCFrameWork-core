<?php

namespace emadisavi\phpmvc;

class Session {

    protected const FLASH_KEY = 'flash_massages';

    public function __construct()
    {
        session_start();

        $flashMassages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach($flashMassages as $key => &$flashMassage) {
            //mark to be removed
            $flashMassage['remove'] = true;
        }

        $_SESSION[self::FLASH_KEY] = $flashMassages;


        
    }

    public function setFlash($key, $massage)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            "remove" => false,
            "value" => $massage
        ];
    }

    public function getFlash($key)
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    public function set($key,$value)    
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        return $_SESSION[$key] ?? false;
    }

    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function __destruct()
    {
        $flashMassages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach($flashMassages as $key => &$flashMassage) {
            if ($flashMassage['remove']) {
                unset($flashMassages[$key]);
            }
        }

        $_SESSION[self::FLASH_KEY] = $flashMassages;
    }

}