<?php
class GardenController extends AbstractController {
    private $garden;

    public function execute($fm) {
        if ($fm->isLoggedIn()) {
            $this->garden = $fm->user->garden;
            parent::execute($fm);
        } else {
            throw new Exception("No user is logged in", 403);
        }
    }

    function updateAction() {
        $name = $this->request->get("name");
        if ($name) {
            $this->garden->setName($name);
        }
        if ($this->request->getFile("image")) {
            $targetFile = GARDEN_IMAGE_PATH . $this->garden->getGardenId() . ".svg";

            $check = getimagesize($this->request->getFile("image")["tmp_name"]);

            if($check !== false) {
                if (!move_uploaded_file($this->request->getFile("image")["tmp_name"], $targetFile)) {
                    Util::log("Sorry, there was an error uploading your file.", false);
                }
            }
        }

        echo json_encode([
            "name" => $this->garden->getName(),
            "image" => $this->garden->getImage()
        ]);
    }
}
