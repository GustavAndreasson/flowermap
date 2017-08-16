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
                     $sql .= "WHERE s.species_id = ?";
                     $stmt = $this->conn->prepare($sql);
                     $stmt->execute(array($this->species_id));
                     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
                 $stmt = $this->conn->prepare("INSERT INTO species (species_id, name, url) VALUES (null, ?, ?)");
                 $stmt->execute(array($this->name, $this->url));
                 $this->species_id = $this->conn->lastInsertId();
                 $args = array();
                 $sql = "INSERT INTO species_data (species_id, data_name, data_value) VALUES ";
                 foreach ($this->data as $data_name => $data_value) {
                     $sql .= "(?, ?, ?),";
                     array_push($args, $this->species_id, $data_name, $data_value);
                 }
                 $sql = substr($sql, 0, -1);
                 $stmt = $this->conn->prepare($sql);
                 $stmt->execute($args);
                 if ($img) {
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

    public function get_image() {
        if (file_exists(SPECIES_IMAGE_PATH . $this->get_species_id() . ".jpg")) {
            return "var/images/species/" . $this->get_species_id() . ".jpg";
        } else {
            return "";
        }
    }

    public static function load_url_data($url) {
        $response = Array();
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = FALSE;
        $success = @$doc->loadHTMLFile($url);
        if($success) { 
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
            return $response;
        } else {
            return false;
        }
    }

    public static function search_url($query) {
        $url = "";
        $q_url = "http://floralinnea.se/catalogsearch/result/?q=" . urlencode($query);
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = FALSE;
        $success = @$doc->loadHTMLFile($q_url);
        if ($success) { //Här är något fel
            $result = $doc->getElementsByTagName("h2");
            Util::log("content of h2 " . print_r($result, true));
            if ($result->length > 0) {
                $url = $doc->getElementsByTagName("h2")->item(0)->childNodes->item(0)->getAttribute("href");
            } else {
                Util::log("found no species at " . $q_url);
            }
        } else {
            Util::log("failed to load url " . $q_url);
        }
        return $url;
    }
}