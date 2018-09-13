<?php
class User {
    private $conn;
    private $user_id;
    private $language;
    public $garden;

    public function __construct($conn, $user_id = null, $name = null, $pass_hash = null) {
        $this->conn = $conn;
        if ($user_id) {
            $this->user_id = $user_id;
            try {
                $stmt = $this->conn->prepare("SELECT current_garden_id FROM users WHERE user_id = ?");
                $stmt->execute(array($this->user_id));
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $this->garden = new Garden($this->conn, $result['current_garden_id'], $this->user_id);
                }
            } catch (PDOException $e) {
                Util::log("Something went wrong fetching garden for user: " . $e->getMessage(), true);
            } 
        } elseif ($name && $pass_hash) {
            try {
                $stmt = $this->conn->prepare("INSERT INTO users (user_id, name, password) VALUES (null, ?, ?)");
                $stmt->execute(array($name, $pass_hash));
                $this->user_id = $this->conn->lastInsertId();
                $_SESSION["USER_ID"] = $this->user_id;
                $this->garden = new Garden($this->conn, null, $this->user_id);
                $garden_id = $this->garden->get_garden_id();
                $stmt = $this->conn->prepare("UPDATE users SET current_garden_id = ? where user_id = ?");
                $stmt->execute(array($garden_id, $this->user_id));
            } catch (PDOException $e) {
                Util::log("Something went wrong when creating new user: " . $e->getMessage(), true);
            }
        }
    }

    public function get_user_id() {
        return $this->user_id;
    }

    public function get_language() {
        return $this->language;
    }
}