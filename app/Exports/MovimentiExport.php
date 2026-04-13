<?php

namespace App\Exports;

use App\Models\Movimento;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MovimentiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected $da;
    protected $a;
    protected $conto;
    protected $movimenti;
    protected $fatture_aperte;

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

        $this->movimenti = $query->get();

        $fattureQuery = \App\Models\Fattura::with('categoria')
            ->where('stato', 'aperta')
            ->orderBy('data');

        if ($this->da && $this->a) {
            $fattureQuery->whereBetween('data', [$this->da, $this->a]);
        }

        $this->fatture_aperte = $fattureQuery->get();

        return $this->movimenti;
    }

    public function headings(): array
    {
        return ['Data', 'Descrizione', 'Tipo', 'Conto', 'Categoria', 'Importo (€)', 'Note', 'Stato'];
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
            '',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14,
            'B' => 40,
            'C' => 12,
            'D' => 12,
            'E' => 20,
            'F' => 15,
            'G' => 30,
            'H' => 15,
        ];
    }

    public function styles($sheet)
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F46E5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $this->movimenti->count() + 1;

                // Righe fatture aperte
                foreach ($this->fatture_aperte as $fattura) {
                    $lastRow++;
                    $sheet->setCellValue("A{$lastRow}", $fattura->data->format('d/m/Y'));
                    $sheet->setCellValue("B{$lastRow}", $fattura->descrizione . ' — ' . $fattura->controparte);
                    $sheet->setCellValue("C{$lastRow}", $fattura->tipo === 'attiva' ? 'Entrata' : 'Uscita');
                    $sheet->setCellValue("D{$lastRow}", 'In attesa');
                    $sheet->setCellValue("E{$lastRow}", $fattura->categoria?->nome ?? '—');
                    $sheet->setCellValue("F{$lastRow}", number_format($fattura->importo, 2, ',', '.'));
                    $sheet->setCellValue("G{$lastRow}", '');
                    $sheet->setCellValue("H{$lastRow}", 'Da pagare');
                    $sheet->getStyle("A{$lastRow}:H{$lastRow}")->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF3CD']],
                        'font' => ['italic' => true],
                    ]);
                }

                // Saldi
                $lastRow += 2;

                $movimenti = $this->movimenti;
                $saldo_cassa = $movimenti->where('conto', 'cassa')->where('tipo', 'entrata')->sum('importo')
                             - $movimenti->where('conto', 'cassa')->where('tipo', 'uscita')->sum('importo');
                $saldo_banca = $movimenti->where('conto', 'banca')->where('tipo', 'entrata')->sum('importo')
                             - $movimenti->where('conto', 'banca')->where('tipo', 'uscita')->sum('importo');

                $sheet->setCellValue("E{$lastRow}", 'Saldo Cassa');
                $sheet->setCellValue("F{$lastRow}", number_format($saldo_cassa, 2, ',', '.'));
                $sheet->getStyle("E{$lastRow}:F{$lastRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDBEAFE']],
                ]);
                $lastRow++;

                $sheet->setCellValue("E{$lastRow}", 'Saldo Banca');
                $sheet->setCellValue("F{$lastRow}", number_format($saldo_banca, 2, ',', '.'));
                $sheet->getStyle("E{$lastRow}:F{$lastRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD1FAE5']],
                ]);

                $sheet->getStyle("A1:H1")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);
            },
        ];
    }
}