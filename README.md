<div align="center">
  <img src="https://ui-avatars.com/api/?name=Counsel+OS&background=091426&color=fff&size=128&rounded=true" alt="CounselOS Logo">
  <h1>CounselOS</h1>
  <p><strong>AI-Powered Legal Matter Intelligence Platform</strong></p>
</div>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#ai-prompt-architecture">AI Prompt Architecture</a> •
  <a href="#tech-stack">Tech Stack</a> •
  <a href="#local-setup--demo">Local Setup & Demo</a>
</p>

---

**CounselOS** is an intelligent, streamlined legal practice management MVP designed specifically for small firms and solo attorneys. It moves beyond standard CRM by integrating Google's Gemini AI directly into the document review and research workflows, turning static contracts into actionable intelligence.

<!-- TODO: Add screenshot at docs/screenshots/dashboard.png -->

## Features

- 💼 **Client & Matter Management:** Organize your firm's active cases, clients, and statuses in a clean, modern UI (Lexis Modern design system).
- 📄 **Document Processing Pipeline:** Upload contracts (PDF/DOCX) securely. Text is extracted in the background via queued workers without blocking the UI.
- 🤖 **AI-Generated Insights:** Automatic parsing of key parties, risks, obligations, and deadlines directly from complex legal documents using structured JSON responses.
- 📅 **Actionable Deadlines:** One-click conversion of AI-discovered document deadlines into trackable system tasks.
- 🔍 **Context-Aware Research:** A built-in chat interface that builds contextual prompts from a matter's document insights, allowing attorneys to ask specific questions about a case and receive accurate, cited answers.

---

## AI Prompt Architecture

CounselOS stands out through its highly structured **Prompt Architecture**, located in `app/Support/Prompts/`. Instead of haphazardly sending raw document text to the LLM, the system employs a multi-tiered context builder:

1. **Extraction (Queue-Driven):** `pdfparser` and `phpword` extract raw text securely in the background.
2. **Analysis (JSON Schema):** The `AiAnalysisService` instructs Gemini to return strictly typed JSON (`DocumentInsight`), condensing a 20-page contract into structured intelligence (Risks, Obligations, Deadlines) while discarding noise.
3. **Research Context Assembly:** When an attorney asks a question via the Research tab, the `ResearchService` does **not** send the raw text of all documents (which would blow past context windows and introduce hallucinations). Instead, it injects the *condensed AI insights* into a highly constrained `MatterResearchPrompt`, ensuring answers are cited, relevant, and restricted to the known facts of the case.

<!-- TODO: Add screenshot at docs/screenshots/ai_insights.png -->

---

## Tech Stack

- **Framework:** [Laravel 12](https://laravel.com)
- **Database:** PostgreSQL 16
- **Frontend:** Blade, [Tailwind CSS](https://tailwindcss.com/) (Custom *Lexis Modern* Theme), Alpine.js
- **AI Integration:** Google Gemini API (`gemini-2.5-flash`) via direct HTTP client.
- **Background Processing:** Laravel Database Queues for asynchronous document extraction.

---

## Local Setup & Demo

CounselOS includes a robust demo seeder to instantly populate the application with realistic matters, clients, and pre-analyzed documents for portfolio demonstration.

### 1. Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- PostgreSQL
- A Google Gemini API Key

### 2. Installation
```bash
git clone https://github.com/yourusername/CounselOS.git
cd CounselOS

composer install
npm install
```

### 3. Configuration
Copy the environment file and set your database credentials and Gemini API key:
```bash
cp .env.example .env
php artisan key:generate
```
**Important `.env` variables:**
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=counselos
DB_USERNAME=your_username
DB_PASSWORD=your_password

GEMINI_API_KEY=your_api_key_here
QUEUE_CONNECTION=database
```

### 4. Database & Demo Seeding
Run the migrations and trigger the demo seeder:
```bash
php artisan migrate:fresh --seed
```

### 5. Running the Application
You will need two terminal windows to run the web server and the queue worker simultaneously.

**Terminal 1 (Web Server & Assets):**
```bash
npm run dev
php artisan serve
```

**Terminal 2 (Queue Worker):**
```bash
# Required for document extraction to process in the background
php artisan queue:work
```

### 6. Demo Login Credentials
Access the application at `http://localhost:8000` (or your configured local domain) using the seeded credentials:

- **Email:** `demo@counselos.test`
- **Password:** `password`

---

<div align="center">
  <i>Built with precision for modern legal workflows.</i>
</div>
