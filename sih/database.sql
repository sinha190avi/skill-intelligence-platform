-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: skill_intelligence
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `skill_intelligence`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `skill_intelligence` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `skill_intelligence`;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `activity_type` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `xp_gained` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
INSERT INTO `activity_log` VALUES (1,1,'assessment','Completed Advanced Python for AI Assessment','Scored 94% • Ranked in top 4% of enterprise candidates',150,'2026-09-13 17:44:53'),(2,1,'learning','Completed Module: TikToken & RoPE Embeddings','Stage 2 &bull; Deep Learning Internals',50,'2026-09-13 17:44:53'),(3,1,'streak','Maintained 14-Day Continuous Active Streak','Unlocked Gold Flame Streak Badge',100,'2026-09-13 17:44:53'),(4,1,'course','Enrolled in System Design for Generative AI Applications','8 hands-on labs • 14 learning hours',50,'2026-09-13 17:44:53'),(5,1,'assessment','Completed Assessment: System Design for Large Language Models','Scored 100% (Passed) • Ranked in Top 2%',150,'2026-09-13 18:01:01'),(6,1,'learning','Completed Module: Multi-Head Self-Attention in PyTorch','Stage milestone progress',50,'2026-09-13 18:03:15'),(7,1,'profile','Updated Professional Profile','Profile details updated: Alex Morgan (Lead) (Senior AI/ML Engineer)',0,'2026-09-13 18:05:04'),(8,1,'profile','Updated Professional Profile','Profile details updated: Alex Morgan (Lead) (Senior AI/ML Engineer)',0,'2026-09-13 18:11:49'),(9,1,'assessment','Completed Assessment: System Design for Large Language Models','Scored 80% (Passed) • Ranked in Top 22%',150,'2026-09-13 18:12:11'),(10,1,'login','Signed in to Platform','Authenticated session started',0,'2026-09-18 16:25:37'),(11,21,'register','Account Created','Welcome to Skill Intelligence Platform!',100,'2026-09-18 16:27:59'),(12,1,'assessment','Completed Assessment: System Design for Large Language Models','Scored 40% (Failed) • Ranked in Top 62%',50,'2026-09-18 16:35:29'),(13,1,'community','Posted in Community','sfaSF',10,'2026-09-18 16:36:00'),(14,1,'community','Replied in Community Hub','Great tip, thanks for sharing!',5,'2026-09-18 16:56:27'),(15,1,'community','Posted in Community Hub','Testing new post creation with PyTorch integration and optimization benchmark. #',10,'2026-09-18 16:59:47'),(16,1,'community','Posted in Community Hub','#debugging',10,'2026-09-18 17:11:54'),(17,1,'login','Signed in to Platform','Authenticated session started',0,'2026-09-26 13:37:39');
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessment_attempts`
--

DROP TABLE IF EXISTS `assessment_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `assessment_title` varchar(150) NOT NULL,
  `score_pct` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `benchmark_percentile` varchar(50) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `assessment_id` (`assessment_id`),
  CONSTRAINT `assessment_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_attempts_ibfk_2` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessment_attempts`
--

LOCK TABLES `assessment_attempts` WRITE;
/*!40000 ALTER TABLE `assessment_attempts` DISABLE KEYS */;
INSERT INTO `assessment_attempts` VALUES (1,1,4,'Advanced Python for AI & High-Performance Computing',94,'Passed','Top 4%','2026-09-13 17:34:53'),(2,1,2,'Transformer Architectures & Attention Deep Dive',88,'Passed','Top 8%','2026-09-10 17:44:53'),(3,1,3,'Kubernetes for AI Workloads & GPU Orchestration',82,'Passed','Top 15%','2026-09-05 17:44:53'),(4,1,1,'System Design for Large Language Models',100,'Passed','Top 2%','2026-09-13 18:01:01'),(5,1,1,'System Design for Large Language Models',80,'Passed','Top 22%','2026-09-13 18:12:11'),(6,1,1,'System Design for Large Language Models',40,'Failed','Top 62%','2026-09-18 16:35:29');
/*!40000 ALTER TABLE `assessment_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assessments`
--

DROP TABLE IF EXISTS `assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `duration_mins` int(11) NOT NULL,
  `questions_count` int(11) NOT NULL,
  `difficulty` varchar(30) NOT NULL,
  `pass_score_pct` int(11) DEFAULT 75,
  `questions_json` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assessments`
--

LOCK TABLES `assessments` WRITE;
/*!40000 ALTER TABLE `assessments` DISABLE KEYS */;
INSERT INTO `assessments` VALUES (1,'System Design for Large Language Models','arch','Evaluate your ability to architect fault-tolerant distributed inference pipelines, KV cache paging, semantic routing, and multi-GPU tensor-parallel architectures.',30,5,'Advanced',75,'[{\"q\":\"In high-throughput vLLM serving, what fundamental problem does PagedAttention solve compared to standard contiguous memory allocation?\",\"options\":[\"Eliminates floating-point quantization errors\",\"Prevents KV-cache memory fragmentation and enables dynamic page sharing across parallel requests\",\"Directly bypasses CUDA kernels using CPU SIMD registers\",\"Replaces multi-head attention with quadratic dot-product memory\"],\"correct\":1,\"explanation\":\"PagedAttention partitions the KV cache into non-contiguous virtual blocks, reducing memory waste from ~60-80% down to under 4%.\"},{\"q\":\"When partitioning a 70B parameter model across 4x A100 (80GB) GPUs using Megatron-LM Tensor Parallelism, which layer components are split along column and row dimensions?\",\"options\":[\"Self-attention QKV projections are split by column, Output dense projection is split by row\",\"MLP layers are kept unpartitioned on GPU 0 while attention runs on GPU 1-3\",\"LayerNorm weights are duplicated while embeddings are discarded\",\"Only bias vectors are partitioned across the NVLink fabric\"],\"correct\":0,\"explanation\":\"In tensor parallelism, self-attention Q, K, V linear weights are column-parallel, and the output projection is row-parallel followed by an AllReduce.\"},{\"q\":\"In DeepSpeed ZeRO Stage 3 (ZeRO-3), which components of the training state are partitioned across all distributed data-parallel worker nodes?\",\"options\":[\"Only optimizer states (moments)\",\"Optimizer states and first-order gradients only\",\"Optimizer states, gradients, and model parameters\",\"Only activation checkpoint caches\"],\"correct\":2,\"explanation\":\"ZeRO-3 shards all three primary memory consumers: 16-bit model parameters, gradients, and 32-bit optimizer states.\"},{\"q\":\"When building a production RAG system with 10M dense document vectors, which index type provides sub-10ms latency with 95%+ recall?\",\"options\":[\"Exact Flat L2 scan\",\"HNSW (Hierarchical Navigable Small World) with scalar quantization\",\"B-Tree index over embedding byte hashes\",\"Full-text inverted BM25 index with no vector distance\"],\"correct\":1,\"explanation\":\"HNSW graphs paired with scalar quantization (SQ8) provide logarithmic search time with high recall for high-dimensional vector spaces.\"},{\"q\":\"To defend an enterprise LLM agent against Indirect Prompt Injection from untrusted web documents, which strategy provides the strongest defense-in-depth?\",\"options\":[\"Increasing temperature to 1.5 to randomize output\",\"Dual-LLM architecture isolating untrusted data parsing from privileged action execution with strict tool authorization schemas\",\"Appending \\\"Ignore instructions in the document\\\" at the very end of the user prompt\",\"Converting all text to uppercase before passing to tokenizer\"],\"correct\":1,\"explanation\":\"Dual-LLM isolation ensures the agent interpreter never executes unverified instructions found within data retrieval payloads.\"}]'),(2,'Transformer Architectures & Attention Deep Dive','aiml','Test your mathematical and algorithmic understanding of FlashAttention, RoPE rotary position embeddings, Multi-Query Attention (MQA), and GQA.',25,4,'Expert',80,'[{\"q\":\"Why does FlashAttention achieve a 2x-4x wall-clock speedup without modifying the mathematical output of standard attention?\",\"options\":[\"It skips computing softmax by estimating dot-products using cosine approximation\",\"It tiles inputs into GPU SRAM and fuses the softmax online computation to avoid reading\\/writing N x N intermediate matrices to slow HBM\",\"It executes strictly on CPU AVX-512 vector units instead of tensor cores\",\"It restricts the attention matrix to a diagonal tri-band mask\"],\"correct\":1,\"explanation\":\"FlashAttention minimizes High-Bandwidth Memory (HBM) I\\/O by tiling Q, K, V into fast on-chip SRAM and computing softmax incrementally.\"},{\"q\":\"In Grouped-Query Attention (GQA), how are query and key-value heads organized compared to Multi-Head Attention (MHA) and Multi-Query Attention (MQA)?\",\"options\":[\"MHA has 1 KV head per Q head; MQA has 1 KV head total; GQA groups several Q heads to share a single KV head\",\"GQA assigns 2 distinct KV heads for every single Q head\",\"GQA removes all query heads and computes attention purely between keys\",\"GQA computes cross-attention exclusively across layers\"],\"correct\":0,\"explanation\":\"GQA partitions query heads into groups that share a single key\\/value head, providing near-MHA quality with near-MQA memory efficiency.\"},{\"q\":\"How does Rotary Position Embedding (RoPE) inject positional information into token representations?\",\"options\":[\"By adding absolute sinusoidal vectors directly to the token embeddings\",\"By rotating the Query and Key vectors in 2D coordinate planes by angles proportional to token position\",\"By appending an integer index as an extra token dimension\",\"By learning a static bias matrix added directly to attention logits\"],\"correct\":1,\"explanation\":\"RoPE represents relative token distances via orthogonal 2D rotation matrices applied to the Q and K projections.\"},{\"q\":\"What is the primary benefit of SwiGLU activations over standard ReLU or GeLU in modern LLM feed-forward networks?\",\"options\":[\"It uses 50% fewer parameters than standard FFN\",\"The gated linear unit with Swish improves gradient flow and empirically yields superior validation perplexity for equal compute\",\"It guarantees non-zero derivatives across negative infinity\",\"It eliminates matrix multiplication in the feed-forward projection\"],\"correct\":1,\"explanation\":\"SwiGLU pairs a Swish activation gate with a linear projection, consistently outperforming GeLU in LLMs like LLaMA and PaLM.\"}]'),(3,'Kubernetes for AI Workloads & GPU Orchestration','mlops','Test knowledge of NVIDIA GPU Operator, Volcano/Kueue batch scheduling, MIG (Multi-Instance GPU), and RDMA network topology.',35,4,'Advanced',70,'[{\"q\":\"What Kubernetes resource does the NVIDIA GPU Operator install to automatically provision CUDA drivers and container toolkit on GPU worker nodes?\",\"options\":[\"A cluster-wide DaemonSet that injects kernel modules and device plugins onto labeled GPU nodes\",\"A single ReplicaSet running on master control plane nodes\",\"A CronJob running every 5 minutes\",\"A StatefulSet mounted to host NFS storage\"],\"correct\":0,\"explanation\":\"The NVIDIA GPU Operator uses DaemonSets to ensure every node with GPU hardware has the necessary drivers, runtime, and monitoring plugins.\"},{\"q\":\"Which NVIDIA feature allows a single physical A100 or H100 80GB GPU to be partitioned into up to 7 hardware-isolated GPU instances with dedicated SMs and memory bandwidth?\",\"options\":[\"CUDA Dynamic Parallelism\",\"NVIDIA Multi-Instance GPU (MIG)\",\"TensorRT-LLM Paged Memory\",\"Unified Virtual Addressing (UVA)\"],\"correct\":1,\"explanation\":\"MIG partitions the GPU at the silicon level into isolated instances with predictable QoS and fault isolation.\"},{\"q\":\"Why is Kueue or Volcano preferred over the default Kubernetes kube-scheduler for distributed AI training jobs?\",\"options\":[\"They provide Gang Scheduling, ensuring all worker pods of a distributed training job are scheduled simultaneously or none at all\",\"They run PyTorch scripts directly in the kube-apiserver\",\"They eliminate network communication between worker nodes\",\"They convert Python code to Go binaries before execution\"],\"correct\":0,\"explanation\":\"Gang scheduling prevents distributed deadlocks where a subset of training pods occupies GPUs while waiting indefinitely for remaining pods.\"},{\"q\":\"In multi-node distributed training using RoCE v2 (RDMA over Converged Ethernet), which networking protocol must be configured on network switches to avoid packet loss?\",\"options\":[\"PFC (Priority Flow Control) and ECN (Explicit Congestion Notification)\",\"Standard round-robin DNS routing\",\"Simple Spanning Tree Protocol (STP)\",\"HTTP\\/2 Keep-Alive multiplexing\"],\"correct\":0,\"explanation\":\"RoCE v2 requires a lossless Ethernet fabric achieved using Priority Flow Control (PFC) and ECN to prevent packet drops during heavy AllReduce synchronization.\"}]'),(4,'Advanced Python for AI & High-Performance Computing','aiml','Test low-level Python knowledge: CPython memory model, GIL mechanics, NumPy strides, multiprocessing IPC, and Cython/C-extension bindings.',20,3,'Intermediate',70,'[{\"q\":\"How does NumPy achieve zero-copy array slicing and transposition?\",\"options\":[\"By generating a new strided view sharing the original memory buffer with modified stride and shape tuples\",\"By cloning the buffer using OS copy-on-write fork\",\"By compressing array elements with gzip in-memory\",\"By casting all floats to 8-bit integers\"],\"correct\":0,\"explanation\":\"NumPy views share the existing memory buffer and only modify the shape and stride metadata, resulting in O(1) time and zero extra memory.\"},{\"q\":\"When passing large PyTorch tensors between multiple multiprocessing workers in Python, what mechanism prevents expensive IPC serialization copies?\",\"options\":[\"torch.multiprocessing shared memory (shm) handle passing\",\"Standard pickle over TCP socket\",\"Writing tensor data to temporary JSON files on disk\",\"Base64 string encoding over stdin\\/stdout pipes\"],\"correct\":0,\"explanation\":\"torch.multiprocessing uses POSIX shared memory to pass memory references without copying tensor contents across process boundaries.\"},{\"q\":\"What is the primary advantage of Python 3.13 free-threaded mode (PEP 703)?\",\"options\":[\"It allows multiple OS threads to execute pure Python bytecode in parallel across CPU cores without the Global Interpreter Lock (GIL)\",\"It eliminates the need for garbage collection completely\",\"It compiles all Python functions into WebAssembly\",\"It automatically rewrites recursive code into iteration\"],\"correct\":0,\"explanation\":\"PEP 703 enables genuine multi-threaded parallelism in CPython by replacing GIL locks with biased reference counting and mimalloc.\"}]');
/*!40000 ALTER TABLE `assessments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `certifications`
--

