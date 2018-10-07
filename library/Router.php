<?php
class Router {
private static $request;

    public static function route($request) {
        self::$request = $request;

        if ($request->getUriPart(0)) {
            $controller = strtoupper(substr($request->getUriPart(0), 0, 1)) . substr($request->getUriPart(0), 1);
        } else {
            $controller = "Index";
        }

        $controllerName = $controller . "Controller";
        if (file_exists(CONTROLLERS_PATH . $controllerName . ".php")) {
            if (!class_exists($controllerName)) {
                require(CONTROLLERS_PATH . $controllerName . ".php");
            }
            return new $controllerName($request);
        } else {
            throw new Exception("$controllerName does not exist", 404);
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
