<?php
class FlowerMap {
    private $conn;
    public $user;

    public function __construct() {
        $servername = "localhost";
        $username = "flowermap";
        $password = "flowermappwd";

        try {
            $this->conn = new PDO("mysql:host=$servername;dbname=flowermap", $username, $password);
            // set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            Util::log("Connection failed: " . $e->getMessage(), true);
            die();
        }

        if (session_status() == PHP_SESSION_NONE) {
            $session_timeout = 86400; // 24 hours
            session_start([
                'cookie_lifetime' => $session_timeout,
                'gc_maxlifetime' => $session_timeout
            ]);
        }

        $this->user = new User($this->conn);
    }

    public function __destruct() {
        $this->conn = null;
    }

    public function isLoggedIn() {
        return $this->user->isLoggedIn();
    }

    public function getMessage() {
        if (isset($_SESSION["MESSAGE"])) {
            $message = $_SESSION["MESSAGE"];
            unset($_SESSION["MESSAGE"]);
            return $message;
        }
    }
}
