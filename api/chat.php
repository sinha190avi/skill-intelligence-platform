<?php

require_once __DIR__ . '/config.php';

$userId = $_SESSION['user_id'] ?? 1;
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT `id`, `sender`, `message_html`, `created_at` FROM `chat_messages` WHERE `user_id` = ? ORDER BY `id` ASC");
    $stmt->execute([$userId]);
    $messages = $stmt->fetchAll();

    jsonResponse([
        'status' => 'success',
        'data' => $messages
    ]);
} elseif ($method === 'POST') {
    $input = getRequestBody();
    $userPrompt = trim($input['message'] ?? '');

    if (!$userPrompt) {
        jsonError('Message cannot be empty');
    }

    
    $safeUserPrompt = htmlspecialchars($userPrompt, ENT_QUOTES, 'UTF-8');
    $pdo->prepare("INSERT INTO `chat_messages` (`user_id`, `sender`, `message_html`) VALUES (?, 'user', ?)")
        ->execute([$userId, $safeUserPrompt]);

    
    $uStmt = $pdo->prepare("SELECT `full_name`, `target_role`, `karma_xp` FROM `users` WHERE `id` = ?");
    $uStmt->execute([$userId]);
    $user = $uStmt->fetch();

    $stStmt = $pdo->prepare("SELECT `target_readiness_pct`, `mastered_competencies_count` FROM `progress_stats` WHERE `user_id` = ?");
    $stStmt->execute([$userId]);
    $stats = $stStmt->fetch();

    
    $gapStmt = $pdo->prepare("SELECT `name`, `proficiency_pct`, `category` FROM `skills` WHERE `user_id` = ? ORDER BY `proficiency_pct` ASC LIMIT 2");
    $gapStmt->execute([$userId]);
    $weakSkills = $gapStmt->fetchAll();

    $lowestSkill1 = $weakSkills[0]['name'] ?? 'Distributed Training';
    $lowestScore1 = $weakSkills[0]['proficiency_pct'] ?? 70;
    $lowestSkill2 = $weakSkills[1]['name'] ?? 'Enterprise AI Governance';
    $lowestScore2 = $weakSkills[1]['proficiency_pct'] ?? 64;

    $targetRole = $user['target_role'] ?? 'Lead AI Architect';
    $readinessPct = $stats['target_readiness_pct'] ?? 78;

    
    $lower = strtolower($userPrompt);
    $replyHtml = '';

    if (strpos($lower, 'gap') !== false || strpos($lower, 'architect') !== false || strpos($lower, 'readiness') !== false) {
        $replyHtml = "
            <p>📊 <strong>Real-Time Skill Gap Analysis for {$targetRole}:</strong></p>
            <p style=\"margin-top: 6px;\">Your current verified readiness is <strong>{$readinessPct}%</strong>. According to your live database benchmarks, your top 2 priority areas are:</p>
            <ul style=\"margin: 8px 0; padding-left: 20px;\">
              <li><strong>{$lowestSkill2} (Current Proficiency: {$lowestScore2}%):</strong> Focus on safety guardrails, prompt injection mitigation, and audit compliance.</li>
              <li><strong>{$lowestSkill1} (Current Proficiency: {$lowestScore1}%):</strong> DeepSpeed ZeRO-3 memory partitioning and Megatron-LM tensor parallelism.</li>
            </ul>
            <p style=\"margin-top: 6px;\">💡 <em>Action Plan:</em> Completing <a href=\"courses.html\" style=\"font-weight:700; color:var(--primary-500);\">Production Kubernetes for AI Workloads</a> will elevate your readiness by ~+12% to surpass the 90% benchmark threshold.</p>
        ";
    } elseif (strpos($lower, 'flash') !== false || strpos($lower, 'attention') !== false || strpos($lower, 'triton') !== false) {
        $replyHtml = "
            <p>⚡ <strong>FlashAttention-2 &amp; Custom Triton Kernels:</strong></p>
            <p style=\"margin-top: 6px;\">Standard multi-head attention computes $S = QK^T$ and $P = \\text{softmax}(S)$ in slow GPU High-Bandwidth Memory (HBM), resulting in quadratic $O(N^2)$ memory reads and writes.</p>
            <div class=\"code-block\"># FlashAttention-2 Tiling in SRAM with Triton
@triton.jit
def _fwd_kernel(Q, K, V, Out, sm_scale, BLOCK_M: tl.constexpr, BLOCK_N: tl.constexpr):
    # Online softmax accumulator in fast on-chip SRAM
    m_i = tl.zeros([BLOCK_M], dtype=tl.float32) - float(\"inf\")
    l_i = tl.zeros([BLOCK_M], dtype=tl.float32)
    acc = tl.zeros([BLOCK_M, BLOCK_DMODEL], dtype=tl.float32)
    # Block loop over keys &amp; values without HBM spills...</div>
            <p style=\"margin-top: 6px;\"><strong>Key Wins:</strong> 2.5x speedup, 10x memory reduction, and flawless handling of 128k+ token context windows!</p>
        ";
    } elseif (strpos($lower, 'plan') !== false || strpos($lower, 'roadmap') !== false || strpos($lower, '30-day') !== false) {
        $replyHtml = "
            <p>📅 <strong>Accelerated 30-Day Sprint to {$targetRole}:</strong></p>
            <ul style=\"margin: 8px 0; padding-left: 20px;\">
              <li><strong>Week 1 (Days 1-7):</strong> Master PyTorch DDP primitives and NCCL collective communications.</li>
              <li><strong>Week 2 (Days 8-14):</strong> Implement DeepSpeed ZeRO-1, 2, and 3 memory sharding on a 4-GPU cluster.</li>
              <li><strong>Week 3 (Days 15-21):</strong> Deploy vLLM with PagedAttention and FP8 quantization for production serving.</li>
              <li><strong>Week 4 (Days 22-30):</strong> Complete the <em>System Design for LLMs</em> assessment to lock in verified certification.</li>
            </ul>
            <p style=\"margin-top: 6px;\">You can track these directly on your <a href=\"learning-path.html\" style=\"font-weight:700; color:var(--primary-500);\">Learning Path</a>!</p>
        ";
    } elseif (strpos($lower, 'assessment') !== false || strpos($lower, 'test') !== false || strpos($lower, 'quiz') !== false) {
        $replyHtml = "
            <p>📝 <strong>Recommended Next Assessment:</strong></p>
            <p style=\"margin-top: 6px;\">Based on your highest proficiency skills, taking this verified challenge will boost your profile credibility:</p>
            <div style=\"background:var(--bg-card); border:1px solid var(--border-subtle); padding:12px; border-radius:8px; margin:10px 0;\">
              <strong>System Design for Large Language Models</strong>
              <div style=\"font-size:11.5px; color:var(--text-muted); margin-top:2px;\">⏱ 30 Mins • 5 Questions • High Industry Benchmark Weight</div>
              <a href=\"assessments.html\" class=\"btn btn-primary btn-sm\" style=\"margin-top:8px; display:inline-block;\">Launch Assessment Now →</a>
            </div>
        ";
    } else {
        $replyHtml = "
            <p>Hello {$user['full_name']}! In enterprise cognitive architectures, balancing GPU cluster utilization, memory sharding (ZeRO / Megatron), and sub-10ms latency requires end-to-end telemetry from hardware kernels up to orchestration.</p>
            <p style=\"margin-top: 8px;\">You currently have <strong>{$stats['mastered_competencies_count']} mastered competencies</strong> toward <strong>{$targetRole}</strong>. Feel free to ask about architecture diagrams, code implementations, or study recommendations!</p>
        ";
    }

    
    $pdo->prepare("INSERT INTO `chat_messages` (`user_id`, `sender`, `message_html`) VALUES (?, 'assistant', ?)")
        ->execute([$userId, $replyHtml]);

    jsonResponse([
        'status' => 'success',
        'data' => [
            'reply' => $replyHtml,
            'timestamp' => date('H:i')
        ]
    ]);
} else {
    jsonError('Method not allowed', 405);
}
