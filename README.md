# PageTurner Online Bookstore Management System

## Laboratory Activity 8 — AI Review Summarization & Sentiment Analysis

---

## Requirements

Before starting, make sure you have the following installed:

- PHP 8.3+
- Composer
- Node.js & npm
- MySQL
- XAMPP (or any local server)
- Ollama — download from [ollama.com](https://ollama.com)

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/your-username/your-repo-name.git
cd your-repo-name
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install frontend dependencies

```bash
npm install
```

### 4. Set up environment file

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure your `.env` file

Open `.env` and fill in your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pageturner_bookstore
DB_USERNAME=root
DB_PASSWORD=
```

Also add your AI configuration:

```env
GEMINI_API_KEY=your-gemini-api-key-here
GEMINI_MODEL=gemini-2.0-flash

AI_DEFAULT_PROVIDER=gemini
AI_FALLBACK_ENABLED=true

OLLAMA_ENABLED=true
OLLAMA_BASE_URL=http://localhost:11434
OLLAMA_MODEL=llama3.2
```

To get a free Gemini API key, go to [aistudio.google.com](https://aistudio.google.com), sign in, and click **Get API key**.

### 6. Run migrations

```bash
php artisan migrate
```

### 7. Seed the database

```bash
php artisan db:seed
```

> Note: The mass book seeder will insert 1,000,000 book records. This may take several minutes.

After seeding, add reviews for testing:

```bash
php artisan db:seed --class=ReviewSeeder
```

### 8. Build frontend assets

```bash
npm run build
```

### 9. Fix SSL certificates (Windows/XAMPP only)

If you are on Windows using XAMPP, you need to fix SSL for API calls to work.

1. Download the certificate file from [https://curl.se/ca/cacert.pem](https://curl.se/ca/cacert.pem) and save it as `C:\xampp\cacert.pem`
2. Find which `php.ini` your CLI is using:

```bash
php --ini
```

3. Open that `php.ini` file and set:

```ini
curl.cainfo = "C:\xampp\cacert.pem"
openssl.cafile = "C:\xampp\cacert.pem"
```

4. Save and close the terminal, then reopen it

### 10. Pull the Ollama model

```bash
ollama pull llama3.2
```

> This downloads about 2GB. Only needs to be done once.

---

## Running the Application

You need **3 terminals** running at the same time:

**Terminal 1 — Web server:**

```bash
php artisan serve
```

**Terminal 2 — Queue worker (required for AI jobs):**

```bash
php artisan queue:work --queue=ai-tasks,default
```

**Terminal 3 — Ollama (AI fallback):**
Ollama usually starts automatically. Verify it's running by visiting:
http://localhost:11434
If it shows `Ollama is running`, you're good. If not:

```bash
ollama serve
```

Then open your browser and go to:
http://127.0.0.1:8000

---

## Default Admin Account

Email: admin@pageturner.com
Password: password

---

## Using the AI Feature

1. Log in as admin
2. Go to any book page that has reviews
3. Scroll to the bottom — you will see a **"Generate AI Analysis"** button
4. Click it and wait a few seconds
5. Refresh the page — the AI sentiment card will appear showing:
    - Overall sentiment (Positive / Negative / Neutral / Mixed)
    - Sentiment score
    - Summary of reviews
    - Sentiment breakdown bar
    - Key themes

To view all AI usage and analyses, go to:
http://127.0.0.1:8000/admin/ai-dashboard

---

## AI Provider Notes

| Provider          | Role     | Free Tier          |
| ----------------- | -------- | ------------------ |
| Google Gemini     | Primary  | 1,500 requests/day |
| Ollama (llama3.2) | Fallback | Unlimited (local)  |

If Gemini hits its rate limit, the system automatically falls back to Ollama. No action needed.

---

## Troubleshooting

**Queue job keeps failing:**

```bash
php artisan queue:failed
php artisan tinker
DB::table('failed_jobs')->orderBy('id', 'desc')->first()->exception;
```

**SSL error on API calls:**
Follow Step 9 in the installation guide above.

**Ollama model not found:**

```bash
ollama pull llama3.2
```

**Config changes not reflecting:**

```bash
php artisan config:clear
php artisan cache:clear
```

---

## Project Structure (AI-related files)

app/
├── Services/
│ ├── AIServiceManager.php # Handles Gemini + Ollama with fallback
│ └── ReviewAnalysisService.php # Builds prompts and parses AI responses
├── Jobs/
│ └── AnalyzeBookReviews.php # Queue job for async AI processing
├── Models/
│ ├── AiReviewAnalysis.php # Stores analysis results
│ └── AiUsageLog.php # Tracks API usage per provider
├── Http/Controllers/
│ └── ReviewAnalysisController.php # Triggers analysis, shows dashboard
database/
├── migrations/
│ ├── ...\_create_ai_review_analyses_table.php
│ └── ...\_create_ai_usage_logs_table.php
config/
└── ai.php # AI provider configuration
resources/views/
├── books/show.blade.php # Displays AI sentiment card
└── admin/ai-dashboard.blade.php # Admin usage dashboard
