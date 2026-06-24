<?php

namespace App\Support;

/**
 * CSV row-building/escaping, extracted from the pattern in
 * App\Filament\Pages\Courses::exportCsv(). The result is handed to a
 * Livewire `download-csv` dispatch — see
 * resources/views/filament/pages/partials/_csv-download-script.blade.php
 * for the browser-side listener that triggers the actual file download.
 */
class CsvExporter
{
    public static function build(array $headerRow, iterable $dataRows): string
    {
        $rows = [$headerRow];

        foreach ($dataRows as $row) {
            $rows[] = $row;
        }

        return implode("\n", array_map(
            fn ($row) => implode(',', array_map(
                fn ($value) => '"' . str_replace('"', '""', (string) $value) . '"',
                $row
            )),
            $rows
        ));
    }
}
