<?php
class Router {
private static $request;

    public static function route($request) {
        self::$request = $request;

        $controller = $request->getUriPart(0) ? $request->getUriPart(0) : "index";
	$controllerName = preg_replace_callback('/(^|-)([a-z])/', function($m) { return strtoupper($m[2]); }, $controller);
        $controllerClass = $controllerName . "Controller";
        if (file_exists(CONTROLLERS_PATH . $controllerClass . ".php")) {
            if (!class_exists($controllerClass)) {
                require(CONTROLLERS_PATH . $controllerClass . ".php");
            }
            return new $controllerClass($request);
        } else {
            throw new Exception("$controllerClass does not exist", 404);
        }
    }

    public static function redirect($uri) {
        $target = self::$request->getRoot();
        $uriParts = explode('/', $uri);
        $ix = 0;
        foreach($uriParts as $part) {
            if ($part != "") {
                if ($part = "*") {
                    if (self::$request->getUriPart($ix)) {
                        $target .= "/" . self::$request->getUriPart($ix);
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
