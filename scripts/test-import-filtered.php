<?php

// Test import with filters
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\SeasonImportService;
use App\Services\MyAnimeListService;

echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "  TESTE DE IMPORTAÇÃO - WINTER 2025 (COM FILTROS)\n";
echo "═══════════════════════════════════════════════════════════\n\n";

$malService = new MyAnimeListService();
$importService = new SeasonImportService($malService);

echo "Importando Winter 2025...\n\n";

try {
    $stats = $importService->importSeason(2025, 'winter');
    
    echo "═══════════════════════════════════════════════════════════\n";
    echo "  RESULTADO\n";
    echo "═══════════════════════════════════════════════════════════\n\n";
    
    echo "Total retornado pela API: {$stats['total']}\n";
    echo "Importados: {$stats['imported']}\n";
    echo "Atualizados: {$stats['updated']}\n";
    echo "Pulados (music/pv/cm): {$stats['skipped']}\n";
    echo "Erros: {$stats['errors']}\n\n";
    
    $relevant = $stats['imported'] + $stats['updated'];
    $filtered = $stats['skipped'];
    
    echo "✓ Animes relevantes importados: $relevant\n";
    echo "✓ Animes filtrados (não relevantes): $filtered\n\n";
    
} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n\n";
    exit(1);
}
