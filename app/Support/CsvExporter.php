<?php

namespace App\Support;

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
