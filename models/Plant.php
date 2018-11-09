<?php
class Plant {
    private $conn;
    private $gardenId;
    private $plantId;
    private $description;
    private $coordX;
    private $coordY;
    public $speciesId;

    public function __construct($conn, $gardenId, $plantId, $description, $coordX, $coordY, $speciesId) {
        $this->conn = $conn;
        $this->gardenId = $gardenId;
        $this->plantId = $plantId;
        $this->description = $description;
        $this->coordX = $coordX;
        $this->coordY = $coordY;
        $this->speciesId = $speciesId;

        if (!$plantId) {
            try {
                $now = date("Y-m-d H:i:s");
                $sql = "INSERT INTO plants (plant_id, species_id, garden_id, description, coord_x, coord_y, created_date) ";
                $sql .= "VALUES (null, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(array($this->speciesId, $this->gardenId, $this->description, $this->coordX, $this->coordY, $now));
                $this->plantId = intval($this->conn->lastInsertId());
            } catch (PDOException $e) {
                Util::log("Something went wrong when creating new plant: " . $e->getMessage(), true);
            }
        }
    }

    public function getPlantId() {
        return $this->plantId;
    }
    public function getSpeciesId() {
        return $this->speciesId;
    }
    public function getDescription() {
        return $this->description;
    }
    public function getCoordX() {
        return $this->coordX;
    }
    public function getCoordY() {
        return $this->coordY;
    }
    public function setDescription($description) {
        $this->description = $description;
    }
    public function setCoordX($coordX) {
        $this->coordX = $coordX;
    }
    public function setCoordY($coordY) {
        $this->coordY = $coordY;
    }

    public function getImage() {
        if (file_exists(PLANT_IMAGE_PATH . $this->plantId . ".jpg")) {
            return "var/images/plants/" . $this->plantId . ".jpg";
        } elseif (file_exists(SPECIES_IMAGE_PATH . $this->getSpeciesId() . ".jpg")) {
            return "var/images/species/" . $this->getSpeciesId() . ".jpg";
        } else {
            return "";
        }
    }

    public function save() {
        $sql = "UPDATE plants SET species_id = ?, description = ?, coord_x = ?, coord_y = ? ";
        $sql .= "WHERE plant_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($this->speciesId, $this->description, $this->coordX, $this->coordY, $this->plantId));
    }

    public function delete() {
        $sql = "DELETE FROM plants WHERE plant_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($this->plantId));
    }

    public function getJsonData() {
        $jsonData = array();
        $jsonData['id'] = $this->getPlantId();
        $jsonData['speciesId'] = $this->getSpeciesId();
        $jsonData['description'] = $this->getDescription();
        $jsonData['coordX'] = $this->getCoordX();
        $jsonData['coordY'] = $this->getCoordY();
        $jsonData['image'] = $this->getImage();
        return $jsonData;
    }
}