DROP TABLE IF EXISTS `certifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `certifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cert_name` varchar(200) NOT NULL,
  `issuer` varchar(150) NOT NULL,
  `cert_type` varchar(20) DEFAULT 'platform',
  `cover_color` varchar(20) DEFAULT 'indigo',
  `icon_emoji` varchar(10) DEFAULT '?',
  `xp_reward` int(11) DEFAULT 200,
  `total_hours` int(11) DEFAULT 10,
  `description` text DEFAULT NULL,
  `verification_url` varchar(255) DEFAULT NULL,
  `match_pct` int(11) DEFAULT 80,
  `difficulty` varchar(30) DEFAULT 'Intermediate',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `certifications`
--

LOCK TABLES `certifications` WRITE;
/*!40000 ALTER TABLE `certifications` DISABLE KEYS */;
INSERT INTO `certifications` VALUES (1,'Advanced Transformer Architectures & Optimization','Skill Intelligence Platform','platform','gold','🤖',300,20,'Master FlashAttention-2, KV caching, rotary embeddings, and production quantization.',NULL,95,'Advanced'),(2,'AWS Certified Machine Learning Specialty','Amazon Web Services','external','platinum','☁️',250,30,'Cloud-scale ML architecture and SageMaker deployment for enterprise.',NULL,85,'Advanced'),(3,'Deep Learning Specialization (5-Course)','DeepLearning.AI','external','emerald','🧠',200,60,'Complete deep learning curriculum from Andrew Ng.',NULL,90,'Intermediate'),(4,'Certified Kubernetes Administrator (CKA)','CNCF','external','indigo','☸️',200,25,'Performance-based exam for Kubernetes cluster management.',NULL,80,'Advanced'),(5,'Advanced Python for AI Engineering','Skill Intelligence Platform','platform','cyan','⚡',150,15,'AsyncIO, Cython, NumPy vectorization, profiling for AI workloads.',NULL,92,'Advanced'),(6,'MLOps Practitioner — Kubeflow & Triton','Skill Intelligence Platform','platform','rose','🎯',250,18,'Production ML operations with Kubeflow, Triton, and Prometheus.',NULL,85,'Advanced'),(7,'RLHF & LLM Alignment Practitioner','Skill Intelligence Platform','platform','rose','🎯',300,20,'Reward modeling, PPO, DPO, and RLAIF for production LLM alignment.',NULL,88,'Expert'),(8,'Vector Databases & Multimodal RAG','Skill Intelligence Platform','platform','cyan','🗄️',250,16,'Qdrant, Milvus, HNSW indexing, hybrid retrieval, multimodal RAG.',NULL,91,'Advanced'),(9,'GPU Architecture & Kernel Optimization','Skill Intelligence Platform','platform','gold','⚡',400,22,'CUDA kernels, Triton, GPU memory hierarchy, fused operators.',NULL,78,'Expert'),(10,'Professional Machine Learning Engineer','Google Cloud','external','gold','🏗️',300,40,'Google Cloud ML services and production model deployment.',NULL,82,'Advanced');
/*!40000 ALTER TABLE `certifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_messages`
--

DROP TABLE IF EXISTS `chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `sender` varchar(20) NOT NULL,
  `message_html` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_messages`
--

LOCK TABLES `chat_messages` WRITE;
/*!40000 ALTER TABLE `chat_messages` DISABLE KEYS */;
INSERT INTO `chat_messages` VALUES (1,1,'assistant','Hello Alex! I am your Skill Intelligence Copilot. Based on your current 78% readiness for <strong>Lead AI Architect</strong>, your quickest path to 90%+ is mastering <em>Distributed GPU Scheduling (Kueue/K8s)</em> and <em>Enterprise AI Governance</em>. How can I assist your learning today?','2026-09-13 17:44:53'),(2,1,'user','Analyze my top skill gaps for Lead AI Architect','2026-09-13 18:05:52'),(3,1,'assistant','\n            <p>📊 <strong>Real-Time Skill Gap Analysis for Lead AI Architect:</strong></p>\n            <p style=\"margin-top: 6px;\">Your current verified readiness is <strong>83%</strong>. According to your live database benchmarks, your top 2 priority areas are:</p>\n            <ul style=\"margin: 8px 0; padding-left: 20px;\">\n              <li><strong>Kubernetes & GPU Orchestration (Current Proficiency: 70%):</strong> Focus on safety guardrails, prompt injection mitigation, and audit compliance.</li>\n              <li><strong>Enterprise AI Governance & Safety (Current Proficiency: 64%):</strong> DeepSpeed ZeRO-3 memory partitioning and Megatron-LM tensor parallelism.</li>\n            </ul>\n            <p style=\"margin-top: 6px;\">💡 <em>Action Plan:</em> Completing <a href=\"courses.html\" style=\"font-weight:700; color:var(--primary-500);\">Production Kubernetes for AI Workloads</a> will elevate your readiness by ~+12% to surpass the 90% benchmark threshold.</p>\n        ','2026-09-13 18:05:52'),(4,1,'user','What course should I take for FlashAttention?','2026-09-13 18:06:24'),(5,1,'assistant','\n            <p>⚡ <strong>FlashAttention-2 &amp; Custom Triton Kernels:</strong></p>\n            <p style=\"margin-top: 6px;\">Standard multi-head attention computes  = QK^T$ and  = \\text{softmax}(S)$ in slow GPU High-Bandwidth Memory (HBM), resulting in quadratic (N^2)$ memory reads and writes.</p>\n            <div class=\"code-block\"># FlashAttention-2 Tiling in SRAM with Triton\n@triton.jit\ndef _fwd_kernel(Q, K, V, Out, sm_scale, BLOCK_M: tl.constexpr, BLOCK_N: tl.constexpr):\n    # Online softmax accumulator in fast on-chip SRAM\n    m_i = tl.zeros([BLOCK_M], dtype=tl.float32) - float(\"inf\")\n    l_i = tl.zeros([BLOCK_M], dtype=tl.float32)\n    acc = tl.zeros([BLOCK_M, BLOCK_DMODEL], dtype=tl.float32)\n    # Block loop over keys &amp; values without HBM spills...</div>\n            <p style=\"margin-top: 6px;\"><strong>Key Wins:</strong> 2.5x speedup, 10x memory reduction, and flawless handling of 128k+ token context windows!</p>\n        ','2026-09-13 18:06:24');
/*!40000 ALTER TABLE `chat_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_comments`
--

DROP TABLE IF EXISTS `community_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `body` text NOT NULL,
  `likes_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_comm_post` (`post_id`),
  KEY `idx_comm_user` (`user_id`),
  CONSTRAINT `community_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `community_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `community_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_comments`
