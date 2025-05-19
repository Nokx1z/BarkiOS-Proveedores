<?php
class FrontController {
    private $controller;
    private $action;
    private $params = [];

    public function __construct() {
        $this->parseUrl();
        $this->loadController();
    }

    private function parseUrl() {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $scriptName = $_SERVER['SCRIPT_NAME']; // Ej: /app/index.php

        $basePath = str_replace('/index.php', '', $scriptName); // /app
        $relativeUri = str_replace($basePath, '', $requestUri); // /admin/products...

        $uriParts = explode('?', $relativeUri);
        $path = trim($uriParts[0], '/');
        $segments = explode('/', $path);

        // /admin/products/index
        $this->controller = $this->sanitize($segments[1] ?? 'products');
        $this->action = $this->sanitize($segments[2] ?? 'index');
        $this->params = array_slice($segments, 3);
    }

    private function sanitize($input) {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $input);
    }

    private function loadController() {
        $controllerName = ucfirst($this->controller) . 'Controller';
        $controllerFile = __DIR__ . '/Admin/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            die("Error 404: Controlador no encontrado ($controllerFile)");
        }

        require_once $controllerFile;

        if (!class_exists($controllerName)) {
            die("Error 500: Clase del controlador no existe ($controllerName)");
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $this->action)) {
            die("Error 404: Acción '{$this->action}' no encontrada.");
        }

        call_user_func_array([$controller, $this->action], $this->params);
    }
}


// Uso
new FrontController();