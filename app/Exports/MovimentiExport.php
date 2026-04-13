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

                // --- Intestazione ---
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

                // --- Riga 3: intestazioni colonne ---
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

                // --- Saldi iniziali (riga 3, in alto a destra) ---
                $saldo_cassa_iniziale = Movimento::where('tipo', 'entrata')->where('conto', 'cassa')->sum('importo')
                                      - Movimento::where('tipo', 'uscita')->where('conto', 'cassa')->sum('importo');
                $saldo_banca_iniziale = Movimento::where('tipo', 'entrata')->where('conto', 'banca')->sum('importo')
                                      - Movimento::where('tipo', 'uscita')->where('conto', 'banca')->sum('importo');

                // Calcola saldi di apertura (tutto prima del periodo filtrato)
                if ($this->da) {
                    $saldo_cassa_ap = Movimento::where('tipo', 'entrata')->where('conto', 'cassa')->whereDate('data', '<', $this->da)->sum('importo')
                                    - Movimento::where('tipo', 'uscita')->where('conto', 'cassa')->whereDate('data', '<', $this->da)->sum('importo');
                    $saldo_banca_ap = Movimento::where('tipo', 'entrata')->where('conto', 'banca')->whereDate('data', '<', $this->da)->sum('importo')
                                    - Movimento::where('tipo', 'uscita')->where('conto', 'banca')->whereDate('data', '<', $this->da)->sum('importo');
                } else {
                    $saldo_cassa_ap = 0;
                    $saldo_banca_ap = 0;
                }

                // Riga saldi di apertura
                $sheet->setCellValue('A4', 'Saldi di apertura');
                $sheet->setCellValue('G4', number_format($saldo_cassa_ap, 2, ',', '.'));
                $sheet->setCellValue('J4', number_format($saldo_banca_ap, 2, ',', '.'));
                $sheet->getStyle('A4:J4')->applyFromArray([
                    'font' => ['bold' => true, 'italic' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF2F2F2']],
                ]);

                // --- Raccolta righe (movimenti + fatture) ---
                $movimentiQuery = Movimento::with('categoria')->orderBy('data')->orderBy('id');
                if ($this->da) $movimentiQuery->whereDate('data', '>=', $this->da);
                if ($this->a)  $movimentiQuery->whereDate('data', '<=', $this->a);
                if ($this->conto) $movimentiQuery->where('conto', $this->conto);
                $movimenti = $movimentiQuery->get();

                $fattureQuery = Fattura::with('categoria')->orderBy('data')->orderBy('id');
                if ($this->da) $fattureQuery->whereDate('data', '>=', $this->da);
                if ($this->a)  $fattureQuery->whereDate('data', '<=', $this->a);
                $fatture = $fattureQuery->get();

                // Unisci e ordina per data
                $righe = collect();
                foreach ($movimenti as $m) {
                    $righe->push([
                        'tipo'         => 'movimento',
                        'data'         => $m->data,
                        'controparte'  => '',
                        'descrizione'  => $m->descrizione,
                        'conto'        => $m->conto,
                        'verso'        => $m->tipo,
                        'importo'      => (float) $m->importo,
                    ]);
                }
                foreach ($fatture as $f) {
                    $righe->push([
                        'tipo'         => 'fattura',
                        'data'         => $f->data,
                        'controparte'  => $f->controparte,
                        'descrizione'  => $f->descrizione . ($f->numero ? ' n. ' . $f->numero : ''),
                        'conto'        => null,
                        'verso'        => $f->tipo === 'attiva' ? 'entrata' : 'uscita',
                        'importo'      => (float) $f->importo,
                        'stato'        => $f->stato,
                    ]);
                }
                $righe = $righe->sortBy('data')->values();

                // --- Scrivi le righe ---
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

                        // Sfondo alternato chiaro
                        $bgColor = ($row % 2 === 0) ? 'FFFFFFFF' : 'FFF9F9F9';
                        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                        ]);

                    } else {
                        // Fattura — sfondo giallo se aperta, verde chiaro se pagata
                        $isFatturaPagata = $riga['stato'] === 'pagata';
                        $bgColor = $isFatturaPagata ? 'FFE8F5E9' : 'FFFFFDE7';

                        // Mostra in entrambe le colonne (cassa e banca) come "in attesa"
                        // oppure solo come voce informativa
                        if ($verso === 'uscita') {
                            $sheet->setCellValue("F{$row}", number_format($importo, 2, ',', '.'));
                            $sheet->setCellValue("I{$row}", '');
                        } else {
                            $sheet->setCellValue("E{$row}", number_format($importo, 2, ',', '.'));
                            $sheet->setCellValue("H{$row}", '');
                        }

                        $stato_label = $isFatturaPagata ? ' [pagata]' : ' [da pagare]';
                        $sheet->setCellValue("D{$row}", $stato_label);

                        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                            'font' => ['italic' => true],
                        ]);
                    }

                    $row++;
                }

                // --- Riga totali finali ---
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

                // --- Bordi su tutto ---
                $lastRow = $row;
                $sheet->getStyle("A3:J{$lastRow}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
                ]);
            },
        ];
    }
}