<?php
class IndexController extends AbstractController {
    public function index_action() {
        $fm = $this->fm;

        if ($fm->is_logged_in()) {
            $T = new Translate($fm->user->get_language());
        } else {
            $T = new Translate();
        }

        include(TEMPLATES_PATH . "main.phtml");

        return true;
    }
}
