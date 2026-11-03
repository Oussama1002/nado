<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

/**
 * Export générique : en-têtes + lignes déjà préparées (voir Admin\ExportController).
 *
 * Toutes les valeurs sont écrites en TEXTE explicite : les numéros de téléphone,
 * VIN, matricules et grands identifiants ne basculent plus en notation
 * scientifique et ne perdent pas leurs zéros de tête.
 */
class GenericExport extends DefaultValueBinder implements FromArray, WithHeadings, WithCustomValueBinder
{
    public function __construct(private array $headings, private array $rows)
    {
    }

    public function bindValue(Cell $cell, $value): bool
    {
        $cell->setValueExplicit((string) ($value ?? ''), DataType::TYPE_STRING);

        return true;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
