<?php
require_once 'User.php';

class Admin extends User {
    public function getAllAds() {
        $this->db->query('SELECT * FROM ads');
        return $this->db->resultset();
    }

    public function getAllUsers() {
        $this->db->query('SELECT * FROM users');
        return $this->db->resultset();
    }

    public function getAllConversations() {
        $this->db->query('SELECT * FROM conversations');
        return $this->db->resultset();
    }

    public function deleteAd($ad_id) {
        $this->db->query('DELETE FROM ads WHERE id = :ad_id');
        $this->db->bind(':ad_id', $ad_id);
        return $this->db->execute();
    }

    public function deleteUser($user_id) {
        $this->db->query('DELETE FROM users WHERE id = :user_id');
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }
}
?>
