<?php

session_start();

/* =========================================================
   GENERATE SCHEDULE (2‑PROMPT VERSION WITH AVAILABLE SLOTS)
   ---------------------------------------------------------
   - Prompt A: Assign faculty + room (NO time)
   - Prompt B: Assign day + time using available room slots
   ========================================================= */

require __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../connection/connection.php';
include __DIR__ . '/../../config_openai.php';

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Psr18Client;

/* =========================================================
   HELPER FUNCTIONS
   ========================================================= */

function timeToMinutes(string $time): int {
    [$h, $m] = explode(':', $time);
    return ((int)$h * 60) + (int)$m;
}

function minutesToTime(int $minutes): string {
    $h = floor($minutes / 60);
    $m = $minutes % 60;
    return sprintf('%02d:%02d', $h, $m);
}

function subtractIntervals(array $base, array $occupied): array {
    $result = [$base];

    foreach ($occupied as $occ) {
        $new = [];
        foreach ($result as $slot) {
            if ($occ['end'] <= $slot['start'] || $occ['start'] >= $slot['end']) {
                $new[] = $slot;
                continue;
            }
            if ($occ['start'] > $slot['start']) {
                $new[] = ['start' => $slot['start'], 'end' => $occ['start']];
            }
            if ($occ['end'] < $slot['end']) {
                $new[] = ['start' => $occ['end'], 'end' => $slot['end']];
            }
        }
        $result = $new;
    }
    return $result;
}

function buildAvailableSlotsByRoom(array $rooms, array $occupiedSlots): array {
    $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

    $schoolStart = timeToMinutes('07:30');
    $lunchStart  = timeToMinutes('12:00');
    $lunchEnd    = timeToMinutes('13:00');
    $schoolEnd   = timeToMinutes('17:30');

    $result = [];

    foreach ($rooms as $room) {
        $roomName = $room['room_name'];
        $result[$roomName] = [];

        foreach ($days as $day) {
            $baseSlots = [
                ['start' => $schoolStart, 'end' => $lunchStart],
                ['start' => $lunchEnd,   'end' => $schoolEnd],
            ];

            $roomOccupied = [];
            foreach ($occupiedSlots as $occ) {
                if ($occ['room'] === $roomName && $occ['day'] === $day) {
                    $roomOccupied[] = [
                        'start' => timeToMinutes($occ['time_start']),
                        'end'   => timeToMinutes($occ['time_end'])
                    ];
                }
            }

            foreach ($baseSlots as $base) {
                $free = subtractIntervals($base, $roomOccupied);
                foreach ($free as $slot) {
                    if ($slot['end'] > $slot['start']) {
                        $result[$roomName][] = [
                            'day'   => $day,
                            'start' => minutesToTime($slot['start']),
                            'end'   => minutesToTime($slot['end'])
                        ];
                    }
                }
            }
        }
    }
    return $result;
}

function fitsAvailableSlot(array $row, array $availableSlotsByRoom): bool {
    if (!isset($availableSlotsByRoom[$row['room']])) return false;

    foreach ($availableSlotsByRoom[$row['room']] as $slot) {
        if (
            $row['day'] === $slot['day'] &&
            $row['time_start'] >= $slot['start'] &&
            $row['time_end'] <= $slot['end']
        ) {
            return true;
        }
    }
    return false;
}

/* =========================================================
   FETCH DATA
   ========================================================= */

$subjects = $conn->query("SELECT a.assign_subject AS subject, s.subject_name, s.units, a.section_course, a.section_year FROM assign_section_subjects a JOIN manage_subjects s ON s.subject = a.assign_subject")->fetch_all(MYSQLI_ASSOC);
$sections = $conn->query("SELECT section_name, section_course, section_year, total_students FROM manage_sections")->fetch_all(MYSQLI_ASSOC);
$faculty  = $conn->query("SELECT faculty_name, employment_type, current_status, total_hours_per_week FROM manage_faculty WHERE current_status='Active'")->fetch_all(MYSQLI_ASSOC);
$rooms    = $conn->query("SELECT room_name, room_capacity FROM manage_rooms")->fetch_all(MYSQLI_ASSOC);

$faculty_days = [];
$res = $conn->query("SELECT faculty_name, day FROM manage_faculty_days");
while ($r = $res->fetch_assoc()) {
    $faculty_days[$r['faculty_name']][] = $r['day'];
}

$faculty_subjects = [];
$res = $conn->query("SELECT faculty_name, subject FROM manage_faculty_subject");
while ($r = $res->fetch_assoc()) {
    $faculty_subjects[$r['faculty_name']][] = $r['subject'];
}

/* =========================================================
   OPENAI CLIENT
   ========================================================= */

$client = OpenAI::factory()
    ->withApiKey($OPENAI_API_KEY)
    ->withHttpClient(new Psr18Client(HttpClient::create(['timeout' => 120])))
    ->make();

/* =========================================================
   PROMPTS
   ========================================================= */

$promptA = <<<PROMPT
You are a deterministic university class assignment engine.

CRITICAL:
- RETURN ONLY VALID JSON
- NO explanations, markdown, or extra text

