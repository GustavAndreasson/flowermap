<?php

class Garden {
    private $conn;
    private $garden_id;
    private $name;
    public $plants;
    public $species;

    public function __construct($conn, $garden_id, $user_id = null, $name = null) {
        $this->conn = $conn;
        $this->species = Array();
        try {
            $sql = "SELECT s.species_id, s.name, s.url, sd.data_name, sd.data_value FROM species s ";
            $sql .= "LEFT JOIN species_data sd ON s.species_id = sd.species_id ";
            $sql .= "ORDER BY s.name";
            $stmt =  $this->conn->prepare($sql);
            $stmt->execute();
            $tmp_species = Array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tmp_species[$row['species_id']]['name'] = $row['name'];
                $tmp_species[$row['species_id']]['url'] = $row['url'];
                $tmp_species[$row['species_id']]['data'] = array();
                if ($row['data_name']) {
                    $tmp_species[$row['species_id']]['data'][$row['data_name']] = $row['data_value'];
                }
            }
            foreach ($tmp_species as $id => $species) {
                $this->species[$id] = new Species($this->conn, $id, $species['name'], $species['url'], $species['data']);
            }
        } catch (PDOException $e) {
            Util::log("Something went wrong fetching species list for garden: " . $e->getMessage(), true);
        }
        $this->plants = Array();
        if ($garden_id) {
            $this->garden_id = $garden_id;
            try {
                $stmt =  $this->conn->prepare("SELECT name FROM gardens WHERE garden_id = ?");
                $stmt->execute(array($this->garden_id));
                $this->name = $stmt->fetch(PDO::FETCH_ASSOC)['name'];
                $sql = "SELECT p.plant_id, p.species_id, p.description, p.coord_x, p.coord_y FROM plants p ";
                $sql .= "WHERE p.garden_id = ?";
                $stmt =  $this->conn->prepare($sql);
                $stmt->execute(array($this->garden_id));
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $this->plants[$row['plant_id']] = new Plant(
                        $this->conn, $this->garden_id, $row['plant_id'],
                        $row['description'], $row['coord_x'], $row['coord_y'],
                        $this->species[$row['species_id']]);
                }
            } catch (PDOException $e) {
                Util::log("Something went wrong fetching plants for garden: " . $e->getMessage(), true);
            }
        } elseif ($user_id) {
            try {
                $sql = "INSERT INTO gardens (garden_id, user_id, name) ";
                $sql .= "VALUES (null, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(array($user_id, $name));
                $this->garden_id = $this->conn->lastInsertId();
                $this->name = $name;
                $_SESSION["GARDEN_ID"] = $this->garden_id;
            } catch (PDOException $e) {
                Util::log("Something went wrong when creating new garden: " . $e->getMessage(), true);
            }
        }
    }

    public function get_garden_id() {
        return $this->garden_id;
    }

    public function get_name() {
        return $this->name;
    }

    public function set_name($name) {
        try {
            $stmt =  $this->conn->prepare("UPDATE gardens set name=?");
            $stmt->execute(array($name));
            $this->name = $name;
        } catch (PDOException $e) {
            Util::log("Something went wrong when updating name of garden: " . $e->getMessage(), true);
        }
    }

    public function get_image() {
        if (file_exists(GARDEN_IMAGE_PATH . $this->get_garden_id() . ".svg")) {
            return "var/images/gardens/" . $this->get_garden_id() . ".svg";
        } else {
            return "";
        }
    }

    public function add_plant($description, $coord_x = 0, $coord_y = 0, $species) {
        $plant = new Plant($this->conn, $this->garden_id, null, $description, $coord_x, $coord_y, $species);
        $this->plants[$plant->get_plant_id()] = $plant;
        return $plant;
    }

    public function add_species($name, $url = null, $data = null, $img = null) {
        $species = new Species($this->conn, null, $name, $url, $data, $img);
        $this->species[$species->get_species_id()] = $species;
        return $species;
    }
}
