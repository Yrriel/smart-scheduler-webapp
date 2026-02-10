<?php

session_start();

/* ============================

   generate_schedule function:
        - Generate Schedule from OpenAI
        - Redirect to edit.php after generating.

        Note from Dev: 
            this generation type relies on free tier of openai, means it'll
            be slower but hey, atleast it's free.

            if it had an error of : Maximum execution time of 120 seconds exceeded
            just increase the time. check readme.txt

============================ */

// if (isset($_SESSION['schedule_running'])) {
//     die("⏳ Schedule generation already in progress.");
// }

// $_SESSION['schedule_running'] = true;

/* ============================

    planning to uncomment that section above so that
    there will be no multiple requests

============================ */


require __DIR__ . '/../../vendor/autoload.php';
include __DIR__ . '/../connection/connection.php';
include __DIR__ . '/../../config_openai.php';

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Psr18Client;

/* ============================
   FETCH & NORMALIZE DATA
============================ */

$subjects = $conn->query("
    SELECT 
        a.assign_subject AS subject,
        s.subject_name,
        s.units,
        a.section_course,
        a.section_year
    FROM assign_section_subjects a
    JOIN manage_subjects s 
        ON s.subject = a.assign_subject
")->fetch_all(MYSQLI_ASSOC);

if (empty($subjects)) {
    die("❌ No assigned subjects found.");
}

$sections = $conn->query("
    SELECT section_name, section_course, section_year, total_students
    FROM manage_sections
")->fetch_all(MYSQLI_ASSOC);


$faculty = $conn->query("
    SELECT faculty_name, employment_type, current_status, total_hours_per_week
    FROM manage_faculty
")->fetch_all(MYSQLI_ASSOC);

$rooms = $conn->query("
    SELECT room_name, room_capacity
    FROM manage_rooms
")->fetch_all(MYSQLI_ASSOC);

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

/* ============================
   BUILD AI INPUT
============================ */

$inputData = [
    'assigned_subjects' => $subjects,
    'sections' => $sections,
    'faculty' => $faculty,
    'faculty_days' => $faculty_days,
    'faculty_subjects' => $faculty_subjects,
    'rooms' => $rooms,
    'constraints' => [
        'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
        'start_time' => '07:30',
        'end_time' => '17:30'
    ]
];

/* ============================
   OPENAI CLIENT (PSR-18)
============================ */

$symfonyClient = HttpClient::create([
    'timeout' => 120,
    'max_duration' => 180
]);

$psr18Client = new Psr18Client($symfonyClient);

$client = OpenAI::factory()
    ->withApiKey($OPENAI_API_KEY)
    ->withHttpClient($psr18Client)
    ->make();

/* ============================
   PROMPT (STRICT SCHEMA)
============================ */

$prompt = <<<PROMPT
You are a deterministic university scheduling engine that MUST generate a complete, conflict-free schedule.

# CRITICAL: RETURN ONLY VALID JSON
- NO explanations, comments, markdown blocks, or non-JSON text
- Output must be parseable by JSON.parse()
- Do not wrap in ```json``` code blocks

# OUTPUT FORMAT
Return a SINGLE JSON OBJECT with this exact structure:

```
{
  "schedules": [ <array of schedule entries> ],
  "metadata": {
    "total_entries": <number>,
    "sections_processed": [ <array of section names> ],
    "conflicts_resolved": <number>
  }
}
```

Each schedule entry MUST include EXACTLY these fields:
- section (string)
- subject (string: subject code)
- subject_name (string)
- faculty (string: full name)
- room (string: room identifier)
- day (string: Monday/Tuesday/Wednesday/Thursday/Friday/Saturday)
- time_start (string: HH:mm format)
- time_end (string: HH:mm format)

# TIME RULES (STRICTLY ENFORCE)
- School operating hours: 07:30 to 17:30
- Mandatory lunch break: 12:00 to 13:00 (NO CLASSES)
- All times use 24-hour HH:mm format (e.g., "08:00", "14:30")
- time_start MUST be < time_end
- Classes CANNOT start before 07:30 or end after 17:30
- Classes CANNOT overlap the 12:00-13:00 lunch period

# SCHEDULING ALGORITHM
1. Process ALL sections provided in the input
2. For each section, schedule ALL subjects assigned to it
3. Assign time slots sequentially, checking for conflicts BEFORE finalizing
4. If a subject duration causes lunch overlap or exceeds 17:30:
   - Split the class into smaller sessions
   - Schedule remaining hours on a different day/time
   - Example: 3-hour class starting at 11:00 → split into 1hr (11:00-12:00) + 2hr (13:00-15:00) OR move entirely to another day

# CONFLICT PREVENTION (VALIDATE BEFORE ASSIGNING)
Before assigning ANY schedule entry, verify:

1. **Section Conflict**: The section is NOT already scheduled at this day+time
2. **Faculty Conflict**: The faculty is NOT teaching another section at this day+time
3. **Room Conflict**: The room is NOT occupied by another section at this day+time
4. **Capacity Check**: room.capacity >= section.capacity (room must accommodate all students)
5. **Time Overlap**: No partial or complete overlap with existing entries

Time overlap detection:
- Entry A overlaps Entry B if they share the same day AND:
  - A.time_start < B.time_end AND A.time_end > B.time_start

If ANY conflict exists, try alternative:
- Different time slot on the same day
- Different day entirely
- Different room (if room conflict only)

DO NOT assign if conflicts cannot be resolved.

# ROOM ASSIGNMENT RULES
- Any available room may be used regardless of type (LAB, CHEMLAB, AVR, NSTP, regular classroom)
- CRITICAL: room.capacity >= section.capacity (room must fit all students in the section)
- Check room availability at the specific day+time before assignment
- Prefer exact capacity matches when possible to optimize room usage

# MANDATORY COMPLETENESS CHECK
- EVERY section in the input MUST appear in the output
- EVERY subject assigned to a section MUST be scheduled
- If a section or subject is missing, retry scheduling with different time/room allocation
- Include sections_processed array in metadata to verify completeness

# QUALITY REQUIREMENTS
- Minimize scheduling gaps for each section
- Distribute classes evenly across weekdays when possible
- Avoid back-to-back classes exceeding 4 hours without breaks (except lunch)
- Prefer morning slots for laboratory subjects if possible

# EXAMPLE OUTPUT

{
  "schedules": [
    {
      "section": "BSCS 1A",
      "subject": "COMPROG1",
      "subject_name": "Computer Programming 1",
      "faculty": "Juan Dela Cruz",
      "room": "LAB 101",
      "day": "Monday",
      "time_start": "08:00",
      "time_end": "11:00"
    },
    {
      "section": "BSCS 1A",
      "subject": "CALCULUS1",
      "subject_name": "Calculus 1",
      "faculty": "Maria Santos",
      "room": "ROOM 201",
      "day": "Monday",
      "time_start": "13:00",
      "time_end": "15:00"
    },
    {
      "section": "BSIT 2B",
      "subject": "DBMS",
      "subject_name": "Database Management Systems",
      "faculty": "Pedro Reyes",
      "room": "LAB 102",
      "day": "Tuesday",
      "time_start": "09:00",
      "time_end": "12:00"
    }
  ],
  "metadata": {
    "total_entries": 3,
    "sections_processed": ["BSCS 1A", "BSIT 2B"],
    "conflicts_resolved": 2
  }
}

# DEBUGGING CHECKLIST (INTERNAL - DO NOT OUTPUT)
Before returning JSON, verify:
- [ ] All sections from input are in sections_processed
- [ ] No duplicate (section + day + time) combinations
- [ ] No duplicate (faculty + day + time) combinations  
- [ ] No duplicate (room + day + time) combinations
- [ ] No time ranges overlap lunch (12:00-13:00)
- [ ] All time_start values are < time_end
- [ ] All times are within 07:30-17:30
- [ ] All rooms have capacity >= section capacity
- [ ] JSON is valid (no trailing commas, proper escaping)
PROMPT;

/* ============================
   CALL OPENAI
============================ */

/* ============================
   GENERATE PER SECTION (FIX B)
============================ */

$allSchedules = [];
$skippedSections = [];

foreach ($sections as $section) {

    // Build section-specific input
    $sectionInputData = [
        'assigned_subjects' => array_values(array_filter(
            $subjects,
            fn($s) =>
                $s['section_course'] === $section['section_course'] &&
                $s['section_year'] === $section['section_year']
        )),
        'sections' => [$section],
        'faculty' => $faculty,
        'faculty_days' => $faculty_days,
        'faculty_subjects' => $faculty_subjects,
        'rooms' => $rooms,
        'constraints' => [
            'days' => ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],
            'start_time' => '07:30',
            'end_time' => '17:30'
        ]
    ];

    $response = null;
    $maxRetries = 5;
    $delay = 2;

    for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
        try {
            $response = $client->chat()->create([
                'model' => 'gpt-4.1-mini',
                'max_tokens' => 1200,
                'messages' => [
                    ['role' => 'system', 'content' => 'You generate conflict-free university schedules and output JSON only.'],
                    ['role' => 'user', 'content' => $prompt . "\n\nDATA:\n" . json_encode($sectionInputData)]
                ],
                'response_format' => ['type' => 'json_object']
            ]);
            break;
        } catch (
            \OpenAI\Exceptions\RateLimitException |
            \OpenAI\Exceptions\TransporterException $e
        ) {
            // If still rate-limited after retries, skip this section
            if ($attempt === $maxRetries) {
                error_log(
                    "Skipped section {$section['section_name']} due to rate limit."
                );
                $skippedSections[] = $section['section_name'];
                continue 2; // ⬅ exit retry loop AND move to next section
            }

            sleep($delay);
            $delay *= 2;
        }
    }

    if (
        !$response ||
        !isset($response['choices'][0]['message']['content'])
    ) {
        continue;
    }

    $jsonText = $response['choices'][0]['message']['content'];
    $data = json_decode($jsonText, true);

    if (
        !is_array($data) ||
        !isset($data['schedules']) ||
        !is_array($data['schedules'])
    ) {
        continue;
    }

    foreach ($data['schedules'] as $row) {
        $allSchedules[] = $row;
    }

    // pacing — CRITICAL
    /* ============================
        There's a Request Rate limit, delay is needed.
       ============================ */
    sleep(2);
}


$rows = $allSchedules;

file_put_contents(
    __DIR__ . '/skipped_sections.json',
    json_encode($skippedSections, JSON_PRETTY_PRINT)
);


file_put_contents(
    __DIR__ . '/output.json',
    json_encode(
        ['schedules' => $rows],
        JSON_PRETTY_PRINT
    )
);


/* ============================
   DONE
============================ */

// unset($_SESSION['schedule_running']);

header("Location: edit_schedule.php");
exit;
