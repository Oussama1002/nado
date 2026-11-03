<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Export générique : reçoit des en-têtes + des lignes déjà préparées
 * (voir Admin\ExportController) et produit un fichier .xlsx.
 */
class GenericExport implements FromArray, WithHeadings
{
    public function __construct(private array $headings, private array $rows)
    {
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
