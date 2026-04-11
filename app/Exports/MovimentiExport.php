<?php

namespace App\Exports;

use App\Models\Movimento;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MovimentiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $da;
    protected $a;
    protected $conto;

    public function __construct($da = null, $a = null, $conto = null)
    {
        $this->da    = $da;
        $this->a     = $a;
        $this->conto = $conto;
    }

    public function collection()
    {
        $query = Movimento::with('categoria')->orderBy('data');

        if ($this->da && $this->a) {
            $query->whereBetween('data', [$this->da, $this->a]);
        }

        if ($this->conto) {
            $query->where('conto', $this->conto);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Data',
            'Descrizione',
            'Tipo',
            'Conto',
            'Categoria',
            'Importo (€)',
            'Note',
        ];
    }

    public function map($movimento): array
    {
        return [
            $movimento->data->format('d/m/Y'),
            $movimento->descrizione,
            ucfirst($movimento->tipo),
            ucfirst($movimento->conto),
            $movimento->categoria?->nome ?? '—',
            number_format($movimento->importo, 2, ',', '.'),
            $movimento->note ?? '',
        ];
    }
}