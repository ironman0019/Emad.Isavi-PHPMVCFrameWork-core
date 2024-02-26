<?php

namespace emadisavi\phpmvc\middlewares;

use emadisavi\phpmvc\Application;
use emadisavi\phpmvc\exceptions\ForbiddenException;

class AuthMiddleware extends BaseMiddleware {

    public array $actions = [];

    public function __construct(array $actions = [])  
    {
        $this->actions = $actions;
    }

    public function execute()
    {
        if (Application::isGuest()) {
            if (empty($actions) && in_array(Application::$app->controller->action, $this->actions)) {
                
                throw new ForbiddenException();
                // var_dump($this->actions);
            }
        }
    }

}