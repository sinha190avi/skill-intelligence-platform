<?php

header('Content-Type: application/json');

$host   = 'localhost';
$port   = 3306;
$user   = 'root';
$pass   = '';
$dbname = 'skill_intelligence';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $log = [];

    
    
    
    $cols = $pdo->query("SHOW COLUMNS FROM users LIKE 'password_hash'")->fetchAll();
    if (!$cols) {
        $pdo->exec("ALTER TABLE users ADD COLUMN `password_hash` VARCHAR(255) DEFAULT NULL AFTER `bio`");
        $log[] = 'Added password_hash column to users';
    } else {
        $log[] = 'password_hash column already exists';
    }

    
    
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `leaderboard_scores` (
        `id`            INT AUTO_INCREMENT PRIMARY KEY,
        `user_id`       INT NOT NULL UNIQUE,
        `xp_points`     INT DEFAULT 0,
        `xp_weekly`     INT DEFAULT 0,
        `xp_monthly`    INT DEFAULT 0,
        `xp_quarterly`  INT DEFAULT 0,
        `streak_days`   INT DEFAULT 0,
        `specialty`     VARCHAR(100) DEFAULT 'AI Engineering',
        `readiness_pct` INT DEFAULT 0,
        `trend`         VARCHAR(10) DEFAULT 'same',
        `change_label`  VARCHAR(20) DEFAULT '–',
        `updated_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $log[] = 'leaderboard_scores table ready';

    
    
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `job_listings` (
        `id`            INT AUTO_INCREMENT PRIMARY KEY,
        `title`         VARCHAR(200) NOT NULL,
        `company`       VARCHAR(150) NOT NULL,
        `location`      VARCHAR(100) NOT NULL,
        `job_type`      VARCHAR(30) DEFAULT 'fulltime',
        `match_pct`     INT DEFAULT 70,
        `match_level`   VARCHAR(20) DEFAULT 'good',
        `salary_min`    INT DEFAULT 0,
        `salary_max`    INT DEFAULT 0,
        `salary_label`  VARCHAR(50) DEFAULT '',
        `skills_tags`   TEXT,
        `description`   TEXT,
        `skill_gaps`    TEXT DEFAULT NULL,
        `company_emoji` VARCHAR(10) DEFAULT '🏢',
        `company_bg`    VARCHAR(50) DEFAULT 'rgba(99,102,241,0.15)',
        `posted_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `is_active`     TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $log[] = 'job_listings table ready';

    
    
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `job_applications` (
        `id`          INT AUTO_INCREMENT PRIMARY KEY,
        `user_id`     INT NOT NULL,
        `job_id`      INT NOT NULL,
        `action_type` VARCHAR(20) NOT NULL DEFAULT 'apply',
        `status`      VARCHAR(30) DEFAULT 'submitted',
        `applied_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`job_id`) REFERENCES `job_listings`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $log[] = 'job_applications table ready';

    
    
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `certifications` (
        `id`               INT AUTO_INCREMENT PRIMARY KEY,
        `cert_name`        VARCHAR(200) NOT NULL,
        `issuer`           VARCHAR(150) NOT NULL,
        `cert_type`        VARCHAR(20) DEFAULT 'platform',
        `cover_color`      VARCHAR(20) DEFAULT 'indigo',
        `icon_emoji`       VARCHAR(10) DEFAULT '🏅',
        `xp_reward`        INT DEFAULT 200,
        `total_hours`      INT DEFAULT 10,
        `description`      TEXT,
        `verification_url` VARCHAR(255) DEFAULT NULL,
        `match_pct`        INT DEFAULT 80,
        `difficulty`       VARCHAR(30) DEFAULT 'Intermediate'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $log[] = 'certifications table ready';

    
    
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `user_certifications` (
        `id`            INT AUTO_INCREMENT PRIMARY KEY,
        `user_id`       INT NOT NULL,
        `cert_id`       INT NOT NULL,
        `status`        VARCHAR(20) DEFAULT 'in_progress',
        `progress_pct`  INT DEFAULT 0,
        `score_pct`     INT DEFAULT NULL,
        `cert_id_code`  VARCHAR(50) DEFAULT NULL,
        `earned_at`     TIMESTAMP NULL DEFAULT NULL,
        `enrolled_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `user_cert_unique` (`user_id`, `cert_id`),
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`cert_id`) REFERENCES `certifications`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $log[] = 'user_certifications table ready';

    
    
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `community_posts` (
        `id`             INT AUTO_INCREMENT PRIMARY KEY,
        `user_id`        INT NOT NULL,
        `body`           TEXT NOT NULL,
        `tags`           VARCHAR(300) DEFAULT '',
        `post_type`      VARCHAR(30) DEFAULT 'discussion',
        `has_code`       TINYINT(1) DEFAULT 0,
        `code_block`     TEXT DEFAULT NULL,
        `likes_count`    INT DEFAULT 0,
        `comments_count` INT DEFAULT 0,
        `is_pinned`      TINYINT(1) DEFAULT 0,
        `created_at`     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $log[] = 'community_posts table ready';

    
    
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `community_reactions` (
        `id`            INT AUTO_INCREMENT PRIMARY KEY,
        `post_id`       INT NOT NULL,
        `user_id`       INT NOT NULL,
        `reaction_type` VARCHAR(20) DEFAULT 'like',
        `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `unique_reaction` (`post_id`, `user_id`, `reaction_type`),
        FOREIGN KEY (`post_id`) REFERENCES `community_posts`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $log[] = 'community_reactions table ready';

    
    
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `community_events` (
        `id`           INT AUTO_INCREMENT PRIMARY KEY,
        `title`        VARCHAR(200) NOT NULL,
        `event_type`   VARCHAR(50) DEFAULT 'session',
        `event_date`   DATE NOT NULL,
        `host_name`    VARCHAR(150) DEFAULT '',
        `attendees`    INT DEFAULT 0,
        `prize_label`  VARCHAR(100) DEFAULT NULL,
        `color`        VARCHAR(30) DEFAULT 'cyan',
        `rsvp_open`    TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $log[] = 'community_events table ready';

    
    
    

    
    $lbCount = (int)$pdo->query("SELECT COUNT(*) FROM leaderboard_scores")->fetchColumn();
    if ($lbCount === 0) {
        $engineers = [
            [1,  'alex.morgan@enterprise.ai',    'Alex Morgan',   'Senior AI/ML Eng',     'Transformers',     2450,  340, 1200, 3800,  14, 78,  'up',   '+340'],
            [2,  'sayan.ghosh@deepmind.ai',       'Sayan Ghosh',   'Lead AI Architect',    'Transformers',    12480, 1240, 4800, 9200,  28, 96,  'up',   '+1240'],
            [3,  'riya.kapoor@research.ai',        'Riya Kapoor',   'AI Researcher',        'Generative AI',    9820,  890, 3900, 7400,  21, 93,  'up',   '+890'],
            [4,  'vivek.nair@ml.io',               'Vivek Nair',    'ML Engineer',          'PyTorch/CUDA',     8650,  640, 3200, 6800,  17, 90,  'down', '-80'],
            [5,  'priya.anand@sysai.io',           'Priya Anand',   'AI Systems Lead',      'MLOps',            7920,  720, 3100, 6200,  14, 88,  'up',   '+190'],
            [6,  'arjun.ahuja@principal.ai',       'Arjun Ahuja',   'Principal Eng',        'LLM Alignment',    7240,  520, 2800, 5700,  12, 85,  'same', '–'],
            [7,  'megha.singh@ragsys.ai',          'Megha Singh',   'AI Engineer II',       'RAG Systems',      6800,  480, 2600, 5400,  10, 83,  'up',   '+130'],
            [8,  'tanmay.kumar@rlhf.ai',           'Tanmay Kumar',  'ML Researcher',        'RL & Alignment',   6450,  420, 2400, 5100,   9, 81,  'up',   '+90'],
            [9,  'divya.pillai@embeddings.ai',     'Divya Pillai',  'Data Scientist III',   'Embeddings',       6100,  380, 2200, 4900,   8, 80,  'down', '-60'],
            [10, 'soham.pal@vectordb.ai',          'Soham Pal',     'AI Engineer',          'Vector DBs',       5840,  340, 2000, 4700,   7, 78,  'up',   '+75'],
            [11, 'nikita.arora@nlp.ai',            'Nikita Arora',  'ML Engineer',          'NLP / BERT',       5520,  300, 1900, 4500,   6, 76,  'same', '–'],
            [12, 'kartik.menon@sysdesign.ai',      'Kartik Menon',  'AI Architect Intern',  'System Design',    5100,  420, 1800, 4100,  11, 74,  'up',   '+150'],
            [13, 'aanya.gupta@multimodal.ai',      'Aanya Gupta',   'Research Engineer',    'Multimodal AI',    4830,  280, 1700, 3900,   5, 72,  'down', '-45'],
            [14, 'rohit.sharma@k8s.ai',            'Rohit Sharma',  'ML Ops Specialist',    'Kubernetes AI',    4680,  360, 1600, 3700,  14, 71,  'up',   '+100'],
            [15, 'ishaan.luthra@finetune.ai',      'Ishaan Luthra', 'AI Engineer II',       'Fine-Tuning',      4450,  250, 1500, 3600,   9, 70,  'same', '–'],
            [16, 'sunita.bhat@dist.ai',            'Sunita Bhat',   'Principal ML',         'Distributed ML',   4210,  310, 1400, 3400,   8, 69,  'up',   '+88'],
            [17, 'manav.reddy@neural.ai',          'Manav Reddy',   'Data Scientist II',    'Neural Nets',      3980,  220, 1300, 3200,   6, 68,  'down', '-30'],
            [18, 'prita.sood@genai.ai',            'Prita Sood',    'AI Researcher',        'GenAI Systems',    3740,  200, 1200, 3000,   7, 67,  'up',   '+60'],
            [19, 'nihal.gowda@pytorch.ai',         'Nihal Gowda',   'ML Engineer',          'PyTorch',          2200,  180, 1000, 2600,   5, 65,  'same', '–'],
            [20, 'zara.ahmad@nlp.ai',              'Zara Ahmad',    'AI Intern',            'NLP',              1980,  160,  900, 2300,   3, 62,  'up',   '+40'],
        ];

        
        $userInsert = $pdo->prepare("INSERT IGNORE INTO users (full_name, email, role_title, target_role, avatar_initials, karma_xp, rank_percentile) VALUES (?, ?, ?, 'Lead AI Architect', ?, ?, ?)");
        $lbInsert   = $pdo->prepare("INSERT IGNORE INTO leaderboard_scores (user_id, xp_points, xp_weekly, xp_monthly, xp_quarterly, streak_days, readiness_pct, specialty, trend, change_label) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        foreach ($engineers as $eng) {
            [$uid, $email, $name, $role, $specialty, $xp, $weekly, $monthly, $quarterly, $streak, $readiness, $trend, $change] = $eng;
            
            $existing = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $existing->execute([$email]);
            $existingUser = $existing->fetch();

            if ($existingUser) {
                $realUid = $existingUser['id'];
            } else {
                $initials = strtoupper(implode('', array_map(fn($w) => $w[0], array_slice(explode(' ', $name), 0, 2))));
                $userInsert->execute([$name, $email, $role, $initials, $xp, 'Top ' . $readiness . '%']);
                $realUid = (int)$pdo->lastInsertId();
            }
            $lbInsert->execute([$realUid, $xp, $weekly, $monthly, $quarterly, $streak, $readiness, $specialty, $trend, $change]);
        }
        $log[] = 'Leaderboard seeded with 20 engineers';
    } else {
        $log[] = 'Leaderboard already seeded';
    }

    
    $jobCount = (int)$pdo->query("SELECT COUNT(*) FROM job_listings")->fetchColumn();
    if ($jobCount === 0) {
        $jobs = [
            ['Lead AI Architect — LLM Systems', 'Google DeepMind India', 'Bengaluru, Hybrid', 'fulltime', 96, 'high', 6000000, 9000000, '₹60–90L / year', 'PyTorch, Transformers, CUDA, System Design', 'Design and lead distributed LLM training and serving infrastructure at exascale.', NULL, '🤖', 'rgba(99,102,241,0.15)'],
            ['Principal ML Engineer — GenAI Platform', 'Microsoft Azure AI', 'Hyderabad, Remote', 'remote', 92, 'high', 5500000, 8000000, '₹55–80L / year', 'MLOps, Kubernetes, RAG, LLMs', 'Build the next generation of GenAI platform services on Azure.', NULL, '☁️', 'rgba(56,189,248,0.15)'],
            ['Staff AI Engineer — Recommendation Systems', 'Flipkart AI Labs', 'Bengaluru', 'fulltime', 87, 'good', 4200000, 6500000, '₹42–65L / year', 'Deep Learning, Python, Vector DBs', 'Build ML recommendation and search systems at Flipkart scale.', 'Collaborative Filtering', '🧠', 'rgba(16,185,129,0.15)'],
            ['Senior AI Research Engineer', 'NVIDIA AI Research', 'Pune, Hybrid', 'fulltime', 84, 'good', 5000000, 7500000, '₹50–75L / year', 'CUDA Kernels, C++, Triton', 'Advance GPU compute for AI at NVIDIA Research.', NULL, '⚡', 'rgba(168,85,247,0.15)'],
            ['AI Platform Architect', 'Infosys Topaz AI', 'Mumbai, Hybrid', 'fulltime', 81, 'good', 3500000, 5500000, '₹35–55L / year', 'MLflow, Airflow, Spark, Kafka', 'Design enterprise AI platform infrastructure.', NULL, '🚀', 'rgba(245,158,11,0.15)'],
            ['Research Scientist — Foundation Models', 'Sarvam AI', 'Bengaluru', 'fulltime', 74, 'fair', 4500000, 7000000, '₹45–70L / year', 'LLM Pre-training, RLHF, JAX', 'Research and train Indian language foundation models.', 'JAX / Flax, Pre-training at Scale', '🔬', 'rgba(244,63,94,0.12)'],
            ['MLOps Lead — AI Infrastructure', 'Swiggy AI', 'Bengaluru, Remote', 'remote', 79, 'good', 3800000, 5800000, '₹38–58L / year', 'Kubeflow, Triton, Prometheus, Ray', 'Own the ML platform and model deployment lifecycle.', NULL, '🍔', 'rgba(16,185,129,0.12)'],
            ['Senior LLM Engineer', 'Ola Electric AI', 'Bengaluru', 'fulltime', 83, 'good', 4500000, 7000000, '₹45–70L / year', 'LLMs, Python, vLLM, LangChain', 'Build LLM-powered product features at Ola.', NULL, '⚡', 'rgba(245,158,11,0.12)'],
        ];

        $stmt = $pdo->prepare("INSERT INTO job_listings (title, company, location, job_type, match_pct, match_level, salary_min, salary_max, salary_label, skills_tags, description, skill_gaps, company_emoji, company_bg) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($jobs as $j) {
            $stmt->execute($j);
        }
        $log[] = 'Job listings seeded (8 jobs)';
    } else {
        $log[] = 'Job listings already seeded';
    }

    
    $certCount = (int)$pdo->query("SELECT COUNT(*) FROM certifications")->fetchColumn();
    if ($certCount === 0) {
        $certs = [
            ['Advanced Transformer Architectures & Optimization', 'Skill Intelligence Platform', 'platform', 'gold',     '🤖', 300, 20, 'Master FlashAttention-2, KV caching, rotary embeddings, and production quantization.', 95, 'Advanced'],
            ['AWS Certified Machine Learning Specialty',           'Amazon Web Services',         'external', 'platinum', '☁️', 250, 30, 'Cloud-scale ML architecture and SageMaker deployment for enterprise.', 85, 'Advanced'],
            ['Deep Learning Specialization (5-Course)',           'DeepLearning.AI',              'external', 'emerald',  '🧠', 200, 60, 'Complete deep learning curriculum from Andrew Ng.', 90, 'Intermediate'],
            ['Certified Kubernetes Administrator (CKA)',          'CNCF',                         'external', 'indigo',   '☸️', 200, 25, 'Performance-based exam for Kubernetes cluster management.', 80, 'Advanced'],
            ['Advanced Python for AI Engineering',                'Skill Intelligence Platform', 'platform', 'cyan',     '⚡', 150, 15, 'AsyncIO, Cython, NumPy vectorization, profiling for AI workloads.', 92, 'Advanced'],
            ['MLOps Practitioner — Kubeflow & Triton',           'Skill Intelligence Platform', 'platform', 'rose',     '🎯', 250, 18, 'Production ML operations with Kubeflow, Triton, and Prometheus.', 85, 'Advanced'],
            ['RLHF & LLM Alignment Practitioner',                'Skill Intelligence Platform', 'platform', 'rose',     '🎯', 300, 20, 'Reward modeling, PPO, DPO, and RLAIF for production LLM alignment.', 88, 'Expert'],
            ['Vector Databases & Multimodal RAG',                'Skill Intelligence Platform', 'platform', 'cyan',     '🗄️', 250, 16, 'Qdrant, Milvus, HNSW indexing, hybrid retrieval, multimodal RAG.', 91, 'Advanced'],
            ['GPU Architecture & Kernel Optimization',           'Skill Intelligence Platform', 'platform', 'gold',     '⚡', 400, 22, 'CUDA kernels, Triton, GPU memory hierarchy, fused operators.', 78, 'Expert'],
            ['Professional Machine Learning Engineer',            'Google Cloud',                 'external', 'gold',     '🏗️', 300, 40, 'Google Cloud ML services and production model deployment.', 82, 'Advanced'],
        ];

        $stmt = $pdo->prepare("INSERT INTO certifications (cert_name, issuer, cert_type, cover_color, icon_emoji, xp_reward, total_hours, description, match_pct, difficulty) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($certs as $c) { $stmt->execute($c); }
        $log[] = 'Certifications catalog seeded (10 certs)';

        
        $userCerts = [
            [1, 1, 'earned', 100, 94, 'SIP-2026-TRF-8821', '2026-08-15'],
            [1, 2, 'earned', 100, 89, 'AWS-2026-MLS-4492', '2026-02-10'],
            [1, 3, 'earned', 100, 97, 'DL-2025-SPEC-1123', '2025-12-01'],
            [1, 4, 'earned', 100, 88, 'CKA-2025-CERT-772', '2025-09-20'],
            [1, 5, 'earned', 100, 94, 'SIP-2026-PY-6614',  '2026-09-10'],
            [1, 6, 'earned', 100, 76, 'SIP-2026-OPS-3301', '2026-07-05'],
            [1, 7, 'in_progress', 65,  NULL, NULL, NULL],
            [1, 10,'in_progress', 38,  NULL, NULL, NULL],
            [1, 9, 'in_progress', 12,  NULL, NULL, NULL],
        ];
        $ucStmt = $pdo->prepare("INSERT IGNORE INTO user_certifications (user_id, cert_id, status, progress_pct, score_pct, cert_id_code, earned_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($userCerts as $uc) { $ucStmt->execute($uc); }
        $log[] = 'User certifications seeded (6 earned, 3 in progress)';
    } else {
        $log[] = 'Certifications already seeded';
    }

    
    $postCount = (int)$pdo->query("SELECT COUNT(*) FROM community_posts")->fetchColumn();
    if ($postCount === 0) {
        
        $userMap = [];
        $rows = $pdo->query("SELECT id, email FROM users LIMIT 20")->fetchAll();
        foreach ($rows as $r) { $userMap[$r['email']] = $r['id']; }

        $mainUid = $userMap['alex.morgan@enterprise.ai'] ?? 1;
        $sayanUid = $userMap['sayan.ghosh@deepmind.ai'] ?? 2;
        $riyaUid  = $userMap['riya.kapoor@research.ai']   ?? 3;
        $priyaUid = $userMap['priya.anand@sysai.io']      ?? 5;
        $tanmayUid = $userMap['tanmay.kumar@rlhf.ai']     ?? 8;

        $posts = [
            [$sayanUid,  "🚀 Just deployed a zero-downtime Triton Inference Server upgrade with 3× throughput improvement. Key insight: batching policy matters way more than I expected. Changed from max_batch_size=1 to max_batch_size=64 with dynamic batching — GPU utilization jumped from 34% to 91%.", '#mlops,#triton,#gpu', 'discussion', 1, "dynamic_batching {\n  preferred_batch_size: [ 16, 32, 64 ]\n  max_queue_delay_microseconds: 5000\n}", 142, 38, 1],
            [$riyaUid,   "Hot take: most RAG implementations fail not because of retrieval quality, but because of chunking strategy. Semantic chunking with a sliding 50-token overlap consistently outperforms fixed-size chunking by 18–27% on retrieval F1 in my benchmarks. Anyone seeing this pattern?", '#llm,#rag,#retrieval', 'discussion', 0, NULL, 89, 21, 0],
            [$tanmayUid, "Struggling with gradient explosions in my custom RLHF training loop. PPO reward clipping is set to ε=0.2 but still seeing NaN losses after ~3k steps. I've tried gradient clipping at max_norm=1.0. Anyone experienced this?", '#rlhf,#pytorch,#debugging', 'question', 1, "optimizer = AdamW(\n    model.parameters(),\n    lr=1e-5,\n    weight_decay=0.01,\n    betas=(0.9, 0.999)\n)\ntorch.nn.utils.clip_grad_norm_(model.parameters(), max_norm=1.0)", 34, 15, 0],
            [$priyaUid,  "PSA: If you're still doing `for batch in dataloader` on CPU-bottlenecked pipelines — try num_workers=8 + pin_memory=True + prefetch_factor=4. Cut our training epoch time from 4.8 min → 2.1 min on a single A100. Huge win for dataset-heavy workloads. 🔥", '#pytorch,#performance,#dataloader', 'discussion', 0, NULL, 201, 44, 0],
            [$mainUid,   "Sharing my FlashAttention-2 benchmark results vs standard attention on RTX 4090:\n\nStandard: 23.4 ms / token @ batch=32\nFlashAttention-2: 6.1 ms / token @ batch=32 — 3.8× faster 🚀\n\nSequence length matters A LOT. Below 512 tokens gains are modest. Beyond 2048 is where it really shines.", '#llm,#flashattention,#benchmarks', 'discussion', 0, NULL, 117, 29, 0],
        ];

        $postStmt = $pdo->prepare("INSERT INTO community_posts (user_id, body, tags, post_type, has_code, code_block, likes_count, comments_count, is_pinned) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($posts as $p) { $postStmt->execute($p); }
        $log[] = 'Community posts seeded (5 posts)';
    } else {
        $log[] = 'Community posts already seeded';
    }

    
    $evtCount = (int)$pdo->query("SELECT COUNT(*) FROM community_events")->fetchColumn();
    if ($evtCount === 0) {
        $pdo->prepare("INSERT INTO community_events (title, event_type, event_date, host_name, attendees, prize_label, color) VALUES
            ('Advanced RAG Architecture Workshop', 'session', '2026-09-22', 'Riya Kapoor', 156, NULL, 'cyan'),
            ('Enterprise GenAI Challenge 2026', 'hackathon', '2026-09-28', 'Skill Intelligence', 84, '₹2L prize pool', 'amber'),
            ('LLM Fine-Tuning Masterclass', 'session', '2026-10-05', 'Sayan Ghosh', 210, NULL, 'indigo'),
            ('MLOps Career Day — Live Q&A', 'session', '2026-10-12', 'Priya Anand', 89, NULL, 'emerald')
        ")->execute();
        $log[] = 'Community events seeded (4 events)';
    } else {
        $log[] = 'Community events already seeded';
    }

    
    $existLb = $pdo->prepare("SELECT id FROM leaderboard_scores WHERE user_id = 1");
    $existLb->execute();
    if (!$existLb->fetch()) {
        $pdo->exec("INSERT IGNORE INTO leaderboard_scores (user_id, xp_points, xp_weekly, xp_monthly, xp_quarterly, streak_days, readiness_pct, specialty, trend, change_label) VALUES (1, 2450, 340, 1200, 3800, 14, 78, 'Transformers', 'up', '+340')");
        $log[] = 'Alex Morgan added to leaderboard';
    }

    echo json_encode([
        'status'    => 'success',
        'message'   => 'Extended database setup complete.',
        'timestamp' => date('Y-m-d H:i:s'),
        'log'       => $log
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
