<?php

require_once("../library/config.php");

if (isset($_REQUEST["action"])) {
    $action = $_REQUEST["action"];
    $fm = new FlowerMap();
    $garden = $fm->user->garden;
    
    switch($action) {
    case "add_plant":
        add_plant($garden);
        break;
    default:
        break;
    }
}

function add_plant($garden) {
    $name = $_REQUEST["name"];
    $description = $_REQUEST["description"];
    $coord_x = $_REQUEST["coord_x"];
    $coord_y = $_REQUEST["coord_y"];
    
    $plant_id = $garden->add_plant($name, $description, $coord_x, $coord_y);

    $target_file = PLANT_IMAGE_PATH . $plant_id . ".jpg";

    $check = getimagesize($_FILES["image"]["tmp_name"]);

    if($check !== false) {
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            Util::log("Sorry, there was an error uploading your file.", false);
        }
    }
    
    header("Location: /flowermap");
    exit();
}