<?php
class SpeciesController extends AbstractController {
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
        foreach ($this->garden->species as $species) {
            $response[$species->getSpeciesId()] = $species->getJsonData();
        }
        echo json_encode($response);
    }

    function addAction() {
        $name = $this->request->get("name");
        $data = $this->request->get("data");
        if ($data === null) {
            $data = array();
        }
        $url = $this->request->get("url");
        $img = $this->request->get("species_image");
        $species = $this->garden->addSpecies($name, $url, $data, $img);
        echo json_encode($species->getJsonData());
    }

    function updateAction() {
        $speciesId = $this->request->get("species_id");
        $species = $this->garden->species[$speciesId];

        $name = $this->request->get("name");
        $data = $this->request->get("data");
        $url = $this->request->get("url");
        $img = $this->request->get("species_image");

        $species->setName($name);
        $species->setUrl($url);
        $species->setData($data);
        $species->setImage($img);

        $species->save();

        echo json_encode($species->getJsonData());
    }

    function loadIdAction() {
        $id = $this->request->get("id");
        $species = $this->garden->species[$id];
        echo json_encode($species->getJsonData());
    }

    function loadUrlAction() {
        $url = $this->request->get("url");
        if (!$url) {
            $name = trim($this->request->get("name"));
            $urlName = strtolower($name);
            $urlName = str_replace(" ", "-", $urlName);
            $urlName = str_replace("å", "a", $urlName);
            $urlName = str_replace("ä", "a", $urlName);
            $urlName = str_replace("ö", "o", $urlName);
            $url = "https://floralinnea.se/" . $urlName . ".html";
            $speciesInfo = Species::loadUrlData($url);
            if (!$speciesInfo) {
                Util::log("Could not load url " . $url . ". Searching for " . $name . "...");
                $url = Species::searchUrl($name);
                if ($url) {
                    Util::log("...found " . $url);
                    $speciesInfo = Species::loadUrlData($url);
                }
            }
        } else {
            $speciesInfo = Species::loadUrlData($url);
        }
        echo json_encode($speciesInfo);
    }
}
