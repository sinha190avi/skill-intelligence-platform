<?php

session_start();
require_once __DIR__ . '/config.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$userId = $_SESSION['user_id'] ?? 1;
$body   = getRequestBody();

if ($action === 'list') {
    $filter = $_GET['filter'] ?? 'all';

    $where  = 'WHERE uc.user_id = ?';
    $params = [$userId];

    if ($filter === 'earned')   { $where .= " AND uc.status = 'earned'"; }
    if ($filter === 'progress') { $where .= " AND uc.status = 'in_progress'"; }
    if ($filter === 'platform') { $where .= " AND c.cert_type = 'platform'"; }
    if ($filter === 'external') { $where .= " AND c.cert_type = 'external'"; }

    $stmt = $pdo->prepare("
        SELECT uc.*, c.cert_name, c.issuer, c.cert_type, c.cover_color,
               c.icon_emoji, c.xp_reward, c.total_hours, c.description,
               c.verification_url
        FROM user_certifications uc
        JOIN certifications c ON uc.cert_id = c.id
        $where
        ORDER BY uc.earned_at DESC, uc.id DESC
    ");
    $stmt->execute($params);
    $certs = $stmt->fetchAll();

    foreach ($certs as &$cert) {
        if ($cert['status'] === 'earned' && $cert['earned_at']) {
            $cert['earned_label'] = date('M d, Y', strtotime($cert['earned_at']));
        }
        $cert['share_url'] = 'http://localhost/sih/sih/certifications.html?verify=' . ($cert['cert_id']) . '&uid=' . $userId;
    }
    unset($cert);

    jsonResponse(['status' => 'success', 'data' => $certs]);
}

if ($action === 'catalog') {
    $stmt = $pdo->prepare("
        SELECT c.*,
               (SELECT COUNT(*) FROM user_certifications uc WHERE uc.cert_id = c.id AND uc.user_id = ?) AS is_enrolled
        FROM certifications c
        WHERE c.id NOT IN (SELECT cert_id FROM user_certifications WHERE user_id = ?)
        ORDER BY c.match_pct DESC
    ");
    $stmt->execute([$userId, $userId]);
    jsonResponse(['status' => 'success', 'data' => $stmt->fetchAll()]);
}

if ($action === 'start') {
    $certId = (int)($body['cert_id'] ?? 0);
    if (!$certId) jsonError('cert_id is required.');

    
    $check = $pdo->prepare("SELECT id FROM user_certifications WHERE user_id = ? AND cert_id = ?");
    $check->execute([$userId, $certId]);
    if ($check->fetch()) {
        jsonResponse(['status' => 'success', 'message' => 'Already enrolled']);
    }

    
    $certStmt = $pdo->prepare("SELECT * FROM certifications WHERE id = ?");
    $certStmt->execute([$certId]);
    $cert = $certStmt->fetch();
    if (!$cert) jsonError('Certification not found.', 404);

    $pdo->prepare("INSERT INTO user_certifications (user_id, cert_id, status, progress_pct) VALUES (?, ?, 'in_progress', 0)")->execute([$userId, $certId]);
    $pdo->prepare("UPDATE users SET karma_xp = karma_xp + 50 WHERE id = ?")->execute([$userId]);
    $pdo->prepare("INSERT INTO activity_log (user_id, activity_type, title, description, xp_gained) VALUES (?, 'certification', 'Started Certification Path', ?, 50)")->execute([$userId, $cert['cert_name']]);
    $pdo->prepare("INSERT INTO notifications (user_id, title, message, time_ago, type) VALUES (?, 'Cert Path Started!', ?, 'Just now', 'info')")->execute([$userId, 'You enrolled in: ' . $cert['cert_name']]);

    jsonResponse(['status' => 'success', 'message' => 'Enrolled in certification path', 'xp_gained' => 50], 201);
}

if ($action === 'share') {
    $certId = (int)($body['cert_id'] ?? 0);
    if (!$certId) jsonError('cert_id is required.');

    $check = $pdo->prepare("SELECT uc.id, c.cert_name FROM user_certifications uc JOIN certifications c ON uc.cert_id = c.id WHERE uc.user_id = ? AND uc.cert_id = ? AND uc.status = 'earned'");
    $check->execute([$userId, $certId]);
    $row = $check->fetch();
    if (!$row) jsonError('Earned certification not found.', 404);

    $shareUrl = 'http://localhost/sih/sih/certifications.html?verify=' . $certId . '&uid=' . $userId;
    jsonResponse([
        'status'    => 'success',
        'share_url' => $shareUrl,
        'cert_name' => $row['cert_name'],
        'message'   => 'Share link generated'
    ]);
}

if ($action === 'stats') {
    $earned = $pdo->prepare("SELECT COUNT(*) FROM user_certifications WHERE user_id = ? AND status = 'earned'");
    $earned->execute([$userId]);
    $progress = $pdo->prepare("SELECT COUNT(*) FROM user_certifications WHERE user_id = ? AND status = 'in_progress'");
    $progress->execute([$userId]);
    $xpFromCerts = $pdo->prepare("SELECT SUM(c.xp_reward) FROM user_certifications uc JOIN certifications c ON uc.cert_id = c.id WHERE uc.user_id = ? AND uc.status = 'earned'");
    $xpFromCerts->execute([$userId]);

    jsonResponse(['status' => 'success', 'data' => [
        'earned_count'    => (int)$earned->fetchColumn(),
        'progress_count'  => (int)$progress->fetchColumn(),
        'xp_from_certs'   => (int)($xpFromCerts->fetchColumn() ?? 0),
        'cert_rank'       => 'Top 5%'
    ]]);
}

jsonError('Unknown action: ' . $action);
