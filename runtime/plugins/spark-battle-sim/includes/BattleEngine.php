<?php

declare(strict_types=1);

require_once __DIR__ . '/CharacterRepository.php';
require_once __DIR__ . '/SparkProtocolAdapter.php';

final class Spark_Battle_BattleEngine
{
    /** @var Spark_Battle_CharacterRepository */
    private $repo;

    public function __construct(Spark_Battle_CharacterRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Resolve a battle from existing shortcode slugs through Spark Protocol.
     *
     * @return array<string,mixed>
     */
    public function run(string $fighter_a_slug, string $fighter_b_slug): array
    {
        $fighterA = $this->repo->get($fighter_a_slug);
        $fighterB = $this->repo->get($fighter_b_slug);

        if (!is_array($fighterA)) {
            throw new InvalidArgumentException("Unknown fighter: {$fighter_a_slug}");
        }

        if (!is_array($fighterB)) {
            throw new InvalidArgumentException("Unknown fighter: {$fighter_b_slug}");
        }

        return SparkProtocolAdapter::resolve($fighterA, $fighterB, false);
    }
}
