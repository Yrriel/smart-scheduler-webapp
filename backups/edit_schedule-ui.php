<?php
// edit_schedule.php
$output = __DIR__ . '/output.json';
$edit   = __DIR__ . '/edit-output.json';

if (!file_exists($edit)) {
    copy($output, $edit);
}

$jsonFile = __DIR__ . '/edit-output.json';

if (!file_exists($jsonFile)) {
    die("❌ output.json not found. Please generate schedule first.");
}

$data = json_decode(file_get_contents($jsonFile), true);

if (!isset($data['schedules']) || !is_array($data['schedules'])) {
    die("❌ Invalid output.json format.");
}

$schedules = $data['schedules'];

/* ============================
   CONFIG
============================ */

$rooms = ['201','204','301','302','303','304','AVR','CHEMLAB','NSTP'];
$days  = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

$times = [];
$start = strtotime('7:00 AM');
$end   = strtotime('5:30 PM');

while ($start < $end) {
    $times[] = date('g:i A', $start);
    $start = strtotime('+30 minutes', $start);
}

/* ============================
   HELPERS
============================ */

function timeIndex($time, $times) {
    $formatted = date('g:i A', strtotime($time));
    return array_search($formatted, $times);
}

$timeRowCount = count($times);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Schedule Grid</title>

<style>
body {
    margin: 0;
    padding: 20px;
    background: #f4f6fb;
    font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
}

/* ============================
   TOP BAR
============================ */

.top-bar {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
    align-items: center;
    flex-wrap: wrap;
}

select {
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    font-size: 14px;
}

button {
    border: none;
    background: #304ffe;
    color: #fff;
    padding: 10px 16px;
    border-radius: 8px;
    cursor: pointer;
}

button.secondary {
    background: #e5e7eb;
    color: #111;
}

/* ============================
   GRID
============================ */

.schedule-wrapper {
    overflow-x: auto;
}

.schedule-grid {
    display: grid;
    grid-template-columns: 130px repeat(9, minmax(120px, 1fr));
    grid-template-rows: 48px repeat(<?= $timeRowCount ?>, 48px);
    background: #e5e7eb;
    border-radius: 12px;
}

/* ============================
   HEADERS
============================ */

.grid-header {
    background: #304ffe;
    color: #fff;
    font-weight: 600;
    text-align: center;
    line-height: 48px;
    position: sticky;
    top: 0;
    z-index: 10;
}

.time-header {
    background: #1e3a8a;
    color: #fff;
    position: sticky;
    left: 0;
    z-index: 11;
    display: flex;
    align-items: center;
    padding-left: 10px;
}

/* ============================
   TIME COLUMN
============================ */

.time-col {
    background: #1e3a8a;
    color: #fff;
    font-size: 13px;
    padding-left: 10px;
    display: flex;
    align-items: center;
    position: sticky;
    left: 0;
    z-index: 9;
}

/* ============================
   CELLS
============================ */

.cell {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
}

/* ============================
   EVENTS
============================ */

