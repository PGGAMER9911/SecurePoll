# SecurePoll

> **Modern, Privacy-Centric, Auditable Polling & Organization Voting Platform**

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://www.php.net/)
[![Database](https://img.shields.io/badge/MySQL-8.0%2B-orange.svg)](https://www.mysql.com/)
[![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-v4-38bdf8.svg)](https://tailwindcss.com/)
[![License: Custom Source-Available](https://img.shields.io/badge/License-Custom%20Source--Available-red.svg)](LICENSE)

---

## ⚠️ Source-Available Project Notice

**SecurePoll is a source-available project governed by a custom licence. It is NOT an Open Source Initiative (OSI) approved open-source project.**

- The source code is publicly viewable for inspection, education, security review, and personal evaluation.
- **Attribution is mandatory**: Any permitted copying, redistribution, or modification must retain the original copyright notice and credit the author (`Parth Gamit / PGGAMER9911`).
- **Commercial use is prohibited** without prior written authorization from the copyright holder.
- **Public visibility on GitHub does not grant unrestricted permission** to reuse, monetize, host, or redistribute this software.
- For commercial licensing inquiries or written authorization, contact: [gamitparth04@gmail.com](mailto:gamitparth04@gmail.com).
- Full legal terms are documented in the [LICENSE](LICENSE) file.

---

## 📌 Overview

SecurePoll is an enterprise-grade polling and digital voting platform engineered with PHP and MySQL. Designed for universities, institutions, enterprises, and online communities, SecurePoll pairs rigorous cryptographic anonymity guarantees with deep multi-tenant organization management, tamper-evident audit trails, and device trust controls.

Whether conducting university student council elections, classroom instant feedback, departmental polls, or boardroom ballots, SecurePoll delivers privacy for voters without sacrificing administrative verifiability.

---

## ✨ Key Features

### 1. Multi-Tenant Organization Management
- **Hierarchical Scopes**: Supports multiple organization types (Universities, Colleges, Schools, Companies, Communities).
- **Sub-Units**: Organize members by Departments and custom Groups/Cohorts.
- **Role-Based Access Control (RBAC)**: Fine-grained permissions (`Owner`, `Admin`, `Teacher`, `Student`, `Member`, and custom roles).
- **Targeted Polling**: Restrict poll eligibility to specific organizations, departments, or member groups.
- **CSV Member Onboarding**: Bulk import users with automatic temporary password provisioning and email onboarding.

### 2. Multi-Mode Voting Engine
- **Single-Choice**: Standard single-option selection.
- **Multiple-Choice**: Select multiple options with configurable min/max choices.
- **Rating Scale**: 1-to-5 star or numerical rating.
- **Yes/No/Abstain**: Quick sentiment feedback ballots.
- **Result Visibility Rules**: Live results, results revealed after poll closes, or results restricted strictly to poll creators.

### 3. Privacy & Anti-Tamper Security Architecture
- **Cryptographic Anonymity**: Voter identity is uncoupled from vote records through salted cryptographic voter hashes (`voter_hash`), preventing deanonymization while strictly enforcing one-person-one-vote.
- **Device Fingerprinting & Trust**: Inspects device signatures, user agents, screen resolutions, and IP addresses to detect suspicious activity and enforce OTP challenge verification on unfamiliar devices.
- **Email OTP Verification**: Two-factor authentication (2FA) via time-sensitive one-time passcodes sent via PHPMailer (SMTP).
- **Tamper-Evident Audit Logs**: System-wide event tracking for security events, authentication changes, poll modifications, and moderation actions.
- **CSRF & Rate Limiting**: Token-bound form submissions and granular rate limiting across sensitive endpoints (login, voting, registration).
- **Single-Use Bound Invitation Tokens**: Private polls generate cryptographically secure, expiring invitation tokens bound to authorized voter accounts.

### 4. Background Automation & Scheduler
- Automated poll lifecycle management (transitioning `Draft` → `Scheduled` → `Active` → `Ended` → `Archived`).
- Queued email notification delivery for invitations, vote receipts, and reminder dispatches (`scheduler.php`).

### 5. Modern User Interface
- Clean, responsive UI with **Light**, **Dark**, and **System** theme modes.
- Built-in live progress bars, stateful button loading spinners, and bfcache safety.
- Interactive charts and real-time dashboard analytics.

---

## 🏗️ Architecture

```
SecurePoll/
├── app/
│   ├── Constants/      # Notification types and system constants
│   ├── Controllers/    # Application controllers (Auth, Poll, Org, Settings, Profile)
│   ├── Core/           # Router, Database singleton, Base Controller, Base Model
│   ├── Helpers/        # Environment parser (.env), CSRF security, Session manager
│   ├── Middleware/     # Auth guard, CSRF guard, Rate limiter
│   ├── Models/         # Database models (User, Poll, Vote, Organization, AuditLog)
│   ├── Services/       # Business logic (Auth, Poll, Email, Notification, Device)
│   └── Views/          # PHP view templates, partials, components, and layouts
├── config/             # Database and application configuration files
├── docs/               # Architecture, database schema, and migration documentation
│   ├── migrations/     # Versioned incremental SQL migration files and master runner
│   └── schema.sql      # Complete master DDL schema
├── public/             # Web server document root (index.php, CSS, JS, branding)
├── resources/          # Tailwind CSS source files
├── routes/             # Web routing definitions (web.php)
├── storage/            # Local cache, temporary files, and backup storage
└── scheduler.php       # CLI background queue worker and lifecycle scheduler
```

---

## 🚀 Getting Started

### Prerequisites

- **PHP 8.2+** with extensions: `pdo_mysql`, `curl`, `mbstring`, `openssl`
- **MySQL 8.0+** or **MariaDB 10.5+**
- **Composer** (v2+)
- **Node.js** (v18+) & **npm** (for compiling CSS assets)
- Local web server (e.g., Apache via XAMPP, Nginx, or PHP CLI built-in server)

---

### Installation Steps

#### 1. Clone the Repository
```bash
git clone https://github.com/PGGAMER9911/SecurePoll.git
cd SecurePoll
```

#### 2. Configure Environment Variables
Copy the template configuration file:
```bash
cp .env.example .env
```
Open `.env` in an editor and update your database and SMTP credentials:
```env
APP_NAME=SecurePoll
APP_ENV=development
APP_URL=http://localhost/SecurePoll/public
APP_TIMEZONE=Asia/Kolkata

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=securepoll_db
DB_USERNAME=root
DB_PASSWORD=

SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your_email@gmail.com
SMTP_PASSWORD=your_app_password
SMTP_ENCRYPTION=tls
SMTP_FROM_EMAIL=your_email@gmail.com
SMTP_FROM_NAME="SecurePoll"

APP_KEY=generate_a_random_32_character_key_here
```

#### 3. Install PHP Dependencies
```bash
composer install
```

#### 4. Initialize Database Schema
1. Create the database in MySQL:
   ```sql
   CREATE DATABASE securepoll_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Import the base schema:
   ```bash
   mysql -u root -p securepoll_db < docs/schema.sql
   ```
3. Run incremental migrations:
   ```bash
   php docs/migrations/run_migrations.php
   ```

#### 5. Build Front-End Assets (Optional)
```bash
npm install
npm run build:css
```

#### 6. Start the Web Server
If using Apache/XAMPP, place `SecurePoll` inside your `htdocs` folder and navigate to:
```
http://localhost/SecurePoll/public
```
Or start PHP's built-in development server:
```bash
php -S 127.0.0.1:8000 -t public
```

#### 7. Background Worker (Scheduler)
To process poll closures and queue email notifications, configure a cron job or execute:
```bash
php scheduler.php
```

---

## 🛡️ Security & Privacy Practices

- **Zero Plaintext Passwords**: All user credentials are protected with `bcrypt` / `ARGON2ID` algorithms via PHP's `password_hash()`.
- **Prepared Statements**: All database operations use `PDO` parameterized queries to prevent SQL injection.
- **Cross-Site Scripting (XSS)**: Output rendering uses `htmlspecialchars()` escaping.
- **CSRF Validation**: State-changing POST/PUT requests require synchronized session tokens.
- **Device Trust Challenge**: Logins from unrecognized browsers require email OTP validation before granting session trust.

---

## 📄 Licence and Terms of Use

SecurePoll is released under the **SecurePoll Custom Attribution and Non-Commercial Licence**.

### Summary of Conditions:
1. **Public Code Viewability**: You may view, clone, and inspect the code for learning, security research, and personal evaluation.
2. **Attribution Mandatory**: You must prominently credit **Parth Gamit (PGGAMER9911)** and link to [https://github.com/PGGAMER9911/SecurePoll](https://github.com/PGGAMER9911/SecurePoll) in all permitted copies or derivative works.
3. **Non-Commercial Only**: You may NOT sell, license, bundle into commercial offerings, host as a paid/commercial service (SaaS), or monetize this software in any form without prior express written authorization.
4. **No Implied Endorsement**: You may not use the SecurePoll name or author branding to imply endorsement or official affiliation.
5. **AS-IS Disclaimer**: The software is provided without warranties of any kind.
6. **Not Open Source**: This is a restricted source-available licence, not an OSI open-source licence.

For full legal terms, consult the [LICENSE](LICENSE) file.

### Commercial Licensing Contact
To request written permission for commercial use or custom enterprise licensing:
- **Author**: Parth Gamit
- **GitHub**: [@PGGAMER9911](https://github.com/PGGAMER9911)
- **Email**: [gamitparth04@gmail.com](mailto:gamitparth04@gmail.com)
