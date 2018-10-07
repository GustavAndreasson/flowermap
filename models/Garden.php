<?php

class Garden {
    private $conn;
    private $gardenId;
    private $name;
    public $plants;
    public $species;

    public function __construct($conn, $gardenId, $userId = null, $name = null) {
        $this->conn = $conn;
        $this->species = array();
        try {
            $sql = "SELECT s.species_id, s.name, s.url, sd.data_name, sd.data_value FROM species s ";
            $sql .= "LEFT JOIN species_data sd ON s.species_id = sd.species_id ";
            $sql .= "ORDER BY s.name";
            $stmt =  $this->conn->prepare($sql);
            $stmt->execute();
            $tmpSpecies = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tmpSpecies[$row['species_id']]['name'] = $row['name'];
                $tmpSpecies[$row['species_id']]['url'] = $row['url'];
                $tmpSpecies[$row['species_id']]['data'] = array();
                if ($row['data_name']) {
                    $tmpSpecies[$row['species_id']]['data'][$row['data_name']] = $row['data_value'];
                }
            }
            foreach ($tmpSpecies as $id => $species) {
                $this->species[$id] = new Species($this->conn, $id, $species['name'], $species['url'], $species['data']);
            }
        } catch (PDOException $e) {
            Util::log("Something went wrong fetching species list for garden: " . $e->getMessage(), true);
        }
        $this->plants = Array();
        if ($gardenId) {
            $this->gardenId = $gardenId;
            try {
                $stmt =  $this->conn->prepare("SELECT name FROM gardens WHERE garden_id = ?");
                $stmt->execute(array($this->gardenId));
                $this->name = $stmt->fetch(PDO::FETCH_ASSOC)['name'];
                $sql = "SELECT p.plant_id, p.species_id, p.description, p.coord_x, p.coord_y FROM plants p ";
                $sql .= "WHERE p.garden_id = ?";
                $stmt =  $this->conn->prepare($sql);
                $stmt->execute(array($this->gardenId));
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $this->plants[$row['plant_id']] = new Plant(
                        $this->conn, $this->gardenId, $row['plant_id'],
                        $row['description'], $row['coord_x'], $row['coord_y'],
                        $this->species[$row['species_id']]);
                }
            } catch (PDOException $e) {
                Util::log("Something went wrong fetching plants for garden: " . $e->getMessage(), true);
            }
        } elseif ($userId) {
            try {
                $sql = "INSERT INTO gardens (garden_id, user_id, name) ";
                $sql .= "VALUES (null, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(array($userId, $name));
                $this->gardenId = $this->conn->lastInsertId();
                $this->name = $name;
                $_SESSION["GARDEN_ID"] = $this->gardenId;
            } catch (PDOException $e) {
                Util::log("Something went wrong when creating new garden: " . $e->getMessage(), true);
            }
        }
    }

    public function getGardenId() {
        return $this->gardenId;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        try {
            $stmt =  $this->conn->prepare("UPDATE gardens set name=?");
            $stmt->execute(array($name));
            $this->name = $name;
        } catch (PDOException $e) {
            Util::log("Something went wrong when updating name of garden: " . $e->getMessage(), true);
        }
    }

    public function getImage() {
        if (file_exists(GARDEN_IMAGE_PATH . $this->getGardenId() . ".svg")) {
            return "var/images/gardens/" . $this->getGardenId() . ".svg";
        } else {
            return "";
        }
    }

    public function addPlant($description, $coordX = 0, $coordY = 0, $species) {
        $plant = new Plant($this->conn, $this->gardenId, null, $description, $coordX, $coordY, $species);
        $this->plants[$plant->getPlantId()] = $plant;
        return $plant;
    }

    public function addSpecies($name, $url = null, $data = null, $img = null) {
        $species = new Species($this->conn, null, $name, $url, $data, $img);
        $this->species[$species->getSpeciesId()] = $species;
        return $species;
    }
}
