<?php
$path = __DIR__ . '/../storage/imports/india_pincodes.csv';
if (!file_exists($path)) { echo "File not found: $path\n"; exit(1); }
$h = fopen($path, 'r');
$header = fgetcsv($h);
if (!$header) { echo "Empty file or invalid CSV\n"; exit(1); }
$lower = array_map('strtolower', $header);
$pinIdx = null;
foreach ($lower as $i => $col) {
    if (strpos($col, 'pincode') !== false || strpos($col, 'pin') !== false) { $pinIdx = $i; break; }
}
if ($pinIdx === null) { echo "Could not locate pincode column\n"; exit(1); }
$unique = [];
$rows = 0;
while (($row = fgetcsv($h)) !== false) {
    $rows++;
    if (!isset($row[$pinIdx])) continue;
    $pin = preg_replace('/\D/', '', $row[$pinIdx]);
    if (strlen($pin) !== 6) continue;
    $unique[$pin] = true;
}
fclose($h);
$uniq = array_keys($unique);
sort($uniq);
echo "Rows processed: $rows\n";
echo "Unique valid pincodes: " . count($uniq) . "\n";
echo "Sample (first 20):\n" . implode(', ', array_slice($uniq, 0, 20)) . "\n";
