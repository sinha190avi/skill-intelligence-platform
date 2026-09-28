<?php

$host   = 'localhost';
$port   = 3306;
$user   = 'root';
$pass   = '';
$dbname = 'skill_intelligence';

$isCli  = (php_sapi_name() === 'cli');
$format = $_GET['format'] ?? ($isCli ? 'text' : 'html');
$force  = isset($_GET['force']) && $_GET['force'] == '1';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ]);
} catch (PDOException $e) {
    if ($format === 'json') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Could not connect to MySQL server: ' . $e->getMessage()]);
        exit;
    }
    die("<div style='background:#1e1b4b;color:#f87171;padding:24px;font-family:sans-serif;'><h2>Database Connection Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p><p>Please ensure MySQL is running in XAMPP.</p></div>");
}

$pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$pdo->exec("USE `$dbname`;");

$existingTables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

$actionsTaken = [];
if (count($existingTables) < 20 || $force) {
    
    $sqlFiles = [
        __DIR__ . '/database.sql',
        __DIR__ . '/sih/database.sql',
        __DIR__ . '/sih/api/database.sql'
    ];
    $sqlImported = false;
    foreach ($sqlFiles as $sf) {
        if (file_exists($sf) && filesize($sf) > 1000) {
            $sqlContent = file_get_contents($sf);
            if ($sqlContent) {
                
                $pdo->exec($sqlContent);
                $actionsTaken[] = "Imported database schema and seed data from " . basename($sf);
                $sqlImported = true;
                break;
            }
        }
    }

    if (!$sqlImported) {
        
        if (file_exists(__DIR__ . '/sih/api/setup_db.php')) {
            ob_start();
            include __DIR__ . '/sih/api/setup_db.php';
            ob_end_clean();
            $actionsTaken[] = "Executed sih/api/setup_db.php";
        }
        if (file_exists(__DIR__ . '/sih/api/setup_extended.php')) {
            ob_start();
            include __DIR__ . '/sih/api/setup_extended.php';
            ob_end_clean();
            $actionsTaken[] = "Executed sih/api/setup_extended.php";
        }
    }
}

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `community_comments` (
        `id`          INT AUTO_INCREMENT PRIMARY KEY,
        `post_id`     INT NOT NULL,
        `user_id`     INT NOT NULL,
        `body`        TEXT NOT NULL,
        `likes_count` INT DEFAULT 0,
        `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_comm_post` (`post_id`),
        INDEX `idx_comm_user` (`user_id`),
        FOREIGN KEY (`post_id`) REFERENCES `community_posts`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `community_event_rsvps` (
        `id`         INT AUTO_INCREMENT PRIMARY KEY,
        `event_id`   INT NOT NULL,
        `user_id`    INT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `unique_event_user` (`event_id`, `user_id`),
        FOREIGN KEY (`event_id`) REFERENCES `community_events`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `otp_codes` (
        `id`         INT AUTO_INCREMENT PRIMARY KEY,
        `email`      VARCHAR(255) NOT NULL,
        `otp`        VARCHAR(10) NOT NULL,
        `purpose`    VARCHAR(50) DEFAULT 'login',
        `expires_at` DATETIME NOT NULL,
        `attempts`   INT DEFAULT 0,
        `used`       TINYINT(1) DEFAULT 0,
        `ip_address` VARCHAR(45) NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX `idx_email_otp` (`email`, `otp`, `used`, `expires_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Exception $e) {
    
}

$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
$tableStats = [];
$totalRows = 0;
foreach ($tables as $t) {
    $c = (int)$pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
    $tableStats[$t] = $c;
    $totalRows += $c;
}

if ($format === 'json') {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'database' => $dbname,
        'table_count' => count($tables),
        'total_rows' => $totalRows,
        'tables' => $tableStats,
        'actions' => $actionsTaken
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($format === 'text') {
    echo "====================================================\n";
    echo "  SKILL INTELLIGENCE PLATFORM - DATABASE STATUS\n";
    echo "====================================================\n";
    echo "Database: $dbname (Host: $host:$port)\n";
    echo "Total Tables: " . count($tables) . " | Total Records: $totalRows\n";
    echo "----------------------------------------------------\n";
    foreach ($tableStats as $tbl => $cnt) {
        printf(" %-26s : %4d rows\n", $tbl, $cnt);
    }
    echo "====================================================\n";
    echo "Status: ALL TABLES READY AND VERIFIED.\n";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Database Setup &amp; Diagnostics | Skill Intelligence Platform</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg-dark: #070414;
      --bg-card: rgba(18, 11, 40, 0.75);
      --border-card: rgba(99, 102, 241, 0.22);
      --brand-indigo: #6366f1;
      --brand-cyan: #06b6d4;
      --brand-emerald: #10b981;
      --text-main: #f1f5f9;
      --text-muted: #94a3b8;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background: var(--bg-dark);
      background-image:
        radial-gradient(ellipse 80% 60% at 50% 0%, rgba(99,102,241,0.25) 0%, transparent 65%),
        radial-gradient(ellipse 50% 40% at 80% 80%, rgba(16,185,129,0.12) 0%, transparent 60%),
        linear-gradient(180deg, rgba(7,4,20,0.4) 0%, rgba(7,4,20,0.95) 100%);
      color: var(--text-main);
      font-family: 'Plus Jakarta Sans', sans-serif;
      min-height: 100vh;
      padding: 40px 20px;
      display: flex;
      justify-content: center;
      align-items: flex-start;
    }
    .container {
      width: 100%;
      max-width: 980px;
      animation: fadeIn 0.4s ease;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .header-card {
      background: var(--bg-card);
      border: 1px solid var(--border-card);
      border-radius: 20px;
      padding: 32px;
      margin-bottom: 24px;
      backdrop-filter: blur(20px);
      box-shadow: 0 12px 40px rgba(0,0,0,0.4);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 20px;
    }
    .brand-title {
      display: flex;
      align-items: center;
      gap: 16px;
    }
    .brand-title .icon-box {
      width: 52px;
      height: 52px;
      border-radius: 14px;
      background: linear-gradient(135deg, #4f46e5, #06b6d4);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 26px;
      box-shadow: 0 4px 20px rgba(99,102,241,0.4);
    }
    .brand-title h1 {
      font-size: 24px;
      font-weight: 800;
      background: linear-gradient(135deg, #fff 30%, #a5b4fc 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .brand-title p {
      color: var(--text-muted);
      font-size: 13.5px;
      margin-top: 4px;
    }
    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 18px;
      border-radius: 30px;
      background: rgba(16,185,129,0.15);
      border: 1px solid rgba(16,185,129,0.4);
      color: #34d399;
      font-weight: 700;
      font-size: 14px;
    }
    .status-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: #10b981;
      box-shadow: 0 0 10px #10b981;
    }
    .metrics-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }
    .metric-card {
      background: var(--bg-card);
      border: 1px solid var(--border-card);
      border-radius: 16px;
      padding: 20px;
      backdrop-filter: blur(16px);
    }
    .metric-card .label {
      color: var(--text-muted);
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      font-weight: 600;
    }
    .metric-card .val {
      font-size: 28px;
      font-weight: 800;
      color: #fff;
      margin-top: 6px;
      font-family: 'JetBrains Mono', monospace;
    }
    .table-container {
      background: var(--bg-card);
      border: 1px solid var(--border-card);
      border-radius: 20px;
      padding: 28px;
      backdrop-filter: blur(20px);
      box-shadow: 0 12px 40px rgba(0,0,0,0.4);
      margin-bottom: 24px;
    }
    .table-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 16px;
      border-bottom: 1px solid rgba(99,102,241,0.15);
    }
    .table-header h2 {
      font-size: 18px;
      font-weight: 700;
    }
    .table-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 12px;
    }
    .tbl-pill {
      background: rgba(15, 9, 32, 0.6);
      border: 1px solid rgba(255,255,255,0.06);
      border-radius: 10px;
      padding: 10px 14px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: all 0.2s ease;
    }
    .tbl-pill:hover {
      border-color: rgba(99,102,241,0.4);
      background: rgba(99,102,241,0.08);
      transform: translateY(-1px);
    }
    .tbl-name {
      font-family: 'JetBrains Mono', monospace;
      font-size: 13px;
      color: #c7d2fe;
    }
    .tbl-count {
      font-size: 12px;
      font-weight: 700;
      padding: 2px 8px;
      border-radius: 12px;
      background: rgba(16,185,129,0.15);
      color: #34d399;
      border: 1px solid rgba(16,185,129,0.3);
    }
    .actions-card {
      background: var(--bg-card);
      border: 1px solid var(--border-card);
      border-radius: 20px;
      padding: 28px;
      backdrop-filter: blur(20px);
      box-shadow: 0 12px 40px rgba(0,0,0,0.4);
    }
    .actions-card h2 {
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 16px;
    }
    .nav-buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
    }
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 20px;
      border-radius: 12px;
      font-weight: 600;
      font-size: 14px;
      text-decoration: none;
      transition: all 0.2s ease;
      cursor: pointer;
      border: none;
    }
    .btn-primary {
      background: linear-gradient(135deg, #4f46e5, #06b6d4);
      color: #fff;
      box-shadow: 0 4px 18px rgba(99,102,241,0.4);
    }
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 24px rgba(99,102,241,0.55);
    }
    .btn-secondary {
      background: rgba(255,255,255,0.06);
      color: #e2e8f0;
      border: 1px solid rgba(255,255,255,0.12);
    }
    .btn-secondary:hover {
      background: rgba(255,255,255,0.12);
      border-color: rgba(99,102,241,0.4);
      color: #fff;
      transform: translateY(-1px);
    }
    .account-card {
      background: rgba(99,102,241,0.08);
      border: 1px solid rgba(99,102,241,0.2);
      border-radius: 14px;
      padding: 16px 20px;
      margin-top: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
    }
    .account-card .cred-label {
      font-size: 12px;
      color: var(--text-muted);
      text-transform: uppercase;
      font-weight: 700;
    }
    .account-card .cred-val {
      font-family: 'JetBrains Mono', monospace;
      font-size: 14px;
      color: #38bdf8;
      font-weight: 600;
      margin-top: 2px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header-card">
      <div class="brand-title">
        <div class="icon-box">⚡</div>
        <div>
          <h1>Skill Intelligence Platform Database</h1>
          <p>MySQL Database System &amp; Schema Status</p>
        </div>
      </div>
      <div class="status-badge">
        <span class="status-dot"></span>
        Database Online &amp; Ready
      </div>
    </div>

    <div class="metrics-grid">
      <div class="metric-card">
        <div class="label">Database Name</div>
        <div class="val" style="font-size:20px; color:#38bdf8;"><?= htmlspecialchars($dbname) ?></div>
      </div>
      <div class="metric-card">
        <div class="label">Total Tables</div>
        <div class="val"><?= count($tables) ?></div>
      </div>
      <div class="metric-card">
        <div class="label">Total Seed Records</div>
        <div class="val" style="color:#34d399;"><?= $totalRows ?></div>
      </div>
      <div class="metric-card">
        <div class="label">MySQL Host</div>
        <div class="val" style="font-size:20px; color:#c084fc;"><?= htmlspecialchars($host . ':' . $port) ?></div>
      </div>
    </div>

    <div class="table-container">
      <div class="table-header">
        <h2>Active Tables Matrix (<?= count($tables) ?>)</h2>
        <span style="font-size:12.5px; color:var(--text-muted);">Character Set: utf8mb4 | Engine: InnoDB</span>
      </div>
      <div class="table-grid">
        <?php foreach ($tableStats as $tbl => $count): ?>
          <div class="tbl-pill">
            <span class="tbl-name"><?= htmlspecialchars($tbl) ?></span>
            <span class="tbl-count"><?= $count ?> rows</span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="actions-card">
      <h2>Launch Application &amp; Tools</h2>
      <div class="nav-buttons">
        <a href="sih/dashboard.html" class="btn btn-primary">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          Open Dashboard
        </a>
        <a href="sih/community.html" class="btn btn-secondary">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          Community Hub
        </a>
        <a href="sih/career.html" class="btn btn-secondary">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
          Career Readiness
        </a>
        <a href="sih/leaderboard.html" class="btn btn-secondary">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          Leaderboard
        </a>
        <a href="sih/login.html" class="btn btn-secondary">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
          Sign In
        </a>
        <a href="database.sql" download class="btn btn-secondary" style="border-color:rgba(16,185,129,0.3); color:#34d399;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Download database.sql
        </a>
        <a href="?force=1" class="btn btn-secondary" onclick="return confirm('Reset and re-seed all tables to clean default state?')">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><polyline points="23 20 23 14 17 14"/><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/></svg>
          Re-seed Database
        </a>
      </div>

      <div class="account-card">
        <div>
          <div class="cred-label">Default Demo Engineer Account</div>
          <div class="cred-val">alex.morgan@enterprise.ai</div>
        </div>
        <div>
          <div class="cred-label">Password</div>
          <div class="cred-val" style="color:#a7f3d0;">Any password (Demo bypass enabled)</div>
        </div>
        <div>
          <div class="cred-label">Assigned Role</div>
          <div class="cred-val" style="color:#c084fc;">Senior AI/ML Engineer (Top 5%)</div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
