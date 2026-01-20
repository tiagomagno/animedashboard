<?php

use App\Models\Anime;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Verificando Ratings de 2025:\n";
$stats = Anime::whereHas('season', fn($q) => $q->where('year', 2025))
    ->select('rating', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
    ->groupBy('rating')
    ->get();

foreach ($stats as $stat) {
    echo "Rating: " . ($stat->rating ?? 'NULL') . " - Total: " . $stat->total . "\n";
}
