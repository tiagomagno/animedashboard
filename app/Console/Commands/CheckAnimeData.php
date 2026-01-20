<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckAnimeData extends Command
{
    protected $signature = 'anime:check';
    protected $description = 'Verifica os dados dos animes importados';

    public function handle()
    {
        $this->info('Verificando dados dos animes...');
        $this->newLine();

        // Total de animes
        $total = DB::table('animes')->count();
        $this->info("Total de animes: $total");
        $this->newLine();

        // Animes por temporada
        $seasons = DB::table('animes')
            ->join('seasons', 'animes.season_id', '=', 'seasons.id')
            ->select('seasons.year', 'seasons.season', DB::raw('count(*) as total'))
            ->groupBy('seasons.year', 'seasons.season')
            ->get();

        $this->info('Animes por temporada:');
        foreach ($seasons as $season) {
            $this->line("  {$season->year} {$season->season}: {$season->total} animes");
        }
        $this->newLine();

        // Primeiros 10 animes
        $animes = DB::table('animes')
            ->join('seasons', 'animes.season_id', '=', 'seasons.id')
            ->select('animes.id', 'animes.title', 'animes.mal_id', 'seasons.year', 'seasons.season')
            ->limit(10)
            ->get();

        $this->info('Primeiros 10 animes:');
        foreach ($animes as $anime) {
            $this->line("  {$anime->id}. {$anime->title} ({$anime->year} {$anime->season}) - MAL ID: {$anime->mal_id}");
        }

        return 0;
    }
}
