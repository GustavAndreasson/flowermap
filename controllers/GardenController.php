<?php
class GardenController extends AbstractController {
    private $garden;

    public function execute($fm) {
        if ($fm->is_logged_in()) {
            $this->garden = $fm->user->garden;
            parent::execute($fm);
        } else {
            throw new Exception("No user is logged in", 403);
        }
    }

    function update_action() {
        $name = $this->request->get("name");
        if ($name) {
            $this->garden->set_name($name);
        }
        if ($this->request->get_file("image")) {
            $target_file = GARDEN_IMAGE_PATH . $this->garden->get_garden_id() . ".svg";

            $check = getimagesize($this->request->get_file("image")["tmp_name"]);

            if($check !== false) {
                if (!move_uploaded_file($this->request->get_file("image")["tmp_name"], $target_file)) {
                    Util::log("Sorry, there was an error uploading your file.", false);
                }
            }
        }

        echo json_encode([
            "name" => $this->garden->get_name(),
            "image" => $this->garden->get_image()
        ]);
    }
}
