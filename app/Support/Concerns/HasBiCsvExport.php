<?php

namespace App\Support\Concerns;

use App\Support\CsvExporter;
use Illuminate\Support\Str;

/**
 * Generic "Export CSV" for Business Intelligence pages (app/Filament/Pages/BI/*).
 * Every BI page's getViewData() already returns a flat 'kpis' => [key => scalar]
 * array, so this trait needs no per-page configuration — it just dumps those
 * KPIs as Metric/Value rows. Pairs with resources/views/filament/pages/bi/_bi-actions.blade.php
 * (the "Export CSV" button) and partials/_csv-download-script.blade.php (the
 * browser-side listener that turns the dispatched event into an actual download).
 */
trait HasBiCsvExport
{
    public function exportCsv(): void
    {
        $kpis = $this->getViewData()['kpis'] ?? [];

        $header = ['Metric', 'Value'];
        $rows = [];

        foreach ($kpis as $key => $value) {
            $label = Str::of($key)->snake(' ')->replace('_', ' ')->title();
            $rows[] = [$label, is_scalar($value) ? $value : json_encode($value)];
        }

        $slug = Str::kebab(class_basename(static::class));

        $this->dispatch('download-csv',
            content: CsvExporter::build($header, $rows),
            filename: $slug . '-' . now()->format('Y-m-d') . '.csv',
        );
    }
}
