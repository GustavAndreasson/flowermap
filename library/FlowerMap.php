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

        if (isset($_SESSION["USER_ID"])) {
            $this->user = new User($this->conn, $_SESSION["USER_ID"]);
        }
    }

    public function __destruct() {
        $this->conn = null;
    }

    public function login($name, $password) {
        try {
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("SELECT password, user_id FROM users WHERE name = ?");
            $stmt->execute(array($name));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && password_verify($password, $result['password'])) {
                $this->user = new User($this->conn, $result['user_id']);
                $_SESSION["USER_ID"] = $this->user->get_user_id();
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            Util::log("Something went wrong when logging in: " . $e->getMessage(), true);
            return false;
        }
    }

    public function register($name, $password) {
        try {
            if ($name == "" || $password == "") {
                return false;
            }
            $stmt = $this->conn->prepare("SELECT user_id FROM users WHERE name = ?");
            $stmt->execute(array($name));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                $pass_hash = password_hash($password, PASSWORD_DEFAULT);
                $this->user = new User($this->conn, null, $name, $pass_hash);
                $_SESSION["USER_ID"] = $this->user->get_user_id();
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            Util::log("Something went wrong when registering user: " . $e->getMessage(), true);
            return false;
        }
    }

    public function logout() {
        $_SESSION["USER_ID"] = null;
        $this->user = null;
    }

    public function is_logged_in() {
        if ($this->user) {
            return true;
        } else {
            return false;
        }
    }

    public function get_message() {
        if (isset($_SESSION["MESSAGE"])) {
            $message = $_SESSION["MESSAGE"];
            unset($_SESSION["MESSAGE"]);
            return $message;
        }
    }
}
