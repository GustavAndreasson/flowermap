<?php
class GardenController extends AbstractController {
    private $garden;

    public function execute($fm) {
        if ($fm->is_logged_in()) {
            $this->garden = $fm->user->garden;
            parent::execute($fm);
        } else {
            echo "NOT LOGGED IN";
        }
        return true;
    }

    function update_action() {
        $name = isset($_REQUEST["name"]) ? $_REQUEST["name"] : false;
        if ($name) {
            $this->garden->set_name($name);
        }
        if (isset($_FILES["image"]) && $_FILES["image"]["tmp_name"]) {
            $target_file = GARDEN_IMAGE_PATH . $this->garden->get_garden_id() . ".svg";

            $check = getimagesize($_FILES["image"]["tmp_name"]);

            if($check !== false) {
                if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
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
