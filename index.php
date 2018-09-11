<?php
require_once("library/config.php");
$fm = new FlowerMap();

$controller = Router::route();
$controller->execute($fm);

/*if (!($controller && $controller->execute($fm))) {
    if ($fm->is_logged_in()) {
        $T = new Translate($fm->user->get_language());
    } else {
        $T = new Translate();
    }

    include(TEMPLATES_PATH . "main.phtml");
}*/
