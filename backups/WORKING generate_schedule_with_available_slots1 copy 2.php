<?php

session_start();
set_time_limit(0);
ignore_user_abort(true);

file_put_contents(
        __DIR__ . '/generation_debug.log',
        date('c') . " | Start" . PHP_EOL

    );

// file_put_contents(
//     __DIR__ . '/generation_debug.log',
//     date('c') . " | Start" . PHP_EOL

//     );


/* =========================================================
   GENERATE SCHEDULE (BATCH TIME-ONLY, RATE-LIMIT PROOF)
   ---------------------------------------------------------
   - Faculty resolved from manage_faculty_subject (PHP)
   - Room assigned deterministically (PHP)
   - ONE OpenAI call per batch for TIME placement
   - Safe even on very low RPM / free tier
   ========================================================= */

require __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../connection/connection.php';
include __DIR__ . '/../../config_openai.php';

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Psr18Client;

/* =========================================================
   CONFIG
   ========================================================= */

$BATCH_SIZE   = 5;    // fewer items per request
$MAX_RETRIES  = 1;
$BATCH_DELAY  = 15;   // 15 seconds between calls

/* =========================================================
   HELPER FUNCTIONS
   ========================================================= */

function timeToMinutes(string $time): int {
    [$h, $m] = explode(':', $time);
    return ((int)$h * 60) + (int)$m;
}

function minutesToTime(int $minutes): string {
    return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
}

function subtractIntervals(array $base, array $occupied): array {
    $slots = [$base];
    foreach ($occupied as $occ) {
        $new = [];
        foreach ($slots as $slot) {
            if ($occ['end'] <= $slot['start'] || $occ['start'] >= $slot['end']) {
                $new[] = $slot;
                continue;
            }
            if ($occ['start'] > $slot['start']) {
                $new[] = ['start'=>$slot['start'], 'end'=>$occ['start']];
            }
            if ($occ['end'] < $slot['end']) {
                $new[] = ['start'=>$occ['end'], 'end'=>$slot['end']];
            }
        }
        $slots = $new;
    }
    return $slots;
}

function buildAvailableSlotsByRoom(array $rooms, array $occupiedSlots): array {
    $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

    $school = [
        'start'=>timeToMinutes('07:30'),
        'lunch_start'=>timeToMinutes('12:00'),
        'lunch_end'=>timeToMinutes('13:00'),
        'end'=>timeToMinutes('17:30')
    ];

    $result = [];

    foreach ($rooms as $room) {
        $roomName = $room['room_name'];
        foreach ($days as $day) {
            $baseSlots = [
                ['start'=>$school['start'], 'end'=>$school['lunch_start']],
                ['start'=>$school['lunch_end'], 'end'=>$school['end']]
            ];

            $occupied = [];
            foreach ($occupiedSlots as $occ) {
                if ($occ['room']===$roomName && $occ['day']===$day) {
                    $occupied[] = [
                        'start'=>timeToMinutes($occ['time_start']),
                        'end'=>timeToMinutes($occ['time_end'])
                    ];
                }
            }

            foreach ($baseSlots as $base) {
                foreach (subtractIntervals($base, $occupied) as $slot) {
                    if ($slot['end']>$slot['start']) {
                        $result[$roomName][] = [
                            'day'=>$day,
                            'start'=>minutesToTime($slot['start']),
                            'end'=>minutesToTime($slot['end'])
                        ];
                    }
                }
            }
        }
    }
    return $result;
}

function fitsAvailableSlot(array $row, array $availableSlotsByRoom): bool {
    $rowStart = timeToMinutes($row['time_start']);
    $rowEnd   = timeToMinutes($row['time_end']);

    foreach ($availableSlotsByRoom[$row['room']] ?? [] as $slot) {
        if (
            $row['day'] === $slot['day']
            && $rowStart >= timeToMinutes($slot['start'])
            && $rowEnd   <= timeToMinutes($slot['end'])
        ) {
            return true;
        }
    }
    return false;
}


