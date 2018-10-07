<?php
class UserController extends AbstractController {

    function loginAction() {
        $name = $this->request->get("name");
        $password = $this->request->get("password");

        if (!$this->fm->login($name, $password)) {
            $_SESSION["MESSAGE"] = "Fel användarnamn eller lösenord";
        }
        Router::redirect("/");
        exit();
    }

    function logoutAction() {
        $this->fm->logout();
        Router::redirect("/");
        exit();
    }

    function registerAction() {
        $name = $this->request->get("name");
        $password = $this->request->get("password");

        if (!$this->fm->register($name, $password)) {
            $_SESSION["MESSAGE"] = "Det finns redan en användare med det här användarnamnet";
        }
        Router::redirect("/");
        exit();
    }
}
