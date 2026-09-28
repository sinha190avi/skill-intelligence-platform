# Skill Intelligence Platform 🚀

An AI-driven Skill Intelligence and Career Pathway Platform built for **Smart India Hackathon (SIH)**.

---

## ✨ Features

- **🎯 Personalized Learning Paths**: Dynamic, role-based roadmaps tailored to industry demand and individual learner gaps.
- **🤖 AI Assistant Copilot**: Real-time guidance, code review, conceptual explanations, and automated feedback.
- **📊 Adaptive Skill Assessments**: Timed interactive assessments with instant scoring, feedback, and performance breakdowns.
- **🏆 Community & Leaderboards**: Peer collaboration, global leaderboards, hackathon challenges, and achievements.
- **📈 Analytics & Profile**: Visual skill matrices, verification badges, and comprehensive learning tracking.

---

## 🛠️ Tech Stack

- **Frontend**: Vanilla JavaScript (ES6+), HTML5, Modern CSS (Glassmorphism & Cyberpunk Design System)
- **Backend**: PHP 8.x (RESTful API architecture)
- **Database**: MySQL / MariaDB (Prepared statements, relational schemas)
- **Deployment**: Compatible with XAMPP, Apache, Nginx, Alwaysdata, and InfinityFree

---

## 🚀 Getting Started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL/MariaDB) or any PHP 8+ web server
- Web browser (Chrome, Edge, Firefox, Brave)

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/sinha190avi/skill-intelligence-platform.git
   ```
2. **Move to web root:**
   Place the project folder into your web server's document root (e.g., `C:/xampp/htdocs/sih`).

3. **Database Setup:**
   - Start Apache and MySQL in XAMPP.
   - Run the database initializer by navigating to:
     ```
     http://localhost/sih/init_database.php
     ```
     *Alternatively, import `database.sql` into phpMyAdmin.*

4. **Launch the Application:**
   Open your browser and navigate to:
   ```
   http://localhost/sih/
   ```

---

## 📂 Project Structure

```
sih/
├── api/                   # REST API backend handlers
│   ├── config.php         # Database and session configuration
│   ├── ...
├── sih/                   # Application frontend & assets
│   ├── landing.html       # Platform landing page
│   ├── dashboard.html     # Student & user dashboard
│   ├── learning-path.html # Personalized curriculum
│   ├── assessments.html   # Skill evaluation engine
│   ├── ai-assistant.html  # Interactive AI copilot
│   ├── community.html     # Forums & peer network
│   ├── leaderboard.html   # Rankings & gamification
│   ├── profile.html       # User skill profile & certificates
│   └── ...
├── database.sql           # Database schema and seed data
├── init_database.php      # Automated database initialization script
└── README.md
```

---

## 📄 License

This project is licensed under the MIT License.
