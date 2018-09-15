<?php
class Router {
private static $request;

    public static function route($request) {
        self::$request = $request;

        if ($request->get_uri_part(0)) {
            $controller = strtoupper(substr($request->get_uri_part(0), 0, 1)) . substr($request->get_uri_part(0), 1);
        } else {
            $controller = "Index";
        }

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

    public static function redirect($uri) {
        $target = self::$request->get_root();
        $uri_parts = explode('/', $uri);
        $ix = 0;
        foreach($uri_parts as $part) {
            if ($part != "") {
                if ($part = "*") {
                    if (self::$request->get_uri_part($ix)) {
                        $target .= "/" . self::$request->get_uri_part($ix);
                    } else {
                        throw new Exception("Incorrect redirect wildcard");
                    }
                } else {
                    $target .= "/" . $part;
                }
                $ix += 1;
            }
        }
        header("Location: " . $target);
    }
}
