<?php
$log = __DIR__ . '/generation_debug.log';

if (!file_exists($log)) {
    echo "Waiting for logs...\n";
    exit;
}

echo file_get_contents($log);
