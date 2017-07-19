<?php

class Garden {
    private $conn;
    private $garden_id;
    public $plants;
    public $species;

    public function __construct($conn, $garden_id, $user_id = null) {
        $this->conn = $conn;
        $this->species = Array();
        try {
            $sql = "SELECT s.species_id, s.name, s.url, sd.data_name, sd.data_value FROM species s ";
            $sql .= "LEFT JOIN species_data sd ON s.species_id = sd.species_id";
            $tmp_species = Array();
            foreach ($this->conn->query($sql) as $row) {
                $tmp_species[$row['species_id']]['name'] = $row['name'];
                $tmp_species[$row['species_id']]['url'] = $row['url'];
                $tmp_species[$row['species_id']]['data'][$row['data_name']] = $row['data_value'];
                //Util::log($row['name'] . " " . $row['url'] . " " . $row['data_name'] . " " . $row['data_value']);
            }
            //Util::log(print_r($tmp_species, true));
            foreach ($tmp_species as $id => $species) {
                $this->species[$id] = new Species($this->conn, $id, $species['name'], $species['url'], $species['data']);
            }
            Util::log(print_r($this->species, true));
        } catch (PDOException $e) {
            Util::log("Something went wrong fetching species list for garden: " . $e->getMessage(), true);
        }
        $this->plants = Array();
        if ($garden_id) {
            $this->garden_id = $garden_id;
            try {
                $sql = "SELECT p.plant_id, p.species_id, p.description, p.coord_x, p.coord_y FROM plants p ";
                $sql .= "WHERE p.garden_id = {$this->garden_id}";
                foreach ($this->conn->query($sql) as $row) {
                    $this->plants[$row['plant_id']] = new Plant($this->conn, $this->garden_id, $row['plant_id'],
                                                                $row['description'], $row['coord_x'], $row['coord_y'],
                                                                $this->species[$row['species_id']]);
                }
            } catch (PDOException $e) {
                Util::log("Something went wrong fetching plants for garden: " . $e->getMessage(), true);
            }
        } elseif ($user_id) {
            try {
                $sql = "INSERT INTO gardens (garden_id, user_id) ";
                $sql .= "VALUES (null, $user_id)";
                $this->conn->exec($sql);
                $this->garden_id = $this->conn->lastInsertId();
                $_SESSION["GARDEN_ID"] = $this->garden_id;
            } catch (PDOException $e) {
                Util::log("Something went wrong when creating new garden: " . $e->getMessage(), true);
            }
        }
    }

    public function get_garden_id() {
        return $this->garden_id;
    }

    public function add_plant($description, $coord_x = 0, $coord_y = 0, $species) {
        $plant = new Plant($this->conn, $this->garden_id, null, $description, $coord_x = 0, $coord_y = 0, $species);
        $this->plants[$plant->get_plant_id()] = $plant;
        return $plant;
    }

    public function add_species($name, $url = null, $data = null) {
        $species = new Species($this->conn, null, $name, $url, $data);
        $this->species[$species->get_species_id()] = $species;
        return $species;
    }
}