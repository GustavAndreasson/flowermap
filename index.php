<?php
require_once("library/config.php");
$fm = new FlowerMap();

try {
    $request = new Request();
    $controller = Router::route($request);
    $controller->execute($fm);
} catch(Exception $e) {
    echo $e->getMessage();
}
