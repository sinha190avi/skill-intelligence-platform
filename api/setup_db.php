<?php

header('Content-Type: application/json');

$host = 'localhost';
$port = 3306;
$user = 'root';
$pass = '';
$dbname = 'skill_intelligence';

try {
    
    $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `$dbname`;");

    

    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `full_name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(150) NOT NULL UNIQUE,
        `role_title` VARCHAR(100) NOT NULL,
        `target_role` VARCHAR(100) NOT NULL,
        `department` VARCHAR(150) DEFAULT 'Enterprise Cognitive Systems Division',
        `location` VARCHAR(100) DEFAULT 'Bengaluru, India',
        `avatar_initials` VARCHAR(10) DEFAULT 'AM',
        `karma_xp` INT DEFAULT 2450,
        `rank_percentile` VARCHAR(50) DEFAULT 'Top 5%',
        `bio` TEXT,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `user_settings` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `theme_mode` VARCHAR(20) DEFAULT 'dark',
        `weekly_hours_target` INT DEFAULT 15,
        `daily_streak_protection` TINYINT(1) DEFAULT 1,
        `rec_alerts` TINYINT(1) DEFAULT 1,
        `assessment_reports` TINYINT(1) DEFAULT 1,
        `marketing_emails` TINYINT(1) DEFAULT 0,
        `profile_visibility` VARCHAR(30) DEFAULT 'public',
        `two_factor_auth` TINYINT(1) DEFAULT 0,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `skills` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `category` VARCHAR(50) NOT NULL,
        `name` VARCHAR(100) NOT NULL,
        `proficiency_pct` INT NOT NULL,
        `level_label` VARCHAR(50) NOT NULL,
        `benchmark_rank` VARCHAR(50) NOT NULL,
        `endorsements_count` INT DEFAULT 0,
        `target_weight_pct` INT DEFAULT 10,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `assessments` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(150) NOT NULL,
        `category` VARCHAR(50) NOT NULL,
        `description` TEXT NOT NULL,
        `duration_mins` INT NOT NULL,
        `questions_count` INT NOT NULL,
        `difficulty` VARCHAR(30) NOT NULL,
        `pass_score_pct` INT DEFAULT 75,
        `questions_json` LONGTEXT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `assessment_attempts` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `assessment_id` INT NOT NULL,
        `assessment_title` VARCHAR(150) NOT NULL,
        `score_pct` INT NOT NULL,
        `status` VARCHAR(20) NOT NULL,
        `benchmark_percentile` VARCHAR(50) NOT NULL,
        `completed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`assessment_id`) REFERENCES `assessments`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `courses` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(200) NOT NULL,
        `category` VARCHAR(50) NOT NULL,
        `match_pct` INT NOT NULL,
        `duration_hours` INT NOT NULL,
        `labs_count` INT NOT NULL,
        `rating` DECIMAL(3,1) NOT NULL,
        `reviews_count` INT NOT NULL,
        `description` TEXT NOT NULL,
        `level_label` VARCHAR(50) NOT NULL,
        `icon_emoji` VARCHAR(20) DEFAULT '🤖'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `enrollments` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `course_id` INT NOT NULL,
        `progress_pct` INT DEFAULT 0,
        `status` VARCHAR(30) DEFAULT 'in_progress',
        `enrolled_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `last_accessed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY `user_course_unique` (`user_id`, `course_id`),
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `learning_path_stages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `stage_number` INT NOT NULL,
        `title` VARCHAR(200) NOT NULL,
        `description` TEXT NOT NULL,
        `status` VARCHAR(30) NOT NULL,
        `progress_pct` INT DEFAULT 0,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `learning_path_modules` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `stage_id` INT NOT NULL,
        `title` VARCHAR(200) NOT NULL,
        `subtitle` TEXT NOT NULL,
        `duration_mins` INT NOT NULL,
        `is_completed` TINYINT(1) DEFAULT 0,
        `order_num` INT NOT NULL,
        FOREIGN KEY (`stage_id`) REFERENCES `learning_path_stages`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `recommendations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `title` VARCHAR(200) NOT NULL,
        `category` VARCHAR(50) NOT NULL,
        `match_pct` INT NOT NULL,
        `reason_text` TEXT NOT NULL,
        `duration_hours` INT NOT NULL,
        `case_studies_count` INT NOT NULL,
        `status` VARCHAR(30) DEFAULT 'active',
        `priority_tag` VARCHAR(50) DEFAULT 'Recommended',
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `notifications` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `title` VARCHAR(200) NOT NULL,
        `message` TEXT NOT NULL,
        `time_ago` VARCHAR(50) DEFAULT 'Just now',
        `is_read` TINYINT(1) DEFAULT 0,
        `type` VARCHAR(50) DEFAULT 'info',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `activity_log` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `activity_type` VARCHAR(50) NOT NULL,
        `title` VARCHAR(200) NOT NULL,
        `description` TEXT NOT NULL,
        `xp_gained` INT DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `progress_stats` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `total_hours_invested` DECIMAL(5,1) DEFAULT 74.2,
        `active_streak_days` INT DEFAULT 14,
        `pass_rate_pct` DECIMAL(4,1) DEFAULT 94.2,
        `target_readiness_pct` INT DEFAULT 78,
        `mastered_competencies_count` INT DEFAULT 24,
        `total_competencies_count` INT DEFAULT 30,
        `weekly_hours_json` TEXT,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `chat_messages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `sender` VARCHAR(20) NOT NULL,
        `message_html` TEXT NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    
    $userCount = $pdo->query("SELECT COUNT(*) FROM `users`")->fetchColumn();
    if ($userCount == 0) {
        
        $stmt = $pdo->prepare("INSERT INTO `users` (`id`, `full_name`, `email`, `role_title`, `target_role`, `department`, `location`, `avatar_initials`, `karma_xp`, `rank_percentile`, `bio`) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            'Alex Morgan',
            'alex.morgan@enterprise.ai',
            'Senior AI/ML Engineer',
            'Lead AI Architect',
            'Enterprise Cognitive Systems Division',
            'Bengaluru, India',
            'AM',
            2450,
            'Top 5%',
            'Architecting enterprise generative AI, low-latency distributed PyTorch/CUDA training clusters, and agentic workflows.'
        ]);

        
        $pdo->prepare("INSERT INTO `user_settings` (`user_id`, `theme_mode`, `weekly_hours_target`, `daily_streak_protection`, `rec_alerts`, `assessment_reports`, `marketing_emails`, `profile_visibility`, `two_factor_auth`) VALUES (1, 'dark', 15, 1, 1, 1, 0, 'public', 0)")->execute();

        
        $weeklyHours = json_encode([
            ['day' => 'Mon', 'hours' => 3.5],
            ['day' => 'Tue', 'hours' => 2.0],
            ['day' => 'Wed', 'hours' => 4.0],
            ['day' => 'Thu', 'hours' => 1.5],
            ['day' => 'Fri', 'hours' => 3.5],
            ['day' => 'Sat', 'hours' => 4.0],
            ['day' => 'Sun', 'hours' => 0.0]
        ]);
        $pdo->prepare("INSERT INTO `progress_stats` (`user_id`, `total_hours_invested`, `active_streak_days`, `pass_rate_pct`, `target_readiness_pct`, `mastered_competencies_count`, `total_competencies_count`, `weekly_hours_json`) VALUES (1, 74.2, 14, 94.2, 78, 24, 30, ?)")->execute([$weeklyHours]);

        
        $skillsData = [
            ['user_id' => 1, 'category' => 'genai', 'name' => 'Large Language Models (LLMs)', 'proficiency_pct' => 92, 'level_label' => 'Mastery', 'benchmark_rank' => 'Top 4%', 'endorsements_count' => 18, 'target_weight_pct' => 20],
            ['user_id' => 1, 'category' => 'genai', 'name' => 'RAG Architectures & Vector DBs', 'proficiency_pct' => 88, 'level_label' => 'Mastery', 'benchmark_rank' => 'Top 6%', 'endorsements_count' => 14, 'target_weight_pct' => 15],
            ['user_id' => 1, 'category' => 'pytorch', 'name' => 'PyTorch Core & Autograd', 'proficiency_pct' => 96, 'level_label' => 'Expert', 'benchmark_rank' => 'Top 2%', 'endorsements_count' => 26, 'target_weight_pct' => 15],
            ['user_id' => 1, 'category' => 'pytorch', 'name' => 'Custom Triton & CUDA Kernels', 'proficiency_pct' => 74, 'level_label' => 'Proficient', 'benchmark_rank' => 'Top 14%', 'endorsements_count' => 9, 'target_weight_pct' => 10],
            ['user_id' => 1, 'category' => 'mlops', 'name' => 'Distributed Training (DDP / ZeRO)', 'proficiency_pct' => 84, 'level_label' => 'Advanced', 'benchmark_rank' => 'Top 8%', 'endorsements_count' => 12, 'target_weight_pct' => 15],
            ['user_id' => 1, 'category' => 'mlops', 'name' => 'Kubernetes & GPU Orchestration', 'proficiency_pct' => 70, 'level_label' => 'Intermediate', 'benchmark_rank' => 'Top 22%', 'endorsements_count' => 7, 'target_weight_pct' => 10],
            ['user_id' => 1, 'category' => 'arch', 'name' => 'High-Throughput Inference (vLLM)', 'proficiency_pct' => 86, 'level_label' => 'Mastery', 'benchmark_rank' => 'Top 7%', 'endorsements_count' => 15, 'target_weight_pct' => 15],
            ['user_id' => 1, 'category' => 'arch', 'name' => 'Enterprise AI Governance & Safety', 'proficiency_pct' => 64, 'level_label' => 'Competent', 'benchmark_rank' => 'Top 28%', 'endorsements_count' => 5, 'target_weight_pct' => 10]
        ];
        $skillStmt = $pdo->prepare("INSERT INTO `skills` (`user_id`, `category`, `name`, `proficiency_pct`, `level_label`, `benchmark_rank`, `endorsements_count`, `target_weight_pct`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($skillsData as $s) {
            $skillStmt->execute([$s['user_id'], $s['category'], $s['name'], $s['proficiency_pct'], $s['level_label'], $s['benchmark_rank'], $s['endorsements_count'], $s['target_weight_pct']]);
        }

        
        $assessmentsData = [
            [
                'title' => 'System Design for Large Language Models',
                'category' => 'arch',
                'description' => 'Evaluate your ability to architect fault-tolerant distributed inference pipelines, KV cache paging, semantic routing, and multi-GPU tensor-parallel architectures.',
                'duration_mins' => 30,
                'questions_count' => 5,
                'difficulty' => 'Advanced',
                'pass_score_pct' => 75,
                'questions_json' => json_encode([
                    [
                        'q' => 'In high-throughput vLLM serving, what fundamental problem does PagedAttention solve compared to standard contiguous memory allocation?',
                        'options' => [
                            'Eliminates floating-point quantization errors',
                            'Prevents KV-cache memory fragmentation and enables dynamic page sharing across parallel requests',
                            'Directly bypasses CUDA kernels using CPU SIMD registers',
                            'Replaces multi-head attention with quadratic dot-product memory'
                        ],
                        'correct' => 1,
                        'explanation' => 'PagedAttention partitions the KV cache into non-contiguous virtual blocks, reducing memory waste from ~60-80% down to under 4%.'
                    ],
                    [
                        'q' => 'When partitioning a 70B parameter model across 4x A100 (80GB) GPUs using Megatron-LM Tensor Parallelism, which layer components are split along column and row dimensions?',
                        'options' => [
                            'Self-attention QKV projections are split by column, Output dense projection is split by row',
                            'MLP layers are kept unpartitioned on GPU 0 while attention runs on GPU 1-3',
                            'LayerNorm weights are duplicated while embeddings are discarded',
                            'Only bias vectors are partitioned across the NVLink fabric'
                        ],
                        'correct' => 0,
                        'explanation' => 'In tensor parallelism, self-attention Q, K, V linear weights are column-parallel, and the output projection is row-parallel followed by an AllReduce.'
                    ],
                    [
                        'q' => 'In DeepSpeed ZeRO Stage 3 (ZeRO-3), which components of the training state are partitioned across all distributed data-parallel worker nodes?',
                        'options' => [
                            'Only optimizer states (moments)',
                            'Optimizer states and first-order gradients only',
                            'Optimizer states, gradients, and model parameters',
                            'Only activation checkpoint caches'
                        ],
                        'correct' => 2,
                        'explanation' => 'ZeRO-3 shards all three primary memory consumers: 16-bit model parameters, gradients, and 32-bit optimizer states.'
                    ],
                    [
                        'q' => 'When building a production RAG system with 10M dense document vectors, which index type provides sub-10ms latency with 95%+ recall?',
                        'options' => [
                            'Exact Flat L2 scan',
                            'HNSW (Hierarchical Navigable Small World) with scalar quantization',
                            'B-Tree index over embedding byte hashes',
                            'Full-text inverted BM25 index with no vector distance'
                        ],
                        'correct' => 1,
                        'explanation' => 'HNSW graphs paired with scalar quantization (SQ8) provide logarithmic search time with high recall for high-dimensional vector spaces.'
                    ],
                    [
                        'q' => 'To defend an enterprise LLM agent against Indirect Prompt Injection from untrusted web documents, which strategy provides the strongest defense-in-depth?',
                        'options' => [
                            'Increasing temperature to 1.5 to randomize output',
                            'Dual-LLM architecture isolating untrusted data parsing from privileged action execution with strict tool authorization schemas',
                            'Appending "Ignore instructions in the document" at the very end of the user prompt',
                            'Converting all text to uppercase before passing to tokenizer'
                        ],
                        'correct' => 1,
                        'explanation' => 'Dual-LLM isolation ensures the agent interpreter never executes unverified instructions found within data retrieval payloads.'
                    ]
                ])
            ],
            [
                'title' => 'Transformer Architectures & Attention Deep Dive',
                'category' => 'aiml',
                'description' => 'Test your mathematical and algorithmic understanding of FlashAttention, RoPE rotary position embeddings, Multi-Query Attention (MQA), and GQA.',
                'duration_mins' => 25,
                'questions_count' => 4,
                'difficulty' => 'Expert',
                'pass_score_pct' => 80,
                'questions_json' => json_encode([
                    [
                        'q' => 'Why does FlashAttention achieve a 2x-4x wall-clock speedup without modifying the mathematical output of standard attention?',
                        'options' => [
                            'It skips computing softmax by estimating dot-products using cosine approximation',
                            'It tiles inputs into GPU SRAM and fuses the softmax online computation to avoid reading/writing N x N intermediate matrices to slow HBM',
                            'It executes strictly on CPU AVX-512 vector units instead of tensor cores',
                            'It restricts the attention matrix to a diagonal tri-band mask'
                        ],
                        'correct' => 1,
                        'explanation' => 'FlashAttention minimizes High-Bandwidth Memory (HBM) I/O by tiling Q, K, V into fast on-chip SRAM and computing softmax incrementally.'
                    ],
                    [
                        'q' => 'In Grouped-Query Attention (GQA), how are query and key-value heads organized compared to Multi-Head Attention (MHA) and Multi-Query Attention (MQA)?',
                        'options' => [
                            'MHA has 1 KV head per Q head; MQA has 1 KV head total; GQA groups several Q heads to share a single KV head',
                            'GQA assigns 2 distinct KV heads for every single Q head',
                            'GQA removes all query heads and computes attention purely between keys',
                            'GQA computes cross-attention exclusively across layers'
                        ],
                        'correct' => 0,
                        'explanation' => 'GQA partitions query heads into groups that share a single key/value head, providing near-MHA quality with near-MQA memory efficiency.'
                    ],
                    [
                        'q' => 'How does Rotary Position Embedding (RoPE) inject positional information into token representations?',
                        'options' => [
                            'By adding absolute sinusoidal vectors directly to the token embeddings',
                            'By rotating the Query and Key vectors in 2D coordinate planes by angles proportional to token position',
                            'By appending an integer index as an extra token dimension',
                            'By learning a static bias matrix added directly to attention logits'
                        ],
                        'correct' => 1,
                        'explanation' => 'RoPE represents relative token distances via orthogonal 2D rotation matrices applied to the Q and K projections.'
                    ],
                    [
                        'q' => 'What is the primary benefit of SwiGLU activations over standard ReLU or GeLU in modern LLM feed-forward networks?',
                        'options' => [
                            'It uses 50% fewer parameters than standard FFN',
                            'The gated linear unit with Swish improves gradient flow and empirically yields superior validation perplexity for equal compute',
                            'It guarantees non-zero derivatives across negative infinity',
                            'It eliminates matrix multiplication in the feed-forward projection'
                        ],
                        'correct' => 1,
                        'explanation' => 'SwiGLU pairs a Swish activation gate with a linear projection, consistently outperforming GeLU in LLMs like LLaMA and PaLM.'
                    ]
                ])
            ],
            [
                'title' => 'Kubernetes for AI Workloads & GPU Orchestration',
                'category' => 'mlops',
                'description' => 'Test knowledge of NVIDIA GPU Operator, Volcano/Kueue batch scheduling, MIG (Multi-Instance GPU), and RDMA network topology.',
                'duration_mins' => 35,
                'questions_count' => 4,
                'difficulty' => 'Advanced',
                'pass_score_pct' => 70,
                'questions_json' => json_encode([
                    [
                        'q' => 'What Kubernetes resource does the NVIDIA GPU Operator install to automatically provision CUDA drivers and container toolkit on GPU worker nodes?',
                        'options' => [
                            'A cluster-wide DaemonSet that injects kernel modules and device plugins onto labeled GPU nodes',
                            'A single ReplicaSet running on master control plane nodes',
                            'A CronJob running every 5 minutes',
                            'A StatefulSet mounted to host NFS storage'
                        ],
                        'correct' => 0,
                        'explanation' => 'The NVIDIA GPU Operator uses DaemonSets to ensure every node with GPU hardware has the necessary drivers, runtime, and monitoring plugins.'
                    ],
                    [
                        'q' => 'Which NVIDIA feature allows a single physical A100 or H100 80GB GPU to be partitioned into up to 7 hardware-isolated GPU instances with dedicated SMs and memory bandwidth?',
                        'options' => [
                            'CUDA Dynamic Parallelism',
                            'NVIDIA Multi-Instance GPU (MIG)',
                            'TensorRT-LLM Paged Memory',
                            'Unified Virtual Addressing (UVA)'
                        ],
                        'correct' => 1,
                        'explanation' => 'MIG partitions the GPU at the silicon level into isolated instances with predictable QoS and fault isolation.'
                    ],
                    [
                        'q' => 'Why is Kueue or Volcano preferred over the default Kubernetes kube-scheduler for distributed AI training jobs?',
                        'options' => [
                            'They provide Gang Scheduling, ensuring all worker pods of a distributed training job are scheduled simultaneously or none at all',
                            'They run PyTorch scripts directly in the kube-apiserver',
                            'They eliminate network communication between worker nodes',
                            'They convert Python code to Go binaries before execution'
                        ],
                        'correct' => 0,
                        'explanation' => 'Gang scheduling prevents distributed deadlocks where a subset of training pods occupies GPUs while waiting indefinitely for remaining pods.'
                    ],
                    [
                        'q' => 'In multi-node distributed training using RoCE v2 (RDMA over Converged Ethernet), which networking protocol must be configured on network switches to avoid packet loss?',
                        'options' => [
                            'PFC (Priority Flow Control) and ECN (Explicit Congestion Notification)',
                            'Standard round-robin DNS routing',
                            'Simple Spanning Tree Protocol (STP)',
                            'HTTP/2 Keep-Alive multiplexing'
                        ],
                        'correct' => 0,
                        'explanation' => 'RoCE v2 requires a lossless Ethernet fabric achieved using Priority Flow Control (PFC) and ECN to prevent packet drops during heavy AllReduce synchronization.'
                    ]
                ])
            ],
            [
                'title' => 'Advanced Python for AI & High-Performance Computing',
                'category' => 'aiml',
                'description' => 'Test low-level Python knowledge: CPython memory model, GIL mechanics, NumPy strides, multiprocessing IPC, and Cython/C-extension bindings.',
                'duration_mins' => 20,
                'questions_count' => 3,
                'difficulty' => 'Intermediate',
                'pass_score_pct' => 70,
                'questions_json' => json_encode([
                    [
                        'q' => 'How does NumPy achieve zero-copy array slicing and transposition?',
                        'options' => [
                            'By generating a new strided view sharing the original memory buffer with modified stride and shape tuples',
                            'By cloning the buffer using OS copy-on-write fork',
                            'By compressing array elements with gzip in-memory',
                            'By casting all floats to 8-bit integers'
                        ],
                        'correct' => 0,
                        'explanation' => 'NumPy views share the existing memory buffer and only modify the shape and stride metadata, resulting in O(1) time and zero extra memory.'
                    ],
                    [
                        'q' => 'When passing large PyTorch tensors between multiple multiprocessing workers in Python, what mechanism prevents expensive IPC serialization copies?',
                        'options' => [
                            'torch.multiprocessing shared memory (shm) handle passing',
                            'Standard pickle over TCP socket',
                            'Writing tensor data to temporary JSON files on disk',
                            'Base64 string encoding over stdin/stdout pipes'
                        ],
                        'correct' => 0,
                        'explanation' => 'torch.multiprocessing uses POSIX shared memory to pass memory references without copying tensor contents across process boundaries.'
                    ],
                    [
                        'q' => 'What is the primary advantage of Python 3.13 free-threaded mode (PEP 703)?',
                        'options' => [
                            'It allows multiple OS threads to execute pure Python bytecode in parallel across CPU cores without the Global Interpreter Lock (GIL)',
                            'It eliminates the need for garbage collection completely',
                            'It compiles all Python functions into WebAssembly',
                            'It automatically rewrites recursive code into iteration'
                        ],
                        'correct' => 0,
                        'explanation' => 'PEP 703 enables genuine multi-threaded parallelism in CPython by replacing GIL locks with biased reference counting and mimalloc.'
                    ]
                ])
            ]
        ];

        $assStmt = $pdo->prepare("INSERT INTO `assessments` (`title`, `category`, `description`, `duration_mins`, `questions_count`, `difficulty`, `pass_score_pct`, `questions_json`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($assessmentsData as $a) {
            $assStmt->execute([$a['title'], $a['category'], $a['description'], $a['duration_mins'], $a['questions_count'], $a['difficulty'], $a['pass_score_pct'], $a['questions_json']]);
        }

        
        $pdo->prepare("INSERT INTO `assessment_attempts` (`user_id`, `assessment_id`, `assessment_title`, `score_pct`, `status`, `benchmark_percentile`, `completed_at`) VALUES 
            (1, 4, 'Advanced Python for AI & High-Performance Computing', 94, 'Passed', 'Top 4%', NOW() - INTERVAL 10 MINUTE),
            (1, 2, 'Transformer Architectures & Attention Deep Dive', 88, 'Passed', 'Top 8%', NOW() - INTERVAL 3 DAY),
            (1, 3, 'Kubernetes for AI Workloads & GPU Orchestration', 82, 'Passed', 'Top 15%', NOW() - INTERVAL 8 DAY)
        ")->execute();

        
        $coursesData = [
            [
                'title' => 'System Design for Generative AI Applications',
                'category' => 'genai',
                'match_pct' => 98,
                'duration_hours' => 14,
                'labs_count' => 8,
                'rating' => 4.9,
                'reviews_count' => 1240,
                'description' => 'Architect low-latency enterprise RAG pipelines, vector caching layers, semantic routers, and multi-agent coordination frameworks.',
                'level_label' => 'Advanced',
                'icon_emoji' => '🤖'
            ],
            [
                'title' => 'Production Kubernetes for AI Workloads',
                'category' => 'mlops',
                'match_pct' => 94,
                'duration_hours' => 18,
                'labs_count' => 12,
                'rating' => 4.8,
                'reviews_count' => 890,
                'description' => 'Deploy GPU clusters, configure NVIDIA GPU Operator, batch scheduling with Kueue, and autoscaling LLM inference with KServe.',
                'level_label' => 'Advanced',
                'icon_emoji' => '☸️'
            ],
            [
                'title' => 'FlashAttention-2 & Custom Triton CUDA Kernels',
                'category' => 'deeplearning',
                'match_pct' => 95,
                'duration_hours' => 22,
                'labs_count' => 10,
                'rating' => 4.95,
                'reviews_count' => 640,
                'description' => 'Write custom GPU SRAM-optimized fused kernels using OpenAI Triton to accelerate transformer training and inference by 3x.',
                'level_label' => 'Expert',
                'icon_emoji' => '⚡'
            ],
            [
                'title' => 'Distributed PyTorch: DeepSpeed ZeRO & Megatron-LM',
                'category' => 'mlops',
                'match_pct' => 91,
                'duration_hours' => 20,
                'labs_count' => 9,
                'rating' => 4.85,
                'reviews_count' => 780,
                'description' => 'Master 3D parallelism (Tensor, Pipeline, Data Parallelism), ZeRO memory partitioning, and FP8 mixed precision on multi-node clusters.',
                'level_label' => 'Advanced',
                'icon_emoji' => '🚀'
            ],
            [
                'title' => 'High-Throughput LLM Serving with vLLM & TensorRT-LLM',
                'category' => 'arch',
                'match_pct' => 96,
                'duration_hours' => 16,
                'labs_count' => 7,
                'rating' => 4.9,
                'reviews_count' => 1120,
                'description' => 'Implement continuous batching, PagedAttention KV-cache management, speculative decoding, and FP4 quantization for production LLMs.',
                'level_label' => 'Expert',
                'icon_emoji' => '📈'
            ],
            [
                'title' => 'Enterprise AI Governance, Red Teaming & Guardrails',
                'category' => 'genai',
                'match_pct' => 89,
                'duration_hours' => 12,
                'labs_count' => 5,
                'rating' => 4.75,
                'reviews_count' => 520,
                'description' => 'Build automated security guardrails against prompt injection, jailbreaking, differential privacy leaks, and EU AI Act compliance.',
                'level_label' => 'Intermediate',
                'icon_emoji' => '🛡️'
            ]
        ];

        $crsStmt = $pdo->prepare("INSERT INTO `courses` (`title`, `category`, `match_pct`, `duration_hours`, `labs_count`, `rating`, `reviews_count`, `description`, `level_label`, `icon_emoji`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($coursesData as $c) {
            $crsStmt->execute([$c['title'], $c['category'], $c['match_pct'], $c['duration_hours'], $c['labs_count'], $c['rating'], $c['reviews_count'], $c['description'], $c['level_label'], $c['icon_emoji']]);
        }

        
        $pdo->prepare("INSERT INTO `enrollments` (`user_id`, `course_id`, `progress_pct`, `status`) VALUES 
            (1, 1, 65, 'in_progress'),
            (1, 3, 30, 'in_progress')
        ")->execute();

        
        $stages = [
            [
                'stage_number' => 1,
                'title' => 'Stage 1: Mathematical Foundations & High-Performance Python',
                'description' => 'Linear algebra, multivariate calculus, optimization algorithms, and CPython profiling.',
                'status' => 'completed',
                'progress_pct' => 100,
                'modules' => [
                    ['title' => 'Vector & Matrix Calculus for Deep Learning', 'subtitle' => 'Jacobians, Hessians, and automatic differentiation engines', 'duration_mins' => 180, 'is_completed' => 1],
                    ['title' => 'Advanced NumPy & Vectorized Computations', 'subtitle' => 'Memory layouts, strides, broadcasting, and Cython compilation', 'duration_mins' => 240, 'is_completed' => 1],
                    ['title' => 'CPython Internals, Memory Management & GIL', 'subtitle' => 'Reference counting, cycle detection, and multiprocessing', 'duration_mins' => 210, 'is_completed' => 1]
                ]
            ],
            [
                'stage_number' => 2,
                'title' => 'Stage 2: Deep Learning Internals & Transformer Architectures',
                'description' => 'Autograd engine from scratch, attention mechanisms, rotary embeddings, and PyTorch internals.',
                'status' => 'in_progress',
                'progress_pct' => 65,
                'modules' => [
                    ['title' => 'Building an Autograd Engine from Scratch in Python', 'subtitle' => 'Computational DAGs, backprop topological sort, and tape-based autodiff', 'duration_mins' => 300, 'is_completed' => 1],
                    ['title' => 'Multi-Head Attention & KV Caching Mechanics', 'subtitle' => 'Scaled dot-product attention, masking, and memory footprints', 'duration_mins' => 270, 'is_completed' => 1],
                    ['title' => 'Modern Tokenization & Rotary Position Embeddings (RoPE)', 'subtitle' => 'Byte-Pair Encoding (BPE), TikToken, and rotational embeddings', 'duration_mins' => 240, 'is_completed' => 1],
                    ['title' => 'Multi-Head Self-Attention in PyTorch', 'subtitle' => 'CUDA tensor operations, forward/backward implementations, hands-on lab', 'duration_mins' => 180, 'is_completed' => 0],
                    ['title' => 'Transformer Block Normalization & Residual Streams', 'subtitle' => 'Pre-LN vs Post-LN, RMSNorm, and residual scale stabilization', 'duration_mins' => 200, 'is_completed' => 0]
                ]
            ],
            [
                'stage_number' => 3,
                'title' => 'Stage 3: Distributed Training & Large-Scale MLOps',
                'description' => 'Multi-GPU scaling, ZeRO optimizer partitioning, Megatron tensor parallelism, and Slurm clusters.',
                'status' => 'locked',
                'progress_pct' => 0,
                'modules' => [
                    ['title' => 'PyTorch Distributed Data Parallel (DDP) Internals', 'subtitle' => 'NCCL collective communications, Ring-AllReduce, and bucket tuning', 'duration_mins' => 240, 'is_completed' => 0],
                    ['title' => 'DeepSpeed ZeRO-1, 2, and 3 Memory Sharding', 'subtitle' => 'Eliminating redundant optimizer and parameter states', 'duration_mins' => 320, 'is_completed' => 0],
                    ['title' => 'Megatron-LM 3D Parallelism', 'subtitle' => 'Tensor, pipeline, and data parallel coordination for 70B+ models', 'duration_mins' => 360, 'is_completed' => 0]
                ]
            ],
            [
                'stage_number' => 4,
                'title' => 'Stage 4: Enterprise LLM Architecture & Production Guardrails',
                'description' => 'Continuous batching, vLLM PagedAttention, speculative decoding, and safety red teaming.',
                'status' => 'locked',
                'progress_pct' => 0,
                'modules' => [
                    ['title' => 'vLLM & PagedAttention Dynamic Memory Management', 'subtitle' => 'Sub-millisecond token generation and KV cache eviction', 'duration_mins' => 280, 'is_completed' => 0],
                    ['title' => 'Speculative Decoding with Draft Verification Models', 'subtitle' => '2.5x inference speedup without precision degradation', 'duration_mins' => 220, 'is_completed' => 0],
                    ['title' => 'Enterprise Guardrails & Red-Teaming Defense', 'subtitle' => 'Mitigating indirect prompt injections and hallucination cascades', 'duration_mins' => 260, 'is_completed' => 0]
                ]
            ]
        ];

        $stageStmt = $pdo->prepare("INSERT INTO `learning_path_stages` (`user_id`, `stage_number`, `title`, `description`, `status`, `progress_pct`) VALUES (?, ?, ?, ?, ?, ?)");
        $modStmt = $pdo->prepare("INSERT INTO `learning_path_modules` (`stage_id`, `title`, `subtitle`, `duration_mins`, `is_completed`, `order_num`) VALUES (?, ?, ?, ?, ?, ?)");

        foreach ($stages as $stg) {
            $stageStmt->execute([1, $stg['stage_number'], $stg['title'], $stg['description'], $stg['status'], $stg['progress_pct']]);
            $stgId = $pdo->lastInsertId();
            $order = 1;
            foreach ($stg['modules'] as $m) {
                $modStmt->execute([$stgId, $m['title'], $m['subtitle'], $m['duration_mins'], $m['is_completed'], $order++]);
            }
        }

        
        $recs = [
            [
                'title' => 'Production Kubernetes for AI Workloads',
                'category' => 'MLOps & GPU Scheduling',
                'match_pct' => 96,
                'reason_text' => 'Directly bridges your highest-weighted target role gap (-26% in Distributed GPU Scheduling).',
                'duration_hours' => 18,
                'case_studies_count' => 6,
                'status' => 'active',
                'priority_tag' => 'Critical Priority'
            ],
            [
                'title' => 'Enterprise AI Governance, Red Teaming & Guardrails',
                'category' => 'AI Safety & Security',
                'match_pct' => 94,
                'reason_text' => 'Standard prerequisite for Architect-level roles in financial and enterprise AI sectors.',
                'duration_hours' => 12,
                'case_studies_count' => 4,
                'status' => 'active',
                'priority_tag' => 'High Match'
            ],
            [
                'title' => 'Triton Custom Kernels & FlashAttention-2',
                'category' => 'GPU Kernel Engineering',
                'match_pct' => 91,
                'reason_text' => 'Highly valued skill that compliments your 96% PyTorch mastery level.',
                'duration_hours' => 22,
                'case_studies_count' => 10,
                'status' => 'active',
                'priority_tag' => 'Recommended'
            ]
        ];

        $recStmt = $pdo->prepare("INSERT INTO `recommendations` (`user_id`, `title`, `category`, `match_pct`, `reason_text`, `duration_hours`, `case_studies_count`, `status`, `priority_tag`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($recs as $r) {
            $recStmt->execute([1, $r['title'], $r['category'], $r['match_pct'], $r['reason_text'], $r['duration_hours'], $r['case_studies_count'], $r['status'], $r['priority_tag']]);
        }

        
        $pdo->prepare("INSERT INTO `notifications` (`user_id`, `title`, `message`, `time_ago`, `is_read`, `type`) VALUES 
            (1, 'Assessment Completed!', 'You scored 94% on Advanced Python for AI.', '10 mins ago', 0, 'success'),
            (1, 'New Recommendation', 'System Design for LLMs matches your target role (Lead AI Architect).', '2 hours ago', 0, 'info'),
            (1, 'Streak Milestone', 'You have maintained a 14-day active learning streak.', 'Yesterday', 0, 'streak')
        ")->execute();

        
        $pdo->prepare("INSERT INTO `activity_log` (`user_id`, `activity_type`, `title`, `description`, `xp_gained`) VALUES 
            (1, 'assessment', 'Completed Advanced Python for AI Assessment', 'Scored 94% • Ranked in top 4% of enterprise candidates', 150),
            (1, 'learning', 'Completed Module: TikToken & RoPE Embeddings', 'Stage 2 &bull; Deep Learning Internals', 50),
            (1, 'streak', 'Maintained 14-Day Continuous Active Streak', 'Unlocked Gold Flame Streak Badge', 100),
            (1, 'course', 'Enrolled in System Design for Generative AI Applications', '8 hands-on labs • 14 learning hours', 50)
        ")->execute();

        
        $pdo->prepare("INSERT INTO `chat_messages` (`user_id`, `sender`, `message_html`) VALUES 
            (1, 'assistant', 'Hello Alex! I am your Skill Intelligence Copilot. Based on your current 78% readiness for <strong>Lead AI Architect</strong>, your quickest path to 90%+ is mastering <em>Distributed GPU Scheduling (Kueue/K8s)</em> and <em>Enterprise AI Governance</em>. How can I assist your learning today?')
        ")->execute();
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Database skill_intelligence initialized and seeded successfully.',
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
