<?php

require_once __DIR__ . '/config.php';

$userId = $_SESSION['user_id'] ?? 1;
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM `skills` WHERE `user_id` = ? ORDER BY `proficiency_pct` DESC");
    $stmt->execute([$userId]);
    $skills = $stmt->fetchAll();

    jsonResponse([
        'status' => 'success',
        'data' => $skills
    ]);
} elseif ($method === 'POST') {
    $input = getRequestBody();
    $action = $input['action'] ?? 'endorse';

    if ($action === 'endorse') {
        $skillId = (int)($input['skill_id'] ?? 0);
        $skillName = trim($input['skill_name'] ?? '');

        if (!$skillId && $skillName) {
            $sLookup = $pdo->prepare("SELECT `id` FROM `skills` WHERE `name` LIKE ? AND `user_id` = ? LIMIT 1");
            $sLookup->execute(["%$skillName%", $userId]);
            $skillId = (int)$sLookup->fetchColumn();
        }

        if (!$skillId) {
            jsonError('Skill not found');
        }

        $pdo->prepare("UPDATE `skills` SET `endorsements_count` = `endorsements_count` + 1 WHERE `id` = ? AND `user_id` = ?")
            ->execute([$skillId, $userId]);

        $sStmt = $pdo->prepare("SELECT * FROM `skills` WHERE `id` = ?");
        $sStmt->execute([$skillId]);
        $skill = $sStmt->fetch();

        
        $pdo->prepare("UPDATE `users` SET `karma_xp` = `karma_xp` + 10 WHERE `id` = ?")->execute([$userId]);

        
        $pdo->prepare("INSERT INTO `activity_log` (`user_id`, `activity_type`, `title`, `description`, `xp_gained`) VALUES (?, 'endorsement', ?, ?, 10)")
            ->execute([$userId, "Received Endorsement: {$skill['name']}", "Total endorsements: {$skill['endorsements_count']}", 10]);

        jsonResponse([
            'status' => 'success',
            'message' => "Endorsed {$skill['name']}",
            'data' => [
                'skill_id' => $skill['id'],
                'skill_name' => $skill['name'],
                'endorsements_count' => (int)$skill['endorsements_count']
            ]
        ]);
    } else {
        jsonError("Unknown action: $action");
    }
} else {
    jsonError('Method not allowed', 405);
}
