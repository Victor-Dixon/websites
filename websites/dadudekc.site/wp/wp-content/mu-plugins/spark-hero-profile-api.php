<?php
/**
 * Plugin Name: Spark Hero Profile API
 * Description: Hero profile, stats, feats, record, notoriety, and leaderboard endpoints.
 * Version: 0.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

final class Dadudekc_Spark_Hero_Profile_API {
    const REST_NS = 'spark/v1';
    const SPARKS_META = 'spark_saved_dossiers_v1';
    const HERO_META = 'spark_hero_profile_v1';
    const FEATS_META = 'spark_hero_feats_v1';
    const RECORD_META = 'spark_hero_record_v1';
    const NOTORIETY_META = 'spark_hero_notoriety_v1';

    public static function boot(): void {
        add_action('rest_api_init', [__CLASS__, 'routes']);
    }

    public static function routes(): void {
        register_rest_route(self::REST_NS, '/hero', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'hero'],
            'permission_callback' => function () {
                return is_user_logged_in();
            },
        ]);

        register_rest_route(self::REST_NS, '/hero/feat', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'add_feat'],
            'permission_callback' => function () {
                return is_user_logged_in();
            },
        ]);

        register_rest_route(self::REST_NS, '/hero/rename', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'rename_hero'],
            'permission_callback' => function () {
                return is_user_logged_in();
            },
        ]);

        register_rest_route(self::REST_NS, '/leaderboard', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'leaderboard'],
            'permission_callback' => '__return_true',
        ]);
    }

    private static function latest_spark(int $user_id): array {
        $saved = get_user_meta($user_id, self::SPARKS_META, true);
        if (!is_array($saved) || count($saved) < 1) {
            return [];
        }
        return end($saved) ?: [];
    }

    private static function default_record(): array {
        return [
            'wins' => 0,
            'losses' => 0,
            'missions_completed' => 0,
            'dispatch_responses' => 0,
        ];
    }

    private static function default_feats(): array {
        return [
            [
                'title' => 'Origin Awakened',
                'description' => 'Completed the Origin Lab and generated a Spark dossier.',
                'notoriety' => 10,
                'earned_at' => current_time('mysql'),
            ],
        ];
    }

    private static function moves_for(array $spark): array {
        $lead = strtolower((string) ($spark['lead_domain'] ?? 'mind'));
        $manifested = array_map('strtolower', (array) ($spark['manifested'] ?? []));

        $moves = [
            ['name' => 'Spark Guard', 'type' => 'Defense', 'power' => 20, 'description' => 'A baseline defensive stance powered by will.'],
            ['name' => 'Heroic Rebuttal', 'type' => 'Counter', 'power' => 25, 'description' => 'Turns pressure into a clean counteraction.'],
        ];

        $domain_moves = [
            'mind' => ['name' => 'Mind Pierce', 'type' => 'Psychic', 'power' => 42, 'description' => 'Reads intent and strikes the decision point.'],
            'energy' => ['name' => 'Voltage Burst', 'type' => 'Energy', 'power' => 44, 'description' => 'A focused pulse of raw Spark output.'],
            'body' => ['name' => 'Titan Brace', 'type' => 'Body', 'power' => 40, 'description' => 'Absorbs impact and returns force.'],
            'shadow' => ['name' => 'Blindside Step', 'type' => 'Shadow', 'power' => 38, 'description' => 'Vanishes from the obvious angle and reappears with leverage.'],
            'light' => ['name' => 'Reveal Flash', 'type' => 'Light', 'power' => 39, 'description' => 'Exposes hidden weakness and breaks confusion.'],
            'motion' => ['name' => 'Velocity Feint', 'type' => 'Motion', 'power' => 37, 'description' => 'Forces opponents to react to the wrong timing.'],
            'nature' => ['name' => 'Rootbind', 'type' => 'Nature', 'power' => 36, 'description' => 'Anchors the field and limits enemy movement.'],
            'structure' => ['name' => 'Architect Lock', 'type' => 'Structure', 'power' => 41, 'description' => 'Controls the battlefield by locking key variables.'],
        ];

        foreach (array_unique(array_merge([$lead], $manifested)) as $d) {
            if (isset($domain_moves[$d])) {
                $moves[] = $domain_moves[$d];
            }
        }

        return $moves;
    }

    private static function calculate_notoriety(array $spark, array $record, array $feats): int {
        $base = 0;
        $base += intval($spark['power_signature_rating'] ?? 0);
        $base += intval($spark['combat_capability_rating'] ?? 0);
        $base += intval($record['wins'] ?? 0) * 25;
        $base += intval($record['missions_completed'] ?? 0) * 15;
        $base += intval($record['dispatch_responses'] ?? 0) * 8;
        foreach ($feats as $feat) {
            $base += intval($feat['notoriety'] ?? 0);
        }
        return max(0, $base);
    }

    private static function rank_for(int $notoriety): string {
        if ($notoriety >= 1000) return 'Mythic Spark';
        if ($notoriety >= 500) return 'City Icon';
        if ($notoriety >= 250) return 'District Hero';
        if ($notoriety >= 100) return 'Rising Spark';
        return 'Unknown Hero';
    }

    public static function hero(WP_REST_Request $request) {
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);

        $spark = self::latest_spark($user_id);
        $record = get_user_meta($user_id, self::RECORD_META, true);
        $feats = get_user_meta($user_id, self::FEATS_META, true);

        if (!is_array($record)) {
            $record = self::default_record();
            update_user_meta($user_id, self::RECORD_META, $record);
        }

        if (!is_array($feats)) {
            $feats = self::default_feats();
            update_user_meta($user_id, self::FEATS_META, $feats);
        }

        $hero_name = get_user_meta($user_id, 'spark_hero_name', true);
        if (!$hero_name && $user) {
            $hero_name = $user->display_name ?: $user->user_login;
        }

        $payload = isset($spark['payload']) && is_array($spark['payload']) ? $spark['payload'] : $spark;
        $notoriety = self::calculate_notoriety($payload, $record, $feats);
        update_user_meta($user_id, self::NOTORIETY_META, $notoriety);

        return new WP_REST_Response([
            'hero_name' => $hero_name ?: 'Unnamed Spark',
            'rank' => self::rank_for($notoriety),
            'notoriety' => $notoriety,
            'spark' => [
                'title' => $payload['title'] ?? ($spark['title'] ?? 'Unawakened Spark'),
                'lead_domain' => $payload['lead_domain'] ?? ($spark['lead_domain'] ?? 'Unknown'),
                'manifested' => array_values((array) ($payload['manifested'] ?? ($spark['manifested'] ?? []))),
                'power_signature_rating' => intval($payload['power_signature_rating'] ?? 0),
                'combat_capability_rating' => intval($payload['combat_capability_rating'] ?? 0),
            ],
            'moves' => self::moves_for($payload),
            'record' => $record,
            'feats' => array_values($feats),
        ], 200);
    }

    public static function add_feat(WP_REST_Request $request) {
        $user_id = get_current_user_id();
        $params = $request->get_json_params();
        if (!is_array($params)) {
            $params = [];
        }

        $feats = get_user_meta($user_id, self::FEATS_META, true);
        if (!is_array($feats)) {
            $feats = [];
        }

        $feat = [
            'title' => sanitize_text_field((string) ($params['title'] ?? 'Unnamed Feat')),
            'description' => sanitize_text_field((string) ($params['description'] ?? 'A new feat was recorded.')),
            'notoriety' => intval($params['notoriety'] ?? 5),
            'earned_at' => current_time('mysql'),
        ];

        $feats[] = $feat;
        update_user_meta($user_id, self::FEATS_META, $feats);

        return self::hero($request);
    }

    public static function rename_hero(WP_REST_Request $request) {
        $user_id = get_current_user_id();
        $params = $request->get_json_params();
        if (!is_array($params)) {
            $params = [];
        }

        $name = sanitize_text_field((string) ($params['name'] ?? ''));
        if ($name === '') {
            return new WP_REST_Response([
                'code' => 'spark_name_required',
                'message' => 'Spark name is required.',
            ], 400);
        }

        if (strlen($name) > 64) {
            return new WP_REST_Response([
                'code' => 'spark_name_too_long',
                'message' => 'Spark name must be 64 characters or fewer.',
            ], 400);
        }

        $saved = get_user_meta($user_id, self::SPARKS_META, true);
        if (!is_array($saved) || count($saved) < 1) {
            return new WP_REST_Response([
                'code' => 'spark_not_found',
                'message' => 'Save a Spark before renaming.',
            ], 404);
        }

        $idx = count($saved) - 1;
        $saved[$idx]['hero_name'] = $name;
        $saved[$idx]['name'] = $name;
        $saved[$idx]['codename'] = $name;
        $saved[$idx]['spark_name'] = $name;
        $saved[$idx]['renamed_at'] = gmdate('c');

        update_user_meta($user_id, self::SPARKS_META, array_values($saved));
        update_user_meta($user_id, 'spark_hero_name', $name);

        return new WP_REST_Response([
            'renamed' => true,
            'hero_name' => $name,
            'name' => $name,
        ], 200);
    }

    public static function leaderboard(WP_REST_Request $request) {
        $users = get_users(['number' => 100, 'fields' => ['ID', 'display_name', 'user_login']]);
        $rows = [];

        foreach ($users as $user) {
            $spark = self::latest_spark($user->ID);
            if (!$spark) {
                continue;
            }

            $payload = isset($spark['payload']) && is_array($spark['payload']) ? $spark['payload'] : $spark;
            $record = get_user_meta($user->ID, self::RECORD_META, true);
            if (!is_array($record)) {
                $record = self::default_record();
            }
            $feats = get_user_meta($user->ID, self::FEATS_META, true);
            if (!is_array($feats)) {
                $feats = self::default_feats();
            }

            $notoriety = self::calculate_notoriety($payload, $record, $feats);
            $hero_name = get_user_meta($user->ID, 'spark_hero_name', true);
            if (!$hero_name) {
                $hero_name = $user->display_name ?: $user->user_login;
            }

            $rows[] = [
                'hero_name' => $hero_name,
                'rank' => self::rank_for($notoriety),
                'notoriety' => $notoriety,
                'lead_domain' => $payload['lead_domain'] ?? 'Unknown',
                'wins' => intval($record['wins'] ?? 0),
                'missions_completed' => intval($record['missions_completed'] ?? 0),
            ];
        }

        usort($rows, function ($a, $b) {
            return intval($b['notoriety']) <=> intval($a['notoriety']);
        });

        $ranked = [];
        $i = 1;
        foreach ($rows as $row) {
            $row['place'] = $i++;
            $ranked[] = $row;
        }

        return new WP_REST_Response(['leaders' => $ranked], 200);
    }
}

Dadudekc_Spark_Hero_Profile_API::boot();
