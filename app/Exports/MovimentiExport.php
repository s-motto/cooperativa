<?php

namespace App\Exports;

use App\Models\Movimento;
use App\Models\Fattura;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MovimentiExport implements FromArray, WithEvents, WithTitle
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

    public function title(): string
    {
        return 'Prima Nota';
    }

    public function array(): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // --- Titolo ---
                $titolo = 'PRIMA NOTA';
                if ($this->da && $this->a) {
                    $titolo .= ' dal ' . \Carbon\Carbon::parse($this->da)->format('d/m/Y')
                             . ' al ' . \Carbon\Carbon::parse($this->a)->format('d/m/Y');
                }
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', $titolo);
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFBBFCA']],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(22);

                // --- Riga 2: gruppi colonne ---
                $sheet->mergeCells('E2:G2');
                $sheet->setCellValue('E2', 'CASSA');
                $sheet->mergeCells('H2:J2');
                $sheet->setCellValue('H2', 'BANCA');
                $sheet->getStyle('E2:J2')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFC0392B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // --- Riga 3: intestazioni ---
                $headers = ['DATA', 'CLIENTE/FORNITORE', 'DESCRIZIONE', '', 'ENTRATE', 'USCITE', 'SALDO', 'ENTRATE', 'USCITE', 'SALDO'];
                foreach ($headers as $i => $header) {
                    $col = chr(65 + $i);
                    $sheet->setCellValue("{$col}3", $header);
                }
                $sheet->getStyle('A3:J3')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF922B21']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFFFFFF']]],
                ]);

                // --- Saldi di apertura ---
                $saldo_cassa_ap = 0;
                $saldo_banca_ap = 0;

                $saldo_cassa_mov = Movimento::where('descrizione', 'LIKE', 'Saldo iniziale cassa%')->latest()->first();
                $saldo_banca_mov = Movimento::where('descrizione', 'LIKE', 'Saldo iniziale banca%')->latest()->first();

                if ($saldo_cassa_mov) {
                    $saldo_cassa_ap = $saldo_cassa_mov->tipo === 'entrata'
                        ? (float) $saldo_cassa_mov->importo
                        : -(float) $saldo_cassa_mov->importo;
                }
                if ($saldo_banca_mov) {
                    $saldo_banca_ap = $saldo_banca_mov->tipo === 'entrata'
                        ? (float) $saldo_banca_mov->importo
                        : -(float) $saldo_banca_mov->importo;
                }

                // Riga 4: saldi di apertura
                $sheet->setCellValue('A4', 'Saldi di apertura');
                $sheet->setCellValue('G4', number_format($saldo_cassa_ap, 2, ',', '.'));
                $sheet->setCellValue('J4', number_format($saldo_banca_ap, 2, ',', '.'));
                $sheet->getStyle('A4:J4')->applyFromArray([
                    'font' => ['bold' => true, 'italic' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF2F2F2']],
                ]);

                // --- IDs da escludere (saldi iniziali) ---
                $escludi_ids = collect([$saldo_cassa_mov?->id, $saldo_banca_mov?->id])->filter()->all();

                // --- Raccolta movimenti (escludi saldi iniziali) ---
                $movimentiQuery = Movimento::with('categoria')
                    ->whereNotIn('id', $escludi_ids)
                    ->orderBy('data')
                    ->orderBy('id');
                if ($this->da) $movimentiQuery->whereDate('data', '>=', $this->da);
                if ($this->a)  $movimentiQuery->whereDate('data', '<=', $this->a);
                if ($this->conto) $movimentiQuery->where('conto', $this->conto);
                $movimenti = $movimentiQuery->get();

                // --- Raccolta fatture ---
                $fattureQuery = Fattura::with('categoria')->orderBy('data')->orderBy('id');
                if ($this->da) $fattureQuery->whereDate('data', '>=', $this->da);
                if ($this->a)  $fattureQuery->whereDate('data', '<=', $this->a);
                $fatture = $fattureQuery->get();

                // --- Unisci e ordina ---
                $righe = collect();
                foreach ($movimenti as $m) {
                    $righe->push([
                        'tipo'        => 'movimento',
                        'data'        => $m->data,
                        'controparte' => '',
                        'descrizione' => $m->descrizione,
                        'conto'       => $m->conto,
                        'verso'       => $m->tipo,
                        'importo'     => (float) $m->importo,
                        'stato'       => null,
                    ]);
                }
                foreach ($fatture as $f) {
                    $righe->push([
                        'tipo'        => 'fattura',
                        'data'        => $f->data,
                        'controparte' => $f->controparte,
                        'descrizione' => $f->descrizione . ($f->numero ? ' n. ' . $f->numero : ''),
                        'conto'       => null,
                        'verso'       => $f->tipo === 'attiva' ? 'entrata' : 'uscita',
                        'importo'     => (float) $f->importo,
                        'stato'       => $f->stato,
                    ]);
                }
                $righe = $righe->sortBy('data')->values();

                // --- Scrivi righe ---
                $row         = 5;
                $saldo_cassa = $saldo_cassa_ap;
                $saldo_banca = $saldo_banca_ap;

                foreach ($righe as $riga) {
                    $sheet->setCellValue("A{$row}", $riga['data']->format('d/m/Y'));
                    $sheet->setCellValue("B{$row}", $riga['controparte']);
                    $sheet->setCellValue("C{$row}", $riga['descrizione']);

                    $importo = $riga['importo'];
                    $verso   = $riga['verso'];
                    $conto   = $riga['conto'];

                    if ($riga['tipo'] === 'movimento') {
                        if ($conto === 'cassa') {
                            if ($verso === 'entrata') {
                                $sheet->setCellValue("E{$row}", number_format($importo, 2, ',', '.'));
                                $saldo_cassa += $importo;
                            } else {
                                $sheet->setCellValue("F{$row}", number_format($importo, 2, ',', '.'));
                                $saldo_cassa -= $importo;
                            }
                            $sheet->setCellValue("G{$row}", number_format($saldo_cassa, 2, ',', '.'));
                        } else {
                            if ($verso === 'entrata') {
                                $sheet->setCellValue("H{$row}", number_format($importo, 2, ',', '.'));
                                $saldo_banca += $importo;
                            } else {
                                $sheet->setCellValue("I{$row}", number_format($importo, 2, ',', '.'));
                                $saldo_banca -= $importo;
                            }
                            $sheet->setCellValue("J{$row}", number_format($saldo_banca, 2, ',', '.'));
                        }

                        $bgColor = ($row % 2 === 0) ? 'FFFFFFFF' : 'FFF9F9F9';
                        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                        ]);

                    } else {
                        // Fattura
                        $isFatturaPagata = $riga['stato'] === 'pagata';
                        $bgColor = $isFatturaPagata ? 'FFE8F5E9' : 'FFFFFDE7';

                        if ($verso === 'uscita') {
                            $sheet->setCellValue("F{$row}", number_format($importo, 2, ',', '.'));
                        } else {
                            $sheet->setCellValue("E{$row}", number_format($importo, 2, ',', '.'));
                        }

                        $sheet->setCellValue("D{$row}", $isFatturaPagata ? '[pagata]' : '[da pagare]');

                        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                            'font' => ['italic' => true],
                        ]);
                    }

                    $row++;
                }

                // --- Saldo finale ---
                $row++;
                $sheet->setCellValue("D{$row}", 'SALDO FINALE');
                $sheet->setCellValue("G{$row}", number_format($saldo_cassa, 2, ',', '.'));
                $sheet->setCellValue("J{$row}", number_format($saldo_banca, 2, ',', '.'));
                $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFBBFCA']],
                ]);

                // --- Larghezze colonne ---
                $sheet->getColumnDimension('A')->setWidth(14);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(40);
                $sheet->getColumnDimension('D')->setWidth(14);
                $sheet->getColumnDimension('E')->setWidth(14);
                $sheet->getColumnDimension('F')->setWidth(14);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(14);
                $sheet->getColumnDimension('I')->setWidth(14);
                $sheet->getColumnDimension('J')->setWidth(14);

                // --- Bordi ---
                $lastRow = $row;
                $sheet->getStyle("A3:J{$lastRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
                ]);
            },
        ];
    }
}