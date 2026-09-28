<?php

require_once __DIR__ . '/config.php';

$userId = $_SESSION['user_id'] ?? 1;

$uStmt = $pdo->prepare("SELECT `id`, `full_name`, `email`, `role_title`, `target_role`, `avatar_initials`, `karma_xp`, `rank_percentile` FROM `users` WHERE `id` = ?");
$uStmt->execute([$userId]);
$user = $uStmt->fetch();

$statStmt = $pdo->prepare("SELECT * FROM `progress_stats` WHERE `user_id` = ?");
$statStmt->execute([$userId]);
$stats = $statStmt->fetch() ?: [
    'target_readiness_pct' => 78,
    'mastered_competencies_count' => 24,
    'total_competencies_count' => 30,
    'active_streak_days' => 14,
    'total_hours_invested' => 74.2
];

$skillCountStmt = $pdo->prepare("SELECT 
    COUNT(*) AS total_skills,
    SUM(CASE WHEN proficiency_pct >= 80 THEN 1 ELSE 0 END) AS mastered_skills,
    ROUND(AVG(proficiency_pct)) AS avg_proficiency
    FROM `skills` WHERE `user_id` = ?");
$skillCountStmt->execute([$userId]);
$skillCounts = $skillCountStmt->fetch();

$masteredCount = $skillCounts['mastered_skills'] ? (int)$skillCounts['mastered_skills'] : (int)$stats['mastered_competencies_count'];
$totalSkillsCount = $skillCounts['total_skills'] ? (int)$skillCounts['total_skills'] : (int)$stats['total_competencies_count'];

$stageStmt = $pdo->prepare("SELECT * FROM `learning_path_stages` WHERE `user_id` = ? AND `status` = 'in_progress' ORDER BY `stage_number` ASC LIMIT 1");
$stageStmt->execute([$userId]);
$currentStage = $stageStmt->fetch();

$nextModule = null;
if ($currentStage) {
    $modStmt = $pdo->prepare("SELECT * FROM `learning_path_modules` WHERE `stage_id` = ? AND `is_completed` = 0 ORDER BY `order_num` ASC LIMIT 1");
    $modStmt->execute([$currentStage['id']]);
    $nextModule = $modStmt->fetch();
}

$skillsStmt = $pdo->prepare("SELECT `id`, `category`, `name`, `proficiency_pct`, `level_label`, `benchmark_rank` FROM `skills` WHERE `user_id` = ? ORDER BY `proficiency_pct` DESC LIMIT 6");
$skillsStmt->execute([$userId]);
$competencies = $skillsStmt->fetchAll();

$recCoursesStmt = $pdo->query("SELECT `id`, `title`, `category`, `match_pct`, `duration_hours`, `labs_count`, `rating`, `reviews_count`, `description`, `level_label`, `icon_emoji` FROM `courses` ORDER BY `match_pct` DESC LIMIT 2");
$recommendedCourses = $recCoursesStmt->fetchAll();

$actStmt = $pdo->prepare("SELECT `id`, `activity_type`, `title`, `description`, `xp_gained`, `created_at` FROM `activity_log` WHERE `user_id` = ? ORDER BY `id` DESC LIMIT 6");
$actStmt->execute([$userId]);
$activities = $actStmt->fetchAll();

foreach ($activities as &$act) {
    $time = strtotime($act['created_at']);
    $diff = time() - $time;
    if ($diff < 60) $act['time_ago'] = 'Just now';
    elseif ($diff < 3600) $act['time_ago'] = floor($diff / 60) . ' mins ago';
    elseif ($diff < 86400) $act['time_ago'] = floor($diff / 3600) . ' hrs ago';
    else $act['time_ago'] = floor($diff / 86400) . ' days ago';
}

$notifStmt = $pdo->prepare("SELECT `id`, `title`, `message`, `time_ago`, `is_read`, `type` FROM `notifications` WHERE `user_id` = ? ORDER BY `id` DESC LIMIT 5");
$notifStmt->execute([$userId]);
$notifications = $notifStmt->fetchAll();

$unreadCountStmt = $pdo->prepare("SELECT COUNT(*) FROM `notifications` WHERE `user_id` = ? AND `is_read` = 0");
$unreadCountStmt->execute([$userId]);
$unreadNotifs = (int)$unreadCountStmt->fetchColumn();

jsonResponse([
    'status' => 'success',
    'data' => [
        'user' => $user,
        'kpis' => [
            'readiness_pct' => (int)$stats['target_readiness_pct'],
            'mastered_competencies' => $masteredCount,
            'total_competencies' => $totalSkillsCount,
            'active_streak_days' => (int)$stats['active_streak_days'],
            'total_hours' => (float)$stats['total_hours_invested'],
            'career_milestone' => 'Stage ' . ($currentStage['stage_number'] ?? 2) . ' of 4 • ' . ($user['target_role'] ?? 'Lead AI')
        ],
        'current_milestone' => [
            'stage_number' => $currentStage['stage_number'] ?? 2,
            'stage_title' => $currentStage['title'] ?? 'Stage 2: Deep Learning Internals & Transformer Architectures',
            'progress_pct' => (int)($currentStage['progress_pct'] ?? 65),
            'next_module' => $nextModule ? [
                'title' => $nextModule['title'],
                'subtitle' => $nextModule['subtitle'],
                'duration_mins' => (int)$nextModule['duration_mins']
            ] : null
        ],
        'streak' => [
            'days' => (int)$stats['active_streak_days'],
            'days_grid' => [
                ['day' => 'Mon', 'active' => true],
                ['day' => 'Tue', 'active' => true],
                ['day' => 'Wed', 'active' => true],
                ['day' => 'Thu', 'active' => true],
                ['day' => 'Fri', 'active' => true],
                ['day' => 'Sat', 'active' => true],
                ['day' => 'Sun', 'active' => false]
            ]
        ],
        'competencies' => $competencies,
        'recommended_courses' => $recommendedCourses,
        'recent_activities' => $activities,
        'notifications' => [
            'unread_count' => $unreadNotifs,
            'items' => $notifications
        ]
    ]
]);
