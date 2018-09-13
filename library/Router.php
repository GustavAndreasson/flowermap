<?php
class Router {
    public static function route($request) {
        /*$request_uri = rtrim($_SERVER['REQUEST_URI'], '/');
        $request_parts = explode("/", $request_uri);
        if ($request_parts[0] == "") {
            array_shift($request_parts);
        }
        if ($request_parts[0] == "flowermap") {
            array_shift($request_parts);
        }*/
        $controller = $request->get_part(0) ? strtoupper(substr($request->get_part(0), 0, 1)) . substr($request->get_part(0), 1) : "Index";
        $controller_name = $controller . "Controller";
        if (file_exists(CONTROLLERS_PATH . $controller_name . ".php")) {
            if (!class_exists($controller_name)) {
                require(CONTROLLERS_PATH . $controller_name . ".php");
            }
            return new $controller_name($request);
        } else {
            throw new Exception("$controller_name does not exist", 404);
        }
    }
}
