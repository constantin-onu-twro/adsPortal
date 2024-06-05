<?php

class Translation {
    private $translations = [];

    public function __construct() {
        $this->loadTranslations();
    }

    private function loadTranslations() {
        $lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';
        $file = __DIR__ . '/../translations/' . $lang . '.json';
        if (file_exists($file)) {
            $this->translations = json_decode(file_get_contents($file), true);
        }
    }

    public function translate($key) {
        return isset($this->translations[$key]) ? $this->translations[$key] : $key;
    }
}
?>
