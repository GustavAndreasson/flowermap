<?php

require_once("../library/config.php");

$fm = new FlowerMap();

if (isset($_REQUEST["action"])) {
    $action = $_REQUEST["action"];
    $fm = new FlowerMap();
    if ($fm->is_logged_in()) {
        $garden = $fm->user->garden;
    } else {
        exit();
    }

    switch($action) {
        case "get_species":
            get_species($garden);
            break;
        case "add_species":
            add_species($garden);
            break;
        case "update_species":
            update_species($garden);
            break;
        case "load_species_id":
            load_species_id($garden);
            break;
        case "load_species_url":
            load_species_url();
            break;
        default:
            break;
    }
}

function get_species($garden) {
    $response = array();
    foreach ($garden->species as $species) {
        $response[$species->get_species_id()] = $species->get_json_data();
    }
    echo json_encode($response);
}

function add_species($garden) {
    $name = $_REQUEST["name"];

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
    echo json_encode($species->get_json_data());
}

function update_species($garden) {
    $species_id = $_REQUEST["species_id"];

    $species = $garden->species[$species_id];

    if ($_FILES["image"]["tmp_name"]) {
        $target_file = SPECIES_IMAGE_PATH . $species_id . ".jpg";

        $check = getimagesize($_FILES["image"]["tmp_name"]);

        if($check !== false) {
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                Util::log("Sorry, there was an error uploading your file.", false);
            }
        }
    }
    echo json_encode($species->get_json_data());
}

function load_species_id($garden) {
    $id = $_REQUEST["id"];
    $species = $garden->species[$id];
    echo json_encode($species->get_json_data());
}

function load_species_url() {
    $url = $_REQUEST["url"];
    if (!$url) {
        $name = trim($_REQUEST["name"]);
        $url_name = strtolower($name);
        $url_name = str_replace(" ", "-", $url_name);
        $url_name = str_replace("å", "a", $url_name);
        $url_name = str_replace("ä", "a", $url_name);
        $url_name = str_replace("ö", "o", $url_name);
        $url = "https://floralinnea.se/" . $url_name . ".html";
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
    echo json_encode($species_info);
}
