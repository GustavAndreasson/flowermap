<?php
class Plant {
    private $conn;
    private $garden_id;
    private $plant_id;
    private $species_id;
    private $name;
    private $description;

    public function __construct($conn, $garden_id, $data) {
        $this->conn = $conn;
        $this->garden_id = $garden_id;
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->coord_x = $data['coord_x'];
        $this->coord_y = $data['coord_y'];
        
        if (isset($data['plant_id'])) {
            $this->plant_id = $data['plant_id'];
        } else {
            if (isset($data['species_id'])) {
                $this->species_id = $data['species_id'];
            } else {
                try {
                    $sql = "INSERT INTO species (species_id, name) ";
                    $sql .= "VALUES (null, '{$this->name}')";
                    $this->conn->exec($sql);
                    $this->species_id = $this->conn->lastInsertId();
                } catch (PDOException $e) {
                    Util::log("Something went wrong when creating new species: " . $e->getMessage(), true);
                }
            }
            try {
                $now = date("Y-m-d H:i:s");
                $sql = "INSERT INTO plants (plant_id, species_id, garden_id, description, coord_x, coord_y, created_date) ";
                $sql .= "VALUES (null, {$this->species_id}, {$this->garden_id}, '{$this->description}', {$this->coord_x}, {$this->coord_y}, '$now')";
                $this->conn->exec($sql);
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
        return $this->species_id;
    }
    public function get_name() {
        return $this->name;
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

    public function get_image() {
        if (file_exists(PLANT_IMAGE_PATH . $this->plant_id . ".jpg")) {
            return "var/images/plants/" . $this->plant_id . ".jpg";
        } elseif (file_exists(SPECIES_IMAGE_PATH . $this->species_id . ".jpg")) {
            return "var/images/species/" . $this->species_id . ".jpg";
        } else {
            return "";
        }
    }
}