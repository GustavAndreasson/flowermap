<?php
class UserController extends AbstractController {

    function login_action() {
        $name = $this->request->get("name");
        $password = $this->request->get("password");

        if (!$this->fm->login($name, $password)) {
            $_SESSION["MESSAGE"] = "Fel användarnamn eller lösenord";
        }
        Router::redirect("/");
        exit();
    }

    function logout_action() {
        $this->fm->logout();
        Router::redirect("/");
        exit();
    }

    function register_action() {
        $name = $this->request->get("name");
        $password = $this->request->get("password");

        if (!$this->fm->register($name, $password)) {
            $_SESSION["MESSAGE"] = "Det finns redan en användare med det här användarnamnet";
        }
        Router::redirect("/");
        exit();
    }
}
