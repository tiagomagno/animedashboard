<?php

namespace App\Console\Commands;

use App\Services\SeasonImportService;
use Illuminate\Console\Command;

class ImportYear extends Command
{
    protected $signature = 'anime:import-year {year}';
    protected $description = 'Importa todas as 4 temporadas de um ano';

    protected SeasonImportService $importService;

    public function __construct(SeasonImportService $importService)
    {
        parent::__construct();
        $this->importService = $importService;
    }

    public function handle()
    {
        $year = $this->argument('year');
        $seasons = ['winter', 'spring', 'summer', 'fall'];
        
        $this->info("Importando ano completo: $year");
        $this->newLine();

        $totalStats = [
            'total' => 0,
            'imported' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        foreach ($seasons as $season) {
            $this->info("Importando {$season} {$year}...");
            
            try {
                $stats = $this->importService->importSeason($year, $season);
                
                $this->line("  ✓ Total: {$stats['total']}");
                $this->line("  ✓ Importados: {$stats['imported']}");
                $this->line("  ✓ Atualizados: {$stats['updated']}");
                
                if ($stats['errors'] > 0) {
                    $this->warn("  ⚠ Erros: {$stats['errors']}");
                }
                
                // Acumular estatísticas
                foreach ($stats as $key => $value) {
                    $totalStats[$key] += $value;
                }
                
                $this->newLine();
                
                // Delay entre temporadas
                sleep(1);
                
            } catch (\Exception $e) {
                $this->error("  ✗ Erro ao importar {$season}: " . $e->getMessage());
                $this->newLine();
            }
        }

        $this->info("═══════════════════════════════════════════════════════════");
        $this->info("RESUMO DA IMPORTAÇÃO DO ANO {$year}");
        $this->info("═══════════════════════════════════════════════════════════");
        $this->line("Total de animes: {$totalStats['total']}");
        $this->line("Novos: {$totalStats['imported']}");
        $this->line("Atualizados: {$totalStats['updated']}");
        $this->line("Ignorados: {$totalStats['skipped']}");
        
        if ($totalStats['errors'] > 0) {
            $this->warn("Erros: {$totalStats['errors']}");
        }
        
        $this->newLine();
        $this->info("✓ Importação do ano {$year} concluída!");

        return 0;
    }
}
