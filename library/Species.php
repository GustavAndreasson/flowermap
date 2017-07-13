<?php
class Plant {
    private $conn;
    private $species_id;
    private $name;
    private $data;
    private $url;

    public function __construct($conn, $species_id, $name = null, $data = null, $url = null) {
        $this->conn = $conn;
        $this->species_id = $species_id;
        $this->name = $name;
        $this->data = $data;
        $this->url = $url;
        if ($species_id) {
            if (!$name) {
                 try {
                     $sql = "SELECT s.species_id, s.name, s.url, sd.data_name, sd.data_value FROM species s ";
                     $sql .= "JOIN species_data sd ON s.species_id = sd.species_id ";
                     $sql .= "WHERE p.species_id = {$this->species_id}";
                     foreach($this->conn->query($sql) as $row) {
                         $this->name = $row['name'];
                         $this->url = $row['url'];
                         $this->data[$row['data_name']] = $row['data_value']);
                     }
                 } catch (PDOException $e) {
                     Util::log("Something went wrong fetching plants for garden: " . $e->getMessage(), true);
                 }
            }
        } else {
            
        }
    }

    private function load_url_data() {
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = FALSE;
        $doc->loadHTMLFile($url);
        $this->name = $doc->getElementsByTagName("h1")->item(0)->textContent;
        $data_table = $doc->getElementById("product-attribute-specs-table")->childNodes->item(2)->childNodes;
        foreach ($data_table as $row) {
            foreach ($row->childNodes as $node) {
                $data_name = "";
                $data_value = "";
                if ($node->localName == "th") {
                    $data_name = $node->textContent;
                } elseif ($node->localName == "td") {
                    $data_value = $node->textContent;
                }
                if ($data_name && $data_value) {
                    $this->data[$data_name] = $data_value;
                }
            }
        }
        $image_url = $doc->getElementById("image-main")->getAttribute("src");
        file_put_contents(SPECIES_IMAGE_PATH . $this->species_id . ".jpg", file_get_contents($image_url));
    }
}