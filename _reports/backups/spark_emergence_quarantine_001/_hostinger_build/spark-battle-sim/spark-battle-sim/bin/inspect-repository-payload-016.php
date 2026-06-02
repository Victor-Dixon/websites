<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/CharacterRepository.php';

$repo = new Spark_Battle_CharacterRepository();
$all = $repo->all();

echo "ALL_COUNT=" . count($all) . "\n";

foreach ($all as $slug => $character) {
    echo "SLUG={$slug}\n";
    echo "TYPE=" . gettype($character) . "\n";
    if (is_array($character)) {
        echo "KEYS=" . implode(',', array_keys($character)) . "\n";
        echo "JSON=" . json_encode($character, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    }
    echo "---\n";
}
