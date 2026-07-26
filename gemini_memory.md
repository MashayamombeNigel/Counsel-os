# CounselOS — Project Memory

> Last verified: 2026-07-26 (Epic 10 integration complete, full test suite passing)
> Update this file at the end of every epic. Do not let it drift.

---

## Stack

- Laravel 12, PostgreSQL 16 (port 5433 on this dev machine — both PG16 and PG17 are installed)
- Blade + Tailwind (custom design-token classes) + Alpine.js
- Gemini API (`gemini-2.5-flash`) via `Http` facade — no SDK
- `smalot/pdfparser` + `phpoffice/phpword` for extraction
- Database-driver queue (intentional — portfolio scale, no Redis needed)
- Filament v4 for admin — **complete as of Epic 10**
- Laravel Blueprint for initial scaffolding only (no ongoing use)

---

## DB Connection Note

Local dev Postgres is on **port 5433** (PG 16), not the default 5432. Both `.env` and `.env.testing` have been updated to reflect this. CI uses a fresh Docker container so port is irrelevant there.

---

## Epic Status

### ✅ Epic 0 — Scaffolding
Laravel 12 + Postgres initialized, Breeze auth installed. Blueprint generated base models/migrations/controllers from `draft.yaml`. Hand-fixed after generation:
- Foreign-key bug on `uploaded_by`/`created_by`
- `DashboardController` rewritten (Blueprint assumed a `Dashboard` model that doesn't exist)
- Routes re-nested (`/matters/{matter}/documents`, `/tasks`, `/research`) — Blueprint only generates flat routes

### ✅ Epic 1 — Foundation
Auth and layout via Breeze scaffold. Custom "Lexis Modern" redesign applied outside epic numbering — `resources/views/layouts/app.blade.php` is a topnav layout using Material Symbols and design-token Tailwind classes (`bg-surface-container-lowest`, `text-secondary`, etc.), not Breeze's default.

### ✅ Epic 2 — Clients & Matters
`ClientService`/`MatterService` with CRUD, search, and status-change activity logging. Thin controllers throughout. Views: `clients/*`, `matters/create`, `matters/show` (tabbed workspace: Overview / Documents / AI Insights / Research / Tasks / Timeline), `matters/index.blade.php` confirmed present.

### ✅ Epic 3 — Documents & Text Extraction
- `UploadDocumentRequest`: 20 MB cap, PDF/DOCX only
- `DocumentService::storeUpload()`: UUID filenames to avoid collisions and prevent user-supplied paths reaching storage
- Extraction is **queued** via `ExtractDocumentTextJob`, single try (no auto-retry — scanned PDFs won't parse differently on retry)
- `TextExtractionService`: <20 chars of extracted text treated as hard failure (no OCR, by design)
- Status flow: `uploaded → extracting → analysis_pending | failed`, retry from `failed` works

**Open bug:** `DocumentService` and `TextExtractionService` both hardcode `Storage::disk('local')`. Must become `Storage::disk(config('filesystems.default'))` before Epic 8 (deployment), or Laravel Cloud's auto-injected `FILESYSTEM_DISK=s3` will have no effect.

### ✅ Epic 4 — Gemini AI Analysis
- `GeminiClient`: thin `Http::post` wrapper, `temperature: 0.2` for consistent structured output
- `DocumentAnalysisPrompt`: isolated prompt class, truncates to 30,000 chars
- `GeminiJsonParser`: strips markdown fences (Gemini wraps JSON in ` ```json ``` ` despite instructions), lenient validation — only `summary` required, all other keys default to `[]`
- `AiAnalysisService`: calls Gemini, parses, persists `DocumentInsight` via `updateOrCreate`
- `AnalyzeDocumentJob`: queued, single try, `failed()` catches hard crashes that bypass the try/catch
- AI Insights tab renders summary/risks/obligations/deadlines with human-review disclaimer
- Raw Gemini response stored on `DocumentInsight` for debugging, never shown to users by default

### ✅ Epic 5 — Research, Tasks, Polish
- `ResearchService::buildMatterContext()`: uses analyzed document **insight data**, not raw `extracted_text` — keeps prompts small across multi-document matters
- `ResearchService::answerQuestion()`: synchronous Gemini call (not queued — deliberate, research should feel conversational)
- Task CRUD via `TaskService`/`TaskController`
- "Convert deadline to task" is a **prefilled form** — user confirms before saving, via query-param prefill into the Tasks tab
- `source_document_id` on `Task` tracks which AI-extracted deadline originated a task

### ✅ Epic 6 — Demo Data & Portfolio Polish
- `DemoDataSeeder`: creates `demo@counselos.test` / `password` with `is_admin => true`, Acme Property Holdings flagship dataset (Riverside lease matter, pre-analyzed synthetic document with named risks/obligations/deadlines, a converted-deadline task, a research session), plus filler clients/matters
- `dash/` and `dasdesign/` scratch folders confirmed absent from repo
- `README.md` written — **still stale**: CI badge and real screenshots not yet added

### ✅ Epic 7 — Testing + CI
Pest suite — 73 tests, 150 assertions, all passing.

**Unit (`tests/Unit/`):**
- `GeminiJsonParserTest`
- `TextExtractionServiceTest` (testable subclass, tests orchestration logic only)
- `AiAnalysisServiceTest` (`Http::fake`)
- `ResearchServiceTest`

**Feature (`tests/Feature/`):**
- `DocumentUploadTest`
- `DocumentProcessingTest` (`Queue::fake`, asserts job dispatch + retry gating)
- `MatterActivityLogTest`
- `TaskTest`
- `RealDocxExtractionTest` (generates a real docx via phpword's writer)
- `AuthProtectionTest` (guest-redirect sweep)
- `Auth/` subfolder (Breeze-generated profile/password tests)

**CI:** `.github/workflows/tests.yml` — Postgres service container, runs on push to `main` + PRs, Gemini calls faked.

**Test DB:** Dedicated Postgres database `counselos_test` — not SQLite, because the schema uses native enums.

---

### ✅ Epic 10 — Filament Admin Panel (COMPLETE)

Completed out of order (before Epics 8 and 9) per explicit roadmap decision.

**What was built:**
- Filament v4 installed (`filament/filament:^4.0`)
- `filament:install --panels` scaffolded `app/Providers/Filament/AdminPanelProvider.php` (registered in `bootstrap/providers.php`). Panel path: `/admin`. Uses `discoverResources` — no manual registration needed for new resources.
- Migration: `2026_07_26_000000_add_is_admin_to_users_table.php` — adds `boolean is_admin default false` to `users`
- `User` model: implements `FilamentUser`, `is_admin` added to `$fillable` and cast as `boolean`, `canAccessPanel()` gates on `$this->is_admin` (simple binary gate — not an RBAC system)

**Resources (all under `app/Filament/Resources/`):**
- `ClientResource` — full CRUD (list/create/edit/delete)
- `MatterResource` — full CRUD, status badge with color coding
- `DocumentResource` — **no create action** (uploads only come from the product UI). Two conditional retry actions: `retryExtraction` (visible when `failed` + no extracted text) and `retryAnalysis` (visible when `failed` + extracted text exists). Both dispatch the same real jobs the product UI uses.
- `TaskResource` — full CRUD with status/priority badge coloring
- `ActivityLogResource` — **read-only** (no create/edit/delete). Admin visibility into the audit trail.
- `UserResource` — full CRUD, `is_admin` toggle. This is the actual `/admin` access control surface.

**Important v4 type compatibility fix:** Filament v4 declares `$navigationGroup` as `string|UnitEnum|null` and `$navigationIcon` as `string|BackedEnum|null`. Declaring them as `?string` in subclasses causes a PHP fatal error. All resources use the correct union types.

**Demo user:** `demo@counselos.test` / `password` — `is_admin => true` (set in seeder).

---

### ⬜ Epic 9 — Global Full-Text Search (NOT STARTED) — NEXT
Extend current basic search (`matters.title`, `clients.name`, `documents.original_name`) to query `extracted_text` + insight data across all matters.

### ⬜ Epic 8 — Deployment to Laravel Cloud (NOT STARTED)
Hard dependency: fix the `Storage::disk('local')` bug first (see Epic 3 above).

Planned steps:
1. `DocumentService::storeUpload()` and `delete()` → `Storage::disk(config('filesystems.default'))`
2. `TextExtractionService::extract()` → same
3. `composer require league/flysystem-aws-s3-v3`
4. Laravel Cloud dashboard: attach Postgres (auto-injects `DB_*`), attach Object Storage bucket (auto-injects `FILESYSTEM_DISK=s3` + `AWS_*`), add background process `php artisan queue:work`
5. Deploy command: `php artisan migrate --force` only
   - **Do NOT** run `storage:link` — fails on ephemeral storage
   - **Do NOT** run `queue:restart` — Cloud handles this automatically

### ⬜ Epic 11 — Document Comparison (NOT STARTED)
Stretch feature. Not scoped in detail yet.

---

## Roadmap Order

**Actual order: 6 → 7 → 10 → 9 → 8 → 11**

Next up: **Epic 9 (search)**, then Epic 8 (deployment), then Epic 11.

---

## Known Gaps (intentional, not oversights)

- **No firm/user data isolation** — every authenticated user sees all clients/matters. Explicitly out of MVP scope. Deferred to a possible Phase 3.
- **No standalone Documents or Tasks index pages** — both exist only nested inside a matter workspace.
- **No OCR** — scanned/image PDFs fail extraction by design.
- **Filament `is_admin` is not an RBAC system** — it's a binary `/admin` access gate, nothing more.

---

## Key Files Quick Reference

| Concern | File |
|---------|------|
| Upload + storage | `app/Services/DocumentService.php` |
| Text extraction | `app/Services/TextExtractionService.php` |
| AI analysis orchestration | `app/Services/AiAnalysisService.php` |
| Gemini HTTP wrapper | `app/Services/GeminiClient.php` |
| JSON parsing + fence-strip | `app/Support/Json/GeminiJsonParser.php` |
| Document analysis prompt | `app/Support/Prompts/DocumentAnalysisPrompt.php` |
| Research prompt | `app/Support/Prompts/MatterResearchPrompt.php` |
| Research context builder | `app/Services/ResearchService.php` |
| Timeline logging | `app/Services/TimelineService.php` |
| Extraction job | `app/Jobs/ExtractDocumentTextJob.php` |
| Analysis job | `app/Jobs/AnalyzeDocumentJob.php` |
| Demo dataset | `database/seeders/DemoDataSeeder.php` |
| CI workflow | `.github/workflows/tests.yml` |
| App layout (Lexis Modern) | `resources/views/layouts/app.blade.php` |
| Filament panel provider | `app/Providers/Filament/AdminPanelProvider.php` |
| Admin resources | `app/Filament/Resources/` |
