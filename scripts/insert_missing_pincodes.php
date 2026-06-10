<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pincode;
use Illuminate\Support\Facades\DB;

$path = __DIR__ . '/../storage/imports/india_pincodes.csv';
if (!file_exists($path)) { echo "File not found: $path\n"; exit(1); }
$h = fopen($path, 'r');
$header = fgetcsv($h);
$lower = array_map('strtolower', $header);
$pinIdx = null;
foreach ($lower as $i => $col) {
    if (strpos($col, 'pincode') !== false || strpos($col, 'pin') !== false) { $pinIdx = $i; break; }
}
if ($pinIdx === null) { echo "Could not locate pincode column\n"; exit(1); }
$csvPins = [];
while (($row = fgetcsv($h)) !== false) {
    if (!isset($row[$pinIdx])) continue;
    $pin = preg_replace('/\D/', '', $row[$pinIdx]);
    if (strlen($pin) !== 6) continue;
    $csvPins[$pin] = true;
}
fclose($h);
$csvPins = array_keys($csvPins);
sort($csvPins);

$dbPins = Pincode::pluck('pincode')->toArray();
$dbPinsMap = array_flip($dbPins);

$missing = array_diff($csvPins, $dbPins);

if (empty($missing)) {
    echo "No missing pincodes. DB already up to date.\n";
    exit(0);
}

$insertData = [];
$now = date('Y-m-d H:i:s');
foreach ($missing as $pin) {
    $insertData[] = [
        'city_id' => null,
        'pincode' => $pin,
        'area' => null,
        'created_at' => $now,
        'updated_at' => $now,
    ];
}

$chunks = array_chunk($insertData, 1000);
$inserted = 0;
foreach ($chunks as $chunk) {
    DB::table('pincodes')->insert($chunk);
    $inserted += count($chunk);
    echo "Inserted {$inserted}...\n";
}

echo "Done. Inserted {$inserted} missing pincodes.\n";