/* ⬇️ ADD THIS RIGHT AFTER ⬇️ */
function hasRoomConflict(array $row, array $occupiedSlots): bool {
    foreach ($occupiedSlots as $occ) {
        if (
            $occ['room'] === $row['room']
            && $occ['day'] === $row['day']
            && !(
                $row['time_end'] <= $occ['time_start']
                || $row['time_start'] >= $occ['time_end']
            )
        ) {
            return true; // ❌ room-time overlap
        }
    }
    return false; // ✅ safe
}

function resolveFacultyForSubject(string $subject, array $facultySubjects): ?string {
    foreach ($facultySubjects as $faculty => $subjects) {
        if (in_array($subject, $subjects, true)) return $faculty;
    }
    return null;
}

function pickRoom(array $rooms, int $students): string {
    usort($rooms, fn($a,$b) => abs($a['room_capacity']-$students) <=> abs($b['room_capacity']-$students));
    return $rooms[0]['room_name'];
}

function callOpenAIOnce($client, array $payload) {
    return $client->chat()->create($payload);
}

/* =========================================================
   FETCH DATA
   ========================================================= */

$subjects = $conn->query("SELECT a.assign_subject AS subject, s.subject_name, s.units, a.section_course, a.section_year FROM assign_section_subjects a JOIN manage_subjects s ON s.subject = a.assign_subject")->fetch_all(MYSQLI_ASSOC);
$sections = $conn->query("SELECT section_name, section_course, section_year, total_students FROM manage_sections")->fetch_all(MYSQLI_ASSOC);
$rooms    = $conn->query("SELECT room_name, room_capacity FROM manage_rooms")->fetch_all(MYSQLI_ASSOC);

$faculty_subjects = [];
$res = $conn->query("SELECT faculty_name, subject FROM manage_faculty_subject");
while ($r=$res->fetch_assoc()) $faculty_subjects[$r['faculty_name']][]=$r['subject'];

/* =========================================================
   OPENAI CLIENT
   ========================================================= */

$client = OpenAI::factory()
    ->withApiKey($OPENAI_API_KEY)
    ->withHttpClient(new Psr18Client(HttpClient::create(['timeout'=>120])))
    ->make();

/* =========================================================
   CHECK MISTAKES (BATCH TIME ONLY)
   ========================================================= */

   $mistakeSummary = '';
    $mistakeFile = __DIR__ . '/ai_mistakes.json';

    if (file_exists($mistakeFile)) {
        $data = json_decode(file_get_contents($mistakeFile), true);
        if (!empty($data['mistakes'])) {
            $mistakeSummary = "COMMON MISTAKES TO AVOID:\n";
            foreach (array_slice($data['mistakes'], 0, 5) as $m) {
                $mistakeSummary .= "- {$m['subject']} scheduled at {$m['time']} on {$m['day']} is invalid\n";
            }
        }
    }


/* =========================================================
   PROMPT (BATCH TIME ONLY)
   ========================================================= */

$promptBatch = <<<PROMPT
You are a deterministic university scheduling engine.

CRITICAL:
- RETURN ONLY VALID JSON
- NO explanations

$mistakeSummary


TASK:
Assign days and times to ALL provided class assignments.

RULES:
- Use available_slots_by_room as a strict boundary reference
- NO classes from 12:00–13:00
- time_start < time_end
- A room can host ONLY ONE class at any given day and time
- If a room is already occupied at a time, you MUST choose a different time
- Faculty and sections must never overlap in time

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
PROMPT;

/* =========================================================
   BUILD ALL ASSIGNMENTS (NO AI)
   ========================================================= */

$allAssignments = [];
$noAssigned = []; // ✅ declare ONCE

