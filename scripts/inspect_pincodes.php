<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Pincode;
use App\Models\State;
use App\Models\City;

echo "Pincode count: " . Pincode::count() . PHP_EOL;
echo "State count: " . State::count() . PHP_EOL;
echo "City count: " . City::count() . PHP_EOL;

$rows = Pincode::limit(30)->get(['id','city_id','pincode','area'])->toArray();
echo json_encode($rows, JSON_PRETTY_PRINT) . PHP_EOL;
