<?php
$files = ['user.php', 'dashboard.php', 'assessments.php', 'courses.php', 'learning-path.php', 'recommendations.php', 'progress.php', 'notifications.php', 'chat.php', 'skills.php'];
$allOk = true;

foreach ($files as $f) {
    $script = __DIR__ . DIRECTORY_SEPARATOR . $f;
    $env = 'set REQUEST_METHOD=GET&& "C:\\xampp\\php\\php.exe" -f "' . $script . '"';
    $output = shell_exec($env);
    $j = json_decode($output, true);
    if ($j && ($j['status'] ?? '') === 'success') {
        echo "$f: OK\n";
    } else {
        echo "$f: ERR -> " . substr($output, 0, 100) . "\n";
        $allOk = false;
    }
}

echo "\nResult: " . ($allOk ? "ALL ENDPOINTS PASSED" : "SOME FAILED") . "\n";
