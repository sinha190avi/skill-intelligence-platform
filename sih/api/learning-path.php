<?php

require_once __DIR__ . '/config.php';

$userId = $_SESSION['user_id'] ?? 1;
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stgStmt = $pdo->prepare("SELECT * FROM `learning_path_stages` WHERE `user_id` = ? ORDER BY `stage_number` ASC");
    $stgStmt->execute([$userId]);
    $stages = $stgStmt->fetchAll();

    $totalModules = 0;
    $completedModules = 0;
    $totalMinutes = 0;

    foreach ($stages as &$stg) {
        $modStmt = $pdo->prepare("SELECT * FROM `learning_path_modules` WHERE `stage_id` = ? ORDER BY `order_num` ASC");
        $modStmt->execute([$stg['id']]);
        $modules = $modStmt->fetchAll();

        $stageTotal = count($modules);
        $stageCompleted = 0;
        foreach ($modules as $m) {
            $totalModules++;
            $totalMinutes += (int)$m['duration_mins'];
            if ((int)$m['is_completed'] === 1) {
                $completedModules++;
                $stageCompleted++;
            }
        }

        $calcPct = ($stageTotal > 0) ? (int)round(($stageCompleted / $stageTotal) * 100) : 0;
        $stg['progress_pct'] = $calcPct;
        $stg['total_modules'] = $stageTotal;
        $stg['completed_modules'] = $stageCompleted;
        $stg['modules'] = $modules;

        
        if ($calcPct === 100) $stg['status'] = 'completed';
        elseif ($calcPct > 0) $stg['status'] = 'in_progress';
        elseif ($stg['stage_number'] > 2) $stg['status'] = 'locked';
    }

    $overallPct = ($totalModules > 0) ? (int)round(($completedModules / $totalModules) * 100) : 0;
    $hoursInvested = round($totalMinutes / 60, 1);

    jsonResponse([
        'status' => 'success',
        'data' => [
            'overall_journey' => [
                'completion_pct' => $overallPct,
                'completed_modules' => $completedModules,
                'total_modules' => $totalModules,
                'hours_invested' => 74.2
            ],
            'stages' => $stages
        ]
    ]);
} elseif ($method === 'POST') {
    $input = getRequestBody();
    $action = $input['action'] ?? 'toggle_module';

    if ($action === 'toggle_module') {
        $moduleId = (int)($input['module_id'] ?? 0);
        $moduleTitle = trim($input['module_title'] ?? '');

        if (!$moduleId && $moduleTitle) {
            $mLookup = $pdo->prepare("SELECT `id` FROM `learning_path_modules` WHERE `title` LIKE ? LIMIT 1");
            $mLookup->execute(["%$moduleTitle%"]);
            $moduleId = (int)$mLookup->fetchColumn();
        }

        if (!$moduleId) {
            jsonError('Module ID is required');
        }

        $mStmt = $pdo->prepare("SELECT * FROM `learning_path_modules` WHERE `id` = ?");
        $mStmt->execute([$moduleId]);
        $module = $mStmt->fetch();

        if (!$module) {
            jsonError('Module not found', 404);
        }

        $newCompleted = ((int)$module['is_completed'] === 1) ? 0 : 1;
        $pdo->prepare("UPDATE `learning_path_modules` SET `is_completed` = ? WHERE `id` = ?")
            ->execute([$newCompleted, $moduleId]);

        
        $stageId = $module['stage_id'];
        $calcStmt = $pdo->prepare("SELECT COUNT(*) AS total, SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) AS completed FROM `learning_path_modules` WHERE `stage_id` = ?");
        $calcStmt->execute([$stageId]);
        $cRow = $calcStmt->fetch();

        $stagePct = ($cRow['total'] > 0) ? (int)round(($cRow['completed'] / $cRow['total']) * 100) : 0;
        $stageStatus = ($stagePct === 100) ? 'completed' : (($stagePct > 0) ? 'in_progress' : 'locked');

        $pdo->prepare("UPDATE `learning_path_stages` SET `progress_pct` = ?, `status` = ? WHERE `id` = ?")
            ->execute([$stagePct, $stageStatus, $stageId]);

        if ($newCompleted === 1) {
            
            $pdo->prepare("UPDATE `users` SET `karma_xp` = `karma_xp` + 50 WHERE `id` = ?")->execute([$userId]);
            $pdo->prepare("UPDATE `leaderboard_scores` SET `xp_points` = `xp_points` + 50, `xp_weekly` = `xp_weekly` + 50 WHERE `user_id` = ?")->execute([$userId]);
            
            $pdo->prepare("INSERT INTO `activity_log` (`user_id`, `activity_type`, `title`, `description`, `xp_gained`) VALUES (?, 'learning', ?, 'Stage milestone progress', 50)")
                ->execute([$userId, "Completed Module: {$module['title']}"]);
        }

        
        $ovStmt = $pdo->query("SELECT COUNT(*) AS total, SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) AS completed FROM `learning_path_modules`");
        $ov = $ovStmt->fetch();
        $overallPct = ($ov['total'] > 0) ? (int)round(($ov['completed'] / $ov['total']) * 100) : 0;

        jsonResponse([
            'status' => 'success',
            'message' => $newCompleted ? 'Module marked as completed' : 'Module marked as incomplete',
            'data' => [
                'module_id' => $moduleId,
                'is_completed' => $newCompleted,
                'stage_id' => $stageId,
                'stage_progress_pct' => $stagePct,
                'stage_status' => $stageStatus,
                'overall_journey_pct' => $overallPct,
                'completed_modules' => (int)$ov['completed'],
                'total_modules' => (int)$ov['total']
            ]
        ]);
    } else {
        jsonError("Unknown action: $action");
    }
} else {
    jsonError('Method not allowed', 405);
}
