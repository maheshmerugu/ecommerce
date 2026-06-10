<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Use the controller directly
$controller = new App\Http\Controllers\LocationController();

echo "States:\n";
echo $controller->states()->getContent() . "\n\n";

echo "Cities for Karnataka:\n";
$req = Illuminate\Http\Request::create('/','GET',['state'=>'Karnataka']);
echo $controller->cities($req)->getContent() . "\n\n";

echo "Pincodes for Bangalore (city name 'Bangalore'):\n";
$req2 = Illuminate\Http\Request::create('/','GET',['city'=>'Bangalore']);
echo $controller->pincodes($req2)->getContent() . "\n\n";
