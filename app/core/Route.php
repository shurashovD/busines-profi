<?php

class Route
{
    static function start()
    {
        $controller_name_default = 'Main';
        $action_name_default = 'index';
        $controller_name = $controller_name_default;
        $action_name = $action_name_default;

        $path = explode('?', $_SERVER["REQUEST_URI"])[0];
        $routes = explode('/', $path);
        
        if (!empty($routes[1])) {
            $controller_name = $routes[1];

            if ( $controller_name === 'admin' )
            {
                include "basic_auth.php";
            }
            else
            {
                include "auth.php";
            }
        }

        if (!empty($routes[2])) {
            $action_name = $routes[2];
        }

        $model_name = 'Model_' . $controller_name;
        $controller_name = 'Controller_' . $controller_name;
        $action_name = 'action_' . $action_name;

        $model_file = strtolower($model_name) . '.php';
        $model_path = "app/models/" . $model_file;
        if (file_exists($model_path)) {
            include "app/models/" . $model_file;
        }

        $controller_file = strtolower($controller_name) . '.php';
        $controller_path = "app/controllers/" . $controller_file;
        if (file_exists($controller_path)) {
            include "app/controllers/" . $controller_file;
        } else {
            Route::ErrorPage404();
        }

        $controller = new $controller_name;
        $action = $action_name;

        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            Route::ErrorPage404();
        }

    }

    static function ErrorPage404()
    {
        header('HTTP/1.1 404 Not Found');
        header("Status: 404 Not Found");
    }
}