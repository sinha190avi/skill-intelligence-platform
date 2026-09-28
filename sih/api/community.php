<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php';

if (php_sapi_name() === 'cli' && isset($argv[1])) {
    parse_str(implode('&', array_slice($argv, 1)), $cliParams);
    $_GET = array_merge($_GET, $cliParams);
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'posts';
$userId = (int)($_SESSION['user_id'] ?? 1);
$body   = getRequestBody();

function timeAgo($ts) {
    if (!$ts) return 'Just now';
    $time = is_numeric($ts) ? (int)$ts : strtotime($ts);
    $diff = time() - $time;
    if ($diff < 45)     return 'Just now';
    if ($diff < 3600)   return max(1, floor($diff/60)) . 'm ago';
    if ($diff < 86400)  return floor($diff/3600) . 'h ago';
    if ($diff < 604800) return floor($diff/86400) . 'd ago';
    return floor($diff/604800) . 'w ago';
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

    
    $commentCount = (int)$pdo->query("SELECT COUNT(*) FROM community_comments")->fetchColumn();
    if ($commentCount === 0) {
        $uMap = [];
        $uRows = $pdo->query("SELECT id, email FROM users LIMIT 20")->fetchAll();
        foreach ($uRows as $r) { $uMap[$r['email']] = $r['id']; }
        $mainUid  = $uMap['alex.morgan@enterprise.ai'] ?? 1;
        $sayanUid = $uMap['sayan.ghosh@deepmind.ai'] ?? 2;
        $riyaUid  = $uMap['riya.kapoor@research.ai'] ?? 3;
        $priyaUid = $uMap['priya.anand@sysai.io'] ?? 5;
        $tanmayUid= $uMap['tanmay.kumar@rlhf.ai'] ?? 8;

        $seedComments = [
            [1, $riyaUid,  "Dynamic batching was game-changing for our vLLM rollout too. Did you have to tune `max_queue_delay_microseconds` for p99 latency SLA?"],
            [1, $priyaUid, "Huge +1. We also set `model_transaction_policy` to decoupled for streaming responses. Saved another 15% latency."],
            [1, $mainUid,  "Great benchmark Sayan! Added this note to our MLOps playbook."],
            [2, $sayanUid, "Completely agree on semantic chunking. Embedding sentences with spacy/stanza before chunking gives huge boundary clarity."],
            [2, $mainUid,  "Have you tried late chunking with jina-embeddings-v3? It preserves context across chunks very effectively."],
            [3, $sayanUid, "Check if your value head loss is dominating. A common fix is reducing value loss coefficient `vf_coef` from 0.5 to 0.1."],
            [3, $priyaUid, "Also verify your learning rate warm-up schedule. Linear warmup for the first 500 steps prevents initial destabilization."],
            [4, $mainUid,  "Can confirm: `prefetch_factor=4` with PyTorch 2.4 pinned memory cut our disk I/O wait times to almost zero."],
            [4, $tanmayUid,"Watch out for memory leaks if dataset yields tensors directly without cloning when using num_workers > 4."],
            [5, $sayanUid, "FlashAttention-2 really pulls away at context length 4k+. Are you using FP16 or BF16? BF16 gave us better numerical stability with FA2."]
        ];

        $insComm = $pdo->prepare("INSERT INTO community_comments (post_id, user_id, body) VALUES (?, ?, ?)");
        foreach ($seedComments as $sc) {
            
            $pExists = $pdo->prepare("SELECT id FROM community_posts WHERE id = ?");
            $pExists->execute([$sc[0]]);
            if ($pExists->fetch()) {
                $insComm->execute($sc);
            }
        }

        
        $pdo->exec("UPDATE community_posts cp SET comments_count = (SELECT COUNT(*) FROM community_comments cc WHERE cc.post_id = cp.id)");
    }
} catch (Exception $e) {
    
}

if ($action === 'posts') {
    $filter = $_GET['filter'] ?? 'hot';
    $tag    = trim($_GET['tag'] ?? '');
    $search = trim($_GET['search'] ?? '');
    $page   = max(1, (int)($_GET['page'] ?? 1));
    $limit  = min(50, max(1, (int)($_GET['limit'] ?? 15)));
    $offset = ($page - 1) * $limit;

    $whereClauses = [];
    $params = [];

    
    if ($tag !== '') {
        $cleanTag = ltrim($tag, '#');
        $whereClauses[] = "(cp.tags LIKE ? OR cp.tags LIKE ? OR cp.body LIKE ?)";
        $params[] = '%' . $cleanTag . '%';
        $params[] = '%#' . $cleanTag . '%';
        $params[] = '%#' . $cleanTag . '%';
    }

    
    if ($search !== '') {
        $whereClauses[] = "(cp.body LIKE ? OR cp.tags LIKE ? OR u.full_name LIKE ?)";
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
    }

    
    if ($filter === 'questions') {
        $whereClauses[] = "(cp.post_type = 'question' OR cp.body LIKE '%?%')";
    } elseif ($filter === 'bookmarked') {
        $whereClauses[] = "EXISTS (SELECT 1 FROM community_reactions cr WHERE cr.post_id = cp.id AND cr.user_id = ? AND cr.reaction_type = 'bookmark')";
        $params[] = $userId;
    } elseif ($filter === 'my_posts') {
        $whereClauses[] = "cp.user_id = ?";
        $params[] = $userId;
    }

    $whereSql = $whereClauses ? ('WHERE ' . implode(' AND ', $whereClauses)) : '';

    
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM community_posts cp JOIN users u ON cp.user_id = u.id $whereSql");
    $countStmt->execute($params);
    $totalCount = (int)$countStmt->fetchColumn();

    
    $orderBy = match($filter) {
        'new'   => 'cp.created_at DESC',
        'top'   => 'cp.likes_count DESC, cp.created_at DESC',
        default => '(cp.likes_count * 2 + cp.comments_count * 3) DESC, cp.created_at DESC'
    };

    $sql = "
        SELECT cp.*,
               u.full_name AS author_name,
               u.role_title AS author_role,
               u.avatar_initials,
               u.karma_xp,
               (SELECT COUNT(*) FROM community_reactions cr WHERE cr.post_id = cp.id AND cr.user_id = ? AND cr.reaction_type = 'like') AS i_liked,
               (SELECT COUNT(*) FROM community_reactions cr WHERE cr.post_id = cp.id AND cr.user_id = ? AND cr.reaction_type = 'bookmark') AS i_bookmarked,
               (SELECT COUNT(*) FROM community_comments cc WHERE cc.post_id = cp.id) AS real_comments_count
        FROM community_posts cp
        JOIN users u ON cp.user_id = u.id
        $whereSql
        ORDER BY cp.is_pinned DESC, $orderBy
        LIMIT ? OFFSET ?
    ";

    $queryParams = array_merge([$userId, $userId], $params, [$limit, $offset]);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($queryParams);
    $posts = $stmt->fetchAll();

    foreach ($posts as &$post) {
        $post['time_ago']       = timeAgo($post['created_at']);
        $post['i_liked']        = (bool)(int)$post['i_liked'];
        $post['i_bookmarked']   = (bool)(int)$post['i_bookmarked'];
        $post['is_you']         = ((int)$post['user_id'] === (int)$userId);
        
        $post['comments_count'] = max((int)$post['comments_count'], (int)$post['real_comments_count']);
        
        $tagsRaw = trim($post['tags'] ?? '');
        $tagsArr = [];
        if ($tagsRaw) {
            foreach (explode(',', $tagsRaw) as $t) {
                $t = trim($t);
                if ($t) {
                    $tagsArr[] = str_starts_with($t, '#') ? $t : ('#' . $t);
                }
            }
        }
        $post['tags_arr'] = $tagsArr;
    }
    unset($post);

    jsonResponse([
        'status'   => 'success',
        'data'     => $posts,
        'page'     => $page,
        'total'    => $totalCount,
        'has_more' => ($offset + count($posts) < $totalCount)
    ]);
}

if ($action === 'create') {
    $postBody  = trim($body['body'] ?? '');
    $tagsInput = trim($body['tags'] ?? '');
    $postType  = $body['post_type'] ?? '';
    $hasCode   = !empty($body['code_block']);
    $codeBlock = trim($body['code_block'] ?? '');

    if (!$postBody) jsonError('Post body cannot be empty.');

    
    $tagsList = [];
    if ($tagsInput) {
        foreach (explode(',', $tagsInput) as $t) {
            $t = trim($t);
            if ($t) $tagsList[] = str_starts_with($t, '#') ? $t : ('#' . $t);
        }
    }
    
    if (preg_match_all('/#([a-zA-Z0-9_\-]+)/', $postBody, $matches)) {
        foreach ($matches[0] as $foundTag) {
            $foundTag = strtolower($foundTag);
            if (!in_array($foundTag, $tagsList)) {
                $tagsList[] = $foundTag;
            }
        }
    }
    $finalTags = implode(',', array_slice($tagsList, 0, 6));

    
    if (!$postType) {
        $postType = (str_contains($postBody, '?') || stripos($postBody, 'how') === 0 || stripos($postBody, 'anyone') === 0 || stripos($postBody, 'struggling') === 0) ? 'question' : 'discussion';
    }

    $stmt = $pdo->prepare("INSERT INTO community_posts (user_id, body, tags, post_type, has_code, code_block, likes_count, comments_count) VALUES (?, ?, ?, ?, ?, ?, 0, 0)");
    $stmt->execute([$userId, $postBody, $finalTags, $postType, $hasCode ? 1 : 0, $hasCode ? $codeBlock : null]);
    $newId = (int)$pdo->lastInsertId();

    
    $pdo->prepare("UPDATE users SET karma_xp = karma_xp + 10 WHERE id = ?")->execute([$userId]);
    $pdo->prepare("INSERT INTO activity_log (user_id, activity_type, title, description, xp_gained) VALUES (?, 'community', 'Posted in Community Hub', ?, 10)")->execute([$userId, substr($postBody, 0, 80)]);

    
    $uStmt = $pdo->prepare("SELECT full_name, role_title, avatar_initials, karma_xp FROM users WHERE id = ?");
    $uStmt->execute([$userId]);
    $u = $uStmt->fetch();

    $newPost = [
        'id'              => $newId,
        'user_id'         => $userId,
        'body'            => $postBody,
        'tags'            => $finalTags,
        'tags_arr'        => $tagsList,
        'post_type'       => $postType,
        'has_code'        => $hasCode ? 1 : 0,
        'code_block'      => $hasCode ? $codeBlock : null,
        'likes_count'     => 0,
        'comments_count'  => 0,
        'is_pinned'       => 0,
        'created_at'      => date('Y-m-d H:i:s'),
        'time_ago'        => 'Just now',
        'author_name'     => $u['full_name'] ?? 'Alex Morgan',
        'author_role'     => $u['role_title'] ?? 'AI Engineer',
        'avatar_initials' => $u['avatar_initials'] ?? 'AM',
        'karma_xp'        => (int)($u['karma_xp'] ?? 0),
        'i_liked'         => false,
        'i_bookmarked'    => false,
        'is_you'          => true
    ];

    jsonResponse(['status' => 'success', 'message' => 'Post published! +10 XP gained', 'data' => $newPost], 201);
}

if ($action === 'delete') {
    $postId = (int)($body['post_id'] ?? $_GET['post_id'] ?? 0);
    if (!$postId) jsonError('post_id is required.');

    $check = $pdo->prepare("SELECT user_id FROM community_posts WHERE id = ?");
    $check->execute([$postId]);
    $post = $check->fetch();
    if (!$post) jsonError('Post not found.', 404);

    if ((int)$post['user_id'] !== (int)$userId && $userId !== 1) {
        jsonError('You do not have permission to delete this post.', 403);
    }

    $pdo->prepare("DELETE FROM community_posts WHERE id = ?")->execute([$postId]);
    jsonResponse(['status' => 'success', 'message' => 'Post deleted successfully.']);
}

if ($action === 'react') {
    $postId = (int)($body['post_id'] ?? 0);
    $type   = $body['type'] ?? 'like'; 

    if (!$postId) jsonError('post_id is required.');

    $check = $pdo->prepare("SELECT id FROM community_reactions WHERE post_id = ? AND user_id = ? AND reaction_type = ?");
    $check->execute([$postId, $userId, $type]);

    if ($check->fetch()) {
        
        $pdo->prepare("DELETE FROM community_reactions WHERE post_id = ? AND user_id = ? AND reaction_type = ?")->execute([$postId, $userId, $type]);
        $likesCount = (int)$pdo->prepare("SELECT COUNT(*) FROM community_reactions WHERE post_id = ? AND reaction_type = 'like'");
        $cntStmt = $pdo->prepare("SELECT COUNT(*) FROM community_reactions WHERE post_id = ? AND reaction_type = 'like'");
        $cntStmt->execute([$postId]);
        $newLikes = (int)$cntStmt->fetchColumn();
        $pdo->prepare("UPDATE community_posts SET likes_count = ? WHERE id = ?")->execute([$newLikes, $postId]);

        jsonResponse(['status' => 'success', 'reacted' => false, 'likes_count' => $newLikes]);
    }

    
    $pdo->prepare("INSERT INTO community_reactions (post_id, user_id, reaction_type) VALUES (?, ?, ?)")->execute([$postId, $userId, $type]);

    $cntStmt = $pdo->prepare("SELECT COUNT(*) FROM community_reactions WHERE post_id = ? AND reaction_type = 'like'");
    $cntStmt->execute([$postId]);
    $newLikes = (int)$cntStmt->fetchColumn();
    $pdo->prepare("UPDATE community_posts SET likes_count = ? WHERE id = ?")->execute([$newLikes, $postId]);

    if ($type === 'like') {
        
        $authorStmt = $pdo->prepare("SELECT user_id FROM community_posts WHERE id = ?");
        $authorStmt->execute([$postId]);
        $authorId = $authorStmt->fetchColumn();
        if ($authorId && (int)$authorId !== (int)$userId) {
            $pdo->prepare("UPDATE users SET karma_xp = karma_xp + 2 WHERE id = ?")->execute([$authorId]);
        }
    }

    jsonResponse(['status' => 'success', 'reacted' => true, 'likes_count' => $newLikes]);
}

if ($action === 'comments') {
    $postId = (int)($_GET['post_id'] ?? 0);
    if (!$postId) jsonError('post_id is required.');

    $stmt = $pdo->prepare("
        SELECT cc.*,
               u.full_name AS author_name,
               u.role_title AS author_role,
               u.avatar_initials,
               u.karma_xp
        FROM community_comments cc
        JOIN users u ON cc.user_id = u.id
        WHERE cc.post_id = ?
        ORDER BY cc.created_at ASC
    ");
    $stmt->execute([$postId]);
    $comments = $stmt->fetchAll();

    foreach ($comments as &$c) {
        $c['time_ago'] = timeAgo($c['created_at']);
        $c['is_you']   = ((int)$c['user_id'] === (int)$userId);
    }
    unset($c);

    jsonResponse(['status' => 'success', 'data' => $comments]);
}

if ($action === 'comment') {
    $postId      = (int)($body['post_id'] ?? 0);
    $commentBody = trim($body['body'] ?? '');

    if (!$postId) jsonError('post_id is required.');
    if (!$commentBody) jsonError('Comment cannot be empty.');

    $stmt = $pdo->prepare("INSERT INTO community_comments (post_id, user_id, body) VALUES (?, ?, ?)");
    $stmt->execute([$postId, $userId, $commentBody]);
    $newCommentId = (int)$pdo->lastInsertId();

    
    $cStmt = $pdo->prepare("SELECT COUNT(*) FROM community_comments WHERE post_id = ?");
    $cStmt->execute([$postId]);
    $totalComments = (int)$cStmt->fetchColumn();
    $pdo->prepare("UPDATE community_posts SET comments_count = ? WHERE id = ?")->execute([$totalComments, $postId]);

    
    $pdo->prepare("UPDATE users SET karma_xp = karma_xp + 5 WHERE id = ?")->execute([$userId]);
    $pdo->prepare("INSERT INTO activity_log (user_id, activity_type, title, description, xp_gained) VALUES (?, 'community', 'Replied in Community Hub', ?, 5)")->execute([$userId, substr($commentBody, 0, 80)]);

    
    $uStmt = $pdo->prepare("SELECT full_name, role_title, avatar_initials, karma_xp FROM users WHERE id = ?");
    $uStmt->execute([$userId]);
    $u = $uStmt->fetch();

    $newComment = [
        'id'              => $newCommentId,
        'post_id'         => $postId,
        'user_id'         => $userId,
        'body'            => $commentBody,
        'created_at'      => date('Y-m-d H:i:s'),
        'time_ago'        => 'Just now',
        'author_name'     => $u['full_name'] ?? 'Alex Morgan',
        'author_role'     => $u['role_title'] ?? 'AI Engineer',
        'avatar_initials' => $u['avatar_initials'] ?? 'AM',
        'karma_xp'        => (int)($u['karma_xp'] ?? 0),
        'is_you'          => true,
        'comments_count'  => $totalComments
    ];

    jsonResponse(['status' => 'success', 'message' => 'Comment posted! +5 XP', 'data' => $newComment]);
}

if ($action === 'delete_comment') {
    $commentId = (int)($body['comment_id'] ?? $_GET['comment_id'] ?? 0);
    if (!$commentId) jsonError('comment_id is required.');

    $check = $pdo->prepare("SELECT post_id, user_id FROM community_comments WHERE id = ?");
    $check->execute([$commentId]);
    $c = $check->fetch();
    if (!$c) jsonError('Comment not found.', 404);

    if ((int)$c['user_id'] !== (int)$userId && $userId !== 1) {
        jsonError('Unauthorized', 403);
    }

    $postId = (int)$c['post_id'];
    $pdo->prepare("DELETE FROM community_comments WHERE id = ?")->execute([$commentId]);

    
    $cStmt = $pdo->prepare("SELECT COUNT(*) FROM community_comments WHERE post_id = ?");
    $cStmt->execute([$postId]);
    $totalComments = (int)$cStmt->fetchColumn();
    $pdo->prepare("UPDATE community_posts SET comments_count = ? WHERE id = ?")->execute([$totalComments, $postId]);

    jsonResponse(['status' => 'success', 'comments_count' => $totalComments]);
}

if ($action === 'trending') {
    $tagSubtitles = [
        '#pytorch'       => 'Deep learning & tensors',
        '#llm'           => 'Large language models',
        '#mlops'         => 'Workflows & CI/CD',
        '#performance'   => 'Throughput & latency tuning',
        '#dataloader'    => 'Data pipeline optimization',
        '#triton'        => 'Inference server scale',
        '#gpu'           => 'CUDA & accelerator compute',
        '#flashattention'=> 'Attention kernels & speeds',
        '#rag'           => 'Retrieval augmented generation',
        '#retrieval'     => 'Vector search & chunking',
        '#debugging'     => 'Training loss & gradients',
        '#transformers'  => 'Attention & model architectures',
        '#rlhf'          => 'Human alignment & PPO',
        '#benchmarks'    => 'Empirical latency & compute'
    ];

    $stmt = $pdo->query("SELECT tags FROM community_posts WHERE tags IS NOT NULL AND tags != ''");
    $rows = $stmt->fetchAll();

    $tagCount = [];
    foreach ($rows as $row) {
        foreach (explode(',', $row['tags']) as $tag) {
            $tag = trim($tag);
            if (!$tag) continue;
            if (!str_starts_with($tag, '#')) $tag = '#' . $tag;
            $tag = strtolower($tag);
            $tagCount[$tag] = ($tagCount[$tag] ?? 0) + 1;
        }
    }
    arsort($tagCount);

    $top = [];
    $rank = 1;
    foreach (array_slice($tagCount, 0, 8, true) as $t => $cnt) {
        $top[] = [
            'rank'     => $rank++,
            'tag'      => $t,
            'count'    => $cnt,
            'subtitle' => $tagSubtitles[$t] ?? 'Community topic'
        ];
    }

    jsonResponse(['status' => 'success', 'data' => $top]);
}

if ($action === 'members') {
    $stmt = $pdo->query("
        SELECT u.id, u.full_name, u.role_title, u.avatar_initials, u.karma_xp,
               COALESCE(ls.xp_points, u.karma_xp) as xp_points,
               COALESCE(ls.specialty, 'AI Systems') as specialty
        FROM users u
        LEFT JOIN leaderboard_scores ls ON u.id = ls.user_id
        ORDER BY u.karma_xp DESC
        LIMIT 6
    ");
    $members = $stmt->fetchAll();
    jsonResponse(['status' => 'success', 'data' => $members]);
}

if ($action === 'events') {
    $stmt = $pdo->prepare("
        SELECT ce.*,
               (SELECT COUNT(*) FROM community_event_rsvps er WHERE er.event_id = ce.id AND er.user_id = ?) AS is_rsvped
        FROM community_events ce
        ORDER BY ce.event_date ASC
        LIMIT 5
    ");
    $stmt->execute([$userId]);
    $events = $stmt->fetchAll();

    foreach ($events as &$ev) {
        $ev['is_rsvped'] = (bool)(int)$ev['is_rsvped'];
    }
    unset($ev);

    jsonResponse(['status' => 'success', 'data' => $events]);
}

if ($action === 'rsvp') {
    $eventId = (int)($body['event_id'] ?? 0);
    if (!$eventId) jsonError('event_id is required.');

    $check = $pdo->prepare("SELECT id FROM community_event_rsvps WHERE event_id = ? AND user_id = ?");
    $check->execute([$eventId, $userId]);

    if ($check->fetch()) {
        
        $pdo->prepare("DELETE FROM community_event_rsvps WHERE event_id = ? AND user_id = ?")->execute([$eventId, $userId]);
        $pdo->prepare("UPDATE community_events SET attendees = GREATEST(0, attendees - 1) WHERE id = ?")->execute([$eventId]);

        $att = (int)$pdo->prepare("SELECT attendees FROM community_events WHERE id = ?");
        $aStmt = $pdo->prepare("SELECT attendees FROM community_events WHERE id = ?");
        $aStmt->execute([$eventId]);
        $attendees = (int)$aStmt->fetchColumn();

        jsonResponse(['status' => 'success', 'registered' => false, 'attendees' => $attendees, 'message' => 'RSVP cancelled.']);
    }

    
    $pdo->prepare("INSERT INTO community_event_rsvps (event_id, user_id) VALUES (?, ?)")->execute([$eventId, $userId]);
    $pdo->prepare("UPDATE community_events SET attendees = attendees + 1 WHERE id = ?")->execute([$eventId]);

    
    $pdo->prepare("UPDATE users SET karma_xp = karma_xp + 25 WHERE id = ?")->execute([$userId]);
    $pdo->prepare("INSERT INTO activity_log (user_id, activity_type, title, description, xp_gained) VALUES (?, 'community', 'RSVP for Community Event', 'Registered for event', 25)")->execute([$userId]);

    $aStmt = $pdo->prepare("SELECT attendees FROM community_events WHERE id = ?");
    $aStmt->execute([$eventId]);
    $attendees = (int)$aStmt->fetchColumn();

    jsonResponse(['status' => 'success', 'registered' => true, 'attendees' => $attendees, 'message' => 'RSVP confirmed! +25 XP gained.']);
}

if ($action === 'stats') {
    $userCount = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $postCount = (int)$pdo->query("SELECT COUNT(*) FROM community_posts")->fetchColumn();
    $commCount = (int)$pdo->query("SELECT COUNT(*) FROM community_comments")->fetchColumn();

    jsonResponse([
        'status' => 'success',
        'data'   => [
            'members'     => 12400 + $userCount,
            'discussions' => $postCount,
            'comments'    => $commCount,
            'online'      => 284 + ($userCount % 7)
        ]
    ]);
}

jsonError('Unknown action: ' . $action);
