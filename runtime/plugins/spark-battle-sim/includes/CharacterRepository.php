<?php
if (!defined('ABSPATH')) {
    exit;
}

class Spark_Battle_CharacterRepository {
    private string $path;

    public function __construct() {
        $this->path = SPARK_BATTLE_SIM_DIR . 'data/characters';
    }

    public function all(): array {
        $characters = array();

        foreach (glob($this->path . '/*.json') as $file) {
            $json = json_decode(file_get_contents($file), true);

            if (is_array($json) && isset($json['slug'], $json['name'])) {
                $characters[$json['slug']] = $json;
            }
        }

        uasort($characters, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return $characters;
    }

    public function get(string $slug): array {
        $file = $this->path . '/' . sanitize_file_name($slug) . '.json';

        if (!file_exists($file)) {
            throw new RuntimeException('Character not found.');
        }

        $character = json_decode(file_get_contents($file), true);

        if (!is_array($character)) {
            throw new RuntimeException('Invalid character file.');
        }

        return $character;
    }
}
