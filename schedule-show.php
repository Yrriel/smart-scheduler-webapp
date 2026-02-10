<?php
include "connection.php";

/* =======================
   BUILD TIME SLOTS
======================= */
$timeSlots = [];
$start = strtotime('07:00');
$end   = strtotime('17:30');

while ($start < $end) {
    $next = $start + 30 * 60;
    $timeSlots[] = date('g:i A', $start) . ' - ' . date('g:i A', $next);
    $start = $next;
}

/* =======================
   FETCH GENERATED SCHEDULE
======================= */
$scheduleQuery = $conn->query("SELECT * FROM generated_schedule ORDER BY day, time_start");
$scheduleData = [];

while ($row = $scheduleQuery->fetch_assoc()) {
    $scheduleData[] = $row;
}
?>

<style>
.day-cell { padding: 0 !important; vertical-align: top; }
td[rowspan] { position: relative; padding: 0 !important; }

.merged-wrapper {
    position: absolute;
    inset: 0;
    background: #4a6cf7;
    color: white;
    padding: 6px;
    border-radius: 6px;
    box-sizing: border-box;
}

.class-box {
    font-size: 12px;
    line-height: 1.3em;
}

.room-label {
    font-size: 10px;
    opacity: 0.9;
}
</style>

<div class="schedule-table-wrapper">
<table class="schedule-grid">
    <thead>
        <tr>
            <th class="time-header">Time</th>
            <th>MON</th>
            <th>TUE</th>
            <th>WED</th>
            <th>THU</th>
            <th>FRI</th>
            <th>SAT</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($timeSlots as $slot): ?>
        <tr>
            <td class="time-cell"><?= htmlspecialchars($slot) ?></td>
            <td class="day-cell" data-day="Monday"></td>
            <td class="day-cell" data-day="Tuesday"></td>
            <td class="day-cell" data-day="Wednesday"></td>
            <td class="day-cell" data-day="Thursday"></td>
            <td class="day-cell" data-day="Friday"></td>
            <td class="day-cell" data-day="Saturday"></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<script>
const schedule = <?= json_encode($scheduleData) ?>;
const rows = document.querySelectorAll("tbody tr");

schedule.forEach(entry => {
    const startMin = toMinutes(entry.time_start);
    const endMin   = toMinutes(entry.time_end);
    let matchedCells = [];

    rows.forEach(row => {
        const [slotStart, slotEnd] =
            row.querySelector(".time-cell").textContent
            .split(" - ")
            .map(toMinutes);

        if (startMin < slotEnd && endMin > slotStart) {
            const cell = row.querySelector(`.day-cell[data-day="${entry.day}"]`);
            if (cell) matchedCells.push(cell);
        }
    });

    if (matchedCells.length) {
        const top = matchedCells[0];

        top.innerHTML = `
            <div class="merged-wrapper">
                <div class="class-box">
                    <strong>${entry.subject}</strong><br>
                    ${entry.subject_name}<br>
                    ${entry.faculty}<br>
                    <span class="room-label">${entry.room}</span>
                </div>
            </div>
        `;

        top.setAttribute("rowspan", matchedCells.length);
        for (let i = 1; i < matchedCells.length; i++) {
            matchedCells[i].remove();
        }
    }
});

function toMinutes(t) {
    t = t.trim().toUpperCase();
    if (t.includes("AM") || t.includes("PM")) return convertTo24(t);
    let [h, m] = t.split(":").map(Number);
    return h * 60 + m;
}

function convertTo24(t) {
    let [time, mer] = t.split(" ");
    let [h, m] = time.split(":").map(Number);
    if (mer === "PM" && h !== 12) h += 12;
    if (mer === "AM" && h === a12) h = 0;
    return h * 60 + m;
}
</script>
