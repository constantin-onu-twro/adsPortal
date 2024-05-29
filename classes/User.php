<?php
class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function register($username, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $this->db->query("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $this->db->bind(':username', $username);
        $this->db->bind(':email', $email);
        $this->db->bind(':password', $hashedPassword);
        return $this->db->execute();
    }

    public function login($username, $password) {
        $this->db->query("SELECT * FROM users WHERE username = :username");
        $this->db->bind(':username', $username);
        $row = $this->db->single();
        if ($row && password_verify($password, $row['password'])) {
            return $row;
        } else {
            return false;
        }
    }

    public function getUserById($user_id) {
        $this->db->query("SELECT * FROM users WHERE id = :id");
        $this->db->bind(':id', $user_id);
        return $this->db->single();
    }

    public function updateProfile($user_id, $description) {
        $this->db->query('UPDATE users SET description = :description WHERE id = :id');
        $this->db->bind(':description', $description);
        $this->db->bind(':id', $user_id);
        return $this->db->execute();
    }

    public function addProfileImage($user_id, $image) {
        $this->db->query('INSERT INTO user_images (user_id, image) VALUES (:user_id, :image)');
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':image', $image);
        return $this->db->execute();
    }

    public function deleteProfileImage($image_id) {
        $this->db->query('DELETE FROM user_images WHERE id = :id');
        $this->db->bind(':id', $image_id);
        return $this->db->execute();
    }

    public function getProfileImages($user_id) {
        $this->db->query('SELECT * FROM user_images WHERE user_id = :user_id');
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultset();
    }

    public function updateLastActivity($user_id) {
        $this->db->query('UPDATE users SET last_activity = NOW() WHERE id = :id');
        $this->db->bind(':id', $user_id);
        return $this->db->execute();
    }

    public function getOnlineUsers() {
        $this->db->query('SELECT * FROM users WHERE last_activity > DATE_SUB(NOW(), INTERVAL :timeout SECOND)');
        $this->db->bind(':timeout', ONLINE_TIMEOUT);
        return $this->db->resultset();
    }

    public function makeAdmin($user_id) {
        $this->db->query('UPDATE users SET is_admin = 1 WHERE id = :id');
        $this->db->bind(':id', $user_id);
        return $this->db->execute();
    }
}
?>
