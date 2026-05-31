<?php
if (!defined('ABSPATH')) {
    exit;
}

class Spark_Battle_BattleEngine {
    private Spark_Battle_CharacterRepository $repo;

    public function __construct(Spark_Battle_CharacterRepository $repo) {
        $this->repo = $repo;
    }

    public function run(string $fighter_a_slug, string $fighter_b_slug): array {
        $fighter_a = $this->repo->get($fighter_a_slug);
        $fighter_b = $this->repo->get($fighter_b_slug);

        $arena_roller = new Spark_Battle_ArenaRoller();
        $arena = $arena_roller->roll();

        $winner = $this->resolve_winner($fighter_a, $fighter_b);
        $loser = $winner['slug'] === $fighter_a['slug'] ? $fighter_b : $fighter_a;

        $renderer = new Spark_Battle_StoryRenderer();

        return array(
            'title' => $fighter_a['name'] . ' vs. ' . $fighter_b['name'],
            'fighter_a' => $fighter_a,
            'fighter_b' => $fighter_b,
            'arena' => $arena,
            'winner' => $winner,
            'loser' => $loser,
            'story' => $renderer->render($fighter_a, $fighter_b, $winner, $loser, $arena)
        );
    }

    private function resolve_winner(array $fighter_a, array $fighter_b): array {
        $a_score = intval($fighter_a['combat_capability']);
        $b_score = intval($fighter_b['combat_capability']);

        $total = max(1, $a_score + $b_score);
        $a_threshold = intval(round(($a_score / $total) * 100));
        $roll = random_int(1, 100);

        return $roll <= $a_threshold ? $fighter_a : $fighter_b;
    }
}
