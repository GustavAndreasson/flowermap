<?php

require_once("../library/config.php");

if (isset($_REQUEST["action"])) {
    $action = $_REQUEST["action"];
    $fm = new FlowerMap();
    if ($fm->is_logged_in()) {
        $garden = $fm->user->garden;
    } else {
        exit();
    }

    switch($action) {
        case "get_plants":
            get_plants($garden);
            break;
        case "add_plant":
            add_plant($garden);
            break;
        case "update_plant":
            update_plant($garden);
            break;
        case "delete_plant":
            delete_plant($garden);
            break;
        default:
            break;
    }
}

function get_plants($garden) {
    $response = array();
    foreach ($garden->plants as $plant) {
        $response[$plant->get_plant_id()] = $plant->get_json_data();
    }
    echo json_encode($response);
}

function add_plant($garden) {
    $description = $_REQUEST["description"];
    $coord_x = $_REQUEST["coord_x"];
    $coord_y = $_REQUEST["coord_y"];

    $species_id = $_REQUEST["species_id"];
    $species = $garden->species[$species_id];

    $plant = $garden->add_plant($description, $coord_x, $coord_y, $species);

    if ($_FILES["image"]["tmp_name"]) {
        $target_file = PLANT_IMAGE_PATH . $plant->get_plant_id . ".jpg";

        $check = getimagesize($_FILES["image"]["tmp_name"]);

        if($check !== false) {
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                Util::log("Sorry, there was an error uploading your file.", false);
            }
        }
    }

    echo json_encode($plant->get_json_data());
}

function update_plant($garden) {
    $plant_id = $_REQUEST["plant_id"];
    $plant = $garden->plants[$plant_id];
    $description = $_REQUEST["description"];
    $coord_x = $_REQUEST["coord_x"];
    $coord_y = $_REQUEST["coord_y"];

    if ($coord_x || $coord_y) {
        $plant->set_coord_x($coord_x);
        $plant->set_coord_y($coord_y);
    }

    if ($description) {
        $plant->set_description($description);
    }

    $plant->save();

    if ($_FILES["image"]["tmp_name"]) {
        $target_file = PLANT_IMAGE_PATH . $plant_id . ".jpg";

        $check = getimagesize($_FILES["image"]["tmp_name"]);

        if($check !== false) {
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                Util::log("Sorry, there was an error uploading your file.", false);
            }
        }
    }

    echo json_encode($plant->get_json_data());
}

function delete_plant($garden) {
    $plant_id = $_REQUEST["plant_id"];
    $plant = $garden->plants[$plant_id];
    $plant->delete();
    unset($garden->plants[$plant_id]);
}
