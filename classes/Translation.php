<?php
class Translation {
    private $translations;

    public function __construct($language = LANGUAGE) {
        $this->loadTranslations($language);
    }

    public function loadTranslations($language) {
        $filePath = TRANSLATION_PATH . $language . '.json';
        if (file_exists($filePath)) {
            $jsonContent = file_get_contents($filePath);
            $this->translations = json_decode($jsonContent, true);
        } else {
            $this->translations = [];
        }
    }

    public function translate($key) {
        return isset($this->translations[$key]) ? $this->translations[$key] : $key;
    }
}
?>

