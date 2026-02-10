<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$jsonFile = __DIR__ . '/edit-output.json';

if (!file_exists($jsonFile)) {
    die('No schedule data found.');
}

$data = json_decode(file_get_contents($jsonFile), true);
$schedules = $data['schedules'] ?? [];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Schedule');

/* =========================
   HEADERS
========================= */

$headers = [
    'Day',
    'Time Start',
    'Time End',
    'Subject',
    'Section',
    'Faculty',
    'Room'
];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}

/* =========================
   DATA ROWS
========================= */

$rowNum = 2;

foreach ($schedules as $s) {
    $sheet->setCellValue("A$rowNum", $s['day']);
    $sheet->setCellValue("B$rowNum", $s['time_start']);
    $sheet->setCellValue("C$rowNum", $s['time_end']);
    $sheet->setCellValue("D$rowNum", $s['subject']);
    $sheet->setCellValue("E$rowNum", $s['section']);
    $sheet->setCellValue("F$rowNum", $s['faculty']);
    $sheet->setCellValue("G$rowNum", $s['room']);
    $rowNum++;
}

/* =========================
   AUTO WIDTH
========================= */
foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

/* =========================
   DOWNLOAD
========================= */

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="schedule.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
