<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestMalApi extends Command
{
    protected $signature = 'mal:test';
    protected $description = 'Testa a conexão com a API do MyAnimeList';

    public function handle()
    {
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('  TESTE DA API MYANIMELIST');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->newLine();

        // Verificar Client ID
        $clientId = config('mal.client_id');
        
        if (!$clientId || $clientId === 'your_client_id_here') {
            $this->error('✗ Client ID não configurado!');
            $this->warn('  Configure MAL_CLIENT_ID no arquivo .env');
            return 1;
        }

        $this->info('✓ Client ID configurado: ' . substr($clientId, 0, 10) . '...');
        $this->newLine();

        // Testar conexão básica
        $this->info('Testando conexão com a API...');
        
        try {
            $response = Http::withHeaders([
                'X-MAL-CLIENT-ID' => $clientId,
            ])->get('https://api.myanimelist.net/v2/anime/1', [
                'fields' => 'id,title'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->info('✓ Conexão estabelecida com sucesso!');
                $this->line('  Anime de teste: ' . ($data['title'] ?? 'N/A'));
                $this->newLine();
            } else {
                $this->error('✗ Falha na conexão: HTTP ' . $response->status());
                $this->line('  Response: ' . $response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('✗ Erro ao conectar: ' . $e->getMessage());
            return 1;
        }

        // Testar endpoint de temporada
        $this->info('Testando endpoint de temporada (Winter 2024)...');
        
        try {
            $response = Http::withHeaders([
                'X-MAL-CLIENT-ID' => $clientId,
            ])->get('https://api.myanimelist.net/v2/anime/season/2024/winter', [
                'fields' => 'id,title,mean',
                'limit' => 3
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->info('✓ Endpoint de temporada funcionando!');
                $this->line('  Animes encontrados: ' . count($data['data']));
                $this->newLine();

                if (isset($data['data']) && count($data['data']) > 0) {
                    $this->line('Primeiros 3 animes:');
                    foreach ($data['data'] as $anime) {
                        $node = $anime['node'];
                        $this->line('  • ' . $node['title'] . ' (Score: ' . ($node['mean'] ?? 'N/A') . ')');
                    }
                }

                $this->newLine();
                $this->info('╔════════════════════════════════════════════════════════╗');
                $this->info('║  ✓ VALIDAÇÃO CONCLUÍDA COM SUCESSO!                   ║');
                $this->info('║  Você pode começar a importar temporadas!             ║');
                $this->info('╚════════════════════════════════════════════════════════╝');
                $this->newLine();

                return 0;
            } else {
                $this->error('✗ Erro ao buscar temporada: HTTP ' . $response->status());
                $this->line('  Response: ' . $response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('✗ Erro ao buscar temporada: ' . $e->getMessage());
            return 1;
        }
    }
}
