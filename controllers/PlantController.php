<?php
class PlantController extends AbstractController {
    private $garden;

    public function execute($fm) {
        if ($fm->is_logged_in()) {
            $this->garden = $fm->user->garden;
            parent::execute($fm);
        } else {
            throw new Exception("No user is logged in", 403);
        }
    }

    function get_action() {
        $response = array();
        foreach ($this->garden->plants as $plant) {
            $response[$plant->get_plant_id()] = $plant->get_json_data();
        }
        echo json_encode($response);
    }

    function add_action() {
        $description = $_REQUEST["description"];
        $coord_x = $_REQUEST["coord_x"];
        $coord_y = $_REQUEST["coord_y"];

        $species_id = $_REQUEST["species_id"];
        $species = $this->garden->species[$species_id];

        $plant = $this->garden->add_plant($description, $coord_x, $coord_y, $species);

        if ($_FILES["image"]["tmp_name"]) {
            $target_file = PLANT_IMAGE_PATH . $plant->get_plant_id . ".jpg";

            $check = getimagesize($_FILES["image"]["tmp_name"]);

            if($check !== false) {
                if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    Util::log("Sorry, there was an error uploading your file.", false);
                }
            }
        }

        echo json_encode($plant->get_json_data());
    }

    function update_action() {
        $plant_id = $_REQUEST["plant_id"];
        $plant = $this->garden->plants[$plant_id];
        $description = isset($_REQUEST["description"]) ? $_REQUEST["description"] : null;
        $coord_x = isset($_REQUEST["coord_x"]) ? $_REQUEST["coord_x"] : null;
        $coord_y = isset($_REQUEST["coord_y"]) ? $_REQUEST["coord_y"] : null;

        if ($coord_x || $coord_y) {
            $plant->set_coord_x($coord_x);
            $plant->set_coord_y($coord_y);
        }

        if ($description) {
            $plant->set_description($description);
        }

        $plant->save();

        if (isset($_FILES["image"]["tmp_name"]) && $_FILES["image"]["tmp_name"]) {
            $target_file = PLANT_IMAGE_PATH . $plant_id . ".jpg";

            $check = getimagesize($_FILES["image"]["tmp_name"]);

            if($check !== false) {
                if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    Util::log("Sorry, there was an error uploading your file.", false);
                }
            }
        }

        echo json_encode($plant->get_json_data());
    }

    function delete_action() {
        $plant_id = $_REQUEST["plant_id"];
        $plant = $this->garden->plants[$plant_id];
        $plant->delete();
        unset($this->garden->plants[$plant_id]);
    }

}
