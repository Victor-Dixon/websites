<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/CharacterRepository.php';
require_once __DIR__ . '/../includes/BattleEngine.php';

putenv('SPARK_BATTLE_FIXED_RNG=1,2,3,4,5,87');

$repo = new Spark_Battle_CharacterRepository();
$engine = new Spark_Battle_BattleEngine($repo);

try {
    $result = $engine->run('captain-cap-wilson', 'the-victor');
    echo "DIRECT_ENGINE_CALL=PASS\n";
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
} catch (Throwable $e) {
    echo "DIRECT_ENGINE_CALL=FAIL\n";
    echo "EXCEPTION_CLASS=" . get_class($e) . "\n";
    echo "EXCEPTION_MESSAGE=" . $e->getMessage() . "\n";
    echo "EXCEPTION_FILE=" . $e->getFile() . "\n";
    echo "EXCEPTION_LINE=" . $e->getLine() . "\n";
    echo "TRACE_BEGIN\n";
    echo $e->getTraceAsString() . "\n";
    echo "TRACE_END\n";
    exit(7);
}
