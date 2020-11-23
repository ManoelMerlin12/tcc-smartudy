<?php

class Routes {
    private $routes = array(
        '/users/add' => 'UsersController@add',
        '/users/edit' => 'UsersController@edit',
        '/questionario/retornarQuestaoAtual' => 'QuestionnaireController@retornarQuestaoAtual',
        '/questionario/responderQuestao' => 'QuestionnaireController@responderQuestao',
    );

    public function __construct()
    {
        $splitRoute = explode('@', $this->setRoute());
        $action = $splitRoute[1];
        if (file_exists('controller/' . $splitRoute[0] . '.php')) {
            require 'controller/' . $splitRoute[0] . '.php';
            $routeToEnter = new $splitRoute[0]();
            $routeToEnter->$action();
        }
    }

    public function setRoute() 
    {
        $route = $this->routes[$_SERVER['REQUEST_URI']];
        return $route;
    }
}

$route = new Routes();