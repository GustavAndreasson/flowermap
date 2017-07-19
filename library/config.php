<?php

defined("LIBRARY_PATH")
    or define("LIBRARY_PATH", dirname(__FILE__) . '/');
     
defined("TEMPLATES_PATH")
    or define("TEMPLATES_PATH", dirname(__FILE__) . '/../templates/');

defined("TRANSLATIONS_PATH")
    or define("TRANSLATIONS_PATH", dirname(__FILE__) . '/../translations/');

defined("LOGS_PATH")
    or define("LOGS_PATH", dirname(__FILE__) . '/../var/logs/');

defined("PLANT_IMAGE_PATH")
    or define("PLANT_IMAGE_PATH", dirname(__FILE__) . '/../var/images/plants/');

defined("SPECIES_IMAGE_PATH")
    or define("SPECIES_IMAGE_PATH", dirname(__FILE__) . '/../var/images/species/');

defined("GARDEN_IMAGE_PATH")
    or define("GARDEN_IMAGE_PATH", dirname(__FILE__) . '/../var/images/gardens/');

require_once(LIBRARY_PATH . "Util.php");
require_once(LIBRARY_PATH . "Translate.php");
require_once(LIBRARY_PATH . "FlowerMap.php");
require_once(LIBRARY_PATH . "User.php");
require_once(LIBRARY_PATH . "Garden.php");
require_once(LIBRARY_PATH . "Plant.php");
require_once(LIBRARY_PATH . "Species.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
