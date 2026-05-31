<?php
if (!defined('ABSPATH')) {
    exit;
}

class Spark_Battle_StoryRenderer {
    public function render(array $a, array $b, array $winner, array $loser, array $arena): string {
        $a_name = $a['name'];
        $b_name = $b['name'];
        $winner_name = $winner['name'];
        $loser_name = $loser['name'];

        $winner_power = $this->power_line($winner);
        $loser_power = $this->power_line($loser);

        return "{$a_name} and {$b_name} enter {$arena['summary']}.

The first exchange tears the quiet out of the arena. {$a_name} brings {$this->power_line($a)}. {$b_name} answers with {$this->power_line($b)}.

{$loser_name} finds a real opening and presses it hard. The strike lands. The battlefield shifts. For a moment, the fight looks ready to turn.

But {$winner_name} survives the worst of it.

The final exchange is not clean. It is not easy. {$winner_name} leans into {$winner_power}, absorbs the danger, and catches the last mistake before {$loser_name} can reset with {$loser_power}.

When the dust settles, {$winner_name} is the one left standing.";
    }

    private function power_line(array $fighter): string {
        $powers = array();

        foreach (($fighter['powers'] ?? array()) as $power) {
            if (isset($power['name'], $power['tier'])) {
                $powers[] = $power['name'] . ' T' . intval($power['tier']);
            }
        }

        if (!$powers) {
            return 'raw grit and timing';
        }

        return implode(', ', array_slice($powers, 0, 4));
    }
}
