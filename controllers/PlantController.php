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
        $description = $this->request->get("description");
        $coord_x = $this->request->get("coord_x");
        $coord_y = $this->request->get("coord_y");

        $species_id = $this->request->get("species_id");
        $species = $this->garden->species[$species_id];

        $plant = $this->garden->add_plant($description, $coord_x, $coord_y, $species);

        if ($this->request->get_file("image")) {
            $target_file = PLANT_IMAGE_PATH . $plant->get_plant_id . ".jpg";

            $check = getimagesize($this->request->get_file("image")["tmp_name"]);

            if($check !== false) {
                if (!move_uploaded_file($this->request->get_file("image")["tmp_name"], $target_file)) {
                    Util::log("Sorry, there was an error uploading your file.", false);
                }
            }
        }

        echo json_encode($plant->get_json_data());
    }

    function update_action() {
        $plant_id = $this->request->get("plant_id");
        $plant = $this->garden->plants[$plant_id];
        $description = $this->request->get("description");
        $coord_x = $this->request->get("coord_x");
        $coord_y = $this->request->get("coord_y");

        if ($coord_x || $coord_y) {
            $plant->set_coord_x($coord_x);
            $plant->set_coord_y($coord_y);
        }

        if ($description) {
            $plant->set_description($description);
        }

        $plant->save();

        if ($this->request->get_file("image")) {
            $target_file = PLANT_IMAGE_PATH . $plant_id . ".jpg";

            $check = getimagesize($this->request->get_file("image"));

            if($check !== false) {
                if (!move_uploaded_file($this->request->get_file("image")["tmp_name"], $target_file)) {
                    Util::log("Sorry, there was an error uploading your file.", false);
                }
            }
        }

        echo json_encode($plant->get_json_data());
    }

    function delete_action() {
        $plant_id = $this->request->get("plant_id");
        $plant = $this->garden->plants[$plant_id];
        $plant->delete();
        unset($this->garden->plants[$plant_id]);
    }

}
