<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$doc = new DOMDocument("1.0", "UTF-8");
$doc->preserveWhiteSpace = FALSE;
@$doc->loadHTMLFile("http://floralinnea.se/catalogsearch/result/?q=körsbär+träd");
echo $doc->saveHTML();
var_dump($doc->getElementsByTagName("h2")[0]);
echo $doc->getElementsByTagName("h2")->item(0)->childNodes->item(0)->getAttribute("href");

