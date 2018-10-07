<?php
class Species {
    private $conn;
    private $speciesId;
    private $name;
    private $data;
    private $url;

    public function __construct($conn, $speciesId, $name = null, $url = null, $data = null, $img = null) {
        $this->conn = $conn;
        $this->speciesId = $speciesId;
        $this->name = $name;
        $this->url = $url;
        $this->data = $data;
        if ($speciesId) {
            if (!$name) {
                $this->data = array();
                try {
                    $sql = "SELECT s.species_id, s.name, s.url, sd.data_name, sd.data_value FROM species s ";
                    $sql .= "JOIN species_data sd ON s.species_id = sd.species_id ";
                    $sql .= "WHERE s.species_id = ?";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute(array($this->speciesId));
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $this->name = $row['name'];
                        $this->url = $row['url'];
                        if ($row['data_name']) {
                            $this->data[$row['data_name']] = $row['data_value'];
                        }
                    }
                } catch (PDOException $e) {
                    Util::log("Something went wrong when fetching species: " . $e->getMessage(), true);
                }
            }
        } else {
            try {
                $stmt = $this->conn->prepare("INSERT INTO species (species_id, name, url) VALUES (null, ?, ?)");
                $stmt->execute(array($this->name, $this->url));
                $this->speciesId = intval($this->conn->lastInsertId());
                if ($this->data) {
                    $args = array();
                    $sql = "INSERT INTO species_data (species_id, data_name, data_value) VALUES ";
                    foreach ($this->data as $dataName => $dataValue) {
                        $sql .= "(?, ?, ?),";
                        array_push($args, $this->speciesId, $dataName, $dataValue);
                    }
                    $sql = substr($sql, 0, -1);
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute($args);
                }
                if ($img) {
                    file_put_contents(SPECIES_IMAGE_PATH . $this->speciesId . ".jpg", file_get_contents($img));
                }
            } catch (PDOException $e) {
                Util::log("Something went wrong when creating new species: " . $e->getMessage(), true);
            }
        }
    }

    public function getSpeciesId() {
        return $this->speciesId;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function getImage() {
        if (file_exists(SPECIES_IMAGE_PATH . $this->getSpeciesId() . ".jpg")) {
            return "var/images/species/" . $this->getSpeciesId() . ".jpg";
        } else {
            return "";
        }
    }

    public function setImage($img) {
        file_put_contents(SPECIES_IMAGE_PATH . $this->speciesId . ".jpg", file_get_contents($img));
    }

    public function save() {
        try {
            $sql = "UPDATE species SET name = ?, url = ? ";
            $sql .= "WHERE species_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(array($this->name, $this->url, $this->speciesId));
            if ($this->data) {
                $args = array();
                $sql = "INSERT INTO speciesData (species_id, data_name, data_value) VALUES ";
                foreach ($this->data as $dataName => $dataValue) {
                    $sql .= "(?, ?, ?),";
                    array_push($args, $this->speciesId, $dataName, $dataValue);
                }
                $sql = substr($sql, 0, -1);
                $sql .= " ON DUPLICATE KEY UPDATE data_value = VALUES (data_value)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($args);
            }
        } catch (PDOException $e) {
            Util::log("Something went wrong when updating species: " . $e->getMessage(), true);
        }
    }

    public function getJsonData() {
        $jsonData = array();
        $jsonData['id'] = $this->speciesId;
        $jsonData['name'] = $this->getName();
        $jsonData['data'] = $this->getData();
        $jsonData['url'] = $this->getUrl();
        $jsonData['image'] = $this->getImage();
        return $jsonData;
    }

    public static function loadUrlData($url) {
        Util::log("Loading data from " . $url);
        $response = Array();
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = FALSE;
        $success = @$doc->loadHTMLFile($url);
        if($success) {
            $response['url'] = $url;
            $response['name'] = $doc->getElementsByTagName("h1")->item(0)->textContent;
            $data = Array();
            $t = $doc->getElementById("product-attribute-specs-table");
            if ($t && $t->childNodes) {
                if ($t->childNodes->item(2)) {
                    $dataTable = $t->childNodes->item(2)->childNodes;
                    foreach ($dataTable as $row) {
                        $dataName = "";
                        $dataValue = "";
                        foreach ($row->childNodes as $node) {
                            if ($node->localName == "th") {
                                $dataName = $node->textContent;
                            } elseif ($node->localName == "td") {
                                $dataValue = $node->textContent;
                            }
                        }
                        if ($dataName && $dataValue) {
                            $data[$dataName] = $dataValue;
                        }
                    }
                }
            }
            $response['data'] = $data;
            $response['image'] = $doc->getElementById("image-main")->getAttribute("src");
            return $response;
        } else {
            return false;
        }
    }

    public static function searchUrl($query) {
        $url = "";
        $qUrl = "https://floralinnea.se/catalogsearch/result/?q=" . urlencode($query);
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = FALSE;
        $success = @$doc->loadHTMLFile($qUrl);
        if ($success) { //Här är något fel
            $result = $doc->getElementsByTagName("h2");
            Util::log("content of h2 " . print_r($result, true));
            if ($result->length > 0) {
                $url = $doc->getElementsByTagName("h2")->item(0)->childNodes->item(0)->getAttribute("href");
            } else {
                Util::log("found no species at " . $qUrl);
            }
        } else {
            Util::log("failed to load url " . $qUrl);
        }
        return $url;
    }
}
