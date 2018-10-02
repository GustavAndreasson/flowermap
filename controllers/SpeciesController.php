<?php
class SpeciesController extends AbstractController {
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
        foreach ($this->garden->species as $species) {
            $response[$species->get_species_id()] = $species->get_json_data();
        }
        echo json_encode($response);
    }

    function add_action() {
        $name = $this->request->get("name");
        $data = $this->request->get("data");
        if ($data === null) {
            $data = array();
        }
        $url = $this->request->get("url");
        $img = $this->request->get("species_image");
        $species = $this->garden->add_species($name, $url, $data, $img);
        echo json_encode($species->get_json_data());
    }

    function update_action() {
        $species_id = $this->request->get("species_id");
        $species = $this->garden->species[$species_id];

        $name = $this->request->get("name");
        $data = $this->request->get("data");
        $url = $this->request->get("url");
        $img = $this->request->get("species_image");

        $species->set_name($name);
        $species->set_url($url);
        $species->set_data($data);
        $species->set_image($img);

        $species->save();

        echo json_encode($species->get_json_data());
    }

    function load_id_action() {
        $id = $this->request->get("id");
        $species = $this->garden->species[$id];
        echo json_encode($species->get_json_data());
    }

    function load_url_action() {
        $url = $this->request->get("url");
        if (!$url) {
            $name = trim($this->request->get("name"));
            $url_name = strtolower($name);
            $url_name = str_replace(" ", "-", $url_name);
            $url_name = str_replace("å", "a", $url_name);
            $url_name = str_replace("ä", "a", $url_name);
            $url_name = str_replace("ö", "o", $url_name);
            $url = "https://floralinnea.se/" . $url_name . ".html";
            $species_info = Species::load_url_data($url);
            if (!$species_info) {
                Util::log("Could not load url " . $url . ". Searching for " . $name . "...");
                $url = Species::search_url($name);
                if ($url) {
                    Util::log("...found " . $url);
                    $species_info = Species::load_url_data($url);
                }
            }
        } else {
            $species_info = Species::load_url_data($url);
        }
        echo json_encode($species_info);
    }
}
