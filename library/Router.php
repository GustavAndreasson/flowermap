<?php
class Router {
    public static function route() {
        $request_uri = rtrim($_SERVER['REQUEST_URI'], '/');
        $request_parts = explode("/", $request_uri);
        if ($request_parts[0] == "") {
            array_shift($request_parts);
        }
        if ($request_parts[0] == "flowermap") {
            array_shift($request_parts);
        }
        $controller = isset($request_parts[0]) ? strtoupper(substr($request_parts[0], 0, 1)) . substr($request_parts[0], 1) : "Index";
        $controller_name = $controller . "Controller";
        if (file_exists(CONTROLLERS_PATH . $controller_name . ".php")) {
            if (!class_exists($controller_name)) {
                require(CONTROLLERS_PATH . $controller_name . ".php");
            }
            array_shift($request_parts);
            return new $controller_name($request_parts);
        } else {
            return false;
        }
    }
}
