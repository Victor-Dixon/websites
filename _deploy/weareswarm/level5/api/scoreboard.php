<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

header('Content-Type: application/json');

try {
    $db = level5_db();

    $categories = $db->query(
        "SELECT slug,label,score,status,sort_order FROM level5_categories ORDER BY sort_order ASC"
    )->fetchAll();

    $missions = $db->query(
        "SELECT id,title,category_slug,reward_points,executor_type,status,objective,proof_required
         FROM level5_missions
         WHERE status='available'
         ORDER BY reward_points DESC, id ASC"
    )->fetchAll();

    $completed = $db->query(
        "SELECT id,title,category_slug,reward_points,proof_url,completed_by,completed_at
         FROM level5_completed_missions
         ORDER BY completed_at DESC
         LIMIT 20"
    )->fetchAll();

    $lowest = null;
    foreach ($categories as $category) {
        if ($lowest === null || (int)$category['score'] < (int)$lowest['score']) {
            $lowest = $category;
        }
    }

    echo json_encode([
        'ok' => true,
        'generated_at' => gmdate('c'),
        'current_unlock_target' => $lowest,
        'categories' => $categories,
        'available_missions' => $missions,
        'completed_missions' => $completed,
    ], JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => $e->getMessage(),
    ], JSON_PRETTY_PRINT);
}
