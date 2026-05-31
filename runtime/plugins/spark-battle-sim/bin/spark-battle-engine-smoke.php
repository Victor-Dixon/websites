<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/CharacterRepository.php';
require_once __DIR__ . '/../includes/BattleEngine.php';

putenv('SPARK_BATTLE_FIXED_RNG=1,2,3,4,5,87');

$repo = new Spark_Battle_CharacterRepository();

$slugs = [];
foreach (['all', 'list', 'characters', 'getAll', 'allCharacters'] as $method) {
    if (method_exists($repo, $method)) {
        $items = $repo->{$method}();
        if (is_array($items)) {
            foreach ($items as $key => $item) {
                if (is_string($key)) {
                    $slugs[] = $key;
                }
                if (is_array($item)) {
                    foreach (['slug', 'id'] as $field) {
                        if (!empty($item[$field]) && is_string($item[$field])) {
                            $slugs[] = $item[$field];
                        }
                    }
                }
            }
        }
        break;
    }
}

if (count(array_unique($slugs)) < 2) {
    // Fallback to known plugin demo slugs from package history.
    $slugs = ['cap-wilson', 'the-victor'];
}

$slugs = array_values(array_unique($slugs));
$a = $slugs[0];
$b = $slugs[1];

$engine = new Spark_Battle_BattleEngine($repo);
$result = $engine->run($a, $b);

echo "SPARK_BATTLE_ENGINE_REPOSITORY_SMOKE=PASS\n";
echo "SLUG_A={$a}\n";
echo "SLUG_B={$b}\n";
echo "RESULT_ENGINE=" . ($result['engine'] ?? 'missing') . "\n";
echo "WINNER=" . ($result['winner']['name'] ?? 'missing') . "\n";
echo "SUMMARY=" . ($result['summary'] ?? 'missing') . "\n";

if (array_key_exists('operator_showwork', $result) && $result['operator_showwork'] !== null) {
    echo "PLAYER_OUTPUT_SHOWWORK_HIDDEN=FAIL\n";
    exit(4);
}

echo "PLAYER_OUTPUT_SHOWWORK_HIDDEN=PASS\n";
