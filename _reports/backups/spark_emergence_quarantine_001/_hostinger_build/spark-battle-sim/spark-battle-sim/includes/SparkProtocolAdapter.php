<?php

declare(strict_types=1);

require_once __DIR__ . '/spark-protocol-autoload.php';

use Spark\Battle\BattleSimulator;
use Spark\Engine\CombatCapability;
use Spark\Model\Power;
use Spark\Model\Sheet;
use Spark\Support\MtRng;
use Spark\Support\SequenceRng;

final class SparkProtocolAdapter
{
    /**
     * Resolve a battle through the deterministic Spark Protocol engine.
     *
     * @param array<string,mixed> $fighterA
     * @param array<string,mixed> $fighterB
     * @param bool $operatorMode
     * @return array<string,mixed>
     */
    public static function resolve(array $fighterA, array $fighterB, bool $operatorMode = false): array
    {
        $sheetA = self::sheetFromCharacter($fighterA);
        $sheetB = self::sheetFromCharacter($fighterB);

        $rng = self::makeRng();
        $report = (new BattleSimulator($rng))->simulate($sheetA, $sheetB);

        $winnerName = $report->winnerName();
        $winner = ($winnerName === $sheetA->maskName()) ? $fighterA : $fighterB;
        $loser = ($winnerName === $sheetA->maskName()) ? $fighterB : $fighterA;

        $safeSummary = sprintf('%s is left standing.', $winnerName);

        $result = [
            'title' => sprintf('%s vs %s', $sheetA->maskName(), $sheetB->maskName()),
            'arena' => [
                'label' => self::arenaLabelFromShowWork($report->showWork()),
                'summary' => self::arenaLabelFromShowWork($report->showWork()),
            ],
            'winner' => [
                'name' => $winnerName,
                'character' => $winner,
            ],
            'loser' => [
                'name' => self::characterName($loser),
                'character' => $loser,
            ],
            'fighter_a' => [
                'name' => $sheetA->maskName(),
                'cc' => $sheetA->combatCapability(),
            ],
            'fighter_b' => [
                'name' => $sheetB->maskName(),
                'cc' => $sheetB->combatCapability(),
            ],
            'summary' => $safeSummary,
            'story' => '<p>' . htmlspecialchars($safeSummary, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</p>',
            'engine' => 'spark-protocol',
            'operator_showwork' => $operatorMode ? $report->showWork() : null,
        ];

        return $result;
    }

    private static function arenaLabelFromShowWork(string $showWork): string
    {
        foreach (preg_split('/\r?\n/', $showWork) as $line) {
            if (strpos($line, 'Arena: ') === 0) {
                return trim(substr($line, strlen('Arena: ')));
            }
        }

        return 'Classified arena conditions';
    }

    /**
     * @param array<string,mixed> $character
     */
    private static function sheetFromCharacter(array $character): Sheet
    {
        $powers = [];

        foreach (self::extractPowers($character) as $power) {
            $name = (string) ($power['name'] ?? $power['power'] ?? '');
            $tier = (int) ($power['tier'] ?? $power['level'] ?? 1);

            if ($name === '') {
                continue;
            }

            $powers[] = new Power($name, max(1, min(5, $tier)));
        }

        if (count($powers) === 0) {
            // Safe fallback keeps shortcode operational if old character data lacks power arrays.
            $powers[] = new Power('Super Strength', 3);
            $powers[] = new Power('Invulnerability', 3);
        }

        $cc = CombatCapability::read($powers)->value();

        return new Sheet(self::characterName($character), $powers, $cc);
    }

    /**
     * @param array<string,mixed> $character
     * @return array<int,array<string,mixed>>
     */
    private static function extractPowers(array $character): array
    {
        foreach (['powers', 'manifested_powers', 'abilities'] as $key) {
            if (isset($character[$key]) && is_array($character[$key])) {
                return array_values(array_filter($character[$key], 'is_array'));
            }
        }

        return [];
    }

    /**
     * @param array<string,mixed> $character
     */
    private static function characterName(array $character): string
    {
        foreach (['name', 'mask_name', 'hero_name', 'title'] as $key) {
            if (!empty($character[$key]) && is_string($character[$key])) {
                return $character[$key];
            }
        }

        return 'Unknown Combatant';
    }

    private static function makeRng()
    {
        $fixed = getenv('SPARK_BATTLE_FIXED_RNG');
        if (is_string($fixed) && trim($fixed) !== '') {
            $seq = array_map('intval', array_filter(array_map('trim', explode(',', $fixed)), 'strlen'));
            return new SequenceRng($seq);
        }

        return new MtRng();
    }
}
