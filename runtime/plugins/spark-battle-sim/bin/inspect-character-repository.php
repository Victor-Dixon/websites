<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/CharacterRepository.php';

$repo = new Spark_Battle_CharacterRepository();
$all = $repo->all();

echo "REPO_ALL_TYPE=" . gettype($all) . "\n";
echo "REPO_ALL_COUNT=" . (is_array($all) ? count($all) : 0) . "\n";

$i = 0;
foreach ($all as $key => $value) {
    echo "ITEM_KEY={$key}\n";
    echo "ITEM_TYPE=" . gettype($value) . "\n";

    if (is_array($value)) {
        echo "ITEM_KEYS=" . implode(',', array_keys($value)) . "\n";
        echo "ITEM_JSON=" . json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    } else {
        var_dump($value);
    }

    $i++;
    if ($i >= 4) {
        break;
    }
}
