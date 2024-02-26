<?php

namespace emadisavi\phpmvc;

class Application {

    public static string $ROOT_DIR;
    public string $userClass;
    public Router $router;
    public Request $request;
    public Response $response;
    public Controller $controller;
    public DataBase $db;
    public Session $session;
    public ?DbModel $user;
    public View $view;
    public static Application $app;

    public function __construct($rootPath, array $config)
    {   
        self::$app = $this;
        self::$ROOT_DIR = $rootPath;
        $this->request = new Request();
        $this->response = new Response();
        $this->controller = new Controller();
        $this->db = new DataBase($config['db']);
        $this->session = new Session();
        $this->view = new View();
        $this->router = new Router($this->request , $this->response);


        $this->userClass = $config['userClass'];

        $primaryValue = $this->session->get('user');
        if ($primaryValue) {

            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        } else
        {
            $this->user = null;
        }
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function login(DbModel $user)
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;

    }

    public function logOut()
    {
        $this->user = null;
        $this->session->remove('user');
    }

    public static function isGuest()
    {
        return !self::$app->user;
    }

    public function run()
    {
        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('_error',['exception' => $e]);
        }
    }


}