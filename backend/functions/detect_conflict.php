<?php

/**
 * Detect scheduling conflicts.
 *
 * @param array $schedules Array of schedule entries
 * @return array [
 *   'has_conflict' => bool,
 *   'conflicts' => array
 * ]
 */
function detect_conflicts(array $schedules): array
{
    $conflicts = [];

    $count = count($schedules);

    for ($i = 0; $i < $count; $i++) {
        for ($j = $i + 1; $j < $count; $j++) {

            $a = $schedules[$i];
            $b = $schedules[$j];

            // Must be same day to conflict
            if ($a['day'] !== $b['day']) {
                continue;
            }

            // Convert time to minutes
            $aStart = time_to_minutes($a['time_start']);
            $aEnd   = time_to_minutes($a['time_end']);
            $bStart = time_to_minutes($b['time_start']);
            $bEnd   = time_to_minutes($b['time_end']);

            // Check overlap
            if ($aStart < $bEnd && $bStart < $aEnd) {

                // Section conflict
                if ($a['section'] === $b['section']) {
                    $conflicts[] = build_conflict('SECTION', $a, $b);
                }

                // Faculty conflict
                if ($a['faculty'] === $b['faculty']) {
                    $conflicts[] = build_conflict('FACULTY', $a, $b);
                }

                // Room conflict
                if ($a['room'] === $b['room']) {
                    $conflicts[] = build_conflict('ROOM', $a, $b);
                }
            }
        }
    }

    return [
        'has_conflict' => !empty($conflicts),
        'conflicts' => $conflicts
    ];
}

/* ============================
   HELPERS
============================ */

function time_to_minutes(string $time): int
{
    [$h, $m] = explode(':', $time);
    return ((int)$h * 60) + (int)$m;
}

function build_conflict(string $type, array $a, array $b): array
{
    return [
        'type' => $type,
        'day' => $a['day'],
        'entry_1' => $a,
        'entry_2' => $b
    ];
}
