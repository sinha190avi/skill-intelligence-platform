<?php

require_once __DIR__ . '/config.php';

$userId = $_SESSION['user_id'] ?? 1;

$uStmt = $pdo->prepare("SELECT `karma_xp`, `rank_percentile` FROM `users` WHERE `id` = ?");
$uStmt->execute([$userId]);
$user = $uStmt->fetch();

$stStmt = $pdo->prepare("SELECT * FROM `progress_stats` WHERE `user_id` = ?");
$stStmt->execute([$userId]);
$stats = $stStmt->fetch() ?: [
    'total_hours_invested' => 74.2,
    'active_streak_days' => 14,
    'pass_rate_pct' => 94.2,
    'target_readiness_pct' => 78,
    'weekly_hours_json' => ''
];

$passStmt = $pdo->prepare("SELECT 
    COUNT(*) as total_attempts,
    SUM(CASE WHEN status = 'Passed' THEN 1 ELSE 0 END) as passed_attempts
    FROM `assessment_attempts` WHERE `user_id` = ?");
$passStmt->execute([$userId]);
$pRow = $passStmt->fetch();
$totalAtt = (int)$pRow['total_attempts'];
$passedAtt = (int)$pRow['passed_attempts'];
$livePassRate = ($totalAtt > 0) ? round(($passedAtt / $totalAtt) * 100, 1) : 94.2;

$weekly = json_decode($stats['weekly_hours_json'], true) ?: [
    ['day' => 'Mon', 'hours' => 3.5],
    ['day' => 'Tue', 'hours' => 2.0],
    ['day' => 'Wed', 'hours' => 4.0],
    ['day' => 'Thu', 'hours' => 1.5],
    ['day' => 'Fri', 'hours' => 3.5],
    ['day' => 'Sat', 'hours' => 4.0],
    ['day' => 'Sun', 'hours' => 0.0]
];

$weeklyTotalHours = 0;
foreach ($weekly as $w) {
    $weeklyTotalHours += (float)$w['hours'];
}

$histStmt = $pdo->prepare("SELECT `id`, `assessment_title`, `score_pct`, `status`, `benchmark_percentile`, `completed_at` FROM `assessment_attempts` WHERE `user_id` = ? ORDER BY `id` DESC LIMIT 6");
$histStmt->execute([$userId]);
$history = $histStmt->fetchAll();

foreach ($history as &$h) {
    $diff = time() - strtotime($h['completed_at']);
    if ($diff < 60) $h['time_ago'] = 'Just now';
    elseif ($diff < 3600) $h['time_ago'] = floor($diff / 60) . ' mins ago';
    elseif ($diff < 86400) $h['time_ago'] = floor($diff / 3600) . ' hrs ago';
    else $h['time_ago'] = date('M d, Y', strtotime($h['completed_at']));
}

$skillsStmt = $pdo->prepare("SELECT `name`, `proficiency_pct`, `level_label`, `benchmark_rank` FROM `skills` WHERE `user_id` = ? ORDER BY `proficiency_pct` DESC");
$skillsStmt->execute([$userId]);
$skills = $skillsStmt->fetchAll();

jsonResponse([
    'status' => 'success',
    'data' => [
        'kpis' => [
            'total_hours' => (float)$stats['total_hours_invested'],
            'active_streak_days' => (int)$stats['active_streak_days'],
            'pass_rate_pct' => $livePassRate,
            'total_xp' => (int)$user['karma_xp'],
            'benchmark_rank' => $user['rank_percentile']
        ],
        'weekly_study_hours' => [
            'total_this_week' => $weeklyTotalHours,
            'target_goal' => 15.0,
            'days' => $weekly
        ],
        'assessment_history' => $history,
        'competencies' => $skills
    ]
]);
