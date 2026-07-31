# CounselOS

**AI-powered legal matter intelligence platform.** Converts uploaded legal documents into structured, searchable insights — summaries, risks, obligations, and deadlines — inside a matter-scoped workspace built for small legal teams.

## Why I built this

Most portfolio projects are demos of a technology. I wanted to build something that demonstrates a *decision-making process* — the kind of judgment that's actually hard to fake in a take-home test or a tutorial clone.

The starting problem is real, not invented for the sake of a case study: small legal teams and solo attorneys lose real time hunting through folders, email threads, and manual notes for the risks, obligations, and deadlines buried inside contracts and leases, because the platforms built to solve this are priced and scoped for firms far larger than them. That gap — underserved by expensive, over-broad legal-tech suites — is where CounselOS sits.

But the deeper reason I built it this way was to prove something to myself as much as to anyone reviewing it: that I could take a project past the point where most of my past work has stalled. I've built plenty of things that were 80% done — the architecture sound, the core feature working — and then never shipped. CounselOS was built end-to-end on purpose: scoped MVP, real AI reliability engineering (not just an API call wrapped in a try/catch), an actual test suite, CI that runs on every push, and a real deployment — specifically to break that pattern.

The other deliberate choice: this isn't a chatbot bolted onto a CRUD app. Every AI feature here is load-bearing — document analysis feeds directly into task creation, research answers are grounded in the matter's own documents, not general knowledge. If you removed the AI layer, the product would no longer do the thing it's for. That's the bar I held it to.

Built solo in Laravel + PostgreSQL + Gemini, as a portfolio-grade MVP demonstrating product thinking, service-oriented architecture, AI prompt design, and pragmatic scope discipline under a real timeline.

---

## The problem

Legal professionals lose time hunting through folders, email threads, and manual notes for the risks, obligations, and deadlines buried inside contracts and leases. Full legal practice platforms are often too expensive or too broad for a small firm's actual workflow.

## The approach

CounselOS isn't a document store with legal branding — it's matter intelligence. Every uploaded document becomes summarized, risk-scored, and operationally useful: upload a lease, get back structured obligations and deadlines you can turn into tasks in one click, and ask plain-language questions about the matter grounded in what's actually been uploaded.

**Core demo flow:** create a matter → upload a document → AI generates structured insights → convert a deadline into a task → ask a research question grounded in the matter's documents.

> ⚠️ **AI safety note:** every AI-generated insight and research answer carries an explicit disclaimer. This is review assistance for a qualified legal professional, not legal advice — a boundary enforced in both the system prompts sent to Gemini and the UI itself.

---

## Screenshots

*(add screenshots of the dashboard, matter workspace, and AI Insights tab here before publishing)*

---

## Tech stack

| Layer | Choice |
|---|---|
| Backend | Laravel 12, PHP 8.2 |
| Database | PostgreSQL 16 |
| Frontend | Blade, Tailwind CSS, Alpine.js |
| AI | Gemini API (`gemini-2.5-flash`) via native `Http` facade — no external SDK |
| Text extraction | `smalot/pdfparser` (PDF), `phpoffice/phpword` (DOCX) |
| Queue | Database driver — extraction and AI document analysis run as background jobs |
| Scaffolding | Laravel Blueprint (models/migrations/CRUD foundation only) |
| Auth | Laravel Breeze |

---

## Architecture

```
Legal Professional
       |
       v
+---------------------------------------------------------+
| CounselOS (Laravel + Blade + Tailwind + Alpine)          |
+---------------------------+-------------------------------+
              |                              |
              v                              v
     +----------------+           +------------------------+
     |  PostgreSQL    |           |  Private file storage   |
     |  relational    |           |  (local disk; S3/R2     |
     |  data          |           |   planned at deploy)    |
     +----------------+           +------------------------+
              |
              v
     +----------------+
     |  Gemini API    |
     |  analysis + Q&A|
     +----------------+
```

**Internal layering:**

```
routes/web.php
    |
    v
Controllers -> Form Requests
    |
    v
Services: ClientService, MatterService, DocumentService,
          TextExtractionService, AiAnalysisService, GeminiClient,
          ResearchService, TaskService, TimelineService, SearchService
    |
    v
Models (Eloquent) -> PostgreSQL
    |
    +--> Storage facade -> private disk
    +--> Jobs (queued): ExtractDocumentTextJob, AnalyzeDocumentJob
```

Controllers are intentionally thin — they validate, delegate to a service, and redirect. All business logic (status transitions, activity logging, AI orchestration) lives in the service layer, which is where the actual engineering decisions are visible.

### Why documents and analysis are separate, queued steps

Text extraction and AI analysis are two independent background jobs (`ExtractDocumentTextJob`, `AnalyzeDocumentJob`), not one synchronous pipeline. This was a deliberate reliability decision: a slow or failing Gemini call should never block or corrupt a successful file upload, and a malformed/scanned PDF failing extraction shouldn't take down the analysis step with it. Each stage has its own status (`uploaded → extracting → analysis_pending → analyzed`, with a `failed` state and manual retry at either stage), and each job runs with `$tries = 1` — a failure here is a data or format problem that won't fix itself through automatic retries, so it's surfaced to the user with a clear error instead of retried silently.

---

## AI prompt architecture

Prompts are isolated in their own classes (`app/Support/Prompts/`), not inlined in service methods — `DocumentAnalysisPrompt` and `MatterResearchPrompt`. Both follow the same contract: a system prompt constraining the model to review-assistance-only, strict-JSON output for document analysis, and a fixed response format (short answer, supporting points, source documents, disclaimer) for research Q&A.

