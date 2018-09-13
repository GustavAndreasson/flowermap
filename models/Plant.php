<?php
class Plant {
    private $conn;
    private $garden_id;
    private $plant_id;
    private $description;
    private $coord_x;
    private $coord_y;
    public $species;

    public function __construct($conn, $garden_id, $plant_id, $description, $coord_x, $coord_y, $species) {
        $this->conn = $conn;
        $this->garden_id = $garden_id;
        $this->plant_id = $plant_id;
        $this->description = $description;
        $this->coord_x = $coord_x;
        $this->coord_y = $coord_y;
        $this->species = $species;

        if (!$plant_id) {
            try {
                $now = date("Y-m-d H:i:s");
                $species_id = $this->species->get_species_id();
                $sql = "INSERT INTO plants (plant_id, species_id, garden_id, description, coord_x, coord_y, created_date) ";
                $sql .= "VALUES (null, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(array($species_id, $this->garden_id, $this->description, $this->coord_x, $this->coord_y, $now));
                $this->plant_id = $this->conn->lastInsertId();
            } catch (PDOException $e) {
                Util::log("Something went wrong when creating new plant: " . $e->getMessage(), true);
            }
        }
    }

    public function get_plant_id() {
        return $this->plant_id;
    }
    public function get_species_id() {
        return $this->species->get_species_id();
    }
    public function get_name() {
        return $this->species->get_name();
    }
    public function get_description() {
        return $this->description;
    }
    public function get_coord_x() {
        return $this->coord_x;
    }
    public function get_coord_y() {
        return $this->coord_y;
    }
    public function set_description($description) {
        $this->description = $description;
    }
    public function set_coord_x($coord_x) {
        $this->coord_x = $coord_x;
    }
    public function set_coord_y($coord_y) {
        $this->coord_y = $coord_y;
    }

    public function get_image() {
        if (file_exists(PLANT_IMAGE_PATH . $this->plant_id . ".jpg")) {
            return "var/images/plants/" . $this->plant_id . ".jpg";
        } elseif (file_exists(SPECIES_IMAGE_PATH . $this->get_species_id() . ".jpg")) {
            return "var/images/species/" . $this->get_species_id() . ".jpg";
        } else {
            return "";
        }
    }

    public function save() {
        $now = date("Y-m-d H:i:s");
        $species_id = $this->species->get_species_id();
        $sql = "UPDATE plants SET species_id = ?, description = ?, coord_x = ?, coord_y = ? ";
        $sql .= "WHERE plant_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($species_id, $this->description, $this->coord_x, $this->coord_y, $this->plant_id));
    }

    public function delete() {
        $sql = "DELETE FROM plants WHERE plant_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($this->plant_id));
    }

    public function get_json_data() {
        $json_data = array();
        $json_data['id'] = $this->get_plant_id();
        $json_data['species_id'] = $this->get_species_id();
        $json_data['name'] = $this->get_name();
        $json_data['description'] = $this->get_description();
        $json_data['coord_x'] = $this->get_coord_x();
        $json_data['coord_y'] = $this->get_coord_y();
        $json_data['image'] = $this->get_image();
        return $json_data;
    }
}
