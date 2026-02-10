<?php
$output = __DIR__ . '/output.json';
$edit   = __DIR__ . '/edit-output.json';

// Explicitly reset edit session to original output
if (file_exists($output)) {
    copy($output, $edit);
}

// Go back to schedule UI (no changes saved)
header("Location: ../../frontend/page_schedule/schedule-ui.php");
exit;
