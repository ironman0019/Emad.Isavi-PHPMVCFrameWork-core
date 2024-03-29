<?php

namespace emadisavi\phpmvc;

use emadisavi\phpmvc\Application;
use emadisavi\phpmvc\middlewares\BaseMiddleware;

class Controller {

    public string $layout = 'main';
    public string $action = '';
    /** 
     * @var \emadisavi\phpmvc\middlewares\BaseMiddleware[]
    */
    protected array $middlewares = [];

    public function render($view, $params = [])
    {
        return Application::$app->view->renderView($view, $params);
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}