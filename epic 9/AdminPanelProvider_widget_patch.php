<?php

/**
 * PATCH INSTRUCTIONS for app/Providers/Filament/AdminPanelProvider.php
 *
 * Find the ->widgets([...]) call in the panel() method chain (filament:install
 * generates this with Filament's own default widgets already in the array,
 * e.g. AccountWidget, FilamentInfoWidget - don't remove those unless you
 * want to).
 *
 * Add these two imports near the top:
 */

use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\FailedDocumentsWidget;

/**
 * Then add both classes into the existing ->widgets([...]) array, e.g.:
 *
 *     ->widgets([
 *         \Filament\Widgets\AccountWidget::class,
 *         StatsOverviewWidget::class,
 *         FailedDocumentsWidget::class,
 *     ])
 *
 * Order matters for layout - StatsOverviewWidget first (compact stat
 * cards at the top), FailedDocumentsWidget second (full-width table
 * below it, per its $sort = 2 and columnSpan = 'full').
 */
