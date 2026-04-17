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
                $sheet->mergeCells('A1:K1');
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
                $headers = ['DATA', 'CLIENTE/FORNITORE', 'DESCRIZIONE', 'STATO', 'ENTRATE', 'USCITE', 'SALDO', 'ENTRATE', 'USCITE', 'SALDO', 'IMPORTO FATTURA'];
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
                // Intestazione colonna K separata
                $sheet->getStyle('K3')->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF5B4FCF']],
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

                $sheet->setCellValue('A4', 'Saldi di apertura');
                $sheet->setCellValue('G4', number_format($saldo_cassa_ap, 2, ',', '.'));
                $sheet->setCellValue('J4', number_format($saldo_banca_ap, 2, ',', '.'));
                $sheet->getStyle('A4:K4')->applyFromArray([
                    'font' => ['bold' => true, 'italic' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF2F2F2']],
                ]);

                // --- IDs da escludere ---
                $escludi_ids = collect([$saldo_cassa_mov?->id, $saldo_banca_mov?->id])->filter()->all();

                // --- Movimenti ---
                $movimentiQuery = Movimento::with('categoria')
                    ->whereNotIn('id', $escludi_ids)
                    ->orderBy('data')->orderBy('id');
                if ($this->da) $movimentiQuery->whereDate('data', '>=', $this->da);
                if ($this->a)  $movimentiQuery->whereDate('data', '<=', $this->a);
                if ($this->conto) $movimentiQuery->where('conto', $this->conto);
                $movimenti = $movimentiQuery->get();

                // --- Fatture ---
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

                    if ($riga['tipo'] === 'movimento') {
                        // Riga normale — aggiorna saldo
                        $importo = $riga['importo'];
                        $verso   = $riga['verso'];
                        $conto   = $riga['conto'];

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
                        $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                        ]);

                    } else {
                        // Riga fattura — NON tocca il saldo, importo in colonna K
                        $isFatturaPagata = $riga['stato'] === 'pagata';
                        $isParziale      = $riga['stato'] === 'parziale';

                        if ($isFatturaPagata) {
                            $bgColor    = 'FFE8F5E9'; // verde chiaro
                            $statoLabel = '✓ pagata';
                        } elseif ($isParziale) {
                            $bgColor    = 'FFFFF3CD'; // giallo arancio
                            $statoLabel = '◑ parziale';
                        } else {
                            $bgColor    = 'FFFFFDE7'; // giallo chiaro
                            $statoLabel = '⏳ da pagare';
                        }

                        $sheet->setCellValue("D{$row}", $statoLabel);
                        $sheet->setCellValue("K{$row}", number_format($riga['importo'], 2, ',', '.'));

                        $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                            'font' => ['italic' => true],
                        ]);

                        // Bordo sinistro colorato per evidenziare le fatture
                        $borderColor = $isFatturaPagata ? 'FF16A34A' : ($isParziale ? 'FFD97706' : 'FFDC2626');
                        $sheet->getStyle("A{$row}")->applyFromArray([
                            'borders' => ['left' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => $borderColor]]],
                        ]);
                    }

                    $row++;
                }

                // --- Saldo finale ---
                $row++;
                $sheet->mergeCells("A{$row}:D{$row}");
                $sheet->setCellValue("A{$row}", 'SALDO FINALE');
                $sheet->setCellValue("G{$row}", number_format($saldo_cassa, 2, ',', '.'));
                $sheet->setCellValue("J{$row}", number_format($saldo_banca, 2, ',', '.'));
                $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                    'font'      => ['bold' => true],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFBBFCA']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // --- Larghezze colonne ---
                $sheet->getColumnDimension('A')->setWidth(13);
                $sheet->getColumnDimension('B')->setWidth(28);
                $sheet->getColumnDimension('C')->setWidth(38);
                $sheet->getColumnDimension('D')->setWidth(13);
                $sheet->getColumnDimension('E')->setWidth(13);
                $sheet->getColumnDimension('F')->setWidth(13);
                $sheet->getColumnDimension('G')->setWidth(13);
                $sheet->getColumnDimension('H')->setWidth(13);
                $sheet->getColumnDimension('I')->setWidth(13);
                $sheet->getColumnDimension('J')->setWidth(13);
                $sheet->getColumnDimension('K')->setWidth(15);

                // --- Bordi generali ---
                $sheet->getStyle("A3:K{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFCCCCCC']]],
                ]);

                // Linea divisoria tra le colonne J e K
                $sheet->getStyle("K3:K{$row}")->applyFromArray([
                    'borders' => ['left' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF5B4FCF']]],
                ]);
            },
        ];
    }
}