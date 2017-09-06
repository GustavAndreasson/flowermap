<?php

require_once("../library/config.php");

if (isset($_REQUEST["action"])) {
    $action = $_REQUEST["action"];
    $plant_id = $_REQUEST["plant_id"];
    $fm = new FlowerMap();
    if ($fm->is_logged_in() && $plant_id) {
        $plant = $fm->user->garden->plants[$plant_id];
    } else {
        header("Location: /flowermap");
        exit();
        return;
    }
    
    switch($action) {
    case "update_plant":
        update_plant($plant);
        break;
    default:
        break;
    }
}

function update_plant($plant) {
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

}