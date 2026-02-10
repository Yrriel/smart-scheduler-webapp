<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/* =========================
   LOAD DATA
========================= */

$data = json_decode(
    file_get_contents(__DIR__ . '/../functions/output.json'),
    true
);

$schedules = $data['schedules'] ?? [];
if (!$schedules) {
    die('No schedule data found.');
}

/* =========================
   CONSTANTS
========================= */

$days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

$times = [];
$start = strtotime('07:00');
$end   = strtotime('17:30');
while ($start < $end) {
    $times[] = date('H:i', $start);
    $start += 1800;
}

function buildRowMap($times) {
    $map = [];
    $r = 2;
    foreach ($times as $t) {
        $map[$t] = $r++;
    }
    return $map;
}

/* =========================
   GROUP VALUES
========================= */

$groups = [
    'day'     => array_unique(array_column($schedules, 'day')),
    'faculty' => array_unique(array_column($schedules, 'faculty')),
    'section' => array_unique(array_column($schedules, 'section')),
    'room'    => array_unique(array_column($schedules, 'room')),
];

foreach ($groups as &$g) sort($g);

/* =========================
   GRID SHEET BUILDER
========================= */

function buildGridSheet(
    Spreadsheet $spreadsheet,
    string $title,
    array $schedules,
    string $view,
    string $filterValue,
    array $times,
    array $days
) {
    $sheet = $spreadsheet->createSheet();
    $sheet->setTitle(substr($title, 0, 31));

    $rowMap = buildRowMap($times);

    /* COLUMNS */
    if ($view === 'day') {
        $columns = array_values(array_unique(array_column($schedules, 'room')));
        sort($columns);
    } else {
        $columns = $days;
    }

    /* HEADER */
    $sheet->setCellValue('A1', 'Time');
    $sheet->getStyle('A1')->getFont()->setBold(true);

    $colMap = [];
    $colIndex = 2;

    foreach ($columns as $c) {
        $cell = Coordinate::stringFromColumnIndex($colIndex) . '1';
        $sheet->setCellValue($cell, $c);
        $sheet->getStyle($cell)->getFont()->setBold(true);
        $sheet->getStyle($cell)->getAlignment()->setHorizontal('center');
        $colMap[$c] = $colIndex;
        $colIndex++;
    }

    foreach ($rowMap as $time => $row) {
        $sheet->setCellValue("A$row", $time);
    }

    /* EVENTS */
    foreach ($schedules as $s) {

        if ($s[$view] !== $filterValue) continue;

        $columnKey = match ($view) {
            'day'     => $s['room'],
            default   => $s['day']
        };

        if (!isset($colMap[$columnKey])) continue;
        if (!isset($rowMap[$s['time_start']], $rowMap[$s['time_end']])) continue;

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

        /* TEXT */
        $text = $s['subject'] . "\n";

        if ($view === 'day') {
            $text .= "{$s['section']}\n{$s['faculty']}";
        } elseif ($view === 'faculty') {
            $text .= "{$s['section']}\n{$s['room']}";
        } elseif ($view === 'section') {
            $text .= "{$s['faculty']}\n{$s['room']}";
        } elseif ($view === 'room') {
            $text .= "{$s['section']}\n{$s['faculty']}";
        }

        $sheet->setCellValue($startCell, $text);

        /* STYLE (UI-LIKE CARD) */
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

    /* COLUMN SIZING */
    foreach (range('A', Coordinate::stringFromColumnIndex(count($columns) + 1)) as $c) {
        $sheet->getColumnDimension($c)->setAutoSize(true);
    }
}

/* =========================
   BUILD WORKBOOK
========================= */

$spreadsheet = new Spreadsheet();
$spreadsheet->removeSheetByIndex(0);

/* Day */
foreach ($groups['day'] as $day) {
    buildGridSheet($spreadsheet, "Day - $day", $schedules, 'day', $day, $times, $days);
}

/* Faculty */
foreach ($groups['faculty'] as $faculty) {
    buildGridSheet($spreadsheet, "Faculty - $faculty", $schedules, 'faculty', $faculty, $times, $days);
}

/* Section */
foreach ($groups['section'] as $section) {
    buildGridSheet($spreadsheet, "Section - $section", $schedules, 'section', $section, $times, $days);
}

/* Room */
foreach ($groups['room'] as $room) {
    buildGridSheet($spreadsheet, "Room - $room", $schedules, 'room', $room, $times, $days);
}

/* =========================
   OUTPUT
========================= */

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="schedule_grid_all_views.xlsx"');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
