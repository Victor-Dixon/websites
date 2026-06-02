<?php
if (!defined('ABSPATH')) {
    exit;
}

class Spark_Battle_ArenaRoller {
    private array $locations = array(
        'City docks',
        'Abandoned warehouse',
        'Skyscraper rooftop',
        'Active construction site',
        'Countryside farm',
        'Downtown intersection',
        'Subway platform / tunnel',
        'River bridge',
        'Multi-level parking garage',
        'Shopping mall atrium',
        'Power substation',
        'Stadium field',
        'Forest edge / treeline',
        'Industrial refinery',
        'Quarry / gravel pit',
        'Hospital district at night',
        'Train yard',
        'Frozen reservoir',
        'Old town square',
        'Wind farm on open hill'
    );

    private array $times = array(
        'pre-dawn',
        'morning',
        'midday',
        'afternoon',
        'dusk',
        'night'
    );

    private array $weather = array(
        'clear and dry conditions',
        'overcast still air',
        'light rain',
        'heavy rain and storm pressure',
        'dense fog',
        'high wind',
        'snow and sleet',
        'heat haze'
    );

    public function roll(): array {
        $location = $this->locations[random_int(0, count($this->locations) - 1)];
        $time = $this->times[random_int(0, count($this->times) - 1)];
        $weather = $this->weather[random_int(0, count($this->weather) - 1)];

        return array(
            'location' => $location,
            'time' => $time,
            'weather' => $weather,
            'summary' => $location . ', ' . $time . ', under ' . $weather
        );
    }
}
