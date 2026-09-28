

(function () {
  'use strict';

  const STORAGE_KEY = 'si_platform_db_v2';

  const API_BASE = (function () {
    const loc = window.location.pathname;
    if (loc.includes('/sih/skill-intelligence-ui/')) {
      return '../api/';
    } else if (loc.includes('/sih/sih/')) {
      return 'api/';
    } else if (loc.includes('/sih/')) {
      return 'sih/api/';
    } else {
      return 'api/';
    }
  })();

  function getInitialSeedData() {
    let activeAuth = null;
    try {
      const stored = sessionStorage.getItem('si_auth_user') || localStorage.getItem('si_auth_user');
      if (stored) {
        const parsed = JSON.parse(stored);
        if (parsed && parsed.email !== 'alex.morgan@enterprise.ai' && parsed.full_name !== 'Alex Morgan') {
          activeAuth = parsed;
        }
      }
    } catch (_) {}

    return {
      user: activeAuth,
      settings: {
        theme_mode: 'dark',
        weekly_hours_target: 15,
        daily_streak_protection: 1,
        rec_alerts: 1,
        assessment_reports: 1,
        marketing_emails: 0,
        profile_visibility: 'public',
        two_factor_auth: 0
      },
      stats: {
        total_hours_invested: 74.2,
        active_streak_days: 14,
        pass_rate_pct: 94.2,
        target_readiness_pct: 78,
        mastered_competencies_count: 24,
        total_competencies_count: 30,
        weekly_hours: [
          { day: 'Mon', hours: 2.5 },
          { day: 'Tue', hours: 3.8 },
          { day: 'Wed', hours: 1.5 },
          { day: 'Thu', hours: 4.2 },
          { day: 'Fri', hours: 2.0 },
          { day: 'Sat', hours: 3.5 },
          { day: 'Sun', hours: 1.8 }
        ]
      },
      skills: [
        { id: 1, category: 'core_ai', name: 'PyTorch & Distributed Training (DDP)', proficiency_pct: 96, level_label: 'Expert', benchmark_rank: 'Top 1.5%', endorsements_count: 38, target_weight_pct: 95 },
        { id: 2, category: 'core_ai', name: 'Transformer Architectures & Attention', proficiency_pct: 92, level_label: 'Expert', benchmark_rank: 'Top 3.0%', endorsements_count: 42, target_weight_pct: 95 },
        { id: 3, category: 'core_ai', name: 'DeepSpeed ZeRO & Model Sharding', proficiency_pct: 88, level_label: 'Advanced', benchmark_rank: 'Top 5.2%', endorsements_count: 29, target_weight_pct: 90 },
        { id: 4, category: 'mlops', name: 'vLLM & Continuous Batching Inference', proficiency_pct: 85, level_label: 'Advanced', benchmark_rank: 'Top 6.8%', endorsements_count: 25, target_weight_pct: 85 },
        { id: 5, category: 'mlops', name: 'Kubernetes for GPUs & Slurm Orchestration', proficiency_pct: 80, level_label: 'Advanced', benchmark_rank: 'Top 8.4%', endorsements_count: 19, target_weight_pct: 80 },
        { id: 6, category: 'deeplearning', name: 'Triton Custom Kernels & FlashAttention-2', proficiency_pct: 74, level_label: 'Intermediate', benchmark_rank: 'Top 14.0%', endorsements_count: 16, target_weight_pct: 85 },
        { id: 7, category: 'arch', name: 'System Design for Large Language Models', proficiency_pct: 84, level_label: 'Advanced', benchmark_rank: 'Top 7.1%', endorsements_count: 31, target_weight_pct: 90 },
        { id: 8, category: 'genai', name: 'Enterprise AI Governance & Red Teaming', proficiency_pct: 72, level_label: 'Intermediate', benchmark_rank: 'Top 16.5%', endorsements_count: 14, target_weight_pct: 75 }
      ],
      courses: [
        {
          id: 1,
          title: 'System Design for Generative AI Applications',
          category: 'arch',
          match_pct: 98,
          duration_hours: 14,
          labs_count: 8,
          rating: 4.9,
          reviews_count: 1240,
          description: 'Architect low-latency enterprise RAG pipelines, vector caching layers, semantic routers, and multi-agent coordination frameworks.',
          level_label: 'Advanced',
          icon_emoji: '🤖',
          is_enrolled: true,
          enrollment_progress: 65
        },
        {
          id: 2,
          title: 'Production Kubernetes for AI Workloads',
          category: 'mlops',
          match_pct: 94,
          duration_hours: 18,
          labs_count: 12,
          rating: 4.8,
          reviews_count: 890,
          description: 'Deploy GPU clusters, configure NVIDIA GPU Operator, batch scheduling with Kueue, and autoscaling LLM inference with KServe.',
          level_label: 'Advanced',
          icon_emoji: '☸️',
          is_enrolled: true,
          enrollment_progress: 40
        },
        {
          id: 3,
          title: 'FlashAttention-2 & Custom Triton CUDA Kernels',
          category: 'deeplearning',
          match_pct: 95,
          duration_hours: 22,
          labs_count: 10,
          rating: 4.95,
          reviews_count: 640,
          description: 'Write custom GPU SRAM-optimized fused kernels using OpenAI Triton to accelerate transformer training and inference by 3x.',
          level_label: 'Expert',
          icon_emoji: '⚡',
          is_enrolled: false,
          enrollment_progress: 0
        },
        {
          id: 4,
          title: 'Distributed PyTorch: DeepSpeed ZeRO & Megatron-LM',
          category: 'mlops',
          match_pct: 91,
          duration_hours: 20,
          labs_count: 9,
          rating: 4.85,
          reviews_count: 780,
          description: 'Master 3D parallelism (Tensor, Pipeline, Data Parallelism), ZeRO memory partitioning, and FP8 mixed precision on multi-node clusters.',
          level_label: 'Advanced',
          icon_emoji: '🚀',
          is_enrolled: false,
          enrollment_progress: 0
        },
        {
          id: 5,
          title: 'High-Throughput LLM Serving with vLLM & TensorRT-LLM',
          category: 'arch',
          match_pct: 96,
          duration_hours: 16,
          labs_count: 7,
          rating: 4.9,
          reviews_count: 1120,
          description: 'Implement continuous batching, PagedAttention KV-cache management, speculative decoding, and FP4 quantization for production LLMs.',
          level_label: 'Expert',
          icon_emoji: '📈',
          is_enrolled: false,
          enrollment_progress: 0
        },
        {
          id: 6,
          title: 'Enterprise AI Governance, Red Teaming & Guardrails',
          category: 'genai',
          match_pct: 89,
          duration_hours: 12,
          labs_count: 5,
          rating: 4.75,
          reviews_count: 520,
          description: 'Build automated security guardrails against prompt injection, jailbreaking, differential privacy leaks, and EU AI Act compliance.',
          level_label: 'Intermediate',
          icon_emoji: '🛡️',
          is_enrolled: false,
          enrollment_progress: 0
        }
      ],
      assessments: [
        {
          id: 1,
          title: 'System Design for Large Language Models',
          category: 'arch',
          description: 'Evaluate your ability to architect fault-tolerant distributed inference pipelines, KV cache paging, semantic routing, and multi-GPU tensor-parallel architectures.',
          duration_mins: 30,
          questions_count: 5,
          difficulty: 'Advanced',
          pass_score_pct: 75,
          questions: [
            {
              q: 'In high-throughput vLLM serving, what fundamental problem does PagedAttention solve compared to standard contiguous memory allocation?',
              options: [
                'Eliminates floating-point quantization errors',
                'Prevents KV-cache memory fragmentation and enables dynamic page sharing across parallel requests',
                'Directly bypasses CUDA kernels using CPU SIMD registers',
                'Replaces multi-head attention with quadratic dot-product memory'
              ],
              correct: 1,
              explanation: 'PagedAttention partitions the KV cache into non-contiguous virtual blocks, reducing memory waste from ~60-80% down to under 4%.'
            },
            {
              q: 'When partitioning a 70B parameter model across 4x A100 (80GB) GPUs using Megatron-LM Tensor Parallelism, which layer components are split along column and row dimensions?',
              options: [
                'Self-attention QKV projections are split by column, Output dense projection is split by row',
                'MLP layers are kept unpartitioned on GPU 0 while attention runs on GPU 1-3',
                'LayerNorm weights are duplicated while embeddings are discarded',
                'Only bias vectors are partitioned across the NVLink fabric'
              ],
              correct: 0,
              explanation: 'In tensor parallelism, self-attention Q, K, V linear weights are column-parallel, and the output projection is row-parallel followed by an AllReduce.'
            },
            {
              q: 'In DeepSpeed ZeRO Stage 3 (ZeRO-3), which components of the training state are partitioned across all distributed data-parallel worker nodes?',
              options: [
                'Only optimizer states (moments)',
                'Optimizer states and first-order gradients only',
                'Optimizer states, gradients, and model parameters',
                'Only activation checkpoint caches'
              ],
              correct: 2,
              explanation: 'ZeRO-3 shards all three primary memory consumers: 16-bit model parameters, gradients, and 32-bit optimizer states.'
            },
            {
              q: 'Which technique allows an LLM serving engine to verify multiple predicted tokens generated by a smaller speculative draft model in a single forward pass?',
              options: [
                'Speculative Decoding with Tree Attention verification',
                'Quantized LoRA Rank Adaptation',
                'Rejection sampling over temperature 0.0',
                'FlashAttention SRAM IO tiling'
              ],
              correct: 0,
              explanation: 'Speculative decoding utilizes a small draft model to generate candidate tokens that the target model validates in parallel in one forward pass.'
            },
            {
              q: 'What is the primary trade-off of using FP8 (E4M3 vs E5M2) precision during LLM training compared to BF16?',
              options: [
                'Double throughput and halved memory with increased susceptibility to gradient underflow/overflow requiring per-tensor scaling',
                'Higher floating-point dynamic range than FP32 but 4x higher memory footprint',
                'Loss of rotary embedding positional alignment',
                'Requires replacing attention layers with convolutional approximations'
              ],
              correct: 0,
              explanation: 'FP8 roughly doubles compute throughput and cuts KV memory in half, but needs dynamic scaling factors due to limited 8-bit dynamic range.'
            }
          ]
        },
        {
          id: 2,
          title: 'Transformer Architectures & Attention Mechanisms',
          category: 'core_ai',
          description: 'Validate depth of understanding in scaled dot-product attention, RoPE positional encodings, KV caching mechanics, and FlashAttention I/O tiling.',
          duration_mins: 25,
          questions_count: 5,
          difficulty: 'Expert',
          pass_score_pct: 80,
          questions: [
            {
              q: 'Why does standard Scaled Dot-Product Attention scale memory quadratically O(N^2) with sequence length N?',
              options: [
                'The full N x N attention matrix (Q @ K^T) must be materialized in GPU High Bandwidth Memory (HBM)',
                'The linear projections Q, K, V require N^2 parameters',
                'Feed-forward MLP layers expand hidden dimensions by 4x',
                'LayerNorm recalculates variance across sequence dimensions'
              ],
              correct: 0,
              explanation: 'Materializing the full N x N attention matrix in GPU HBM is both compute and memory bound as sequence length N grows.'
            },
            {
              q: 'How does FlashAttention achieve a 2x-4x speedup over standard PyTorch attention without changing the mathematical output?',
              options: [
                'Tiling Q, K, V into fast GPU SRAM and using online softmax to avoid reading/writing the large N x N matrix to slow HBM',
                'Pruning 50% of the lowest attention weights dynamically',
                'Quantizing all attention matrix calculations to INT4',
                'Computing attention asynchronously on host CPU threads'
              ],
              correct: 0,
              explanation: 'FlashAttention computes exact attention block-by-block in fast SRAM using an incremental online softmax algorithm.'
            },
            {
              q: 'What advantage does Rotary Position Embedding (RoPE) provide over absolute sinusoidal positional embeddings?',
              options: [
                'Encodes relative position through complex rotation of Q and K vectors, naturally decaying with token distance and facilitating length extrapolation',
                'Requires zero CUDA kernel execution time',
                'Compresses the vocab embedding table by 50%',
                'Guarantees linear time attention scaling'
              ],
              correct: 0,
              explanation: 'RoPE applies an orthogonal rotation matrix to queries and keys such that their dot product depends solely on relative distance (m - n).'
            },
            {
              q: 'During autoregressive text generation, why is the KV Cache indispensable for achieving low latency?',
              options: [
                'Prevents recomputing key and value representations of preceding tokens for each newly generated token, reducing compute from O(N^2) to O(N)',
                'Enables lossless 8-bit quantization of the output vocabulary',
                'Compresses prompts into a 128-token latent vector',
                'Pre-fetches future user prompts from the network queue'
              ],
              correct: 0,
              explanation: 'Without KV cache, generation would recompute keys/values for all previous tokens at every step.'
            },
            {
              q: 'How does Grouped-Query Attention (GQA) differ from Multi-Head Attention (MHA) and Multi-Query Attention (MQA)?',
              options: [
                'GQA shares each key and value head across a subset (group) of query heads, offering a balanced middle ground between MHA capacity and MQA speed',
                'GQA computes attention across batches instead of sequences',
                'GQA applies independent attention masks to each MLP layer',
                'GQA uses different embedding dimensions for prompts and completions'
              ],
              correct: 0,
              explanation: 'GQA groups query heads to share fewer key/value heads, drastically reducing KV cache size with negligible accuracy loss.'
            }
          ]
        },
        {
          id: 3,
          title: 'Distributed PyTorch: DDP, FSDP & Slurm Scheduling',
          category: 'mlops',
          description: 'Benchmark multi-node scaling, NCCL collective operations, gradient bucket sizing, and Fully Sharded Data Parallel (FSDP) tuning.',
          duration_mins: 35,
          questions_count: 5,
          difficulty: 'Advanced',
          pass_score_pct: 75,
          questions: [
            {
              q: 'In PyTorch DistributedDataParallel (DDP), what NCCL collective communication primitive synchronizes gradients across all GPUs during the backward pass?',
              options: [
                'AllReduce (specifically Ring-AllReduce or Tree-AllReduce)',
                'AllGather',
                'Broadcast from Rank 0',
                'ReduceScatter followed by Point-to-Point Send'
              ],
              correct: 0,
              explanation: 'DDP uses Ring or Tree AllReduce over NCCL to aggregate gradients across all distributed workers.'
            },
            {
              q: 'What is the main difference between PyTorch DDP and Fully Sharded Data Parallel (FSDP)?',
              options: [
                'DDP duplicates model parameters on every GPU; FSDP shards parameters, gradients, and optimizer states across ranks (ZeRO-3 style)',
                'DDP only works on single nodes, whereas FSDP requires InfiniBand',
                'FSDP only works for convolutional networks',
                'DDP requires manual CUDA kernel compilation'
              ],
              correct: 0,
              explanation: 'FSDP shards the entire model across GPUs and gathers shards on-demand during forward and backward passes.'
            },
            {
              q: 'Why does DDP group gradients into consecutive buckets (default 25MB) rather than launching an AllReduce per tensor?',
              options: [
                'To overlap communication with computation and amortize NCCL kernel launch overhead across many small gradients',
                'To prevent CPU PCIe bus saturation',
                'To automatically perform gradient clipping',
                'To enable FP8 conversion on the fly'
              ],
              correct: 0,
              explanation: 'Bucketizing groups gradient synchronization, overlapping AllReduce operations with earlier layers backward pass.'
            },
            {
              q: 'In Slurm cluster job submission for a 16-GPU job across 2 nodes, which environment variable indicates the global rank of a process?',
              options: [
                'SLURM_PROCID (or RANK via torchrun)',
                'SLURM_JOB_ID',
                'CUDA_DEVICE_ORDER',
                'TORCH_DISTRIBUTED_PORT'
              ],
              correct: 0,
              explanation: 'SLURM_PROCID specifies the global process index, which torchrun maps to the distributed RANK.'
            },
            {
              q: 'When scaling distributed training across nodes, why is InfiniBand with GPUDirect RDMA preferred over standard TCP/IP Ethernet?',
              options: [
                'Bypasses host CPU and memory subsystem, transferring gradient buffers directly between GPU VRAMs across the network with sub-microsecond latency',
                'Enables automatic checkpoint serialization to S3',
                'Eliminates the need for gradient scaling in FP16 training',
                'Allows GPUs to share L2 caches across physical servers'
              ],
              correct: 0,
              explanation: 'GPUDirect RDMA transfers data directly from GPU memory to the network adapter without copying to host RAM.'
            }
          ]
        },
        {
          id: 4,
          title: 'Advanced Python for AI: CPython Internals & Concurrency',
          category: 'core_ai',
          description: 'Assess master-level understanding of Python memory allocators (pymalloc), garbage collection, GIL evolution (free-threading), and asyncio.',
          duration_mins: 20,
          questions_count: 5,
          difficulty: 'Advanced',
          pass_score_pct: 75,
          questions: [
            {
              q: 'How does CPython manage memory for small objects (< 512 bytes) to minimize OS system call overhead?',
              options: [
                'Using pymalloc with arenas (256KB), pools (4KB), and fixed-size size-class blocks',
                'Directly calling malloc/free for every Python object reference',
                'Memory mapping the whole physical RAM at process startup',
                'Relying solely on OS page tables without internal free lists'
              ],
              correct: 0,
              explanation: 'CPython pymalloc organizes memory into arenas (256KB), pools (4KB), and blocks of 8 to 512 bytes.'
            },
            {
              q: 'What triggers CPython cyclical garbage collector (gc module) to collect unreachable cyclic references?',
              options: [
                'Generational threshold counters (Gen 0, 1, 2) when allocations exceed deallocations',
                'Immediate reference count reaching zero',
                'Every time a thread acquires the GIL',
                'Only when process exits or gc.collect() is called manually'
              ],
              correct: 0,
              explanation: 'CPython reference counting frees non-cyclic objects instantly; the generational gc collects reference cycles when allocation thresholds are reached.'
            },
            {
              q: 'In Python 3.13 free-threaded builds (PEP 703), how is thread safety maintained without the Global Interpreter Lock?',
              options: [
                'Biased reference counting, mimalloc thread-safe allocator, and fine-grained per-object locks',
                'Restricting execution to single CPU core',
                'Converting all threads to greenlet fibers',
                'Compiling Python bytecode to static C binaries at import time'
              ],
              correct: 0,
              explanation: 'PEP 703 replaces the GIL with biased reference counting and fine-grained locking.'
            },
            {
              q: 'Why should CPU-intensive PyTorch tensor operations never be blocked by Python asyncio event loops?',
              options: [
                'Python asyncio is single-threaded cooperative multitasking; blocking CPU calls freeze the entire event loop and all pending concurrent tasks',
                'Asyncio automatically disables PyTorch GPU kernel dispatch',
                'PyTorch tensors cannot be referenced inside async functions',
                'Asyncio enforces synchronous garbage collection during awaits'
              ],
              correct: 0,
              explanation: 'Asyncio operates on a single thread; any long CPU operation halts the loop. Heavy work should be offloaded to thread/process pools.'
            },
            {
              q: 'What does the slots attribute achieve when defined on Python classes?',
              options: [
                'Prevents dynamic dict creation for instances, saving significant memory and speeding up attribute access',
                'Enables automatic multiprocessing serialization',
                'Restricts the class from being inherited',
                'Turns the class into a C++ struct wrapper'
              ],
              correct: 0,
              explanation: '__slots__ reserves space for declared attributes using compact array pointers instead of a dynamic dictionary.'
            }
          ]
        },
        {
          id: 5,
          title: 'Kubernetes & KServe for LLM Inference',
          category: 'mlops',
          description: 'Test production deployment skills including vLLM KServe runtime, GPU sharing (MIG), HPA metrics, and Kueue gang-scheduling.',
          duration_mins: 30,
          questions_count: 5,
          difficulty: 'Advanced',
          pass_score_pct: 75,
          questions: [
            {
              q: 'In KServe v2 Data Plane protocol, what is the recommended metric for Horizontal Pod Autoscaling (HPA) of LLM inference workers?',
              options: [
                'Concurrency / Average Queued Requests per replica (e.g., vLLM waiting request queue length)',
                'Standard CPU utilization percentage',
                'Pod disk I/O read bytes',
                'Node network packet count'
              ],
              correct: 0,
              explanation: 'LLM inference pods quickly saturate on request queues before CPU/GPU metrics spike, making waiting queue length the ideal scaling metric.'
            },
            {
              q: 'How does Multi-Instance GPU (MIG) on NVIDIA A100/H100 benefit multi-tenant model serving?',
              options: [
                'Partitions a single physical GPU into up to 7 isolated GPU instances with dedicated memory, SMs, and memory bus',
                'Doubles the GPU clock frequency during peak traffic',
                'Allows running Linux and Windows containers concurrently on one chip',
                'Automatically converts FP32 weights to INT4'
              ],
              correct: 0,
              explanation: 'NVIDIA MIG partitions memory bandwidth, cache, and compute cores into hardware-isolated instances with guaranteed QoS.'
            },
            {
              q: 'What problem does Kubernetes Kueue batch scheduler solve for distributed ML training jobs?',
              options: [
                'Gang scheduling: ensures all pods of a distributed multi-node job are scheduled simultaneously or none at all, preventing resource deadlocks',
                'Automatically tunes PyTorch learning rate based on GPU temperature',
                'Compiles Python code into WebAssembly',
                'Encrypts GPU communication using TLS 1.3'
              ],
              correct: 0,
              explanation: 'Gang scheduling prevents situations where partial pods occupy GPUs while waiting indefinitely for remaining pods.'
            },
            {
              q: 'Which component in the NVIDIA GPU Operator automatically mounts CUDA drivers and device nodes into Kubernetes worker pods?',
              options: [
                'NVIDIA Container Toolkit & Container Device Interface (CDI)',
                'CoreDNS',
                'Calico CNI plugin',
                'Kube-Proxy iptables rules'
              ],
              correct: 0,
              explanation: 'NVIDIA Container Toolkit injects GPU drivers, libraries, and dev nodes directly into container runtimes.'
            },
            {
              q: 'Why are init containers commonly used in enterprise LLM deployment manifests?',
              options: [
                'To download and verify model weights from object storage (e.g. S3/MinIO) into a shared memory volume before inference starts',
                'To compile the Kubernetes control plane binary',
                'To run user registration database migrations',
                'To simulate fake client traffic during startup'
              ],
              correct: 0,
              explanation: 'Init containers pre-fetch multi-gigabyte model weights and weights caches into shared storage before the serving engine boots.'
            }
          ]
        },
        {
          id: 6,
          title: 'Enterprise AI Governance, Red Teaming & Security',
          category: 'genai',
          description: 'Evaluate enterprise-grade defense against indirect prompt injections, jailbreaks, data leakage, and compliance standards.',
          duration_mins: 25,
          questions_count: 5,
          difficulty: 'Advanced',
          pass_score_pct: 75,
          questions: [
            {
              q: 'What constitutes an Indirect Prompt Injection attack in an enterprise Retrieval-Augmented Generation (RAG) system?',
              options: [
                'Malicious instructions embedded inside retrieved untrusted documents or web data that hijack the LLM execution flow',
                'Sending malformed JSON payloads to the REST API endpoint',
                'Physical tampering with GPU PCIe bus lines',
                'Flooding the tokenizer with out-of-vocabulary Unicode tokens'
              ],
              correct: 0,
              explanation: 'Indirect prompt injections occur when external data parsed by an agent contains adversary commands that hijack the system prompt.'
            },
            {
              q: 'Which defense strategy is most effective at preventing automated prompt leaks in enterprise chatbots?',
              options: [
                'Dual-LLM architecture (Privileged Executor + Unprivileged Untrusted Interface) combined with output guardrail classifiers (e.g., Llama Guard)',
                'Adding "Please do not reveal your instructions" to the prompt',
                'Setting temperature parameter to exactly 0.0',
                'Hiding the API behind Cloudflare bot mitigation only'
              ],
              correct: 0,
              explanation: 'Separating untrusted processing from execution and applying dedicated safety guardrail models provides reliable protection.'
            },
            {
              q: 'What is Differential Privacy in the context of fine-tuning enterprise LLMs on sensitive internal data?',
              options: [
                'Injecting calibrated mathematical noise (e.g., via DP-SGD) to bound the probability that any single individual record can be reconstructed',
                'Encrypting the weights file with AES-256 GCM',
                'Deleting training checkpoints after 30 days',
                'Anonymizing usernames with random UUIDs'
              ],
              correct: 0,
              explanation: 'DP-SGD adds controlled Gaussian noise and clips gradients so the trained model mathematically cannot leak individual training samples.'
            },
            {
              q: 'In the EU Artificial Intelligence Act, how are LLMs integrated into mission-critical infrastructure or HR recruitment classified?',
              options: [
                'High-Risk AI systems requiring mandatory risk management, data governance, logging, and human oversight',
                'Minimal Risk systems with zero regulatory requirements',
                'Prohibited systems that are completely banned in all circumstances',
                'Voluntary self-certification with no documentation'
              ],
              correct: 0,
              explanation: 'AI systems influencing critical infrastructure, hiring, or credit scoring fall under High-Risk classification.'
            },
            {
              q: 'What is Token Smuggling in LLM red teaming?',
              options: [
                'Encoding forbidden prompts using Base64, Rot13, or multi-language Unicode fragments to bypass input keyword filters',
                'Stealing API authorization bearer tokens over unencrypted HTTP',
                'Exploiting tokenizer memory buffer overflow vulnerabilities',
                'Saturating GPU memory with 32k repeated whitespace tokens'
              ],
              correct: 0,
              explanation: 'Token smuggling disguises malicious prompts using alternative encodings or ciphers that safety filters miss but the model decodes.'
            }
          ]
        }
      ],
      assessment_attempts: [
        {
          id: 1,
          assessment_id: 4,
          assessment_title: 'Advanced Python for AI: CPython Internals & Concurrency',
          score_pct: 94,
          status: 'Passed',
          benchmark_percentile: 'Top 3.2%',
          time_ago: '10 mins ago'
        },
        {
          id: 2,
          assessment_id: 1,
          assessment_title: 'System Design for Large Language Models',
          score_pct: 88,
          status: 'Passed',
          benchmark_percentile: 'Top 7.5%',
          time_ago: '3 days ago'
        },
        {
          id: 3,
          assessment_id: 2,
          assessment_title: 'Transformer Architectures & Attention Mechanisms',
          score_pct: 91,
          status: 'Passed',
          benchmark_percentile: 'Top 4.8%',
          time_ago: '1 week ago'
        }
      ],
      stages: [
        {
          stage_number: 1,
          title: 'Stage 1: Advanced Python Foundations & Engineering Core',
          description: 'CPython memory layout, GIL internals, asyncio architecture, and high-performance NumPy vectorization.',
          status: 'completed',
          progress_pct: 100,
          modules: [
            { id: 101, title: 'Python Memory Layout, Objects & Memory Optimization', subtitle: 'Pymalloc, small object arenas, and bytecode disassemblies', duration_mins: 180, is_completed: true },
            { id: 102, title: 'High-Throughput Concurrency: Asyncio & Multiprocessing', subtitle: 'Event loop architecture, non-blocking I/O, IPC queues', duration_mins: 240, is_completed: true },
            { id: 103, title: 'C-Extensions, Cython & PyBind11 Interoperability', subtitle: 'Writing native C++ accelerators for Python runtimes', duration_mins: 200, is_completed: true },
            { id: 104, title: 'CPython Internals, Memory Management & GIL', subtitle: 'Reference counting, cycle detection, and multiprocessing', duration_mins: 210, is_completed: true }
          ]
        },
        {
          stage_number: 2,
          title: 'Stage 2: Deep Learning Internals & Transformer Architectures',
          description: 'Autograd engine from scratch, attention mechanisms, rotary embeddings, and PyTorch internals.',
          status: 'in_progress',
          progress_pct: 60,
          modules: [
            { id: 201, title: 'Building an Autograd Engine from Scratch in Python', subtitle: 'Computational DAGs, backprop topological sort, and tape-based autodiff', duration_mins: 300, is_completed: true },
            { id: 202, title: 'Multi-Head Attention & KV Caching Mechanics', subtitle: 'Scaled dot-product attention, masking, and memory footprints', duration_mins: 270, is_completed: true },
            { id: 203, title: 'Modern Tokenization & Rotary Position Embeddings (RoPE)', subtitle: 'Byte-Pair Encoding (BPE), TikToken, and rotational embeddings', duration_mins: 240, is_completed: true },
            { id: 204, title: 'Multi-Head Self-Attention in PyTorch', subtitle: 'CUDA tensor operations, forward/backward implementations, hands-on lab', duration_mins: 180, is_completed: false },
            { id: 205, title: 'Transformer Block Normalization & Residual Streams', subtitle: 'Pre-LN vs Post-LN, RMSNorm, and residual scale stabilization', duration_mins: 200, is_completed: false }
          ]
        },
        {
          stage_number: 3,
          title: 'Stage 3: Distributed Training & Large-Scale MLOps',
          description: 'Multi-GPU scaling, ZeRO optimizer partitioning, Megatron tensor parallelism, and Slurm clusters.',
          status: 'locked',
          progress_pct: 0,
          modules: [
            { id: 301, title: 'PyTorch Distributed Data Parallel (DDP) Internals', subtitle: 'NCCL collective communications, Ring-AllReduce, and bucket tuning', duration_mins: 240, is_completed: false },
            { id: 302, title: 'DeepSpeed ZeRO-1, 2, and 3 Memory Sharding', subtitle: 'Eliminating redundant optimizer and parameter states', duration_mins: 320, is_completed: false },
            { id: 303, title: 'Megatron-LM 3D Parallelism', subtitle: 'Tensor, pipeline, and data parallel coordination for 70B+ models', duration_mins: 360, is_completed: false }
          ]
        },
        {
          stage_number: 4,
          title: 'Stage 4: Enterprise LLM Architecture & Production Guardrails',
          description: 'Continuous batching, vLLM PagedAttention, speculative decoding, and safety red teaming.',
          status: 'locked',
          progress_pct: 0,
          modules: [
            { id: 401, title: 'vLLM & PagedAttention Dynamic Memory Management', subtitle: 'Sub-millisecond token generation and KV cache eviction', duration_mins: 280, is_completed: false },
            { id: 402, title: 'Speculative Decoding with Draft Verification Models', subtitle: '2.5x inference speedup without precision degradation', duration_mins: 220, is_completed: false },
            { id: 403, title: 'Enterprise Guardrails & Red-Teaming Defense', subtitle: 'Mitigating indirect prompt injections and hallucination cascades', duration_mins: 260, is_completed: false }
          ]
        }
      ],
      recommendations: [
        {
          id: 1,
          title: 'Production Kubernetes for AI Workloads',
          category: 'MLOps & GPU Scheduling',
          match_pct: 96,
          reason_text: 'Directly bridges your highest-weighted target role gap (-26% in Distributed GPU Scheduling).',
          duration_hours: 18,
          case_studies_count: 6,
          status: 'active',
          priority_tag: 'Critical Priority'
        },
        {
          id: 2,
          title: 'Enterprise AI Governance, Red Teaming & Guardrails',
          category: 'AI Safety & Security',
          match_pct: 94,
          reason_text: 'Standard prerequisite for Architect-level roles in financial and enterprise AI sectors.',
          duration_hours: 12,
          case_studies_count: 4,
          status: 'active',
          priority_tag: 'High Match'
        },
        {
          id: 3,
          title: 'Triton Custom Kernels & FlashAttention-2',
          category: 'GPU Kernel Engineering',
          match_pct: 91,
          reason_text: 'Highly valued skill that compliments your 96% PyTorch mastery level.',
          duration_hours: 22,
          case_studies_count: 10,
          status: 'active',
          priority_tag: 'Recommended'
        }
      ],
      notifications: [
        { id: 1, title: '🎉 Assessment Completed!', message: 'You scored 94% on Advanced Python for AI.', time_ago: '10 mins ago', is_read: false },
        { id: 2, title: '✨ New Recommendation', message: 'System Design for LLMs matches your target role (Lead AI Architect).', time_ago: '2 hours ago', is_read: false },
        { id: 3, title: '🔥 Streak Milestone', message: 'You have maintained a 14-day active learning streak!', time_ago: 'Yesterday', is_read: false }
      ],
      activity_log: [
        { id: 1, title: 'Passed Assessment: Advanced Python for AI', description: 'Score: 94% • +150 XP earned • Added to Skill Matrix', time_ago: 'Today at 6:45 PM', status: 'Passed' },
        { id: 2, title: 'Completed Module: Modern Tokenization & RoPE', description: 'Stage 2 &bull; +50 XP gained &bull; Verified Hands-on Lab', time_ago: 'Yesterday', status: 'Passed' },
        { id: 3, title: 'Enrolled in System Design for GenAI', description: '14 Hours masterclass &bull; +50 XP gained', time_ago: '2 days ago', status: 'Passed' }
      ],
      chat_messages: [
        {
          id: 1,
          sender: 'assistant',
          message_html: `
            <p>Hello <strong>Engineer</strong>! 👋 I am your personalized Skill Intelligence Copilot.</p>
            <p style="margin-top: 8px;">I have full context on your profile (Senior AI/ML Engineer), your target role (<strong>Lead AI Architect</strong>, 78% Readiness), and your active streak (<strong>14 Days</strong> 🔥).</p>
            <p style="margin-top: 8px;">How can I accelerate your learning today? You can ask me to explain algorithms, review code, or design study roadmaps.</p>
          `
        }
      ]
    };
  }

  function getDb() {
    try {
      const stored = localStorage.getItem(STORAGE_KEY);
      if (stored) {
        const parsed = JSON.parse(stored);
        if (parsed && parsed.user && (parsed.user.email === 'alex.morgan@enterprise.ai' || parsed.user.full_name === 'Alex Morgan')) {
          let activeAuth = null;
          try {
            const raw = sessionStorage.getItem('si_auth_user') || localStorage.getItem('si_auth_user');
            if (raw) activeAuth = JSON.parse(raw);
          } catch (_) {}
          parsed.user = activeAuth;
          saveDb(parsed);
        }
        return parsed;
      }
    } catch (e) {
      console.warn('LocalStorage access error, resetting mock DB:', e);
    }
    const fresh = getInitialSeedData();
    saveDb(fresh);
    return fresh;
  }

  function saveDb(db) {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(db));
      window.dispatchEvent(new CustomEvent('si:data-changed', { detail: { timestamp: Date.now() } }));
    } catch (e) {
      console.error('Failed to save to localStorage:', e);
    }
  }

  async function request(endpoint, method = 'GET', data = null) {
    const url = API_BASE + endpoint;
    const headers = { 'Accept': 'application/json' };
    try {
      const authRaw = sessionStorage.getItem('si_auth_user') || localStorage.getItem('si_auth_user');
      if (authRaw) {
        const authUser = JSON.parse(authRaw);
        if (authUser && authUser.id) {
          headers['X-User-Id'] = String(authUser.id);
          if (authUser.email) headers['X-User-Email'] = authUser.email;
        }
      }
    } catch (_) {}

    const options = {
      method: method,
      credentials: 'include',
      headers: headers
    };

    if (data && (method === 'POST' || method === 'PUT')) {
      options.headers['Content-Type'] = 'application/json';
      options.body = JSON.stringify(data);
    }

    try {
      const resp = await fetch(url, options);
      if (resp.ok) {
        const text = await resp.text();
        try {
          const json = JSON.parse(text);
          if (json && json.status === 'success') {
            return json;
          }
        } catch (_) {

        }
      }
    } catch (err) {

    }

    return handleLocalMockRequest(endpoint, method, data);
  }

  function handleLocalMockRequest(endpoint, method, data) {
    const db = getDb();
    const cleanEndpoint = endpoint.split('?')[0];

    if (cleanEndpoint === 'user.php') {
      if (method === 'POST') {
        if (data.action === 'update_profile') {
          if (!db.user) db.user = {};
          if (data.full_name) db.user.full_name = data.full_name;
          if (data.email) db.user.email = data.email;
          if (data.role_title) db.user.role_title = data.role_title;
          if (data.target_role) db.user.target_role = data.target_role;
          if (data.bio) db.user.bio = data.bio;
          try {
            sessionStorage.setItem('si_auth_user', JSON.stringify(db.user));
            localStorage.setItem('si_auth_user', JSON.stringify(db.user));
          } catch (_) {}
          saveDb(db);
          return { status: 'success', message: 'Profile updated successfully', data: { user: db.user } };
        } else if (data.action === 'update_settings') {
          Object.assign(db.settings, data);
          saveDb(db);
          return { status: 'success', message: 'Settings updated successfully', data: { settings: db.settings } };
        }
      }
      return {
        status: 'success',
        data: {
          user: db.user,
          settings: db.settings,
          stats: db.stats
        }
      };
    }

    if (cleanEndpoint === 'dashboard.php') {
      const activeStage = db.stages.find(s => s.status === 'in_progress') || db.stages[0];
      const nextMod = activeStage ? activeStage.modules.find(m => !m.is_completed) : null;

      return {
        status: 'success',
        data: {
          user: db.user,
          kpis: {
            readiness_pct: db.stats.target_readiness_pct,
            active_streak_days: db.stats.active_streak_days,
            total_xp: db.user.karma_xp,
            mastered_competencies: db.stats.mastered_competencies_count,
            total_competencies: db.stats.total_competencies_count,
            pass_rate_pct: db.stats.pass_rate_pct,
            total_hours: db.stats.total_hours_invested
          },
          current_milestone: {
            stage_number: activeStage ? activeStage.stage_number : 1,
            stage_title: activeStage ? activeStage.title : 'Stage 1',
            progress_pct: activeStage ? activeStage.progress_pct : 0,
            next_module: nextMod ? { title: nextMod.title, duration_mins: nextMod.duration_mins } : null
          },
          competencies: db.skills,
          recent_assessments: db.assessment_attempts
        }
      };
    }

    if (cleanEndpoint === 'assessments.php') {
      if (method === 'POST' && data && data.action === 'submit') {
        const assessmentId = parseInt(data.assessment_id, 10);
        const userAnswers = data.answers || {};
        const test = db.assessments.find(a => a.id === assessmentId) || db.assessments[0];

        let correctCount = 0;
        test.questions.forEach((q, idx) => {
          if (userAnswers[idx] === q.correct) {
            correctCount++;
          }
        });

        const scorePct = Math.round((correctCount / test.questions.length) * 100);
        const passed = scorePct >= (test.pass_score_pct || 75);
        const status = passed ? 'Passed' : 'Failed';
        const benchmark = scorePct >= 90 ? 'Top 3%' : (scorePct >= 80 ? 'Top 7%' : 'Top 15%');
        const xpEarned = passed ? 150 : 50;

        db.user.karma_xp += xpEarned;

        const newAttempt = {
          id: Date.now(),
          assessment_id: test.id,
          assessment_title: test.title,
          score_pct: scorePct,
          status: status,
          benchmark_percentile: benchmark,
          time_ago: 'Just now'
        };

        db.assessment_attempts.unshift(newAttempt);

        db.activity_log.unshift({
          id: Date.now(),
          title: `Assessment Attempt: ${test.title}`,
          description: `Score: ${scorePct}% (${status}) • ${benchmark} • +${xpEarned} XP`,
          time_ago: 'Just now',
          status: status
        });

        saveDb(db);

        return {
          status: 'success',
          message: 'Assessment submitted successfully',
          data: {
            score_pct: scorePct,
            status: status,
            benchmark_percentile: benchmark,
            xp_earned: xpEarned,
            attempt: newAttempt
          }
        };
      }

      return {
        status: 'success',
        data: {
          assessments: db.assessments,
          history: db.assessment_attempts
        }
      };
    }

    if (cleanEndpoint === 'courses.php') {
      if (method === 'POST' && data) {
        if (data.action === 'enroll') {
          const cId = parseInt(data.course_id, 10);
          const course = db.courses.find(c => c.id === cId);
          if (course) {
            course.is_enrolled = true;
            course.enrollment_progress = 0;
            db.user.karma_xp += 50;

            db.notifications.unshift({
              id: Date.now(),
              title: '🎓 Course Enrolled',
              message: `You successfully enrolled in "${course.title}". (+50 XP)`,
              time_ago: 'Just now',
              is_read: false
            });

            db.activity_log.unshift({
              id: Date.now(),
              title: `Enrolled in ${course.title}`,
              description: `${course.duration_hours} Hours course • +50 XP gained`,
              time_ago: 'Just now',
              status: 'Passed'
            });

            saveDb(db);
            return { status: 'success', message: 'Enrolled in course', data: { course: course } };
          }
        } else if (data.action === 'update_progress') {
          const cId = parseInt(data.course_id, 10);
          const course = db.courses.find(c => c.id === cId);
          if (course) {
            course.enrollment_progress = parseInt(data.progress_pct, 10);
            saveDb(db);
            return { status: 'success', message: 'Progress updated' };
          }
        }
      }

      return {
        status: 'success',
        data: db.courses
      };
    }

    if (cleanEndpoint === 'learning-path.php') {
      if (method === 'POST' && data && data.action === 'toggle_module') {
        const modId = parseInt(data.module_id, 10);
        let foundMod = null;
        let parentStage = null;

        for (const stg of db.stages) {
          const m = stg.modules.find(x => x.id === modId);
          if (m) {
            foundMod = m;
            parentStage = stg;
            break;
          }
        }

        if (foundMod && parentStage) {
          foundMod.is_completed = !foundMod.is_completed;
          if (foundMod.is_completed) {
            db.user.karma_xp += 50;
            db.activity_log.unshift({
              id: Date.now(),
              title: `Completed Module: ${foundMod.title}`,
              description: `${parentStage.title} • +50 XP gained`,
              time_ago: 'Just now',
              status: 'Passed'
            });
          }

          const done = parentStage.modules.filter(m => m.is_completed).length;
          parentStage.progress_pct = Math.round((done / parentStage.modules.length) * 100);
          if (parentStage.progress_pct === 100) {
            parentStage.status = 'completed';

            const nextStage = db.stages.find(s => s.stage_number === parentStage.stage_number + 1);
            if (nextStage && nextStage.status === 'locked') {
              nextStage.status = 'in_progress';
            }
          }

          saveDb(db);

          return {
            status: 'success',
            data: {
              is_completed: foundMod.is_completed,
              stage_progress: parentStage.progress_pct
            }
          };
        }
      }

      let totalMods = 0;
      let completedMods = 0;
      let totalHours = 0;

      db.stages.forEach(s => {
        s.modules.forEach(m => {
          totalMods++;
          totalHours += (m.duration_mins || 60) / 60;
          if (m.is_completed) completedMods++;
        });
      });

      const overallPct = totalMods > 0 ? Math.round((completedMods / totalMods) * 100) : 0;

      return {
        status: 'success',
        data: {
          overall_journey: {
            completion_pct: overallPct,
            completed_modules: completedMods,
            total_modules: totalMods,
            hours_invested: Math.round(totalHours)
          },
          stages: db.stages
        }
      };
    }

    if (cleanEndpoint === 'recommendations.php') {
      if (method === 'POST' && data) {
        const rId = parseInt(data.rec_id, 10);
        const rec = db.recommendations.find(r => r.id === rId);

        if (data.action === 'dismiss') {
          if (rec) {
            rec.status = 'dismissed';
            saveDb(db);
            return { status: 'success', message: 'Recommendation dismissed' };
          }
        } else if (data.action === 'add_to_path') {
          if (rec) {
            rec.status = 'enrolled';
            db.stats.target_readiness_pct = Math.min(95, db.stats.target_readiness_pct + 4);
            db.user.karma_xp += 25;

            const matchedCourse = db.courses.find(c => c.title.toLowerCase().includes(rec.title.toLowerCase()) || rec.title.toLowerCase().includes(c.title.toLowerCase()));
            if (matchedCourse) {
              matchedCourse.is_enrolled = true;
            }

            db.notifications.unshift({
              id: Date.now(),
              title: '🗺️ Added to Learning Path',
              message: `"${rec.title}" is now added to your roadmap. Target readiness elevated to ${db.stats.target_readiness_pct}%.`,
              time_ago: 'Just now',
              is_read: false
            });

            saveDb(db);
            return { status: 'success', message: 'Added to learning path', data: { readiness_pct: db.stats.target_readiness_pct } };
          }
        }
      }

      const activeRecs = db.recommendations.filter(r => r.status !== 'dismissed');

      return {
        status: 'success',
        data: {
          gap_analysis: {
            target_role: db.user.target_role,
            readiness_pct: db.stats.target_readiness_pct,
            mastered_competencies: db.stats.mastered_competencies_count,
            total_competencies: db.stats.total_competencies_count
          },
          recommendations: activeRecs
        }
      };
    }

    if (cleanEndpoint === 'progress.php') {
      const totalWeekly = db.stats.weekly_hours.reduce((acc, d) => acc + d.hours, 0);

      return {
        status: 'success',
        data: {
          kpis: {
            total_hours: db.stats.total_hours_invested,
            active_streak_days: db.stats.active_streak_days,
            pass_rate_pct: db.stats.pass_rate_pct,
            total_xp: db.user.karma_xp,
            benchmark_rank: db.user.rank_percentile
          },
          weekly_study_hours: {
            total_this_week: totalWeekly.toFixed(1),
            target_goal: db.settings.weekly_hours_target || 15,
            days: db.stats.weekly_hours
          },
          assessment_history: db.assessment_attempts,
          activity_log: db.activity_log
        }
      };
    }

    if (cleanEndpoint === 'notifications.php') {
      if (method === 'POST' && data && data.action === 'mark_read') {
        db.notifications.forEach(n => { n.is_read = true; });
        saveDb(db);
        return { status: 'success', message: 'All notifications marked read' };
      }

      const unreadCount = db.notifications.filter(n => !n.is_read).length;
      return {
        status: 'success',
        data: {
          unread_count: unreadCount,
          items: db.notifications
        }
      };
    }

    if (cleanEndpoint === 'skills.php') {
      if (method === 'POST' && data && data.action === 'endorse') {
        const sId = parseInt(data.skill_id, 10);
        const skill = db.skills.find(s => s.id === sId);
        if (skill) {
          skill.endorsements_count = (skill.endorsements_count || 0) + 1;
          db.user.karma_xp += 10;
          saveDb(db);
          return { status: 'success', data: { endorsements_count: skill.endorsements_count } };
        }
      }

      return {
        status: 'success',
        data: db.skills
      };
    }

    if (cleanEndpoint === 'chat.php') {
      if (method === 'POST' && data && data.message) {
        const userMsg = data.message.trim();
        db.chat_messages.push({
          id: Date.now(),
          sender: 'user',
          message_html: escapeHtml(userMsg)
        });

        const replyHtml = generateCopilotReply(userMsg, db);

        db.chat_messages.push({
          id: Date.now() + 1,
          sender: 'assistant',
          message_html: replyHtml
        });

        saveDb(db);

        return {
          status: 'success',
          data: {
            reply: replyHtml
          }
        };
      }

      return {
        status: 'success',
        data: db.chat_messages
      };
    }

    return { status: 'success', data: {} };
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  function generateCopilotReply(prompt, db) {
    const p = prompt.toLowerCase();

    if (p.includes('gap') || p.includes('architect') || p.includes('readiness')) {
      return `
        <p>🎯 <strong>Skill Gap Telemetry for ${db.user.target_role}:</strong></p>
        <p style="margin-top: 6px;">Your current profile readiness is <strong>${db.stats.target_readiness_pct}%</strong> with <strong>${db.stats.mastered_competencies_count} of ${db.stats.total_competencies_count}</strong> competencies verified.</p>
        <ul style="margin: 8px 0; padding-left: 20px;">
          <li><strong>Distributed GPU Scheduling (Gap: -26%):</strong> DeepSpeed ZeRO-3 memory partitioning and Megatron-LM tensor parallelism.</li>
          <li><strong>Enterprise AI Governance & Red Teaming (Gap: -18%):</strong> Safety guardrails, prompt injection mitigation, and audit compliance.</li>
        </ul>
        <p style="margin-top: 6px;">💡 <em>Recommendation:</em> Complete <a href="courses.html" style="font-weight:700; color:var(--brand-cyan);">Production Kubernetes for AI Workloads</a> to elevate your readiness score to <strong>86%+</strong>.</p>
      `;
    }

    if (p.includes('flashattention') || p.includes('kernel') || p.includes('triton')) {
      return `
        <p>⚡ <strong>FlashAttention-2 & SRAM Memory Optimization:</strong></p>
        <p style="margin-top: 6px;">Standard attention materializes an $O(N^2)$ attention matrix in high-bandwidth memory (HBM). FlashAttention-2 avoids this by tiling inputs into ultra-fast on-chip SRAM (~19 TB/s bandwidth on A100/H100) and using online softmax computation.</p>
        <div style="background:var(--bg-elevated); padding:10px 14px; border-radius:8px; margin:8px 0; border:1px solid var(--border-subtle); font-family:var(--font-mono); font-size:12px; color:#a5b4fc;">
          Triton Kernel Tip: Avoid global memory writes in the inner loop. Keep accumulator registers in fp32 while inputs are in fp16/bf16.
        </div>
        <p>Check out our hands-on course <a href="courses.html" style="font-weight:700; color:var(--brand-cyan);">FlashAttention-2 & Custom Triton CUDA Kernels</a> for working implementations.</p>
      `;
    }

    if (p.includes('study plan') || p.includes('30-day') || p.includes('roadmap')) {
      return `
        <p>📅 <strong>30-Day Accelerated Roadmap to Lead AI Architect:</strong></p>
        <ol style="margin: 8px 0; padding-left: 20px;">
          <li><strong>Week 1 (Current):</strong> Finalize Transformer Block Normalization in <a href="learning-path.html" style="font-weight:700; color:var(--brand-cyan);">Stage 2</a> (+100 XP).</li>
          <li><strong>Week 2:</strong> Master Distributed Data Parallel (DDP) and NCCL Ring-AllReduce in Stage 3.</li>
          <li><strong>Week 3:</strong> Deploy KServe & vLLM with PagedAttention on Kubernetes.</li>
          <li><strong>Week 4:</strong> Take the <a href="assessments.html" style="font-weight:700; color:var(--brand-cyan);">System Design for LLMs Assessment</a> to earn your verified credential.</li>
        </ol>
        <p>Your current velocity: <strong>14.2 hrs/week</strong> (Target: ${db.settings.weekly_hours_target} hrs/week). You are on track for Q3 milestone completion!</p>
      `;
    }

    if (p.includes('assessment') || p.includes('test') || p.includes('quiz')) {
      return `
        <p>📝 <strong>Recommended Next Assessment:</strong></p>
        <p style="margin-top: 6px;">Based on your completed modules, you are ready for:</p>
        <div style="padding:10px 14px; background:var(--bg-elevated); border-radius:8px; margin:8px 0; border:1px solid var(--border-subtle);">
          <strong style="color:#fff;">System Design for Large Language Models</strong>
          <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">30 Mins • 5 Questions • Advanced Difficulty • Pass Score: 75%</div>
        </div>
        <p><a href="assessments.html" class="btn btn-primary btn-sm" style="display:inline-block; margin-top:6px;">Launch Assessment ➔</a></p>
      `;
    }

    return `
      <p>In enterprise AI engineering, architecting low-latency LLM serving requires tight integration between GPU hardware kernels (FlashAttention/Triton), memory sharding (ZeRO/Megatron), and batching schedulers (vLLM PagedAttention).</p>
      <p style="margin-top: 8px;">Let me know what specific component you'd like to inspect, or select one of the suggested prompts below to explore your skill gap telemetry.</p>
    `;
  }

  window.api = {
    getUser: () => request('user.php'),
    updateUser: (profileData) => request('user.php', 'POST', { action: 'update_profile', ...profileData }),
    updateSettings: (settingsData) => request('user.php', 'POST', { action: 'update_settings', ...settingsData }),
    getDashboard: () => request('dashboard.php'),
    getAssessments: () => request('assessments.php'),
    submitAssessment: (assessmentId, answers) => request('assessments.php', 'POST', {
      action: 'submit',
      assessment_id: assessmentId,
      answers: answers
    }),
    getCourses: () => request('courses.php'),
    enrollCourse: (courseId, courseTitle = '') => request('courses.php', 'POST', {
      action: 'enroll',
      course_id: courseId,
      course_title: courseTitle
    }),
    updateCourseProgress: (courseId, progressPct) => request('courses.php', 'POST', {
      action: 'update_progress',
      course_id: courseId,
      progress_pct: progressPct
    }),
    getLearningPath: () => request('learning-path.php'),
    toggleModule: (moduleId, moduleTitle = '') => request('learning-path.php', 'POST', {
      action: 'toggle_module',
      module_id: moduleId,
      module_title: moduleTitle
    }),
    getRecommendations: () => request('recommendations.php'),
    dismissRecommendation: (recId, recTitle = '') => request('recommendations.php', 'POST', {
      action: 'dismiss',
      rec_id: recId,
      rec_title: recTitle
    }),
    addRecommendationToPath: (recId, recTitle = '') => request('recommendations.php', 'POST', {
      action: 'add_to_path',
      rec_id: recId,
      rec_title: recTitle
    }),
    getProgress: () => request('progress.php'),
    getNotifications: () => request('notifications.php'),
    markNotificationsRead: () => request('notifications.php', 'POST', { action: 'mark_read' }),
    getSkills: () => request('skills.php'),
    endorseSkill: (skillId, skillName = '') => request('skills.php', 'POST', {
      action: 'endorse',
      skill_id: skillId,
      skill_name: skillName
    }),
    getChatMessages: () => request('chat.php'),
    sendChatMessage: (message) => request('chat.php', 'POST', { message: message })
  };

  async function syncGlobalHeader() {
    const userRes = await window.api.getUser();
    if (userRes && userRes.status === 'success' && userRes.data) {
      const user = userRes.data.user;
      if (user) {
        document.querySelectorAll('.user-avatar').forEach(el => {
          el.textContent = user.avatar_initials || 'AM';
        });

        const parts = (user.full_name || 'User').split(' ');
        const shortName = parts.length > 1 ? `${parts[0]} ${parts[1][0]}.` : parts[0];
        document.querySelectorAll('.nav-user-name').forEach(el => {
          el.textContent = shortName;
        });

        const dropHeader = document.querySelector('#user-dropdown .dropdown-header');
        if (dropHeader) {
          dropHeader.innerHTML = `
            <div>
              <strong>${user.full_name}</strong>
              <div style="font-size:11px;color:var(--text-muted);">${user.email}</div>
              <div style="font-size:10px;color:var(--brand-cyan);font-weight:700;margin-top:2px;">${user.role_title} • ${Number(user.karma_xp).toLocaleString()} XP</div>
            </div>
          `;
        }
      }
    }

    const notifRes = await window.api.getNotifications();
    if (notifRes && notifRes.status === 'success' && notifRes.data) {
      const unreadCount = notifRes.data.unread_count || 0;
      const items = notifRes.data.items || [];

      const badgeDot = document.querySelector('.notification-badge-dot');
      if (badgeDot) {
        badgeDot.style.display = unreadCount > 0 ? 'block' : 'none';
      }

      const notifCountTitle = document.querySelector('#notif-dropdown .dropdown-header strong');
      if (notifCountTitle) {
        notifCountTitle.textContent = `Notifications (${unreadCount})`;
      }

      const notifList = document.getElementById('notification-list');
      if (notifList && items.length > 0) {
        notifList.innerHTML = items.map(n => `
          <div class="notification-item ${n.is_read ? 'read' : 'unread'}">
            <h4>${n.title}</h4>
            <p>${n.message}</p>
            <span class="time">${n.time_ago || 'Just now'}</span>
          </div>
        `).join('');
      }
    }
  }

  window.clearNotifications = async function (e) {
    if (e) e.stopPropagation();
    await window.api.markNotificationsRead();
    const badgeDot = document.querySelector('.notification-badge-dot');
    if (badgeDot) badgeDot.style.display = 'none';

    const notifCountTitle = document.querySelector('#notif-dropdown .dropdown-header strong');
    if (notifCountTitle) notifCountTitle.textContent = 'Notifications (0)';

    const list = document.getElementById('notification-list');
    if (list) {
      list.innerHTML = `<div style="text-align: center; padding: 20px; color: var(--text-muted); font-size: 12px;">No new notifications</div>`;
    }
    if (window.showToast) {
      window.showToast('Notifications Marked Read', 'All notifications caught up.', '🔔');
    }
  };

  window.syncGlobalHeader = syncGlobalHeader;

  window.addEventListener('si:data-changed', () => {
    syncGlobalHeader();
  });

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', syncGlobalHeader);
  } else {
    syncGlobalHeader();
  }

})();
