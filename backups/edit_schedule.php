<?php
// edit_schedule.php

$jsonFile = __DIR__ . '/output.json';

if (!file_exists($jsonFile)) {
    die("❌ output.json not found. Please generate schedule first.");
}

$data = json_decode(file_get_contents($jsonFile), true);

if (!isset($data['schedules']) || !is_array($data['schedules'])) {
    die("❌ Invalid output.json format.");
}

$schedules = $data['schedules'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Schedule</title>

<style>
:root {
    --bg: #f7f9fc;
    --card: #ffffff;
    --border: #e0e0e0;
    --primary: #2563eb;
    --danger: #dc2626;
    --success: #16a34a;
    --muted: #6b7280;
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 24px;
    font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
    background: var(--bg);
    color: #111827;
}

h2 {
    margin-bottom: 16px;
}

.card {
    background: var(--card);
    border-radius: 10px;
    padding: 16px;
    box-shadow: 0 6px 18px rgba(0,0,0,.06);
}

.actions {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

button {
    border: none;
    background: var(--primary);
    color: #fff;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
}

button.secondary {
    background: #e5e7eb;
    color: #111;
}

button.danger {
    background: var(--danger);
}

button:hover {
    opacity: .9;
}

#errorBox {
    white-space: pre-line;
    color: var(--danger);
    margin-bottom: 12px;
    font-weight: 500;
}

.table-wrapper {
    overflow-x: auto;
}

table {
    border-collapse: collapse;
    width: 100%;
    min-width: 900px;
}

th {
    position: sticky;
    top: 0;
    background: #f1f5f9;
    z-index: 1;
    font-size: 13px;
    text-transform: uppercase;
    color: var(--muted);
}

th, td {
    border: 1px solid var(--border);
    padding: 6px;
    text-align: center;
}

tr.conflict {
    background-color: #fee2e2;
}

input {
    width: 100%;
    padding: 6px;
    border-radius: 6px;
    border: 1px solid var(--border);
    font-size: 13px;
}

input:focus {
    outline: none;
    border-color: var(--primary);
}

.footer-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 16px;
    flex-wrap: wrap;
    gap: 12px;
}
</style>

</head>

<body>

<h2>Edit Generated Schedule</h2>
<form action="retry_skipped_sections.php">
    <button>retry generating skipped sections</button>
</form>

<div id="errorBox"></div>

<table id="scheduleTable">
    <thead>
        <tr>
            <th>Section</th>
            <th>Subject</th>
            <th>Subject Name</th>
            <th>Faculty</th>
            <th>Room</th>
            <th>Day</th>
            <th>Start</th>
            <th>End</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($schedules as $i => $row): ?>
        <tr>
            <?php foreach ($row as $key => $value): ?>
                <td>
                    <input
                        type="text"
                        value="<?= htmlspecialchars($value) ?>"
                        data-row="<?= $i ?>"
                        data-field="<?= $key ?>"
                    >
                </td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<button onclick="validateSchedules()">Validate</button>
<a href="save_schedule.php"><button>Confirm & Save</button></a>

<script>
let schedules = <?= json_encode($schedules) ?>;

/* ============================
   HELPERS
============================ */

function timeToMinutes(t) {
    const [h, m] = t.split(':').map(Number);
    return h * 60 + m;
}

function overlap(aStart, aEnd, bStart, bEnd) {
    return aStart < bEnd && bStart < aEnd;
}

/* ============================
   UPDATE DATA FROM INPUTS
============================ */

document.querySelectorAll('input').forEach(input => {
    input.addEventListener('input', e => {
        const row = e.target.dataset.row;
        const field = e.target.dataset.field;
        schedules[row][field] = e.target.value;
    });
});

/* ============================
   VALIDATION
============================ */

function validateSchedules() {
    document.querySelectorAll('tr').forEach(r => r.classList.remove('conflict'));
    document.getElementById('errorBox').innerText = '';

    let errors = [];

    for (let i = 0; i < schedules.length; i++) {
        for (let j = i + 1; j < schedules.length; j++) {

            const a = schedules[i];
            const b = schedules[j];

            if (a.day !== b.day) continue;

            const aStart = timeToMinutes(a.time_start);
            const aEnd   = timeToMinutes(a.time_end);
            const bStart = timeToMinutes(b.time_start);
            const bEnd   = timeToMinutes(b.time_end);

            if (!overlap(aStart, aEnd, bStart, bEnd)) continue;

            if (a.section === b.section)
                errors.push(`Section conflict (${a.section}) on ${a.day}`);

            if (a.faculty === b.faculty)
                errors.push(`Faculty conflict (${a.faculty}) on ${a.day}`);

            if (a.room === b.room)
                errors.push(`Room conflict (${a.room}) on ${a.day}`);
        }
    }

    if (errors.length) {
        document.getElementById('errorBox').innerText =
            'Conflicts detected:\n' + [...new Set(errors)].join('\n');
        return false;
    }

    alert('✅ No conflicts detected');
    return true;
}

/* ============================
   SAVE FINAL OUTPUT
============================ */

// function saveSchedules() {
//     if (!validateSchedules()) return;

//     fetch('save_final_output.php', {
//         method: 'POST',
//         headers: { 'Content-Type': 'application/json' },
//         body: JSON.stringify({ schedules })
//     })
//     .then(res => res.text())
//     .then(() => {
//         window.location.href = 'save_schedule.php';
//     })
//     .catch(err => alert('Save failed'));
// }
</script>

</body>
</html>