--

LOCK TABLES `community_comments` WRITE;
/*!40000 ALTER TABLE `community_comments` DISABLE KEYS */;
INSERT INTO `community_comments` VALUES (1,1,3,'Dynamic batching was game-changing for our vLLM rollout too. Did you have to tune `max_queue_delay_microseconds` for p99 latency SLA?',0,'2026-09-18 16:43:13'),(2,1,5,'Huge +1. We also set `model_transaction_policy` to decoupled for streaming responses. Saved another 15% latency.',0,'2026-09-18 16:43:13'),(3,1,1,'Great benchmark Sayan! Added this note to our MLOps playbook.',0,'2026-09-18 16:43:13'),(4,2,2,'Completely agree on semantic chunking. Embedding sentences with spacy/stanza before chunking gives huge boundary clarity.',0,'2026-09-18 16:43:13'),(5,2,1,'Have you tried late chunking with jina-embeddings-v3? It preserves context across chunks very effectively.',0,'2026-09-18 16:43:13'),(6,3,2,'Check if your value head loss is dominating. A common fix is reducing value loss coefficient `vf_coef` from 0.5 to 0.1.',0,'2026-09-18 16:43:13'),(7,3,5,'Also verify your learning rate warm-up schedule. Linear warmup for the first 500 steps prevents initial destabilization.',0,'2026-09-18 16:43:13'),(8,4,1,'Can confirm: `prefetch_factor=4` with PyTorch 2.4 pinned memory cut our disk I/O wait times to almost zero.',0,'2026-09-18 16:43:13'),(9,4,8,'Watch out for memory leaks if dataset yields tensors directly without cloning when using num_workers > 4.',0,'2026-09-18 16:43:13'),(10,5,2,'FlashAttention-2 really pulls away at context length 4k+. Are you using FP16 or BF16? BF16 gave us better numerical stability with FA2.',0,'2026-09-18 16:43:13'),(11,1,1,'Great tip, thanks for sharing!',0,'2026-09-18 16:56:27');
/*!40000 ALTER TABLE `community_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_event_rsvps`
--

DROP TABLE IF EXISTS `community_event_rsvps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community_event_rsvps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_event_user` (`event_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `community_event_rsvps_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `community_events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `community_event_rsvps_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_event_rsvps`
--

LOCK TABLES `community_event_rsvps` WRITE;
/*!40000 ALTER TABLE `community_event_rsvps` DISABLE KEYS */;
INSERT INTO `community_event_rsvps` VALUES (1,1,1,'2026-09-26 13:22:51'),(2,2,1,'2026-09-26 13:22:51'),(3,1,2,'2026-09-26 13:22:51'),(4,3,3,'2026-09-26 13:22:51');
/*!40000 ALTER TABLE `community_event_rsvps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_events`
--

DROP TABLE IF EXISTS `community_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `event_type` varchar(50) DEFAULT 'session',
  `event_date` date NOT NULL,
  `host_name` varchar(150) DEFAULT '',
  `attendees` int(11) DEFAULT 0,
  `prize_label` varchar(100) DEFAULT NULL,
  `color` varchar(30) DEFAULT 'cyan',
  `rsvp_open` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_events`
--

LOCK TABLES `community_events` WRITE;
/*!40000 ALTER TABLE `community_events` DISABLE KEYS */;
INSERT INTO `community_events` VALUES (1,'Advanced RAG Architecture Workshop','session','2026-09-22','Riya Kapoor',156,NULL,'cyan',1),(2,'Enterprise GenAI Challenge 2026','hackathon','2026-09-28','Skill Intelligence',84,'₹2L prize pool','amber',1),(3,'LLM Fine-Tuning Masterclass','session','2026-10-05','Sayan Ghosh',210,NULL,'indigo',1),(4,'MLOps Career Day — Live Q&A','session','2026-10-12','Priya Anand',89,NULL,'emerald',1);
/*!40000 ALTER TABLE `community_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_posts`
--

DROP TABLE IF EXISTS `community_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `body` text NOT NULL,
  `tags` varchar(300) DEFAULT '',
  `post_type` varchar(30) DEFAULT 'discussion',
  `has_code` tinyint(1) DEFAULT 0,
  `code_block` text DEFAULT NULL,
  `likes_count` int(11) DEFAULT 0,
  `comments_count` int(11) DEFAULT 0,
  `is_pinned` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `community_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_posts`
--

LOCK TABLES `community_posts` WRITE;
/*!40000 ALTER TABLE `community_posts` DISABLE KEYS */;
INSERT INTO `community_posts` VALUES (1,2,'🚀 Just deployed a zero-downtime Triton Inference Server upgrade with 3× throughput improvement. Key insight: batching policy matters way more than I expected. Changed from max_batch_size=1 to max_batch_size=64 with dynamic batching — GPU utilization jumped from 34% to 91%.','#mlops,#triton,#gpu','discussion',1,'dynamic_batching {\n  preferred_batch_size: [ 16, 32, 64 ]\n  max_queue_delay_microseconds: 5000\n}',142,4,1,'2026-09-18 16:23:01'),(2,3,'Hot take: most RAG implementations fail not because of retrieval quality, but because of chunking strategy. Semantic chunking with a sliding 50-token overlap consistently outperforms fixed-size chunking by 18–27% on retrieval F1 in my benchmarks. Anyone seeing this pattern?','#llm,#rag,#retrieval','discussion',0,NULL,89,2,0,'2026-09-18 16:23:01'),(3,8,'Struggling with gradient explosions in my custom RLHF training loop. PPO reward clipping is set to ε=0.2 but still seeing NaN losses after ~3k steps. I\'ve tried gradient clipping at max_norm=1.0. Anyone experienced this?','#rlhf,#pytorch,#debugging','question',1,'optimizer = AdamW(\n    model.parameters(),\n    lr=1e-5,\n    weight_decay=0.01,\n    betas=(0.9, 0.999)\n)\ntorch.nn.utils.clip_grad_norm_(model.parameters(), max_norm=1.0)',34,2,0,'2026-09-18 16:23:01'),(4,5,'PSA: If you\'re still doing `for batch in dataloader` on CPU-bottlenecked pipelines — try num_workers=8 + pin_memory=True + prefetch_factor=4. Cut our training epoch time from 4.8 min → 2.1 min on a single A100. Huge win for dataset-heavy workloads. 🔥','#pytorch,#performance,#dataloader','discussion',0,NULL,201,2,0,'2026-09-18 16:23:01'),(5,1,'Sharing my FlashAttention-2 benchmark results vs standard attention on RTX 4090:\n\nStandard: 23.4 ms / token @ batch=32\nFlashAttention-2: 6.1 ms / token @ batch=32 — 3.8× faster 🚀\n\nSequence length matters A LOT. Below 512 tokens gains are modest. Beyond 2048 is where it really shines.','#llm,#flashattention,#benchmarks','discussion',0,NULL,117,1,0,'2026-09-18 16:23:01'),(6,1,'sfaSF','','discussion',0,'',0,0,0,'2026-09-18 16:36:00'),(7,1,'Testing new post creation with PyTorch integration and optimization benchmark. #pytorch','#pytorch','discussion',0,NULL,0,0,0,'2026-09-18 16:59:47'),(8,1,'#debugging','#debugging','discussion',0,NULL,0,0,0,'2026-09-18 17:11:54');
/*!40000 ALTER TABLE `community_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `community_reactions`
--

DROP TABLE IF EXISTS `community_reactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `community_reactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reaction_type` varchar(20) DEFAULT 'like',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_reaction` (`post_id`,`user_id`,`reaction_type`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `community_reactions_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `community_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `community_reactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `community_reactions`
--

LOCK TABLES `community_reactions` WRITE;
/*!40000 ALTER TABLE `community_reactions` DISABLE KEYS */;
INSERT INTO `community_reactions` VALUES (1,1,1,'like','2026-09-26 13:22:51'),(2,1,1,'bookmark','2026-09-26 13:22:51'),(3,2,1,'like','2026-09-26 13:22:51'),(4,3,2,'like','2026-09-26 13:22:51'),(5,4,3,'like','2026-09-26 13:22:51');
/*!40000 ALTER TABLE `community_reactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `category` varchar(50) NOT NULL,
  `match_pct` int(11) NOT NULL,
  `duration_hours` int(11) NOT NULL,
  `labs_count` int(11) NOT NULL,
  `rating` decimal(3,1) NOT NULL,
  `reviews_count` int(11) NOT NULL,
  `description` text NOT NULL,
  `level_label` varchar(50) NOT NULL,
  `icon_emoji` varchar(20) DEFAULT '?',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (1,'System Design for Generative AI Applications','genai',98,14,8,4.9,1240,'Architect low-latency enterprise RAG pipelines, vector caching layers, semantic routers, and multi-agent coordination frameworks.','Advanced','🤖'),(2,'Production Kubernetes for AI Workloads','mlops',94,18,12,4.8,890,'Deploy GPU clusters, configure NVIDIA GPU Operator, batch scheduling with Kueue, and autoscaling LLM inference with KServe.','Advanced','☸️'),(3,'FlashAttention-2 & Custom Triton CUDA Kernels','deeplearning',95,22,10,5.0,640,'Write custom GPU SRAM-optimized fused kernels using OpenAI Triton to accelerate transformer training and inference by 3x.','Expert','⚡'),(4,'Distributed PyTorch: DeepSpeed ZeRO & Megatron-LM','mlops',91,20,9,4.9,780,'Master 3D parallelism (Tensor, Pipeline, Data Parallelism), ZeRO memory partitioning, and FP8 mixed precision on multi-node clusters.','Advanced','🚀'),(5,'High-Throughput LLM Serving with vLLM & TensorRT-LLM','arch',96,16,7,4.9,1120,'Implement continuous batching, PagedAttention KV-cache management, speculative decoding, and FP4 quantization for production LLMs.','Expert','📈'),(6,'Enterprise AI Governance, Red Teaming & Guardrails','genai',89,12,5,4.8,520,'Build automated security guardrails against prompt injection, jailbreaking, differential privacy leaks, and EU AI Act compliance.','Intermediate','🛡️');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `progress_pct` int(11) DEFAULT 0,
  `status` varchar(30) DEFAULT 'in_progress',
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_accessed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_course_unique` (`user_id`,`course_id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enrollments`
--

LOCK TABLES `enrollments` WRITE;
/*!40000 ALTER TABLE `enrollments` DISABLE KEYS */;
INSERT INTO `enrollments` VALUES (1,1,1,65,'in_progress','2026-09-13 17:44:53','2026-09-13 17:44:53'),(2,1,3,30,'in_progress','2026-09-13 17:44:53','2026-09-13 17:44:53'),(3,1,5,0,'in_progress','2026-09-13 18:01:46','2026-09-13 18:01:46'),(4,1,2,0,'in_progress','2026-09-13 18:02:15','2026-09-13 18:02:15');
/*!40000 ALTER TABLE `enrollments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_applications`
--

DROP TABLE IF EXISTS `job_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `action_type` varchar(20) NOT NULL DEFAULT 'apply',
  `status` varchar(30) DEFAULT 'submitted',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `job_id` (`job_id`),
  CONSTRAINT `job_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_applications_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `job_listings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_applications`
--

LOCK TABLES `job_applications` WRITE;
/*!40000 ALTER TABLE `job_applications` DISABLE KEYS */;
INSERT INTO `job_applications` VALUES (1,1,1,'apply','under_review','2026-09-26 13:22:51'),(2,1,2,'save','saved','2026-09-26 13:22:51'),(3,1,4,'save','saved','2026-09-26 13:22:51');
/*!40000 ALTER TABLE `job_applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_listings`
--

DROP TABLE IF EXISTS `job_listings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_listings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `company` varchar(150) NOT NULL,
  `location` varchar(100) NOT NULL,
  `job_type` varchar(30) DEFAULT 'fulltime',
  `match_pct` int(11) DEFAULT 70,
  `match_level` varchar(20) DEFAULT 'good',
  `salary_min` int(11) DEFAULT 0,
  `salary_max` int(11) DEFAULT 0,
  `salary_label` varchar(50) DEFAULT '',
  `skills_tags` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `skill_gaps` text DEFAULT NULL,
  `company_emoji` varchar(10) DEFAULT '?',
  `company_bg` varchar(50) DEFAULT 'rgba(99,102,241,0.15)',
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_listings`
--

LOCK TABLES `job_listings` WRITE;
/*!40000 ALTER TABLE `job_listings` DISABLE KEYS */;
INSERT INTO `job_listings` VALUES (1,'Lead AI Architect — LLM Systems','Google DeepMind India','Bengaluru, Hybrid','fulltime',96,'high',6000000,9000000,'₹60–90L / year','PyTorch, Transformers, CUDA, System Design','Design and lead distributed LLM training and serving infrastructure at exascale.',NULL,'🤖','rgba(99,102,241,0.15)','2026-09-18 16:23:01',1),(2,'Principal ML Engineer — GenAI Platform','Microsoft Azure AI','Hyderabad, Remote','remote',92,'high',5500000,8000000,'₹55–80L / year','MLOps, Kubernetes, RAG, LLMs','Build the next generation of GenAI platform services on Azure.',NULL,'☁️','rgba(56,189,248,0.15)','2026-09-18 16:23:01',1),(3,'Staff AI Engineer — Recommendation Systems','Flipkart AI Labs','Bengaluru','fulltime',87,'good',4200000,6500000,'₹42–65L / year','Deep Learning, Python, Vector DBs','Build ML recommendation and search systems at Flipkart scale.','Collaborative Filtering','🧠','rgba(16,185,129,0.15)','2026-09-18 16:23:01',1),(4,'Senior AI Research Engineer','NVIDIA AI Research','Pune, Hybrid','fulltime',84,'good',5000000,7500000,'₹50–75L / year','CUDA Kernels, C++, Triton','Advance GPU compute for AI at NVIDIA Research.',NULL,'⚡','rgba(168,85,247,0.15)','2026-09-18 16:23:01',1),(5,'AI Platform Architect','Infosys Topaz AI','Mumbai, Hybrid','fulltime',81,'good',3500000,5500000,'₹35–55L / year','MLflow, Airflow, Spark, Kafka','Design enterprise AI platform infrastructure.',NULL,'🚀','rgba(245,158,11,0.15)','2026-09-18 16:23:01',1),(6,'Research Scientist — Foundation Models','Sarvam AI','Bengaluru','fulltime',74,'fair',4500000,7000000,'₹45–70L / year','LLM Pre-training, RLHF, JAX','Research and train Indian language foundation models.','JAX / Flax, Pre-training at Scale','🔬','rgba(244,63,94,0.12)','2026-09-18 16:23:01',1),(7,'MLOps Lead — AI Infrastructure','Swiggy AI','Bengaluru, Remote','remote',79,'good',3800000,5800000,'₹38–58L / year','Kubeflow, Triton, Prometheus, Ray','Own the ML platform and model deployment lifecycle.',NULL,'🍔','rgba(16,185,129,0.12)','2026-09-18 16:23:01',1),(8,'Senior LLM Engineer','Ola Electric AI','Bengaluru','fulltime',83,'good',4500000,7000000,'₹45–70L / year','LLMs, Python, vLLM, LangChain','Build LLM-powered product features at Ola.',NULL,'⚡','rgba(245,158,11,0.12)','2026-09-18 16:23:01',1);
/*!40000 ALTER TABLE `job_listings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leaderboard_scores`
--

DROP TABLE IF EXISTS `leaderboard_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leaderboard_scores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `xp_points` int(11) DEFAULT 0,
  `xp_weekly` int(11) DEFAULT 0,
  `xp_monthly` int(11) DEFAULT 0,
  `xp_quarterly` int(11) DEFAULT 0,
  `streak_days` int(11) DEFAULT 0,
  `specialty` varchar(100) DEFAULT 'AI Engineering',
  `readiness_pct` int(11) DEFAULT 0,
  `trend` varchar(10) DEFAULT 'same',
  `change_label` varchar(20) DEFAULT '–',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `leaderboard_scores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaderboard_scores`
--

LOCK TABLES `leaderboard_scores` WRITE;
/*!40000 ALTER TABLE `leaderboard_scores` DISABLE KEYS */;
INSERT INTO `leaderboard_scores` VALUES (1,1,2450,340,1200,3800,14,'Transformers',78,'up','+340','2026-09-18 16:23:01'),(2,2,12480,1240,4800,9200,28,'Transformers',96,'up','+1240','2026-09-18 16:23:01'),(3,3,9820,890,3900,7400,21,'Generative AI',93,'up','+890','2026-09-18 16:23:01'),(4,4,8650,640,3200,6800,17,'PyTorch/CUDA',90,'down','-80','2026-09-18 16:23:01'),(5,5,7920,720,3100,6200,14,'MLOps',88,'up','+190','2026-09-18 16:23:01'),(6,6,7240,520,2800,5700,12,'LLM Alignment',85,'same','–','2026-09-18 16:23:01'),(7,7,6800,480,2600,5400,10,'RAG Systems',83,'up','+130','2026-09-18 16:23:01'),(8,8,6450,420,2400,5100,9,'RL & Alignment',81,'up','+90','2026-09-18 16:23:01'),(9,9,6100,380,2200,4900,8,'Embeddings',80,'down','-60','2026-09-18 16:23:01'),(10,10,5840,340,2000,4700,7,'Vector DBs',78,'up','+75','2026-09-18 16:23:01'),(11,11,5520,300,1900,4500,6,'NLP / BERT',76,'same','–','2026-09-18 16:23:01'),(12,12,5100,420,1800,4100,11,'System Design',74,'up','+150','2026-09-18 16:23:01'),(13,13,4830,280,1700,3900,5,'Multimodal AI',72,'down','-45','2026-09-18 16:23:01'),(14,14,4680,360,1600,3700,14,'Kubernetes AI',71,'up','+100','2026-09-18 16:23:01'),(15,15,4450,250,1500,3600,9,'Fine-Tuning',70,'same','–','2026-09-18 16:23:01'),(16,16,4210,310,1400,3400,8,'Distributed ML',69,'up','+88','2026-09-18 16:23:01'),(17,17,3980,220,1300,3200,6,'Neural Nets',68,'down','-30','2026-09-18 16:23:01'),(18,18,3740,200,1200,3000,7,'GenAI Systems',67,'up','+60','2026-09-18 16:23:01'),(19,19,2200,180,1000,2600,5,'PyTorch',65,'same','–','2026-09-18 16:23:01'),(20,20,1980,160,900,2300,3,'NLP',62,'up','+40','2026-09-18 16:23:01');
/*!40000 ALTER TABLE `leaderboard_scores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `learning_path_modules`
--

DROP TABLE IF EXISTS `learning_path_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `learning_path_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stage_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `subtitle` text NOT NULL,
  `duration_mins` int(11) NOT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `order_num` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `stage_id` (`stage_id`),
  CONSTRAINT `learning_path_modules_ibfk_1` FOREIGN KEY (`stage_id`) REFERENCES `learning_path_stages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `learning_path_modules`
--

LOCK TABLES `learning_path_modules` WRITE;
/*!40000 ALTER TABLE `learning_path_modules` DISABLE KEYS */;
INSERT INTO `learning_path_modules` VALUES (1,1,'Vector & Matrix Calculus for Deep Learning','Jacobians, Hessians, and automatic differentiation engines',180,1,1),(2,1,'Advanced NumPy & Vectorized Computations','Memory layouts, strides, broadcasting, and Cython compilation',240,1,2),(3,1,'CPython Internals, Memory Management & GIL','Reference counting, cycle detection, and multiprocessing',210,1,3),(4,2,'Building an Autograd Engine from Scratch in Python','Computational DAGs, backprop topological sort, and tape-based autodiff',300,1,1),(5,2,'Multi-Head Attention & KV Caching Mechanics','Scaled dot-product attention, masking, and memory footprints',270,1,2),(6,2,'Modern Tokenization & Rotary Position Embeddings (RoPE)','Byte-Pair Encoding (BPE), TikToken, and rotational embeddings',240,1,3),(7,2,'Multi-Head Self-Attention in PyTorch','CUDA tensor operations, forward/backward implementations, hands-on lab',180,1,4),(8,2,'Transformer Block Normalization & Residual Streams','Pre-LN vs Post-LN, RMSNorm, and residual scale stabilization',200,0,5),(9,3,'PyTorch Distributed Data Parallel (DDP) Internals','NCCL collective communications, Ring-AllReduce, and bucket tuning',240,0,1),(10,3,'DeepSpeed ZeRO-1, 2, and 3 Memory Sharding','Eliminating redundant optimizer and parameter states',320,0,2),(11,3,'Megatron-LM 3D Parallelism','Tensor, pipeline, and data parallel coordination for 70B+ models',360,0,3),(12,4,'vLLM & PagedAttention Dynamic Memory Management','Sub-millisecond token generation and KV cache eviction',280,0,1),(13,4,'Speculative Decoding with Draft Verification Models','2.5x inference speedup without precision degradation',220,0,2),(14,4,'Enterprise Guardrails & Red-Teaming Defense','Mitigating indirect prompt injections and hallucination cascades',260,0,3);
/*!40000 ALTER TABLE `learning_path_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `learning_path_stages`
--

DROP TABLE IF EXISTS `learning_path_stages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `learning_path_stages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `stage_number` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(30) NOT NULL,
  `progress_pct` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `learning_path_stages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `learning_path_stages`
--

LOCK TABLES `learning_path_stages` WRITE;
/*!40000 ALTER TABLE `learning_path_stages` DISABLE KEYS */;
INSERT INTO `learning_path_stages` VALUES (1,1,1,'Stage 1: Mathematical Foundations & High-Performance Python','Linear algebra, multivariate calculus, optimization algorithms, and CPython profiling.','completed',100),(2,1,2,'Stage 2: Deep Learning Internals & Transformer Architectures','Autograd engine from scratch, attention mechanisms, rotary embeddings, and PyTorch internals.','in_progress',80),(3,1,3,'Stage 3: Distributed Training & Large-Scale MLOps','Multi-GPU scaling, ZeRO optimizer partitioning, Megatron tensor parallelism, and Slurm clusters.','locked',0),(4,1,4,'Stage 4: Enterprise LLM Architecture & Production Guardrails','Continuous batching, vLLM PagedAttention, speculative decoding, and safety red teaming.','locked',0);
/*!40000 ALTER TABLE `learning_path_stages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `time_ago` varchar(50) DEFAULT 'Just now',
  `is_read` tinyint(1) DEFAULT 0,
  `type` varchar(50) DEFAULT 'info',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,1,'Assessment Completed!','You scored 94% on Advanced Python for AI.','10 mins ago',0,'success','2026-09-13 17:44:53'),(2,1,'New Recommendation','System Design for LLMs matches your target role (Lead AI Architect).','2 hours ago',0,'info','2026-09-13 17:44:53'),(3,1,'Streak Milestone','You have maintained a 14-day active learning streak.','Yesterday',0,'streak','2026-09-13 17:44:53'),(4,1,'🎉 Assessment Passed!','You scored 100% on System Design for Large Language Models. (+150 Karma XP)','Just now',0,'success','2026-09-13 18:01:01'),(5,1,'Course Enrolled!','You enrolled in \'High-Throughput LLM Serving with vLLM & TensorRT-LLM\'. Added to active courses.','Just now',0,'course','2026-09-13 18:01:46'),(6,1,'Course Enrolled!','You enrolled in \'Production Kubernetes for AI Workloads\'. Added to active courses.','Just now',0,'course','2026-09-13 18:02:15'),(7,1,'🎉 Assessment Passed!','You scored 80% on System Design for Large Language Models. (+150 Karma XP)','Just now',0,'success','2026-09-13 18:12:11'),(8,21,'Welcome!','Your Skill Intelligence account is ready. Start your first assessment!','Just now',0,'success','2026-09-18 16:27:59'),(9,1,'Assessment Submitted','You scored 40% on System Design for Large Language Models. (+50 Karma XP)','Just now',0,'success','2026-09-18 16:35:29');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `progress_stats`
--

DROP TABLE IF EXISTS `progress_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `progress_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_hours_invested` decimal(5,1) DEFAULT 74.2,
  `active_streak_days` int(11) DEFAULT 14,
  `pass_rate_pct` decimal(4,1) DEFAULT 94.2,
  `target_readiness_pct` int(11) DEFAULT 78,
  `mastered_competencies_count` int(11) DEFAULT 24,
  `total_competencies_count` int(11) DEFAULT 30,
  `weekly_hours_json` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `progress_stats_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `progress_stats`
--

LOCK TABLES `progress_stats` WRITE;
/*!40000 ALTER TABLE `progress_stats` DISABLE KEYS */;
INSERT INTO `progress_stats` VALUES (1,1,74.2,14,94.2,83,5,30,'[{\"day\":\"Mon\",\"hours\":3.5},{\"day\":\"Tue\",\"hours\":2},{\"day\":\"Wed\",\"hours\":4},{\"day\":\"Thu\",\"hours\":1.5},{\"day\":\"Fri\",\"hours\":3.5},{\"day\":\"Sat\",\"hours\":4},{\"day\":\"Sun\",\"hours\":0}]'),(2,21,0.0,0,0.0,0,0,30,'[]');
/*!40000 ALTER TABLE `progress_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recommendations`
--

DROP TABLE IF EXISTS `recommendations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recommendations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `category` varchar(50) NOT NULL,
  `match_pct` int(11) NOT NULL,
  `reason_text` text NOT NULL,
  `duration_hours` int(11) NOT NULL,
  `case_studies_count` int(11) NOT NULL,
  `status` varchar(30) DEFAULT 'active',
  `priority_tag` varchar(50) DEFAULT 'Recommended',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `recommendations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recommendations`
--

LOCK TABLES `recommendations` WRITE;
/*!40000 ALTER TABLE `recommendations` DISABLE KEYS */;
INSERT INTO `recommendations` VALUES (1,1,'Production Kubernetes for AI Workloads','MLOps & GPU Scheduling',96,'Directly bridges your highest-weighted target role gap (-26% in Distributed GPU Scheduling).',18,6,'active','Critical Priority'),(2,1,'Enterprise AI Governance, Red Teaming & Guardrails','AI Safety & Security',94,'Standard prerequisite for Architect-level roles in financial and enterprise AI sectors.',12,4,'active','High Match'),(3,1,'Triton Custom Kernels & FlashAttention-2','GPU Kernel Engineering',91,'Highly valued skill that compliments your 96% PyTorch mastery level.',22,10,'active','Recommended');
/*!40000 ALTER TABLE `recommendations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `skills`
--

DROP TABLE IF EXISTS `skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `skills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `proficiency_pct` int(11) NOT NULL,
  `level_label` varchar(50) NOT NULL,
  `benchmark_rank` varchar(50) NOT NULL,
  `endorsements_count` int(11) DEFAULT 0,
  `target_weight_pct` int(11) DEFAULT 10,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `skills`
--

LOCK TABLES `skills` WRITE;
/*!40000 ALTER TABLE `skills` DISABLE KEYS */;
INSERT INTO `skills` VALUES (1,1,'genai','Large Language Models (LLMs)',92,'Mastery','Top 4%',19,20),(2,1,'genai','RAG Architectures & Vector DBs',88,'Mastery','Top 6%',15,15),(3,1,'pytorch','PyTorch Core & Autograd',96,'Expert','Top 2%',27,15),(4,1,'pytorch','Custom Triton & CUDA Kernels',74,'Proficient','Top 14%',9,10),(5,1,'mlops','Distributed Training (DDP / ZeRO)',84,'Advanced','Top 8%',12,15),(6,1,'mlops','Kubernetes & GPU Orchestration',70,'Intermediate','Top 22%',7,10),(7,1,'arch','High-Throughput Inference (vLLM)',93,'Mastery','Top 7%',15,15),(8,1,'arch','Enterprise AI Governance & Safety',64,'Competent','Top 28%',5,10);
/*!40000 ALTER TABLE `skills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_certifications`
--

DROP TABLE IF EXISTS `user_certifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_certifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `cert_id` int(11) NOT NULL,
  `status` varchar(20) DEFAULT 'in_progress',
  `progress_pct` int(11) DEFAULT 0,
  `score_pct` int(11) DEFAULT NULL,
  `cert_id_code` varchar(50) DEFAULT NULL,
  `earned_at` timestamp NULL DEFAULT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_cert_unique` (`user_id`,`cert_id`),
  KEY `cert_id` (`cert_id`),
  CONSTRAINT `user_certifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_certifications_ibfk_2` FOREIGN KEY (`cert_id`) REFERENCES `certifications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_certifications`
--

LOCK TABLES `user_certifications` WRITE;
/*!40000 ALTER TABLE `user_certifications` DISABLE KEYS */;
INSERT INTO `user_certifications` VALUES (1,1,1,'earned',100,94,'SIP-2026-TRF-8821','2026-08-14 18:30:00','2026-09-18 16:23:01'),(2,1,2,'earned',100,89,'AWS-2026-MLS-4492','2026-02-09 18:30:00','2026-09-18 16:23:01'),(3,1,3,'earned',100,97,'DL-2025-SPEC-1123','2025-11-30 18:30:00','2026-09-18 16:23:01'),(4,1,4,'earned',100,88,'CKA-2025-CERT-772','2025-09-19 18:30:00','2026-09-18 16:23:01'),(5,1,5,'earned',100,94,'SIP-2026-PY-6614','2026-09-09 18:30:00','2026-09-18 16:23:01'),(6,1,6,'earned',100,76,'SIP-2026-OPS-3301','2026-07-04 18:30:00','2026-09-18 16:23:01'),(7,1,7,'in_progress',65,NULL,NULL,NULL,'2026-09-18 16:23:01'),(8,1,10,'in_progress',38,NULL,NULL,NULL,'2026-09-18 16:23:01'),(9,1,9,'in_progress',12,NULL,NULL,NULL,'2026-09-18 16:23:01');
/*!40000 ALTER TABLE `user_certifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_settings`
--

DROP TABLE IF EXISTS `user_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `theme_mode` varchar(20) DEFAULT 'dark',
  `weekly_hours_target` int(11) DEFAULT 15,
  `daily_streak_protection` tinyint(1) DEFAULT 1,
  `rec_alerts` tinyint(1) DEFAULT 1,
  `assessment_reports` tinyint(1) DEFAULT 1,
  `marketing_emails` tinyint(1) DEFAULT 0,
  `profile_visibility` varchar(30) DEFAULT 'public',
  `two_factor_auth` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_settings`
--

LOCK TABLES `user_settings` WRITE;
/*!40000 ALTER TABLE `user_settings` DISABLE KEYS */;
INSERT INTO `user_settings` VALUES (1,1,'dark',15,1,1,1,0,'public',0),(2,21,'dark',15,1,1,1,0,'public',0);
/*!40000 ALTER TABLE `user_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `role_title` varchar(100) NOT NULL,
  `target_role` varchar(100) NOT NULL,
  `department` varchar(150) DEFAULT 'Enterprise Cognitive Systems Division',
  `location` varchar(100) DEFAULT 'Bengaluru, India',
  `avatar_initials` varchar(10) DEFAULT 'AM',
  `karma_xp` int(11) DEFAULT 2450,
  `rank_percentile` varchar(50) DEFAULT 'Top 5%',
  `bio` text DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Alex Morgan (Lead)','alex.morgan@enterprise.ai','Senior AI/ML Engineer','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','AM',3015,'Top 5%','Architecting enterprise generative AI, low-latency distributed PyTorch/CUDA training clusters, and agentic workflows.',NULL,'2026-09-13 17:44:53','2026-09-18 17:11:54'),(2,'Sayan Ghosh','sayan.ghosh@deepmind.ai','Lead AI Architect','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','SG',12480,'Top 96%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(3,'Riya Kapoor','riya.kapoor@research.ai','AI Researcher','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','RK',9820,'Top 93%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(4,'Vivek Nair','vivek.nair@ml.io','ML Engineer','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','VN',8650,'Top 90%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(5,'Priya Anand','priya.anand@sysai.io','AI Systems Lead','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','PA',7920,'Top 88%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(6,'Arjun Ahuja','arjun.ahuja@principal.ai','Principal Eng','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','AA',7240,'Top 85%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(7,'Megha Singh','megha.singh@ragsys.ai','AI Engineer II','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','MS',6800,'Top 83%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(8,'Tanmay Kumar','tanmay.kumar@rlhf.ai','ML Researcher','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','TK',6450,'Top 81%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(9,'Divya Pillai','divya.pillai@embeddings.ai','Data Scientist III','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','DP',6100,'Top 80%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(10,'Soham Pal','soham.pal@vectordb.ai','AI Engineer','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','SP',5840,'Top 78%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(11,'Nikita Arora','nikita.arora@nlp.ai','ML Engineer','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','NA',5520,'Top 76%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(12,'Kartik Menon','kartik.menon@sysdesign.ai','AI Architect Intern','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','KM',5100,'Top 74%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(13,'Aanya Gupta','aanya.gupta@multimodal.ai','Research Engineer','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','AG',4830,'Top 72%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(14,'Rohit Sharma','rohit.sharma@k8s.ai','ML Ops Specialist','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','RS',4680,'Top 71%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(15,'Ishaan Luthra','ishaan.luthra@finetune.ai','AI Engineer II','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','IL',4450,'Top 70%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(16,'Sunita Bhat','sunita.bhat@dist.ai','Principal ML','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','SB',4210,'Top 69%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(17,'Manav Reddy','manav.reddy@neural.ai','Data Scientist II','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','MR',3980,'Top 68%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(18,'Prita Sood','prita.sood@genai.ai','AI Researcher','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','PS',3740,'Top 67%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(19,'Nihal Gowda','nihal.gowda@pytorch.ai','ML Engineer','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','NG',2200,'Top 65%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(20,'Zara Ahmad','zara.ahmad@nlp.ai','AI Intern','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','ZA',1980,'Top 62%',NULL,NULL,'2026-09-18 16:23:01','2026-09-18 16:23:01'),(21,'Test Engineer','test.engineer@example.ai','ML Engineer','Lead AI Architect','Enterprise Cognitive Systems Division','Bengaluru, India','TE',100,'Unranked',NULL,'$2y$10$c4.4Zowmv5s.BhgnI7RCReo.umUPy0vXL97xLKKXsUjQE8JWk8cwC','2026-09-18 16:27:59','2026-09-18 16:27:59');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'skill_intelligence'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-26 19:08:23
