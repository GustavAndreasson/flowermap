<?php
class User {
    private $conn;
    private $userId;
    private $language;
    public $garden;

    public function __construct($conn, $userId = null, $name = null, $passHash = null) {
        $this->conn = $conn;
        if ($userId) {
            $this->userId = $userId;
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
        } elseif ($name && $passHash) {
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
            }
        }
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getLanguage() {
        return $this->language;
    }
}
