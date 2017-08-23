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
    case "add_plant":
        add_plant($garden);
        break;
    case "load_species_id":
        load_species_id($garden, $T);
        break;
    case "load_species_url":
        load_species_url($T);
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
    
    header("Location: /flowermap");
    exit();
}

function add_plant($garden) {
    $name = $_REQUEST["name"];
    $description = $_REQUEST["description"];
    $coord_x = $_REQUEST["coord_x"];
    $coord_y = $_REQUEST["coord_y"];

    $species_id = $_REQUEST["species_id"];
    if ($species_id) {
        $species = $garden->species[$species_id];
    } else {
        if (isset($_REQUEST["data"])) {
            $data = $_REQUEST["data"];
        } else {
            $data = null;
        }
        $url = $_REQUEST["url"];
        if (isset($_REQUEST["species_image"])) {
            $img = $_REQUEST["species_image"];
        } else {
            $img = null;
        }
        $species = $garden->add_species($name, $url, $data, $img);
    }

    $plant_id = $garden->add_plant($description, $coord_x, $coord_y, $species)->get_plant_id();
    
    if ($_FILES["image"]["tmp_name"]) {
        $target_file = PLANT_IMAGE_PATH . $plant_id . ".jpg";
        
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        
        if($check !== false) {
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                Util::log("Sorry, there was an error uploading your file.", false);
            }
        }
    }
    
    header("Location: /flowermap");
    exit();
}

function load_species_id($garden, $T) {
    $id = $_REQUEST["id"];
    $species = $garden->species[$id];
    echo '<div class="row"><span class="name">' . $T->__("Name") . '</span>';
    echo '<span class="value">' . $species->get_name() . '</span></div>';
    foreach ($species->get_data() as $name => $value) {
        echo '<div class="row"><span class="data_name">' . $name . '</span>';
        echo '<span class="data_value">' . $value . '</span>';
    }
    if ($species->get_image()) {
        echo '<div class="row">';
        echo '<img src="' . $species->get_image() . '"></div>';
    }
}

function load_species_url($T) {
    $species_info = "";
    $url = $_REQUEST["url"];
    if (!$url) {
        $name = trim($_REQUEST["name"]);
        $url_name = strtolower($name);
        $url_name = str_replace(" ", "-", $url_name);
        $url_name = str_replace("å", "a", $url_name);
        $url_name = str_replace("ä", "a", $url_name);
        $url_name = str_replace("ö", "o", $url_name);
        $url = "http://floralinnea.se/" . $url_name . ".html";
        $species_info = Species::load_url_data($url);
        if (!$species_info) {
            Util::log("Could not load url " . $url . ". Searching for " . $name . "...");
            $url = Species::search_url($name);
            if ($url) {
                Util::log("...found " . $url);
                $species_info = Species::load_url_data($url);
            }
        }
    } else {
        $species_info = Species::load_url_data($url);
    }
    if ($species_info) {
        echo '<input type="hidden" name="loaded_species_name" value="' . $species_info['name'] . '">';
        echo '<input type="hidden" name="loaded_species_url" value="' . $url . '">';
        foreach ($species_info['data'] as $name => $value) {
            echo '<div class="row"><span class="data_name">' . $name . '</span>';
            echo '<span class="data_value">' . $value . '</span>';
            echo '<input type="hidden" name="data[' . $name . ']" value="' . $value . '"></div>';
        }
        echo '<div class="row">';
        echo '<input type="hidden" name="species_image" id="add_plant_image" value="' . $species_info['image'] . '">';
        echo '<img src="' . $species_info['image'] . '"></div>';
    } else {
        echo '<input type="hidden" name="loaded_species_url" value="' . $url . '">';
        echo '<div class="error">' . $T->__("No plant found") . '</div>';
    }
}