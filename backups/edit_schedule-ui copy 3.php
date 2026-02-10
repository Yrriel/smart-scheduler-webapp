<?php
// edit_schedule-ui.php
$output = __DIR__ . '/output.json';
$edit   = __DIR__ . '/edit-output.json';

if (!file_exists($edit)) {
    copy($output, $edit);
}

copy($output, $edit);

$jsonFile = __DIR__ . '/edit-output.json';
if (!file_exists($jsonFile)) {
    die("❌ output.json not found. Please generate schedule first.");
}

$data = json_decode(file_get_contents($jsonFile), true);
if (!isset($data['schedules']) || !is_array($data['schedules'])) {
    die("❌ Invalid output.json format.");
}

$schedules = $data['schedules'];

$rooms = ['201','204','301','302','303','304','AVR','CHEMLAB','NSTP'];
$days  = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

$times = [];
$start = strtotime('7:00 AM');
$end   = strtotime('5:30 PM');
while ($start < $end) {
    $times[] = date('g:i A', $start);
    $start = strtotime('+30 minutes', $start);
}

function timeIndex($time, $times) {
    return array_search(date('g:i A', strtotime($time)), $times);
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

/* ===== LAYOUT ===== */
.admin-layout {
  display: flex;
  height: calc(100vh - 40px);
  gap: 16px;
}

.schedule-content {
  flex: 1;
  display: flex;
  flex-direction: column;
}

/* ===== SIDEBAR ===== */
.filter-sidebar {
  width: 260px;
  background: #fff;
  border-radius: 12px;
  padding: 16px;
  box-shadow: 0 4px 12px rgba(0,0,0,.08);
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.filter-sidebar label {
  font-size: 13px;
  font-weight: 600;
}

.filter-sidebar input,
.filter-sidebar select {
  width: 100%;
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #cbd5e1;
}

.clear-btn {
  margin-top: auto;
  background: #e5e7eb;
  border: none;
  padding: 10px;
  border-radius: 8px;
}

/* ===== SEARCH DROPDOWN ===== */
.search-dropdown { position: relative; }

.search-menu {
  position: absolute;
  top: 110%;
  left: 0;
  right: 0;
  background: #fff;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  max-height: 200px;
  overflow-y: auto;
  display: none;
  z-index: 100;
}

.search-menu.active { display: block; }

.search-item {
  padding: 8px 10px;
  cursor: pointer;
}
.search-item:hover { background: #f1f5f9; }

/* ===== GRID ===== */
.schedule-wrapper { overflow-x: auto; }

.schedule-grid {
  display: grid;
  grid-template-columns: 130px repeat(9, minmax(120px, 1fr));
  grid-template-rows: 48px repeat(<?= $timeRowCount ?>, 48px);
  background: #e5e7eb;
  border-radius: 12px;
}

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

.cell {
  background: #f8fafc;
  border: 1px solid #e5e7eb;
  pointer-events: none;
}

/* ===== EVENTS ===== */
.event {
  background: linear-gradient(135deg, #4f6ef7, #3b5bfd);
  color: #fff;
  border-radius: 8px;
  padding: 6px 8px;
  font-size: 12px;
  box-shadow: 0 6px 14px rgba(0,0,0,.15);
  cursor: grab;
  user-select: none;
  position: relative;
  pointer-events: auto;
  z-index: 5;
}

.event.hidden { display: none; }

.event.dragging {
  cursor: grabbing;
  z-index: 1000;
}

/* resize zones */
.event::before,
.event::after {
  content:"";
  position:absolute;
  left:0;
  width:100%;
  height:8px;
}
.event::before { top:0; cursor:ns-resize; }
.event::after { bottom:0; cursor:ns-resize; }
.event:hover::before,
.event:hover::after { background: rgba(255,255,255,.4); }

button {
  border: none;
  background: #304ffe;
  color: #fff;
  padding: 10px 16px;
  border-radius: 8px;
  cursor: pointer;
}
button.danger { background:#dc2626; }
/* ===== SIDEBAR VIEW MODES ===== */
.view-modes {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 14px;
}

.view-modes h4 {
  font-size: 12px;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  margin: 0 0 6px;
}

.view-btn {
  width: 100%;
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  background: #f8fafc;
  color: #0f172a;
  font-size: 14px;
  font-weight: 600;
  text-align: left;
  cursor: pointer;
  transition: all 0.15s ease;
}

.view-btn:hover {
  background: #eef2ff;
  border-color: #c7d2fe;
}

.view-btn.active {
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  color: #fff;
  border-color: transparent;
  box-shadow: 0 4px 12px rgba(37,99,235,.35);
}

/* ===== SIDEBAR VIEW TABS ===== */
.view-modes {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 14px;
}

.view-modes h4 {
  font-size: 12px;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  margin: 0 0 6px;
}

.view-tab {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
  background: #f8fafc;
  color: #0f172a;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all .15s ease;
}

.view-tab svg {
  width: 18px;
  height: 18px;
  opacity: 0.85;
}

.view-tab:hover {
  background: #eef2ff;
  border-color: #c7d2fe;
}

.view-tab.active {
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  color: #fff;
  border-color: transparent;
  box-shadow: 0 4px 12px rgba(37,99,235,.35);
}

.view-tab.active svg {
  opacity: 1;
}

a{
    text-decoration: none;
}


</style>
</head>

<body>
<div class="admin-layout">

<!-- SIDEBAR -->
<aside class="filter-sidebar">
    <div class="view-modes">
  <h4>View by</h4>

  <a href="?view=day" class="view-tab active" data-view="day">
    <svg viewBox="0 0 24 24" fill="currentColor">
      <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/>
    </svg>
    Day
</a>
    
  <a href="?view=faculty" class="view-tab" data-view="faculty">
    <svg viewBox="0 0 24 24" fill="currentColor">
      <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
    </svg>
    Faculty
</a>

  <a href="?view=section" class="view-tab" data-view="section">
    <svg viewBox="0 0 24 24" fill="currentColor">
      <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
    </svg>
    Section
</a>

  <a href="?view=room" class="view-tab" data-view="room">
    <svg viewBox="0 0 24 24" fill="currentColor">
      <path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/>
    </svg>
    Room
</a>
</div>

<hr class="sidebar-divider">


<hr style="border:none;border-top:1px solid #e5e7eb;margin:12px 0;">

  <label>Day</label>
  <select id="filterDay">
    <option value="">All</option>
    <?php foreach ($days as $d): ?><option><?= $d ?></option><?php endforeach; ?>
  </select>

  <label>Faculty</label>
  <div class="search-dropdown">
    <input id="filterFaculty" placeholder="Search faculty…" autocomplete="off">
    <div class="search-menu" id="facultyMenu"></div>
  </div>

  <label>Room</label>
  <select id="filterRoom">
    <option value="">All</option>
    <?php foreach ($rooms as $r): ?><option><?= $r ?></option><?php endforeach; ?>
  </select>

  <label>Section</label>
  <div class="search-dropdown">
    <input id="filterSection" placeholder="Search section…" autocomplete="off">
    <div class="search-menu" id="sectionMenu"></div>
  </div>

  <button class="clear-btn" id="clearFilters">Clear Filters</button>
  <button onclick="validateSchedules()">Validate</button>
  <a href="cancel_edit_json.php"
   onclick="return confirm('Discard all changes and exit edit mode?');">
  <button class="danger" type="button">Cancel Edit</button>
    </a>
  <a href="save_schedule.php">
    <button>Confirm & Save</button>
  </a>
</aside>

<!-- MAIN -->
<main class="schedule-content">
<h2>Edit Schedule</h2>

<div class="top-bar">
  <label>Day:
    <select id="dayFilter">
      <?php foreach ($days as $d): ?><option><?= $d ?></option><?php endforeach; ?>
    </select>
  </label>
  
</div>

<div class="schedule-wrapper">
<div class="schedule-grid">

<div class="grid-header time-header">Time</div>
<?php foreach ($rooms as $r): ?><div class="grid-header"><?= $r ?></div><?php endforeach; ?>

<?php foreach ($times as $i => $t): ?>
  <div class="time-col" style="grid-row:<?= $i+2 ?>"><?= $t ?></div>
  <?php foreach ($rooms as $r): ?><div class="cell" style="grid-row:<?= $i+2 ?>"></div><?php endforeach; ?>
<?php endforeach; ?>

<?php foreach ($schedules as $row):
  $c = array_search($row['room'],$rooms);
  $rs = timeIndex($row['time_start'],$times);
  $re = timeIndex($row['time_end'],$times);
  if ($c===false||$rs===false||$re===false) continue;
?>
<div class="event"
  data-id="<?= md5($row['section'].$row['subject'].$row['faculty']) ?>"
  data-section="<?= $row['section'] ?>"
  data-day="<?= $row['day'] ?>"
  data-room="<?= $row['room'] ?>"
  data-faculty="<?= $row['faculty'] ?>"
  data-start="<?= $row['time_start'] ?>"
  data-end="<?= $row['time_end'] ?>"
  style="grid-column:<?= $c+2 ?>;grid-row:<?= $rs+2 ?>/<?= $re+2 ?>">
  <strong><?= $row['subject'] ?></strong><br>
  <?= $row['section'] ?><br>
  <small><?= $row['faculty'] ?></small>
</div>
<?php endforeach; ?>

</div>
</div>
</main>
</div>

<script>
    
const ROOMS = <?= json_encode($rooms) ?>;
const TIMES = <?= json_encode($times) ?>;

function gridRowToTime(row) {
  const minutes = (row - 2) * 30 + (7 * 60);
  const h = Math.floor(minutes / 60);
  const m = minutes % 60;
  return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
}

function collectSchedulesFromDOM() {
  return [...document.querySelectorAll('.event')].map(card => {
    const style = getComputedStyle(card);

    return {
      id: card.dataset.id,
      subject: card.querySelector('strong')?.innerText || '',
      section: card.dataset.section,
      faculty: card.dataset.faculty,
      room: card.dataset.room,
      day: card.dataset.day,
      time_start: gridRowToTime(parseInt(style.gridRowStart)),
      time_end: gridRowToTime(parseInt(style.gridRowEnd))
    };
  });
}


async function liveSave() {
  await fetch('save_edit_live.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({
      schedules: collectSchedulesFromDOM()
    })
  });
}


const events = document.querySelectorAll('.event');
const daySelect = document.getElementById('dayFilter');

function filterByDay() {
  events.forEach(e =>
    e.classList.toggle('hidden', e.dataset.day !== daySelect.value)
  );
}
daySelect.onchange = filterByDay;
filterByDay();

function timeToMinutes(t) {
  const [h, m] = t.split(':').map(Number);
  return h * 60 + m;
}

function overlap(a, b, c, d) {
  return a < d && c < b;
}

async function validateSchedules() {
  try {
    // 🔥 Always read the LIVE JSON
    const res = await fetch('edit-output.json?_=' + Date.now());
    if (!res.ok) throw new Error('Cannot load edit-output.json');

    const data = await res.json();
    const schedules = data.schedules || [];

    let errors = [];

    for (let i = 0; i < schedules.length; i++) {
      for (let j = i + 1; j < schedules.length; j++) {

        const a = schedules[i];
        const b = schedules[j];

        // must be same day
        if (a.day !== b.day) continue;

        // must overlap in time
        if (!overlap(
          timeToMinutes(a.time_start),
          timeToMinutes(a.time_end),
          timeToMinutes(b.time_start),
          timeToMinutes(b.time_end)
        )) continue;

        if (a.room === b.room) {
          errors.push(`Room conflict (${a.room}) on ${a.day}`);
        }

        if (a.faculty === b.faculty) {
          errors.push(`Faculty conflict (${a.faculty}) on ${a.day}`);
        }

        if (a.section === b.section) {
          errors.push(`Section conflict (${a.section}) on ${a.day}`);
        }
      }
    }

    alert(
      errors.length
        ? `❌ Conflicts found:\n\n${[...new Set(errors)].join('\n')}`
        : `✅ No conflicts detected (edit-output.json)`
    );

  } catch (err) {
    alert('❌ Validation failed: ' + err.message);
  }
}


/* ===== DRAG ===== */

const CELL_HEIGHT = 48, COL_WIDTH = 160;

events.forEach(card => {
  let sx, sy, oc, or, span;

  card.onmousedown = e => {
    const r = card.getBoundingClientRect();
    const y = e.clientY - r.top;

    // ignore resize handles
    if (y <= 8 || y >= r.height - 8) return;

    e.preventDefault();

    sx = e.clientX;
    sy = e.clientY;

    const s = getComputedStyle(card);
    oc = parseInt(s.gridColumnStart);
    or = parseInt(s.gridRowStart);
    span = parseInt(s.gridRowEnd) - or;

    card.classList.add('dragging');

    document.onmousemove = ev => {
      card.style.transform =
        `translate(${ev.clientX - sx}px, ${ev.clientY - sy}px)`;
    };

    document.onmouseup = async ev => {
      card.classList.remove('dragging');
      card.style.transform = '';

      const newCol =
        Math.max(2, oc + Math.round((ev.clientX - sx) / COL_WIDTH));

      const newRow =
        Math.max(2, or + Math.round((ev.clientY - sy) / CELL_HEIGHT));

      card.style.gridColumnStart = newCol;
      card.style.gridRowStart = newRow;
      card.style.gridRowEnd = newRow + span;

      /* ✅ SYNC ROOM */
      const roomIndex = newCol - 2;
      if (ROOMS[roomIndex]) {
        card.dataset.room = ROOMS[roomIndex];
      }

      /* ✅ SYNC TIME */
      const startIndex = newRow - 2;
      const endIndex = startIndex + span;

      if (TIMES[startIndex] && TIMES[endIndex]) {
        card.dataset.start = TIMES[startIndex];
        card.dataset.end   = TIMES[endIndex];
      }

      document.onmousemove = document.onmouseup = null;

      await liveSave();
    };
  };
});

/* ===== RESIZE ===== */
events.forEach(card=>{
  let from,startY,rs,re;
  card.addEventListener('mousedown',e=>{
    const r=card.getBoundingClientRect();
    const y=e.clientY-r.top;
    if(y<=8)from='top';
    else if(y>=r.height-8)from='bottom';
    else return;
    e.preventDefault();
    startY=e.clientY;
    const s=getComputedStyle(card);
    rs=parseInt(s.gridRowStart);
    re=parseInt(s.gridRowEnd);
    document.onmousemove=ev=>{
      const d=Math.round((ev.clientY-startY)/CELL_HEIGHT);
      if(from==='bottom')card.style.gridRowEnd=Math.max(rs+1,re+d);
      else card.style.gridRowStart=Math.max(2,Math.min(re-1,rs+d));
    };
    document.onmouseup = async () => {
        document.onmousemove = document.onmouseup = null;
        await liveSave();
    };
  });
});
</script>
</body>
</html>
