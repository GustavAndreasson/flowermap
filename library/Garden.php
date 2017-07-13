<?php

class Garden {
    private $conn;
    private $garden_id;
    public $plants;

    public function __construct($conn, $garden_id, $user_id = null) {
        $this->conn = $conn;
        $this->plants = Array();
        if ($garden_id) {
            $this->garden_id = $garden_id;
            try {
                $sql = "SELECT p.plant_id, p.species_id, s.name, p.description, p.coord_x, p.coord_y FROM plants p ";
                $sql .= "JOIN species s ON p.species_id = s.species_id ";
                $sql .= "WHERE p.garden_id = {$this->garden_id}";
                foreach($this->conn->query($sql) as $row) {
                    $this->plants[$row['plant_id']] = new Plant($this->conn, $this->garden_id, $row);
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

    public function add_plant($name, $description, $coord_x = 0, $coord_y = 0) {
        $data = Array();
        $data['name'] = $name;
        $data['description'] = $description;
        $data['coord_x'] = $coord_x;
        $data['coord_y'] = $coord_y;
        $plant = new Plant($this->conn, $this->garden_id, $data);
        $this->plants[$plant->get_plant_id()] = $plant;
        return $plant->get_plant_id();
    }
}