foreach ($sections as $section) {
    $sectionSubjects = array_values(array_filter(
        $subjects,
        fn($s) =>
            $s['section_course'] === $section['section_course']
            && $s['section_year'] === $section['section_year']
    ));

    foreach ($sectionSubjects as $s) {
        $faculty = resolveFacultyForSubject($s['subject'], $faculty_subjects);

        if (!$faculty) {
            $noAssigned[] = [
                'section'        => $section['section_name'],
                'section_course' => $section['section_course'],
                'section_year'   => $section['section_year'],
                'subject'        => $s['subject'],
                'subject_name'   => $s['subject_name']
            ];
            continue; // ⬅️ skip generation for this subject
        }

        $allAssignments[] = [
            'section'      => $section['section_name'],
            'subject'      => $s['subject'],
            'subject_name' => $s['subject_name'],
            'faculty'      => $faculty,
            'room'         => pickRoom($rooms, $section['total_students']),
            'units'        => (int)$s['units']
        ];
    }
}

/* =========================================================
   SAVE UNASSIGNED SUBJECTS (REWRITE EACH GENERATION)
   ========================================================= */

file_put_contents(
    __DIR__ . '/no_assigned_faculty.json',
    json_encode(['unassigned' => $noAssigned], JSON_PRETTY_PRINT)
);

/* =========================================================
   OCCUPIED SLOTS
   ========================================================= */

$tempFile = __DIR__ . '/temp_section.json';
if (!file_exists($tempFile)) file_put_contents($tempFile, json_encode(['occupied'=>[]]));

$occupiedSlots = json_decode(file_get_contents($tempFile), true)['occupied'] ?? [];

/* =========================================================
   BATCH TIME SCHEDULING
   ========================================================= */

// $availableSlotsByRoom = buildAvailableSlotsByRoom($rooms, $occupiedSlots);

$chunks = array_chunk($allAssignments, $BATCH_SIZE);
$finalSchedules = [];

file_put_contents(
    __DIR__ . '/generation_debug.log',
    date('c') . " | Total Assignments: " . count($allAssignments) . PHP_EOL,
    FILE_APPEND
);

file_put_contents(
    __DIR__ . '/generation_debug.log',
    date('c') . " | Total batches: " . count($chunks) . PHP_EOL,
    FILE_APPEND
);

$aiMistakes = [];
// $batchOccupied = $occupiedSlots; // copy global state

foreach ($chunks as $chunkIndex => $chunk) {

    // REBUILD slots using CURRENT occupied state
    $availableSlotsByRoom = buildAvailableSlotsByRoom($rooms, $occupiedSlots);

    // Track conflicts within this batch
    $batchOccupied = $occupiedSlots;


file_put_contents(
    __DIR__ . '/generation_debug.log',
    date('c') . " | Running batch $chunkIndex" . PHP_EOL,
    FILE_APPEND
);

    $payload = [
        'model'=>'gpt-4.1-mini',
        'max_tokens' => 600,
        'messages'=>[
            ['role'=>'system','content'=>'Assign class times only'],
            ['role'=>'user','content'=>$promptBatch."\n".json_encode([
                'assignments'=>$chunk,
                'occupied_slots'=>$occupiedSlots,
                'available_slots_by_room'=>$availableSlotsByRoom
            ])]
        ],
        'response_format'=>['type'=>'json_object']
    ];

    $response = callOpenAIOnce($client, $payload);
    $schedules = json_decode($response['choices'][0]['message']['content'], true)['schedules'] ?? [];

    foreach ($schedules as $row) {
        if (!fitsAvailableSlot($row, $availableSlotsByRoom)
            || hasRoomConflict($row, $batchOccupied)){
            //save mistakes
            $aiMistakes[] = [
            'section'      => $row['section'],
            'subject'      => $row['subject'],
            'subject_name' => $row['subject_name'],
            'faculty'      => $row['faculty'],
            'room'         => $row['room'],
            'units'        => $row['units'] ?? 1,
            'reason'       => 'Room conflict or invalid slot'
        ];

            continue;
        }
        $finalSchedules[] = $row;
        $entry = [
            'day'        => $row['day'],
            'time_start'=> $row['time_start'],
            'time_end'  => $row['time_end'],
            'faculty'   => $row['faculty'],
            'room'      => $row['room'],
            'section'   => $row['section']
        ];

        $occupiedSlots[] = $entry;
        $batchOccupied[] = $entry;
            }

        file_put_contents(
            __DIR__ . '/generation_debug.log',
            date('c') . " | AI returned: " . count($schedules) . PHP_EOL,
            FILE_APPEND
        );


        file_put_contents(
        __DIR__ . '/generation_debug.log',
        date('c') . " | Batch $chunkIndex accepted: " . count($finalSchedules) . PHP_EOL,
        FILE_APPEND
    );


    file_put_contents($tempFile, json_encode(['occupied'=>$occupiedSlots], JSON_PRETTY_PRINT));
    file_put_contents(
        __DIR__ . '/generation_debug.log',
        date('c') . " | Done current batch, wait for $BATCH_DELAY seconds before proceeding to the next batch"  . PHP_EOL,
        FILE_APPEND
    );
    sleep($BATCH_DELAY);
}

