<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$doc = new DOMDocument("1.0", "UTF-8");
$doc->preserveWhiteSpace = FALSE;
@$doc->loadHTMLFile("http://floralinnea.se/featured/alchymist.html");
echo $doc->getElementsByTagName("h1")->item(0)->textContent . "<br><br>";
$data_table = $doc->getElementById("product-attribute-specs-table")->childNodes->item(2)->childNodes;
$data = Array();
foreach ($data_table as $row) {
    $data_name = "";
    $data_value = "";
    foreach ($row->childNodes as $node) {
        if ($node->localName == "th") {
            $data_name = $node->textContent;
            echo $node->textContent . " - ";
        } elseif ($node->localName == "td") {
            $data_value = $node->textContent;
            echo $node->textContent . "<br>";
        }
    }
    if ($data_name && $data_value) {
        $data[$data_name] = $data_value;
    }
}
var_dump($data);
echo "<img src='" . $doc->getElementById("image-main")->getAttribute("src") . "'>";

echo "<br><br><br>";

include("library/Species.php");

$data = Species::load_url_data("http://floralinnea.se/featured/alchymist.html");

var_dump($data);