.event {
    background: linear-gradient(135deg, #4f6ef7, #3b5bfd);
    color: white;
    border-radius: 8px;
    padding: 6px 8px;
    font-size: 12px;
    box-shadow: 0 6px 14px rgba(0,0,0,.15);
    z-index: 5;
}

.event.hidden {
    display: none;
}

.event .subject {
    font-weight: 700;
}

.event .faculty {
    font-size: 11px;
    opacity: .8;
}
.event {
  cursor: grab;
  user-select: none;
}

.event.dragging {
  /* opacity: 0.85; */
  cursor: grabbing;
  z-index: 1000;
}

button.danger {
  background: #dc2626;      /* red */
  color: #fff;
}

button.danger:hover {
  background: #b91c1c;      /* darker red */
}

/* resize */

.event {
  position: relative;
  cursor: grab;
}

/* top resize zone */
.event::before,
.event::after {
  content: "";
  position: absolute;
  left: 0;
  width: 100%;
  height: 8px;
}

/* top edge */
.event::before {
  top: 0;
  cursor: ns-resize;
}

/* bottom edge */
.event::after {
  bottom: 0;
  cursor: ns-resize;
}

.event {
  cursor: grab;
}

.event:hover::before,
.event:hover::after {
  background: rgba(255,255,255,0.4);
}

.cell {
  pointer-events: none;
}

.event {
  pointer-events: auto;
}

.drag-clone {
  position: fixed;
  pointer-events: none;
  z-index: 9999;
  opacity: 0.9;
  transform: translate(-50%, -50%);
}


</style>
</head>

<body>

<section>
    <h2>📅 Generated Schedule</h2>

<div class="top-bar">
    <label>
        Day:
        <select id="dayFilter">
            <?php foreach ($days as $day): ?>
                <option value="<?= $day ?>"><?= $day ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <button onclick="validateSchedules()">Validate</button>

    <form action="retry_skipped_sections.php">
        <button class="secondary">Retry Skipped Sections</button>
    </form>
    <a href="cancel_edit_json.php" onclick="return confirm('Discard all changes and exit edit mode?');">
        <button class="danger">Cancel Edit</button>
    </a>


    <a href="save_schedule.php">
        <button>Confirm & Save</button>
    </a>
</div>

<div class="schedule-wrapper">
<div class="schedule-grid">

    <!-- HEADER -->
    <div class="grid-header time-header">Time</div>
    <?php foreach ($rooms as $room): ?>
        <div class="grid-header"><?= htmlspecialchars($room) ?></div>
    <?php endforeach; ?>

    <!-- TIME ROWS -->
    <?php foreach ($times as $i => $time): ?>
        <div class="time-col" style="grid-row: <?= $i + 2 ?>;">
            <?= $time ?>
        </div>

        <?php foreach ($rooms as $room): ?>
            <div class="cell" style="grid-row: <?= $i + 2 ?>;"></div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <!-- EVENTS -->
    <?php foreach ($schedules as $index => $row): ?>

        <?php
            $col = array_search($row['room'], $rooms);
            $rowStart = timeIndex($row['time_start'], $times);
            $rowEnd   = timeIndex($row['time_end'], $times);
            if ($col === false || $rowStart === false || $rowEnd === false) continue;
        ?>
        <div class="event"
             data-day="<?= htmlspecialchars($row['day']) ?>"
             data-room="<?= htmlspecialchars($row['room']) ?>"
             data-faculty="<?= htmlspecialchars($row['faculty']) ?>"
             data-start="<?= $row['time_start'] ?>"
             data-end="<?= $row['time_end'] ?>"
             style="
                grid-column: <?= $col + 2 ?>;
                grid-row: <?= $rowStart + 2 ?> / <?= $rowEnd + 2 ?>;
             ">
            <div class="subject"><?= htmlspecialchars($row['subject']) ?></div>
            <div><?= htmlspecialchars($row['section']) ?></div>
            <div class="faculty"><?= htmlspecialchars($row['faculty']) ?></div>

            <!-- resize handle -->
            <div class="resize-handle"></div>
        </div>
    <?php endforeach; ?>

</div>
</div>
</section>

<script>
const daySelect = document.getElementById('dayFilter');
const events = document.querySelectorAll('.event');

function filterByDay() {
    const selectedDay = daySelect.value;
    events.forEach(ev => {
        ev.classList.toggle('hidden', ev.dataset.day !== selectedDay);
    });
}

filterByDay();
daySelect.addEventListener('change', filterByDay);

/* ============================
   VALIDATION (PER DAY)
============================ */

function timeToMinutes(t) {
    const [h, m] = t.split(':').map(Number);
    return h * 60 + m;
}

function overlap(aStart, aEnd, bStart, bEnd) {
    return aStart < bEnd && bStart < aEnd;
}

function validateSchedules() {
    const selectedDay = daySelect.value;
    const visible = [...events].filter(e => !e.classList.contains('hidden'));

    let errors = [];

    for (let i = 0; i < visible.length; i++) {
        for (let j = i + 1; j < visible.length; j++) {

            const a = visible[i].dataset;
            const b = visible[j].dataset;

            const aStart = timeToMinutes(a.start);
            const aEnd   = timeToMinutes(a.end);
            const bStart = timeToMinutes(b.start);
            const bEnd   = timeToMinutes(b.end);

            if (!overlap(aStart, aEnd, bStart, bEnd)) continue;

            if (a.room === b.room)
                errors.push(`Room conflict (${a.room})`);

            if (a.faculty === b.faculty)
                errors.push(`Faculty conflict (${a.faculty})`);
        }
    }

    if (errors.length) {
        alert(`❌ Conflicts on ${selectedDay}:\n\n` + [...new Set(errors)].join('\n'));
        return false;
    }

    alert(`✅ No conflicts detected on ${selectedDay}`);
    return true;
}
</script>

<!-- Edit -->

<script>
const grid = document.querySelector('.schedule-grid');
const eventsList = document.querySelectorAll('.event');

const CELL_HEIGHT = 48;
const TIME_COL_WIDTH = 130;
const COL_WIDTH = 160;

eventsList.forEach(card => {
  let startX, startY;
  let origCol, origRow;

  card.addEventListener('mousedown', e => {
    const rect = card.getBoundingClientRect();
    const offsetY = e.clientY - rect.top;

    // If resizing zone → do NOT drag
    if (offsetY <= 8 || offsetY >= rect.height - 8) return;

    e.preventDefault();
    card.classList.add('dragging');

    startX = e.clientX;
    startY = e.clientY;

    const style = window.getComputedStyle(card);
    origCol = parseInt(style.gridColumnStart);
    origRow = parseInt(style.gridRowStart);

    const rowEnd = parseInt(style.gridRowEnd);
    const origSpan = rowEnd - origRow;

    function onMove(ev) {
      const dx = ev.clientX - startX;
      const dy = ev.clientY - startY;
      card.style.transform = `translate(${dx}px, ${dy}px)`;
    }

    function onUp(ev) {
      card.classList.remove('dragging');
      card.style.transform = '';

      const dx = ev.clientX - startX;
      const dy = ev.clientY - startY;

      const colShift = Math.round(dx / COL_WIDTH);
      const rowShift = Math.round(dy / CELL_HEIGHT);

        const newCol = Math.max(2, origCol + colShift);
        const newRow = Math.max(2, origRow + rowShift);

        card.style.gridColumnStart = newCol;
        card.style.gridRowStart = newRow;
        card.style.gridRowEnd = newRow + origSpan;

      document.removeEventListener('mousemove', onMove);
      document.removeEventListener('mouseup', onUp);
    }

    document.addEventListener('mousemove', onMove);
    document.addEventListener('mouseup', onUp);
  });

}); // ← THIS was missing
</script>

<script>
const ROW_HEIGHT = 48; // must match grid-auto-rows

document.querySelectorAll('.event').forEach(eventEl => {
  let resizing = false;
  let resizeFrom = null;
  let startY;
  let startRowStart;
  let startRowEnd;

  eventEl.addEventListener('mousedown', e => {
    const rect = eventEl.getBoundingClientRect();
    const offsetY = e.clientY - rect.top;

    if (offsetY <= 8) resizeFrom = 'top';
    else if (offsetY >= rect.height - 8) resizeFrom = 'bottom';
    else return; // not resizing

    e.preventDefault();
    resizing = true;

    startY = e.clientY;

    const style = window.getComputedStyle(eventEl);
    startRowStart = parseInt(style.gridRowStart);
    startRowEnd   = parseInt(style.gridRowEnd);

    document.addEventListener('mousemove', onResize);
    document.addEventListener('mouseup', stopResize);
  });

  function onResize(e) {
    if (!resizing) return;

    const deltaY = e.clientY - startY;
    const rowDelta = Math.round(deltaY / ROW_HEIGHT);

    if (resizeFrom === 'bottom') {
      const newEnd = Math.max(startRowStart + 1, startRowEnd + rowDelta);
      eventEl.style.gridRowEnd = newEnd;
    }

    if (resizeFrom === 'top') {
      const newStart = Math.min(startRowEnd - 1, startRowStart + rowDelta);
      eventEl.style.gridRowStart = Math.max(2, newStart);
    }
  }

  function stopResize() {
    resizing = false;
    resizeFrom = null;

    document.removeEventListener('mousemove', onResize);
    document.removeEventListener('mouseup', stopResize);

    updateEventTime(eventEl);
  }
});
</script>



</body>
</html>
