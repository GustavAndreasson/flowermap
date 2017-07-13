<?php

require_once("../library/config.php");

if (isset($_REQUEST["action"])) {
    $action = $_REQUEST["action"];
    $fm = new FlowerMap();
    
    switch($action) {
    case "login":
        login($fm);
        break;
    case "logout":
        logout($fm);
        break;
    case "register":
        register($fm);
        break;
    default:
        break;
    }
}

function login($fm) {
    $name = $_REQUEST["name"];
    $password = $_REQUEST["password"];
    
    if (!$fm->login($name, $password)) {
        $_SESSION["MESSAGE"] = "Fel användarnamn eller lösenord";
    }
    header("Location: /flowermap");
    exit();
}

function logout($fm) {
    $fm->logout();
    header("Location: /flowermap");
    exit();
}

function register($fm) {
    $name = $_REQUEST["name"];
    $password = $_REQUEST["password"];
    
    if (!$fm->register($name, $password)) {
        $_SESSION["MESSAGE"] = "Det finns redan en användare med det här användarnamnet";
    }
    header("Location: /flowermap");
    exit();
}