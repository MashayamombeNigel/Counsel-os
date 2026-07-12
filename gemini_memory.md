# CounselOS Context & Memory

## Project Overview
CounselOS is an AI-powered legal matter intelligence platform MVP built for small firms and solo attorneys.
Tech Stack: Laravel 12, PostgreSQL 16, Blade, Tailwind, Alpine.js, Gemini API (gemini-2.5-flash).

## Current Progress (Completed)
- **Epic 0**: Laravel Blueprint scaffolding completed. `draft.yaml` generated models, migrations, controllers, form requests, and basic CRUD routes.
- **Epic 1**: Project initialized. Database migrated (fixed Blueprint foreign key user.by to user.id). Basic service stubs (`DocumentService`, `TextExtractionService`, `AiAnalysisService`, `ResearchService`) with JSON-repair regex fallback logic created.
- **Routing**: `web.php` cleaned up to properly nest matter-related endpoints.

## Current Progress (Completed)
- **Epic 0 & 1**: Setup, database, basic services, and routing.
- **Epic 2**: Client & Matter views implemented. ClientCRUD, Matter Workspace shell with placeholder tabs, and Activity Timeline integration wired.
- **Epic 3**: Document upload flow, secure private storage, text extraction (pdfparser/phpword) via database queue.

## Current Focus: Epic 4 (Gemini AI insights)
