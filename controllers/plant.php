<?php

require_once("../library/config.php");

if (isset($_REQUEST["action"])) {
    $action = $_REQUEST["action"];
    $fm = new FlowerMap();
    if ($fm->is_logged_in()) {
        $T = new Translate($fm->user->get_language());
    } else {
        $T = new Translate();
    }
    
    switch($action) {
    case "load_species":
        load_species($T);
        break;
    default:
        break;
    }
}

function load_species($T) {
    $url = $_REQUEST["url"];
    $species_info = Species::load_url_data($url);
    echo '<div class="row"><label for="add_plant_name">' . $T->__("Name") . '</label>';
    echo '<input type="text" name="name" id="add_plant_name" value="' . $species_info['name'] . '"></div>';
    foreach ($species_info['data'] as $name => $value) {
        echo '<div class="row"><span class="data_name">' . $name . '</span>';
        echo '<span class="data_value">' . $value . '</span>';
        echo '<input type="hidden" name="data[\'' . $name . '\']" value="' . $value . '"></div>';
    }
    echo '<div class="row"><img src="' . $species_info['image'] . '"></div>';
}