<?php

require_once("../library/config.php");

if (isset($_REQUEST["action"])) {
    $action = $_REQUEST["action"];
    $fm = new FlowerMap();
    $garden = $fm->user->garden;
    if ($fm->is_logged_in()) {
        $T = new Translate($fm->user->get_language());
    } else {
        $T = new Translate();
    }


    switch($action) {
    case "update_garden":
        update_garden($garden);
        break;
    default:
        break;
    }
}

function update_garden($garden) {
    $name = $_REQUEST["name"];
    if ($name) {
        $garden->set_name($name);
    }
    if ($_FILES["image"]["tmp_name"]) {
        $target_file = GARDEN_IMAGE_PATH . $garden->get_garden_id() . ".svg";

        $check = getimagesize($_FILES["image"]["tmp_name"]);

        if($check !== false) {
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                Util::log("Sorry, there was an error uploading your file.", false);
            }
        }
    }

    echo json_encode([
        "name" => $garden->get_name(),
        "image" => $garden->get_image()
    ]);
}
