<?php
class PlantController extends AbstractController {
    private $garden;

    public function execute($fm) {
        if ($fm->isLoggedIn()) {
            $this->garden = $fm->user->garden;
            parent::execute($fm);
        } else {
            throw new Exception("No user is logged in", 403);
        }
    }

    function getAction() {
        $response = array();
        foreach ($this->garden->getPlants() as $plant) {
            $response[$plant->getPlantId()] = $plant->getJsonData();
        }
        echo json_encode($response);
    }

    function addAction() {
        $description = $this->request->get("description");
        $coordX = $this->request->get("coord_x");
        $coordY = $this->request->get("coord_y");

        $speciesId = $this->request->get("species_id");

        $plant = $this->garden->addPlant($description, $coordX, $coordY, $speciesId);

        if ($this->request->getFile("image")) {
            $targetFile = PLANT_IMAGE_PATH . $plant->getPlantId() . ".jpg";

            $check = getimagesize($this->request->getFile("image")["tmp_name"]);

            if($check !== false) {
                if (!move_uploaded_file($this->request->getFile("image")["tmp_name"], $targetFile)) {
                    Util::log("Sorry, there was an error uploading your file.", false);
                }
            }
        }

        echo json_encode($plant->getJsonData());
    }

    function updateAction() {
        $plantId = $this->request->get("plant_id");
        $plant = $this->garden->getPlants($plantId);
        $description = $this->request->get("description");
        $coordX = $this->request->get("coord_x");
        $coordY = $this->request->get("coord_y");

        if ($coordX || $coordY) {
            $plant->setCoordX($coordX);
            $plant->setCoordY($coordY);
        }

        if ($description) {
            $plant->setDescription($description);
        }

        $plant->save();

        if ($this->request->getFile("image")) {
            $targetFile = PLANT_IMAGE_PATH . $plantId . ".jpg";

            $check = getimagesize($this->request->getFile("image"));

            if($check !== false) {
                if (!move_uploaded_file($this->request->getFile("image")["tmp_name"], $targetFile)) {
                    Util::log("Sorry, there was an error uploading your file.", false);
                }
            }
        }

        echo json_encode($plant->getJsonData());
    }

    function deleteAction() {
        $plantId = $this->request->get("plant_id");
        $this->garden->deletePlants($plantId);
    }

}