/* =========================================================
   RETRY FAILED ASSIGNMENTS (STEP 1: BUILD RETRY INPUT)
   ========================================================= */



$retryPrompt = <<<PROMPT
You are retrying FAILED schedule assignments.

STRICT RETRY RULES:
- These assignments failed previously
- Be EXTRA careful with conflicts
- NEVER overlap rooms, faculty, or sections
- Do NOT change faculty or room
- Prefer earliest valid time slots
- Avoid late afternoon if possible
- NEVER touch 12:00–13:00

RETURN ONLY VALID JSON.
NO explanations.

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
PROMPT;
$retryAssignments = [];

foreach ($aiMistakes as $m) {
    $retryAssignments[] = [
        'section'      => $m['section'],
        'subject'      => $m['subject'],
        'subject_name' => $m['subject_name'],
        'faculty'      => $m['faculty'],
        'room'         => $m['room'],   // keep same room
        'units'        => (int)$m['units']
    ];
}

if (!empty($retryAssignments)) {

    file_put_contents(
        __DIR__ . '/generation_debug.log',
        date('c') . " | Retrying " . count($retryAssignments) . " failed assignments" . PHP_EOL,
        FILE_APPEND
    );

    $retryPayload = [
        'model' => 'gpt-4.1-mini',
        'max_tokens' => 400,
        'messages' => [
            ['role'=>'system','content'=>'Retry scheduling'],
            ['role'=>'user','content'=>$retryPrompt."\n".json_encode([
                'assignments' => $retryAssignments,
                'occupied_slots' => $occupiedSlots,
                'available_slots_by_room' => $availableSlotsByRoom
            ])]
        ],
        'response_format' => ['type'=>'json_object']
    ];

    $retryResponse = callOpenAIOnce($client, $retryPayload);
    $retrySchedules = json_decode(
        $retryResponse['choices'][0]['message']['content'] ?? '',
        true
    )['schedules'] ?? [];

    foreach ($retrySchedules as $row) {

        if (
            !fitsAvailableSlot($row, $availableSlotsByRoom)
            || hasRoomConflict($row, $occupiedSlots)
        ) {
            continue; // still invalid → give up
        }

        // ✅ accept retry success
        $finalSchedules[] = $row;

        $occupiedSlots[] = [
            'day'        => $row['day'],
            'time_start'=> $row['time_start'],
            'time_end'  => $row['time_end'],
            'faculty'   => $row['faculty'],
            'room'      => $row['room'],
            'section'   => $row['section']
        ];
    }
}

file_put_contents(
        __DIR__ . '/generation_debug.log',
        date('c') . " | Done Generating" . PHP_EOL,
        FILE_APPEND
    );


file_put_contents(__DIR__.'/output.json', json_encode(['schedules'=>$finalSchedules], JSON_PRETTY_PRINT));
header('Location: edit_schedule.php');
exit;
