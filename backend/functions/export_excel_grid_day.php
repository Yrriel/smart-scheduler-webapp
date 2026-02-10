<?php
require __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

/* =========================
   INPUT
========================= */

$view = $_GET['view'] ?? 'day';

$data = json_decode(
    file_get_contents(__DIR__ . '/../functions/edit-output.json'),
    true
);

$schedules = $data['schedules'] ?? [];

/* =========================
   TIME ROWS
========================= */

$times = [];
$start = strtotime('07:00');
$end   = strtotime('17:30');

while ($start < $end) {
    $times[] = date('H:i', $start);
    $start += 1800;
}

$rowMap = [];
$row = 2;

foreach ($times as $t) {
    $rowMap[$t] = $row++;
}

/* =========================
   DYNAMIC COLUMNS
========================= */

$days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

$rooms = array_values(array_unique(array_column($schedules, 'room')));
sort($rooms);

$columns = match ($view) {
    'day'     => $rooms,
    'faculty' => $days,
    'section' => $days,
    'room'    => $days,
    default   => $rooms
};

/* =========================
   CREATE SHEET
========================= */

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle(ucfirst($view));

/* =========================
   HEADER ROW
========================= */

$sheet->setCellValue('A1', 'Time');

$colMap = [];
$colIndex = 2;

foreach ($columns as $col) {
    $cell = Coordinate::stringFromColumnIndex($colIndex) . '1';
    $sheet->setCellValue($cell, $col);
    $colMap[$col] = $colIndex;
    $colIndex++;
}

$lastCol = Coordinate::stringFromColumnIndex(count($columns) + 1);

/* Header style */
$sheet->getStyle("A1:$lastCol" . "1")->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 12,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '304FFE'],
    ],
    'alignment' => [
        'horizontal' => 'center',
        'vertical' => 'center',
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
]);

$sheet->getRowDimension(1)->setRowHeight(34);

/* =========================
   TIME COLUMN
========================= */

foreach ($rowMap as $time => $r) {
    $sheet->setCellValue("A$r", $time);
}

$sheet->getStyle("A2:A" . ($row - 1))->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 11,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '1E3A8A'],
    ],
    'alignment' => [
        'horizontal' => 'left',
        'vertical' => 'center',
    ],
]);

/* =========================
   CELL SIZING
========================= */

for ($r = 2; $r <= $row - 1; $r++) {
    $sheet->getRowDimension($r)->setRowHeight(38);
}

for ($i = 2; $i <= count($columns) + 1; $i++) {
    $col = Coordinate::stringFromColumnIndex($i);
    $sheet->getColumnDimension($col)->setWidth(22);
}

/* =========================
   EVENTS (GRID CARDS)
========================= */

foreach ($schedules as $s) {

    $columnKey = match ($view) {
        'day'     => $s['room'],
        'faculty' => $s['day'],
        'section' => $s['day'],
        'room'    => $s['day'],
        default   => $s['room']
    };

    if (
        !isset($colMap[$columnKey]) ||
        !isset($rowMap[$s['time_start']]) ||
        !isset($rowMap[$s['time_end']])
    ) continue;

    $col = $colMap[$columnKey];
    $rowStart = $rowMap[$s['time_start']];
    $rowEnd   = $rowMap[$s['time_end']] - 1;

    if ($rowEnd < $rowStart) continue;

    $startCell = Coordinate::stringFromColumnIndex($col) . $rowStart;
    $endCell   = Coordinate::stringFromColumnIndex($col) . $rowEnd;

    if ($rowEnd > $rowStart) {
        $sheet->mergeCells("$startCell:$endCell");
    }

    $text = $s['subject'];

    if ($view === 'day') {
        $text .= "\n{$s['section']}\n{$s['faculty']}";
    } elseif ($view === 'faculty') {
        $text .= "\n{$s['section']}\n{$s['room']}";
    }

    $sheet->setCellValue($startCell, $text);

    $range = "$startCell:$endCell";

    $sheet->getStyle($range)->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 11,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4F6EF7'],
        ],
        'alignment' => [
            'wrapText' => true,
            'horizontal' => 'center',
            'vertical' => 'center',
        ],
        'borders' => [
            'outline' => [
                'borderStyle' => Border::BORDER_MEDIUM,
                'color' => ['rgb' => '1E3A8A'],
            ],
        ],
    ]);
}

/* =========================
   FINAL TOUCHES
========================= */

$sheet->freezePane('B2');

/* =========================
   OUTPUT
========================= */

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=schedule_grid_{$view}.xlsx");

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
