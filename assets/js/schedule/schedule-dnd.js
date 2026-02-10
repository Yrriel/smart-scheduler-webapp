/* =========================
   SCHEDULE DRAG + RESIZE
========================= */

function getColumnFromMouseX(x) {
  const grid = document.querySelector('.schedule-grid');
  const headers = grid.querySelectorAll('.grid-header:not(.time-header)');
  if (!headers.length) return 2;

  for (let i = 0; i < headers.length; i++) {
    const rect = headers[i].getBoundingClientRect();
    if (x >= rect.left && x < rect.right) {
      return i + 2; // +2 because column 1 is time column
    }
  }

  // clamp fallback
  if (x < headers[0].getBoundingClientRect().left) return 2;
  return headers.length + 1;
}




(function () {

  const CELL_HEIGHT = 48;

  const events = document.querySelectorAll('.event');

  if (!events.length) return;

  /* ===== DRAG ===== */

  events.forEach(card => {
    let sx, sy, oc, or, span;

    card.addEventListener('mousedown', e => {
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

        const newCol = getColumnFromMouseX(ev.clientX);
        const newRow =
          Math.max(2, or + Math.round((ev.clientY - sy) / CELL_HEIGHT));

        card.style.gridColumnStart = newCol;
        card.style.gridRowStart = newRow;
        card.style.gridRowEnd = newRow + span;

        /* sync room */
        // const roomIndex = newCol - 2;
        // if (window.ROOMS?.[roomIndex]) {
        //   card.dataset.room = window.ROOMS[roomIndex];
        // }

        /* sync column meaning */
        const colIndex = newCol - 2;
        const colValue = window.COLUMNS?.[colIndex];

        if (colValue) {
          if (window.VIEW_MODE === 'day') {
            // columns = rooms
            card.dataset.room = colValue;
          } else {
            // columns = days
            card.dataset.day = colValue;
          }
        }


        /* sync time */
        const startIndex = newRow - 2;
        const endIndex   = startIndex + span;

        // if (window.TIMES?.[startIndex] && window.TIMES?.[endIndex]) {
        //   card.dataset.start = window.TIMES[startIndex];
        //   card.dataset.end   = window.TIMES[endIndex];
        // }
        if (window.TIMES?.[startIndex] && window.TIMES?.[startIndex + span - 1]) {
          card.dataset.start = window.TIMES[startIndex].start;
          card.dataset.end   = window.TIMES[startIndex + span - 1].end;
        }

        if (!card.dataset.start || !card.dataset.end) {
          console.warn('Invalid time sync', card.dataset);
        }



        document.onmousemove = document.onmouseup = null;

        if (typeof window.liveSave === 'function') {
          await window.liveSave();
        }
      };
    });
  });

  /* ===== RESIZE ===== */

  events.forEach(card => {
    let from, startY, rs, re;

    card.addEventListener('mousedown', e => {
      const r = card.getBoundingClientRect();
      const y = e.clientY - r.top;

      if (y <= 8) from = 'top';
      else if (y >= r.height - 8) from = 'bottom';
      else return;

      e.preventDefault();
      startY = e.clientY;

      const s = getComputedStyle(card);
      rs = parseInt(s.gridRowStart);
      re = parseInt(s.gridRowEnd);

      document.onmousemove = ev => {
        const d = Math.round((ev.clientY - startY) / CELL_HEIGHT);

        if (from === 'bottom') {
          card.style.gridRowEnd = Math.max(rs + 1, re + d);
        } else {
          card.style.gridRowStart =
            Math.max(2, Math.min(re - 1, rs + d));
        }
      };

      document.onmouseup = async () => {
        document.onmousemove = document.onmouseup = null;

        if (typeof window.liveSave === 'function') {
          await window.liveSave();
        }
      };
    });
  });

})();
