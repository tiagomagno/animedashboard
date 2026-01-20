<?php

namespace App\Console\Commands;

use App\Models\Season;
use App\Services\SeasonImportService;
use Illuminate\Console\Command;

class UpdateSeasonStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anime:update-stats {season_id? : ID da temporada (opcional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza as estatísticas dos animes de uma temporada';

    protected SeasonImportService $importService;

    public function __construct(SeasonImportService $importService)
    {
        parent::__construct();
        $this->importService = $importService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $seasonId = $this->argument('season_id');

        if ($seasonId) {
            $season = Season::findOrFail($seasonId);
            $this->updateSeason($season);
        } else {
            // Update all active seasons
            $seasons = Season::active()->get();
            
            if ($seasons->isEmpty()) {
                $this->error('Nenhuma temporada ativa encontrada.');
                return 1;
            }

            $this->info("Atualizando {$seasons->count()} temporada(s)...");

            foreach ($seasons as $season) {
                $this->updateSeason($season);
            }
        }

        $this->info('✓ Atualização concluída!');
        return 0;
    }

    protected function updateSeason(Season $season): void
    {
        $this->info("Atualizando: {$season->display_name}");

        $stats = $this->importService->updateSeasonStats($season);

        $this->line("  • Total: {$stats['total']}");
        $this->line("  • Atualizados: {$stats['updated']}");
        
        if ($stats['errors'] > 0) {
            $this->warn("  • Erros: {$stats['errors']}");
        }
    }
}
