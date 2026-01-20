<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AnalyzeAnimeData extends Command
{
    protected $signature = 'anime:analyze {year?}';
    protected $description = 'Analisa a distribuição dos animes importados';

    public function handle()
    {
        $year = $this->argument('year');
        
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('  ANÁLISE DE DADOS DOS ANIMES');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->newLine();

        // Query base
        $query = DB::table('animes')
            ->join('seasons', 'animes.season_id', '=', 'seasons.id');
        
        if ($year) {
            $query->where('seasons.year', $year);
            $this->info("Analisando ano: $year");
        } else {
            $this->info("Analisando todos os anos");
        }
        
        $this->newLine();

        // Total
        $total = $query->count();
        $this->info("Total de animes: $total");
        $this->newLine();

        // Por tipo de mídia
        $byType = DB::table('animes')
            ->join('seasons', 'animes.season_id', '=', 'seasons.id')
            ->select('animes.media_type', DB::raw('count(*) as total'))
            ->when($year, fn($q) => $q->where('seasons.year', $year))
            ->groupBy('animes.media_type')
            ->orderByDesc('total')
            ->get();

        $this->info('Por Tipo de Mídia:');
        foreach ($byType as $type) {
            $percentage = ($type->total / $total) * 100;
            $this->line(sprintf(
                "  %-15s %4d animes (%5.1f%%)",
                $type->media_type ?? 'unknown',
                $type->total,
                $percentage
            ));
        }
        $this->newLine();

        // Por status
        $byStatus = DB::table('animes')
            ->join('seasons', 'animes.season_id', '=', 'seasons.id')
            ->select('animes.status', DB::raw('count(*) as total'))
            ->when($year, fn($q) => $q->where('seasons.year', $year))
            ->groupBy('animes.status')
            ->orderByDesc('total')
            ->get();

        $this->info('Por Status:');
        foreach ($byStatus as $status) {
            $percentage = ($status->total / $total) * 100;
            $this->line(sprintf(
                "  %-20s %4d animes (%5.1f%%)",
                $status->status ?? 'unknown',
                $status->total,
                $percentage
            ));
        }
        $this->newLine();

        // Apenas TV
        $tvCount = DB::table('animes')
            ->join('seasons', 'animes.season_id', '=', 'seasons.id')
            ->where('animes.media_type', 'tv')
            ->when($year, fn($q) => $q->where('seasons.year', $year))
            ->count();

        $this->info("📺 Apenas séries TV: $tvCount animes");
        
        // TV + ONA (streaming)
        $streamingCount = DB::table('animes')
            ->join('seasons', 'animes.season_id', '=', 'seasons.id')
            ->whereIn('animes.media_type', ['tv', 'ona'])
            ->when($year, fn($q) => $q->where('seasons.year', $year))
            ->count();

        $this->info("📺 TV + ONA (streaming): $streamingCount animes");
        $this->newLine();

        return 0;
    }
}
