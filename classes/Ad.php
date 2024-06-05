<?php
require_once 'Database.php';

class Ad {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }


    public function createAd($user_id, $title, $description, $location, $tags, $images, $category_id) {
        $this->db->query("INSERT INTO ads (user_id, title, description, location, category_id) VALUES (:user_id, :title, :description, :location, :category_id)");
        $this->db->bind(':user_id', $user_id);
        $this->db->bind(':title', $title);
        $this->db->bind(':description', $description);
        $this->db->bind(':location', $location);
        $this->db->bind(':category_id', $category_id);
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

    private function addTag($ad_id, $tag) {
        // Check if tag already exists
        $this->db->query("SELECT id FROM tags WHERE tag = :tag");
        $this->db->bind(':tag', $tag);
        $tag_id = $this->db->single()['id'] ?? null;

        if (!$tag_id) {
            $this->db->query("INSERT INTO tags (tag) VALUES (:tag)");
            $this->db->bind(':tag', $tag);
            $this->db->execute();
            $tag_id = $this->db->lastInsertId();
        }

        $this->db->query("INSERT INTO ad_tags (ad_id, tag_id) VALUES (:ad_id, :tag_id)");
        $this->db->bind(':ad_id', $ad_id);
        $this->db->bind(':tag_id', $tag_id);
        $this->db->execute();
    }

    private function addImage($ad_id, $image) {
        $this->db->query("INSERT INTO ad_images (ad_id, image) VALUES (:ad_id, :image)");
        $this->db->bind(':ad_id', $ad_id);
        $this->db->bind(':image', $image);
        $this->db->execute();
    }

    public function getAdById($id) {
        $this->db->query("SELECT * FROM ads WHERE id = :id");
        $this->db->bind(':id', $id);
        $ad = $this->db->single();

        $this->db->query("SELECT image FROM ad_images WHERE ad_id = :id");
        $this->db->bind(':id', $id);
        $ad['images'] = $this->db->resultset();

        $this->db->query("SELECT t.tag FROM tags t INNER JOIN ad_tags at ON t.id = at.tag_id WHERE at.ad_id = :id");
        $this->db->bind(':id', $id);
        $ad['tags'] = $this->db->resultset();

        return $ad;
    }
    
    public function updateAd($id, $title, $description, $location, $category, $newImages, $tags, $removeImages, $rotateImages) {
        $this->db->query("UPDATE ads SET title = :title, description = :description, location = :location, category_id = :category WHERE id = :id");
        $this->db->bind(':title', $title);
        $this->db->bind(':description', $description);
        $this->db->bind(':location', $location);
        $this->db->bind(':category', $category);
        $this->db->bind(':id', $id);
        $this->db->execute();

        // Handle new images
        if (!empty($newImages['name'][0])) {
            foreach ($newImages['tmp_name'] as $index => $tmpName) {
                $imageName = uniqid() . '-' . $newImages['name'][$index];
                move_uploaded_file($tmpName, 'uploads/' . $imageName);
                $this->db->query("INSERT INTO ad_images (ad_id, image) VALUES (:ad_id, :image)");
                $this->db->bind(':ad_id', $id);
                $this->db->bind(':image', $imageName);
                $this->db->execute();
            }
        }

        // Handle removed images
        if (!empty($removeImages)) {
            $removeImagesArray = json_decode($removeImages, true);
            foreach ($removeImagesArray as $image) {
                $this->db->query("DELETE FROM ad_images WHERE ad_id = :ad_id AND image = :image");
                $this->db->bind(':ad_id', $id);
                $this->db->bind(':image', $image);
                $this->db->execute();
                unlink('uploads/' . $image);
            }
        }

        // Handle rotated images
        if (!empty($rotateImages)) {
            $rotateImagesArray = json_decode($rotateImages, true);
            foreach ($rotateImagesArray as $image) {
                $filePath = 'uploads/' . $image;
                $source = imagecreatefromjpeg($filePath);
                $rotate = imagerotate($source, 90, 0);
                imagejpeg($rotate, $filePath);
                imagedestroy($source);
                imagedestroy($rotate);
            }
        }

        // Handle tags
        $this->db->query("DELETE FROM ad_tags WHERE ad_id = :ad_id");
        $this->db->bind(':ad_id', $id);
        $this->db->execute();

        $tagsArray = explode(',', $tags);
        foreach ($tagsArray as $tag) {
            $this->db->query("INSERT INTO ad_tags (ad_id, tag) VALUES (:ad_id, :tag)");
            $this->db->bind(':ad_id', $id);
            $this->db->bind(':tag', trim($tag));
            $this->db->execute();
        }
    }
    
    public function searchAds($keyword) {
        $this->db->query("SELECT ads.*, GROUP_CONCAT(ad_tags.tag) as tags FROM ads LEFT JOIN ad_tags ON ads.id = ad_tags.ad_id WHERE ads.title LIKE :keyword OR ads.description LIKE :keyword OR ad_tags.tag LIKE :keyword GROUP BY ads.id");
        $this->db->bind(':keyword', '%' . $keyword . '%');
        return $this->db->resultset();
    }

    public function getAdsByTag($tag) {
        if ($tag) {
            $this->db->query("SELECT ads.*, GROUP_CONCAT(tags.tag SEPARATOR ', ') as tags
                              FROM ads
                              JOIN ad_tags ON ads.id = ad_tags.ad_id
                              JOIN tags ON ad_tags.tag_id = tags.id
                              WHERE tags.tag = :tag
                              GROUP BY ads.id");
            $this->db->bind(':tag', $tag);
        } else {
            $this->db->query("SELECT ads.*, GROUP_CONCAT(tags.tag SEPARATOR ', ') as tags
                              FROM ads
                              JOIN ad_tags ON ads.id = ad_tags.ad_id
                              JOIN tags ON ad_tags.tag_id = tags.id
                              GROUP BY ads.id");
        }
        return $this->db->resultset();
    }

    public function getAllAds() {
        $this->db->query("SELECT ads.*, GROUP_CONCAT(tags.tag) as tags 
                          FROM ads 
                          LEFT JOIN ad_tags ON ads.id = ad_tags.ad_id 
                          LEFT JOIN tags ON ad_tags.tag_id = tags.id 
                          GROUP BY ads.id");
        $result = $this->db->resultset();
        
        foreach ($result as &$ad) {
            $ad['tags'] = array_map('trim', explode(',', $ad['tags']));
        }
        
        return $result;
    }
    
    public function getAds() {
        $this->db->query("SELECT ads.*, categories.name as category_name FROM ads INNER JOIN categories ON ads.category_id = categories.id");
        $ads = $this->db->resultset();
    
        foreach ($ads as &$ad) {
            // Obține imaginile pentru fiecare anunț
            $this->db->query("SELECT image FROM ad_images WHERE ad_id = :ad_id");
            $this->db->bind(':ad_id', $ad['id']);
            $ad['images'] = $this->db->resultset();
    
            // Obține tag-urile pentru fiecare anunț
            $this->db->query("SELECT t.tag FROM tags t INNER JOIN ad_tags at ON t.id = at.tag_id WHERE at.ad_id = :ad_id");
            $this->db->bind(':ad_id', $ad['id']);
            $ad['tags'] = $this->db->resultset();
        }
    
        return $ads;
    }
    
    public function getAdImages($ad_id) {
       $this->db->query("SELECT * FROM ad_images WHERE ad_id = :ad_id");
        $this->db->bind(':ad_id', $ad_id);
    return $this->db->resultset();
    }
    
    public function getCategories() {
        $this->db->query("SELECT * FROM categories");
        return $this->db->resultset();
    }
    
    public function deleteAd($ad_id) {
        $this->db->query('DELETE FROM ads WHERE id = :ad_id');
        $this->db->bind(':ad_id', $ad_id);
        return $this->db->execute();
    }
}
?>