**Reliability layer:** `GeminiJsonParser` handles the real-world failure mode where Gemini wraps valid JSON in markdown fences despite being told not to — it strips fences, parses, and applies lenient validation (only `summary` is required; a document with no flagged risks isn't a parsing failure, it's a legitimate empty result).

**Research context strategy:** matter research is grounded in each analyzed document's *insight data* (summary, risks, obligations, deadlines) — not raw extracted text — to keep prompts small and avoid ballooning token usage across multi-document matters. This is the deliberately "dumb but working" MVP retrieval strategy; semantic retrieval via pgvector is a scoped-out future upgrade, not an oversight.

---

## Core features (shipped)

- Client and matter management with status lifecycle (`open → in_review → waiting_client → closed`), auto-logged to an activity timeline
- Document upload (PDF/DOCX, 20MB cap) to private storage with UUID-generated filenames
- Queued text extraction with empty-result detection (a "successfully parsed" scanned PDF with no real text is treated as a failure, not silently passed forward — OCR is out of scope)
- Queued Gemini analysis producing structured summaries, key parties, clauses, risks (severity-scored), obligations, deadlines, and lawyer-facing follow-up questions
- Matter-scoped research assistant, grounded in the matter's own analyzed documents, with saved Q&A history
- One-click "convert deadline to task" from AI Insights, with a prefilled-then-confirmed task form
- **Real full-text search** across document content (Postgres `tsvector` + GIN index + `ts_headline`), not just title matching — searches the actual extracted text of every uploaded document, with highlighted result snippets
- Global activity timeline per matter and dashboard-level recent activity feed
- Dashboard redesigned around a custom design system (Lexis Modern) — open matters, pending tasks, document counts, upcoming task list, recent documents, and a visual activity timeline
- **Filament-based internal admin panel** (gated behind an `is_admin` flag, not a full RBAC system) — operational visibility across clients/matters/documents/tasks/activity logs, dashboard widgets (open matters, pending tasks, documents awaiting processing, failed documents needing attention), one-click retry actions for failed extraction/analysis surfaced directly on the dashboard, and relation-manager drill-downs so a matter's documents and tasks are manageable inline
- Automated Pest test suite (Unit + Feature) with GitHub Actions CI running against a real Postgres service container on every push/PR — Gemini calls are faked, nothing hits a real API key in CI

## Known limitations

- **No OCR.** Scanned or image-based PDFs fail extraction by design; demo documents must be clean, text-based files.
- **No firm/user-level data isolation.** Every authenticated user can currently see every client and matter — this is an explicit MVP scope boundary (see spec Section 4), not an oversight. The Filament panel's `is_admin` flag controls *admin panel access only*, not a role/permission system.
- **No standalone Documents/Tasks index pages** outside a matter workspace — both are only accessible nested inside a matter.
- **Research retrieval is intentionally simple** (insight-based context, no embeddings) — accurate for MVP scope, not exhaustive for very large multi-document matters.

## Roadmap

- ~~Filament admin panel~~ — done
- ~~Automated Feature/Unit test coverage + CI~~ — done
- ~~Full-text document search~~ — done
- Deployment to Laravel Cloud — in progress (storage migration to S3-compatible Object Storage, since Cloud's filesystem is ephemeral)
- Document comparison — AI-driven diff/summary between two versions of a document
- pgvector-based semantic search for research (current retrieval is insight-based, not embedding-based)
- Multi-user firm accounts and real role-based permissions (the Filament `is_admin` flag is a simple access gate, not this)

---

## Local setup

```bash
composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
```

Configure `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=counselos_db
DB_USERNAME=counselos
DB_PASSWORD=secret

FILESYSTEM_DISK=local

QUEUE_CONNECTION=database

GEMINI_API_KEY=your-key-here
GEMINI_MODEL=gemini-2.5-flash
```

```bash
php artisan migrate
```

Run the app and the queue worker — extraction and AI analysis won't process without the worker running:

```bash
# Option A — all-in-one (uses the composer dev script)
composer run dev

# Option B — separate terminals
php artisan serve
php artisan queue:work
```

## Demo credentials

- **Email:** `demo@counselos.test`
- **Password:** `password`
- **Admin panel:** `http://localhost:8000/admin` (demo user has `is_admin = true`)

---

## Blueprint usage notes

Laravel Blueprint (`draft.yaml`) generated the initial models, migrations, and base CRUD controllers for all seven core entities. It was deliberately **not** used for AI-specific logic, prompt handling, JSON parsing, or nested route structures — those needed hand-written service classes and manually re-nested routes (Blueprint's `resource: web` shorthand generates flat routes; the API contract needed documents/tasks/research nested under `/matters/{matter}/...`).

One generation quirk worth flagging for anyone re-running `blueprint:build`: a `Dashboard` controller entry using `query: all` shorthand caused Blueprint to assume a non-existent `Dashboard` Eloquent model and generate a broken `Dashboard::all()` call. Dashboard is an aggregate view over several models, not a domain entity — that controller method was rewritten by hand.

## What I'd tell another engineer about this build

The riskiest part of this project was never the Laravel CRUD — it was the AI reliability layer: handling Gemini's occasional markdown-wrapped JSON, deciding what counts as a "failed" versus "empty but valid" analysis, and making sure a bad AI call degrades gracefully instead of corrupting document state. Keeping extraction and analysis as separate queued jobs with independent failure/retry states, rather than one synchronous pipeline, was the single architecture decision that made the AI layer trustworthy enough to demo confidently.

---

*CounselOS is a workflow and document review assistant. AI-generated insights are for professional review support only and do not constitute legal advice.*