TASK:
For the given section, assign ONE faculty member and ONE room to EACH subject.
DO NOT assign days or times.

OUTPUT FORMAT:
{
  "assignments": [
    {
      "section": "string",
      "subject": "string",
      "subject_name": "string",
      "faculty": "string",
      "room": "string",
      "units": number
    }
  ]
}

STRICT RULES:
- EVERY subject in sectionSubjects MUST be assigned exactly once
- Faculty may ONLY teach subjects listed in faculty_subjects[faculty]
- Faculty total assigned units MUST NOT exceed total_hours_per_week
- Faculty current_status MUST be Active
- Rooms must have room_capacity >= section.total_students
- Prefer rooms with the closest matching capacity
- Do NOT invent faculty, rooms, or subjects
- Do NOT output duplicate assignments
PROMPT;

$promptB = <<<PROMPT
You are a deterministic university scheduling engine.

CRITICAL:
- RETURN ONLY VALID JSON
- NO explanations, markdown, or extra text

TASK:
Assign days and times to the given class assignments.

IMPORTANT:
You may ONLY schedule classes inside the provided available_slots_by_room.
If a class does NOT fully fit inside a slot, choose a different slot or day.

OUTPUT FORMAT:
{
  "schedules": [
    {
      "section": "string",
      "subject": "string",
      "subject_name": "string",
      "faculty": "string",
      "room": "string",
      "day": "Monday|Tuesday|Wednesday|Thursday|Friday|Saturday",
      "time_start": "HH:mm",
      "time_end": "HH:mm"
    }
  ]
}

TIME RULES (STRICT):
- School hours: 07:30–17:30
- NO classes from 12:00–13:00
- time_start MUST be earlier than time_end
- Time range MUST be fully contained in ONE available room slot
- Split sessions if class duration exceeds slot length

CONFLICT RULES:
- Do NOT overlap occupied_slots
- Do NOT overlap other classes in this request
- Do NOT modify previously scheduled classes
PROMPT;


/* =========================================================
   OCCUPIED SLOTS STORAGE
   ========================================================= */

$tempFile = __DIR__ . '/temp_section.json';
if (!file_exists($tempFile)) {
    file_put_contents($tempFile, json_encode(['occupied' => []], JSON_PRETTY_PRINT));
}

$allSchedules = [];

/* =========================================================
   MAIN LOOP (PER SECTION)
   ========================================================= */

foreach ($sections as $section) {

    $occupiedData  = json_decode(file_get_contents($tempFile), true);
    $occupiedSlots = $occupiedData['occupied'] ?? [];

    $sectionSubjects = array_values(array_filter($subjects, fn($s) =>
        $s['section_course'] === $section['section_course'] &&
        $s['section_year'] === $section['section_year']
    ));

    /* ---------- PROMPT A ---------- */
    $assignmentInput = compact('sectionSubjects','section','faculty','faculty_days','faculty_subjects','rooms');

    $assignments = json_decode(
        $client->chat()->create([
            'model' => 'gpt-4.1-mini',
            'messages' => [
                ['role'=>'system','content'=>'Assign faculty and room only'],
                ['role'=>'user','content'=>$promptA . "\nDATA:\n" . json_encode($assignmentInput)]
            ],
            'response_format'=>['type'=>'json_object']
        ])['choices'][0]['message']['content'],
        true
    )['assignments'] ?? [];

    /* ---------- AVAILABLE SLOTS ---------- */
    $availableSlotsByRoom = buildAvailableSlotsByRoom($rooms, $occupiedSlots);

    /* ---------- PROMPT B ---------- */
    $timeInput = [
        'assignments' => $assignments,
        'occupied_slots' => $occupiedSlots,
        'available_slots_by_room' => $availableSlotsByRoom
    ];

    $schedules = json_decode(
        $client->chat()->create([
            'model' => 'gpt-4.1-mini',
            'messages' => [
                ['role'=>'system','content'=>'Assign class times only'],
                ['role'=>'user','content'=>$promptB . "\nDATA:\n" . json_encode($timeInput)]
            ],
            'response_format'=>['type'=>'json_object']
        ])['choices'][0]['message']['content'],
        true
    )['schedules'] ?? [];

    /* ---------- VALIDATION ---------- */
    foreach ($schedules as $row) {
        if (!fitsAvailableSlot($row, $availableSlotsByRoom)) {
            throw new Exception('Invalid slot assignment');
        }
        $allSchedules[] = $row;
        $occupiedSlots[] = [
            'day'=>$row['day'],
            'time_start'=>$row['time_start'],
            'time_end'=>$row['time_end'],
            'faculty'=>$row['faculty'],
            'room'=>$row['room'],
            'section'=>$row['section']
        ];
    }

    file_put_contents($tempFile, json_encode(['occupied'=>$occupiedSlots], JSON_PRETTY_PRINT));
    sleep(2);
}

file_put_contents(__DIR__.'/output.json', json_encode(['schedules'=>$allSchedules], JSON_PRETTY_PRINT));
header('Location: edit_schedule.php');
exit;
