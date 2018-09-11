<?php
class UserController extends AbstractController {

    function login_action() {
        $name = $_REQUEST["name"];
        $password = $_REQUEST["password"];

        if (!$this->fm->login($name, $password)) {
            $_SESSION["MESSAGE"] = "Fel användarnamn eller lösenord";
        }
        header("Location: /flowermap");
        exit();
    }

    function logout_action() {
        $this->fm->logout();
        header("Location: /flowermap");
        exit();
    }

    function register_action() {
        $name = $_REQUEST["name"];
        $password = $_REQUEST["password"];

        if (!$this->fm->register($name, $password)) {
            $_SESSION["MESSAGE"] = "Det finns redan en användare med det här användarnamnet";
        }
        header("Location: /flowermap");
        exit();
    }
}
