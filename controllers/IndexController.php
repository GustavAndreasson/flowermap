<?php
class IndexController extends AbstractController {
    public function indexAction() {
        $fm = $this->fm;

        if ($fm->isLoggedIn()) {
            $T = new Translate($fm->user->getLanguage());
        } else {
            $T = new Translate();
        }

        include(TEMPLATES_PATH . "main.phtml");

        return true;
    }
}
