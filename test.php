<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$doc = new DOMDocument("1.0", "UTF-8");
$doc->preserveWhiteSpace = FALSE;
@$doc->loadHTMLFile("http://floralinnea.se/featured/alchymist.html");
echo $doc->getElementsByTagName("h1")->item(0)->textContent . "<br><br>";
$data_table = $doc->getElementById("product-attribute-specs-table")->childNodes->item(2)->childNodes;
foreach ($data_table as $row) {
    foreach ($row->childNodes as $node) {
        if ($node->localName == "th") {
            echo $node->textContent . " - ";
        } elseif ($node->localName == "td") {
            echo $node->textContent . "<br>";
        }
    }
}
echo "<img src='" . $doc->getElementById("image-main")->getAttribute("src") . "'>";

