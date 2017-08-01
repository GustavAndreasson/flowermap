<?php
class Species {
    private $conn;
    private $species_id;
    private $name;
    private $data;
    private $url;

    public function __construct($conn, $species_id, $name = null, $url = null, $data = null, $img = null) {
        $this->conn = $conn;
        $this->species_id = $species_id;
        $this->name = $name;
        $this->url = $url;
        $this->data = $data;
        if ($species_id) {
            if (!$name) {
                 try {
                     $sql = "SELECT s.species_id, s.name, s.url, sd.data_name, sd.data_value FROM species s ";
                     $sql .= "JOIN species_data sd ON s.species_id = sd.species_id ";
                     $sql .= "WHERE s.species_id = {$this->species_id}";
                     foreach($this->conn->query($sql) as $row) {
                         $this->name = $row['name'];
                         $this->url = $row['url'];
                         $this->data[$row['name']] = $row['value'];
                     }
                 } catch (PDOException $e) {
                     Util::log("Something went wrong when fetching species: " . $e->getMessage(), true);
                 }
            }
        } else {
             try {
                 $sql = "INSERT INTO species (species_id, name, url) ";
                 $sql .= "VALUES (null, '{$this->name}', '{$this->url}')";
                 $this->conn->exec($sql);
                 $this->species_id = $this->conn->lastInsertId();
                 $sql = "INSERT INTO species_data (species_id, data_name, data_value) VALUES ";
                 foreach ($this->data as $data_name => $data_value) {
                     $sql .= "({$this->species_id}, $data_name, '$data_value'),";
                 }
                 $sql = substr($sql, 0, -1);
                 $this->conn->exec($sql);
                 if ($img) {
                     Util::log("saving " . $img . " to disk.");
                     file_put_contents(SPECIES_IMAGE_PATH . $this->species_id . ".jpg", file_get_contents($img));
                 }
             } catch (PDOException $e) {
                 Util::log("Something went wrong when creating new species: " . $e->getMessage(), true);
             }
        }
    }

    public function get_species_id() {
        return $this->species_id;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_url() {
        return $this->url;
    }

    public function get_data() {
        return $this->data;
    }

    public static function load_url_data($url) {
        $response = Array();
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = FALSE;
        @$doc->loadHTMLFile($url);
        $response['name'] = $doc->getElementsByTagName("h1")->item(0)->textContent;
        $data_table = $doc->getElementById("product-attribute-specs-table")->childNodes->item(2)->childNodes;
        $data = Array();
        foreach ($data_table as $row) {
            $data_name = "";
            $data_value = "";
            foreach ($row->childNodes as $node) {
                if ($node->localName == "th") {
                    $data_name = $node->textContent;
                } elseif ($node->localName == "td") {
                    $data_value = $node->textContent;
                }
            }
            if ($data_name && $data_value) {
                $data[$data_name] = $data_value;
            }
        }
        $response['data'] = $data;
        $response['image'] = $doc->getElementById("image-main")->getAttribute("src");        
        //file_put_contents(SPECIES_IMAGE_PATH . $this->species_id . ".jpg", file_get_contents($image_url));
        return $response;
    }
}