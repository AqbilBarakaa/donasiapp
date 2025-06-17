<?php
class User {
    private $conn;
    public function __construct($db) { $this->conn = $db; }
    public function register($username, $email, $password, $full_name) {
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $password, $full_name);
        return $stmt->execute();
    }
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($user = $result->fetch_assoc()) {
            if ($password === $user['password']) {
                return $user;
            }
        }
        return false;
    }
}
?>