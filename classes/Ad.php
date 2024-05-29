<?php
class Ad {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function createAd($user_id, $title, $description, $location, $tags, $images) {
        $this->db->query('INSERT INTO ads (user_id, title, description, location) VALUES (:user_id, :title, :description, :location)');
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':title', $title);
        $this->db->bind(':description', $description);
        $this->db->bind(':location', $location);
        $this->db->execute();

        $ad_id = $this->db->lastInsertId();

        foreach ($tags as $tag) {
            $this->addTag($ad_id, $tag);
        }

        foreach ($images as $image) {
            $this->addImage($ad_id, $image);
        }

        return $ad_id;
    }

    public function addTag($ad_id, $tag) {
        $this->db->query('INSERT INTO ad_tags (ad_id, tag) VALUES (:ad_id, :tag)');
        $this->db->bind(':ad_id', $ad_id);
        $this->db->bind(':tag', $tag);
        return $this->db->execute();
    }

    public function addImage($ad_id, $image) {
        $this->db->query('INSERT INTO ad_images (ad_id, image) VALUES (:ad_id, :image)');
        $this->db->bind(':ad_id', $ad_id);
        $this->db->bind(':image', $image);
        return $this->db->execute();
    }

    public function searchAds($keyword) {
        $this->db->query("SELECT ads.*, GROUP_CONCAT(ad_tags.tag) as tags FROM ads LEFT JOIN ad_tags ON ads.id = ad_tags.ad_id WHERE ads.title LIKE :keyword OR ads.description LIKE :keyword OR ad_tags.tag LIKE :keyword GROUP BY ads.id");
        $this->db->bind(':keyword', '%' . $keyword . '%');
        return $this->db->resultset();
    }

    public function getAdsByTag($tag) {
        $this->db->query("SELECT ads.*, GROUP_CONCAT(ad_tags.tag) as tags FROM ads LEFT JOIN ad_tags ON ads.id = ad_tags.ad_id WHERE ad_tags.tag = :tag GROUP BY ads.id");
        $this->db->bind(':tag', $tag);
        return $this->db->resultset();
    }

    public function getAdById($ad_id) {
        $this->db->query("SELECT ads.*, GROUP_CONCAT(ad_tags.tag) as tags FROM ads LEFT JOIN ad_tags ON ads.id = ad_tags.ad_id WHERE ads.id = :ad_id GROUP BY ads.id");
        $this->db->bind(':ad_id', $ad_id);
        return $this->db->single();
    }

    public function getImages($ad_id) {
        $this->db->query('SELECT * FROM ad_images WHERE ad_id = :ad_id');
        $this->db->bind(':ad_id', $ad_id);
        return $this->db->resultset();
    }
}
?>
