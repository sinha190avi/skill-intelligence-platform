<?php

session_start();
require_once __DIR__ . '/config.php';

$action = $_GET['action'] ?? 'list';
$period = $_GET['period'] ?? 'weekly';
$userId = $_SESSION['user_id'] ?? 1;

if ($action === 'my_rank') {
    $stmt = $pdo->prepare("
        SELECT lr.*, u.full_name, u.role_title, u.avatar_initials, u.karma_xp
        FROM leaderboard_scores lr
        JOIN users u ON lr.user_id = u.id
        WHERE lr.user_id = ?
    ");
    $stmt->execute([$userId]);
    $myRow = $stmt->fetch();

    
    if ($myRow) {
        $rankStmt = $pdo->prepare("SELECT COUNT(*) + 1 AS my_rank FROM leaderboard_scores WHERE xp_points > ?");
        $rankStmt->execute([$myRow['xp_points']]);
        $myRow['rank'] = (int)$rankStmt->fetchColumn();
    }

    jsonResponse(['status' => 'success', 'data' => $myRow]);
}

$orderCol = 'xp_points';
if ($period === 'weekly')    $orderCol = 'xp_weekly';
if ($period === 'monthly')   $orderCol = 'xp_monthly';
if ($period === 'quarterly') $orderCol = 'xp_quarterly';

$stmt = $pdo->query("
    SELECT lr.*, u.full_name, u.role_title, u.avatar_initials
    FROM leaderboard_scores lr
    JOIN users u ON lr.user_id = u.id
    ORDER BY lr.{$orderCol} DESC
    LIMIT 50
");
$rows = $stmt->fetchAll();

foreach ($rows as $i => &$row) {
    $row['rank']  = $i + 1;
    $row['is_you'] = ((int)$row['user_id'] === (int)$userId);
    $row['xp_display'] = $period === 'weekly' ? $row['xp_weekly']
        : ($period === 'monthly' ? $row['xp_monthly']
        : ($period === 'quarterly' ? $row['xp_quarterly'] : $row['xp_points']));
}
unset($row);

jsonResponse([
    'status' => 'success',
    'data' => [
        'period'   => $period,
        'rankings' => $rows,
        'total'    => count($rows)
    ]
]);
