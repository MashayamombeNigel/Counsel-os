# CounselOS — Context Addendum (Epic 9 + Expanded Filament)

**Read this alongside `PROJECT_CONTEXT.md`, not instead of it.** That file's verification checklist still applies — run it first if you haven't already. This addendum covers what changed since it was written.

## Verification additions (run these too)

11. Check `app/Services/SearchService.php` exists, and `app/Http/Controllers/SearchController.php` exists.
12. Check `database/migrations/` for a migration adding a `search_vector` generated tsvector column to `documents`.
13. Check `resources/views/search/results.blade.php` exists.
14. Check `app/Filament/Resources/MatterResource/RelationManagers/` contains `DocumentsRelationManager.php` and `TasksRelationManager.php`.
15. Check `app/Filament/Widgets/` contains `StatsOverviewWidget.php` and `FailedDocumentsWidget.php`, and confirm both are registered in `AdminPanelProvider`'s `->widgets([...])` array — this registration step is a manual patch, easy to have been missed.
16. Confirm `routes/web.php`'s `/search` route points to `SearchController::class` — the old `DashboardController::search()` method should have been deleted.

## Epic 9 — Global Full-Text Search ✅ COMPLETE

**Built as real Postgres full-text search, not a shallow `ILIKE`.** A generated `tsvector` column (`search_vector`) on `documents`, combining `original_name` + `extracted_text`, backed by a GIN index — kept automatically in sync by Postgres itself (no model observer needed). `SearchService::searchDocuments()` uses `ts_rank` for relevance ordering and `ts_headline` for match snippets.

**Security detail worth understanding, not just copying:** `ts_headline`'s output contains the document's *raw* underlying text, verbatim and unescaped — if a document's extracted text ever contained literal `<script>` (a genuinely odd but possible PDF artifact), naively injecting that into Blade via `{!! !!}` would be a real XSS hole. The fix implemented: `ts_headline` is configured with unlikely placeholder tokens (`@@HL_START@@`/`@@HL_END@@`) instead of real HTML tags, the *entire* snippet is HTML-escaped via `e()`, and only *then* are the placeholder tokens swapped for real `<mark>` tags. Escape-then-highlight, never highlight-then-escape — the latter would double-escape the app's own tags. This logic lives in `SearchService::searchDocuments()` and is covered by a dedicated test (`SearchServiceTest.php`, the "escapes any raw html in source text" case) — don't remove that test when refactoring this method, it's the one guarding against a real regression class, not a redundant check.

Clients and matters still use simple `ILIKE` — deliberate, not an inconsistency. Short title/name fields don't benefit from full-text ranking the way long extracted document text does; adding FTS there would be complexity without payoff.

`SearchController` replaces the old `search()` method that lived on `DashboardController` — if you find both still present, the old one should have been deleted during this epic and wasn't; remove it.

## Filament expansion ✅ COMPLETE

The original Epic 10 build was flagged by the user as "diluted" — flat CRUD tables only, no operational depth. This addressed that directly:

- **`MatterResource` relation managers**: `DocumentsRelationManager` (read-only list + the same retry actions as the main Documents resource, scoped to just this matter) and `TasksRelationManager` (full CRUD), both appearing as tabs on the Matter edit page. This is the actual point of an admin panel — drilling into a record's related data without leaving the page.
- **`StatsOverviewWidget`**: real operational metrics (open matters, pending tasks, documents awaiting processing, failed document count) — deliberately *not* a copy of the product-side dashboard's stats, since the audiences differ (attorney workflow vs. admin/ops health).
- **`FailedDocumentsWidget`**: the highest-value addition — surfaces every `failed` document directly on the panel's landing page with retry actions inline, rather than requiring an admin to know to navigate to Documents and apply a filter. This is what makes the "retry" actions built in the original Epic 10 actually *discoverable* rather than a feature that only helps someone who already knew to look for it.
- **Global search attributes** added to `Client`, `Matter`, `Document` resources (`getGloballySearchableAttributes()`) — Filament's own built-in top-bar search, separate from and complementary to the product-side Epic 9 search.

**If widgets aren't appearing on the panel dashboard after deploying this**, the most likely cause is the `AdminPanelProvider` registration step — it's a manual patch (see `AdminPanelProvider_widget_patch.php`), not something that happens automatically just because the widget classes exist on disk.

## README

Rewritten to include an actual "why" — grounded in the real business problem (spec Section 1) and the genuine, previously-documented motivation (breaking a pattern of near-complete-but-unshipped projects, building AI that's load-bearing rather than decorative). Nothing in the new narrative section is fabricated backstory; it's the same motivations already on record, written in first person. If asked to touch the README again, preserve that section's honesty — don't let it drift into generic "passionate developer" boilerplate.

## Roadmap status (unchanged from PROJECT_CONTEXT.md's reordering)

Epic 9 now joins Epic 6, 7, 10 as complete. **Epic 8 (deployment) is next**, and is already partially underway outside this document's scope — the user has been deploying to Laravel Cloud in parallel (hit and resolved a PHP-version build error, and a Postgres-version dashboard choice). Confirm with the user directly what Epic 8's actual current state is rather than assuming it hasn't started, since that work happened in conversation, not as a file handoff like the others. Epic 11 (document comparison) remains last, not yet started.
