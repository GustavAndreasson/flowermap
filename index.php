<?php
require_once("library/config.php");
$fm = new FlowerMap();
if ($fm->is_logged_in()) {
    $T = new Translate($fm->user->get_language());
} else {
    $T = new Translate();
}
?>
<!DOCTYPE html>
<html lang="<?= $T->get_language(); ?>">
  <head>
    <title><?= $T->__("Flower Map") ?></title>
    <meta content = "width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name = "viewport" />
    <link rel="stylesheet" type="text/css" href="static/css/stylesheet.css">
    <link rel="icon" type="image/x-icon" href="static/img/favicon.ico">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="static/js/FlowerMap.js"></script>
  </head>
  <body>
    <div class="container">
      <?php include(TEMPLATES_PATH . "header.php"); ?>
      <div class="main">
<?php
    if($fm->is_logged_in()) {
        include(TEMPLATES_PATH . "main.php");
    } else {
        include(TEMPLATES_PATH . "login.php");
    }
?>
      </div>
    </div>
  </body>
</html>
