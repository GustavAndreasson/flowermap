<?php
class User {
    private $conn;
    private $userId;
    private $language;
    public $garden;

    public function __construct($conn) {
        $this->conn = $conn;
        if (isset($_SESSION["USER_ID"])) {
            $this->userId = $_SESSION["USER_ID"];
            try {
                $stmt = $this->conn->prepare("SELECT current_garden_id FROM users WHERE user_id = ?");
                $stmt->execute(array($this->userId));
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $this->garden = new Garden($this->conn, $result['current_garden_id'], $this->userId);
                }
            } catch (PDOException $e) {
                Util::log("Something went wrong fetching garden for user: " . $e->getMessage(), true);
            }
        }
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getLanguage() {
        return $this->language;
    }

    public function login($name, $password) {
        try {
            $passHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("SELECT password, user_id FROM users WHERE name = ?");
            $stmt->execute(array($name));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && password_verify($password, $result['password'])) {
                $this->userId = $result['user_id'];
                $_SESSION["USER_ID"] = $this->userId;
                try {
                    $stmt = $this->conn->prepare("SELECT current_garden_id FROM users WHERE user_id = ?");
                    $stmt->execute(array($this->userId));
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($result) {
                        $this->garden = new Garden($this->conn, $result['current_garden_id'], $this->userId);
                    }
                } catch (PDOException $e) {
                    Util::log("Something went wrong fetching garden for user: " . $e->getMessage(), true);
                }
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            Util::log("Something went wrong when logging in: " . $e->getMessage(), true);
            $this->logout();
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
        } catch (PDOException $e) {
            Util::log("Something went wrong when checking username: " . $e->getMessage(), true);
            return false;
        }
        if (!$result) {
            $passHash = password_hash($password, PASSWORD_DEFAULT);
            try {
                $stmt = $this->conn->prepare("INSERT INTO users (user_id, name, password) VALUES (null, ?, ?)");
                $stmt->execute(array($name, $passHash));
                $this->userId = $this->conn->lastInsertId();
                $_SESSION["USER_ID"] = $this->userId;
                $this->garden = new Garden($this->conn, null, $this->userId);
                $gardenId = $this->garden->getGardenId();
                $stmt = $this->conn->prepare("UPDATE users SET current_garden_id = ? where user_id = ?");
                $stmt->execute(array($gardenId, $this->userId));
            } catch (PDOException $e) {
                Util::log("Something went wrong when creating new user: " . $e->getMessage(), true);
                $this->logout();
                return false;
            }
            $_SESSION["USER_ID"] = $this->user->getUserId();
            return true;
        } else {
            return false;
        }
    }

    public function logout() {
        $_SESSION["USER_ID"] = null;
        $this->userId = null;
    }

    public function isLoggedIn() {
        if ($this->userId) {
            return true;
        }
        return false;
    }
}
