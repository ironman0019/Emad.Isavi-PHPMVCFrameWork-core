<?php

namespace App\core;

class Response {

    public function setStatusCode($code)
    {
        return http_response_code($code);
    }

    public function redirect(string $url)
    {
        header('location: '.$url);
    }
}