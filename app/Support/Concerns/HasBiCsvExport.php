<?php

namespace App\Support\Concerns;

use App\Support\CsvExporter;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

/**
 * Generic "Export CSV" for Business Intelligence pages (app/Filament/Pages/BI/*).
 * Every BI page's getViewData() returns a flat 'kpis' => [key => scalar] array
 * plus assorted leaderboard/table sections (e.g. 'topCourses', 'topInstructors').
 * This trait dumps the KPIs as Metric/Value rows, then appends each tabular
 * section as its own block — so CSV carries the same tables the PDF export
 * already shows, instead of just the KPI summary. Pairs with
 * resources/views/filament/pages/bi/_bi-actions.blade.php (the "Export CSV"
 * button) and partials/_csv-download-script.blade.php (the browser-side
 * listener that turns the dispatched event into an actual download).
 */
trait HasBiCsvExport
{
    public function exportCsv(): void
    {
        $data = $this->getViewData();
        $kpis = $data['kpis'] ?? [];

        $rows = [];

        foreach ($kpis as $key => $value) {
            $label = Str::of($key)->snake(' ')->replace('_', ' ')->title();
            $rows[] = [$label, is_scalar($value) ? $value : json_encode($value)];
        }

        foreach ($data as $section => $value) {
            if ($section === 'kpis' || (! is_array($value) && ! $value instanceof \Traversable)) {
                continue;
            }

            $items = collect($value);
            $first = $items->first();
            $firstArr = match (true) {
                is_array($first) => $first,
                $first instanceof Arrayable => $first->toArray(),
                is_object($first) => (array) $first,
                default => null,
            };

            if (empty($firstArr)) {
                continue;
            }

            $rows[] = [];
            $rows[] = [(string) Str::of((string) $section)->snake(' ')->replace('_', ' ')->title()];
            $rows[] = array_map(
                fn ($k) => (string) Str::of((string) $k)->snake(' ')->replace('_', ' ')->title(),
                array_keys($firstArr)
            );

            foreach ($items as $item) {
                $itemArr = match (true) {
                    is_array($item) => $item,
                    $item instanceof Arrayable => $item->toArray(),
                    default => (array) $item,
                };

                $rows[] = array_map(function ($v) {
                    if (is_scalar($v) || $v === null) {
                        return $v;
                    }
                    if (is_object($v) && method_exists($v, '__toString')) {
                        return (string) $v;
                    }
                    if (is_array($v)) {
                        return $v['title'] ?? $v['name'] ?? json_encode($v);
                    }

                    return $v->title ?? $v->name ?? json_encode($v);
                }, array_values($itemArr));
            }
        }

        $slug = Str::kebab(class_basename(static::class));

        $this->dispatch('download-csv',
            content: CsvExporter::build(['Metric', 'Value'], $rows),
            filename: $slug . '-' . now()->format('Y-m-d') . '.csv',
        );
    }
}
