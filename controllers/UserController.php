<?php
class UserController extends AbstractController {
    private $user;

    public function execute($fm) {
        $this->user = $fm->user;
        parent::execute($fm);
    }

    function loginAction() {
        $name = $this->request->get("name");
        $password = $this->request->get("password");

        if (!$this->user->login($name, $password)) {
            $_SESSION["MESSAGE"] = "Fel användarnamn eller lösenord";
        }
        Router::redirect("/");
        exit();
    }

    function logoutAction() {
        $this->user->logout();
        Router::redirect("/");
        exit();
    }

    function registerAction() {
        $name = $this->request->get("name");
        $password = $this->request->get("password");

        if (!$this->user->register($name, $password)) {
            $_SESSION["MESSAGE"] = "Det finns redan en användare med det här användarnamnet";
        }
        Router::redirect("/");
        exit();
    }
}
