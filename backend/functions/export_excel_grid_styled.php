<?php
require __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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
   TIME GRID
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

$days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

$rooms = array_values(array_unique(array_column($schedules, 'room')));
sort($rooms);

/* =========================
   COLUMNS BY VIEW
========================= */

$columns = match ($view) {
    'day'     => $rooms,
    'faculty' => $days,
    'section' => $days,
    'room'    => $days,
    default   => $rooms
};

/* =========================
   SPREADSHEET SETUP
========================= */

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle(ucfirst($view));

/* ===== Headers ===== */

$sheet->setCellValue('A1', 'Time');

$colIndex = 2;
$colMap = [];

foreach ($columns as $col) {
    $cell = Coordinate::stringFromColumnIndex($colIndex) . '1';
    $sheet->setCellValue($cell, $col);
    $colMap[$col] = $colIndex;
    $colIndex++;
}

/* ===== Header Styling ===== */

$lastCol = Coordinate::stringFromColumnIndex(count($columns) + 1);

$sheet->getStyle("A1:$lastCol" . "1")->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 12,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '1E3A8A'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
]);

$sheet->getRowDimension(1)->setRowHeight(36);

/* ===== Time Column ===== */

foreach ($rowMap as $time => $r) {
    $sheet->setCellValue("A$r", $time);
    $sheet->getRowDimension($r)->setRowHeight(42);
}

$sheet->getStyle("A2:A$row")->applyFromArray([
    'font' => ['bold' => true],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
]);

/* =========================
   EVENTS (CARDS)
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
    ) {
        continue;
    }

    $col = $colMap[$columnKey];
    $rowStart = $rowMap[$s['time_start']];
    $rowEnd   = $rowMap[$s['time_end']] - 1;

    if ($rowEnd < $rowStart) continue;

    $startCell = Coordinate::stringFromColumnIndex($col) . $rowStart;
    $endCell   = Coordinate::stringFromColumnIndex($col) . $rowEnd;
    $range     = "$startCell:$endCell";

    if ($rowEnd > $rowStart) {
        $sheet->mergeCells($range);
    }

    $text = $s['subject'];

    if ($view === 'day') {
        $text .= "\n{$s['section']}\n{$s['faculty']}";
    } elseif ($view === 'faculty') {
        $text .= "\n{$s['section']}\n{$s['room']}";
    } elseif ($view === 'section') {
        $text .= "\n{$s['faculty']}\n{$s['room']}";
    } elseif ($view === 'room') {
        $text .= "\n{$s['section']}\n{$s['faculty']}";
    }

    $sheet->setCellValue($startCell, $text);

    /* ===== CARD STYLE ===== */

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
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
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
   GRID BORDERS
========================= */

$gridEndRow = max($rowMap);

$sheet->getStyle("A1:$lastCol$gridEndRow")
    ->getBorders()->getAllBorders()
    ->setBorderStyle(Border::BORDER_THIN);

/* =========================
   AUTOSIZE + FREEZE
========================= */

for ($i = 1; $i <= count($columns) + 1; $i++) {
    $sheet->getColumnDimension(
        Coordinate::stringFromColumnIndex($i)
    )->setAutoSize(true);
}

$sheet->freezePane('B2');

/* =========================
   OUTPUT
========================= */

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=schedule_grid_{$view}.xlsx");

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
