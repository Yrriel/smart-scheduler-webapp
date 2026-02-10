<?php
$edit   = __DIR__ . '/edit-output.json';
$output = __DIR__ . '/output.json';

if (!file_exists($edit)) {
    die("No edit file to commit.");
}

copy($edit, $output);
unlink($edit); // optional: cleanup

header("Location: save_schedule.php");
exit;
