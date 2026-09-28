<?php

session_start();
require_once __DIR__ . '/config.php';

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';
$userId = $_SESSION['user_id'] ?? 1;
$body   = getRequestBody();

if ($action === 'list') {
    $type  = $_GET['type'] ?? 'all';
    $sort  = $_GET['sort'] ?? 'match';
    $q     = '%' . trim($_GET['q'] ?? '') . '%';

    $where  = "WHERE (j.title LIKE ? OR j.company LIKE ? OR j.skills_tags LIKE ?)";
    $params = [$q, $q, $q];

    if ($type !== 'all') {
        $where   .= " AND j.job_type = ?";
        $params[] = $type;
    }

    $orderMap = [
        'match'  => 'j.match_pct DESC',
        'salary' => 'j.salary_max DESC',
        'date'   => 'j.posted_at DESC'
    ];
    $orderBy = $orderMap[$sort] ?? 'j.match_pct DESC';

    $stmt = $pdo->prepare("
        SELECT j.*,
               (SELECT COUNT(*) FROM job_applications ja WHERE ja.job_id = j.id AND ja.user_id = ? AND ja.action_type = 'apply') AS is_applied,
               (SELECT COUNT(*) FROM job_applications ja WHERE ja.job_id = j.id AND ja.user_id = ? AND ja.action_type = 'save')  AS is_saved
        FROM job_listings j
        $where
        ORDER BY $orderBy
        LIMIT 20
    ");
    array_unshift($params, $userId, $userId);
    $stmt->execute($params);
    $jobs = $stmt->fetchAll();

    foreach ($jobs as &$job) {
        $job['is_applied'] = (bool)(int)$job['is_applied'];
        $job['is_saved']   = (bool)(int)$job['is_saved'];
        $job['skills']     = array_filter(array_map('trim', explode(',', $job['skills_tags'])));

        
        $posted = strtotime($job['posted_at']);
        $diff   = time() - $posted;
        $job['posted_label'] = $diff < 86400 ? 'Today'
            : ($diff < 172800 ? '1d ago'
            : ($diff < 604800 ? floor($diff / 86400) . 'd ago' : floor($diff / 604800) . 'w ago'));
    }
    unset($job);

    jsonResponse(['status' => 'success', 'data' => $jobs]);
}

if ($action === 'apply') {
    $jobId = (int)($body['job_id'] ?? 0);
    if (!$jobId) jsonError('job_id is required.');

    
    $check = $pdo->prepare("SELECT id FROM job_applications WHERE user_id = ? AND job_id = ? AND action_type = 'apply'");
    $check->execute([$userId, $jobId]);
    if ($check->fetch()) {
        jsonResponse(['status' => 'success', 'message' => 'Already applied']);
    }

    $pdo->prepare("INSERT INTO job_applications (user_id, job_id, action_type) VALUES (?, ?, 'apply')")->execute([$userId, $jobId]);
    $pdo->prepare("INSERT INTO activity_log (user_id, activity_type, title, description, xp_gained) VALUES (?, 'career', 'Applied for Job', 'Submitted verified profile application', 20)")->execute([$userId]);

    jsonResponse(['status' => 'success', 'message' => 'Application submitted successfully']);
}

if ($action === 'save') {
    $jobId = (int)($body['job_id'] ?? 0);
    if (!$jobId) jsonError('job_id is required.');

    $check = $pdo->prepare("SELECT id FROM job_applications WHERE user_id = ? AND job_id = ? AND action_type = 'save'");
    $check->execute([$userId, $jobId]);
    if ($check->fetch()) {
        
        $pdo->prepare("DELETE FROM job_applications WHERE user_id = ? AND job_id = ? AND action_type = 'save'")->execute([$userId, $jobId]);
        jsonResponse(['status' => 'success', 'message' => 'Job unsaved', 'saved' => false]);
    }

    $pdo->prepare("INSERT INTO job_applications (user_id, job_id, action_type) VALUES (?, ?, 'save')")->execute([$userId, $jobId]);
    jsonResponse(['status' => 'success', 'message' => 'Job saved', 'saved' => true]);
}

if ($action === 'saved') {
    $stmt = $pdo->prepare("
        SELECT j.*, ja.applied_at
        FROM job_listings j
        JOIN job_applications ja ON j.id = ja.job_id
        WHERE ja.user_id = ? AND ja.action_type = 'save'
        ORDER BY ja.applied_at DESC
    ");
    $stmt->execute([$userId]);
    jsonResponse(['status' => 'success', 'data' => $stmt->fetchAll()]);
}

if ($action === 'applied') {
    $stmt = $pdo->prepare("
        SELECT j.*, ja.applied_at, ja.status AS app_status
        FROM job_listings j
        JOIN job_applications ja ON j.id = ja.job_id
        WHERE ja.user_id = ? AND ja.action_type = 'apply'
        ORDER BY ja.applied_at DESC
    ");
    $stmt->execute([$userId]);
    jsonResponse(['status' => 'success', 'data' => $stmt->fetchAll()]);
}

if ($action === 'stats') {
    $totalJobs   = $pdo->query("SELECT COUNT(*) FROM job_listings")->fetchColumn();
    $appliedCnt  = $pdo->prepare("SELECT COUNT(*) FROM job_applications WHERE user_id = ? AND action_type = 'apply'");
    $appliedCnt->execute([$userId]);
    $savedCnt = $pdo->prepare("SELECT COUNT(*) FROM job_applications WHERE user_id = ? AND action_type = 'save'");
    $savedCnt->execute([$userId]);

    
    $readiness = $pdo->prepare("SELECT target_readiness_pct FROM progress_stats WHERE user_id = ?");
    $readiness->execute([$userId]);
    $rPct = $readiness->fetchColumn() ?? 78;

    jsonResponse(['status' => 'success', 'data' => [
        'total_jobs'     => (int)$totalJobs,
        'applied_count'  => (int)$appliedCnt->fetchColumn(),
        'saved_count'    => (int)$savedCnt->fetchColumn(),
        'readiness_pct'  => (int)$rPct,
        'high_match_count' => (int)$pdo->query("SELECT COUNT(*) FROM job_listings WHERE match_pct >= 90")->fetchColumn()
    ]]);
}

jsonError('Unknown action: ' . $action);
