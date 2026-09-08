<?php

namespace App\Services\Platforma;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * HTTP klijent za integracioni API platforme (/api/integracija).
 */
class PlatformaClient
{
    public function enabled(): bool
    {
        return (bool) config('platforma.enabled');
    }

    // ---------------------------------------------------------------- GET

    public function ping(): array
    {
        return $this->get('/ping', [], 0);
    }

    public function sifarnici(): array
    {
        return $this->get('/sifarnici', [], 3600);
    }

    /** @return array<int, array> */
    public function pretragaStudenata(string $q): array
    {
        return $this->get('/studenti', ['q' => $q]);
    }

    public function student(int $platformaStudentId): array
    {
        return $this->get("/studenti/{$platformaStudentId}");
    }

    /** @return array<int, array> */
    public function polozeniPredmeti(int $platformaStudentId, ?int $upisId = null, ?int $nivoStudijaId = null): array
    {
        return $this->get("/studenti/{$platformaStudentId}/polozeni-predmeti", array_filter([
            'upis_id' => $upisId,
            'nivo_studija_id' => $nivoStudijaId,
        ]));
    }

    /** @return array{akademska_godina_id:int, predmeti:array} */
    public function predmetiFakulteta(int $fakultetId, ?int $nivoStudijaId = null, ?int $akademskaGodinaId = null): array
    {
        return $this->get("/fakulteti/{$fakultetId}/predmeti", array_filter([
            'nivo_studija_id' => $nivoStudijaId,
            'akademska_godina_id' => $akademskaGodinaId,
        ]), 300);
    }

    /** @return array<int, array> */
    public function predavaci(?int $fakultetId = null): array
    {
        return $this->get('/predavaci', array_filter(['fakultet_id' => $fakultetId]), 300);
    }

    // --------------------------------------------------------------- POST

    /**
     * @param array<int, array{predmet_fakultet_semestar_id:int, ocjena:string}> $predmeti
     */
    public function priznajIspite(int $platformaStudentId, int $upisId, string $izvor, array $predmeti, array $meta = []): array
    {
        $payload = $meta + [
            'upis_id' => $upisId,
            'izvor' => $izvor,
            'predmeti' => array_values($predmeti),
        ];

        return $this->send(fn () => $this->request()->post($this->url("/studenti/{$platformaStudentId}/priznati-ispiti"), $payload));
    }

    // ------------------------------------------------------------ interno

    private function get(string $path, array $query = [], ?int $ttl = null): array
    {
        $ttl ??= (int) config('platforma.cache_ttl');
        $key = 'platforma:' . md5($path . '?' . http_build_query($query));

        $call = fn () => $this->send(fn () => $this->request()->get($this->url($path), $query));

        if ($ttl <= 0) {
            return $call();
        }

        return Cache::remember($key, $ttl, $call);
    }

    private function request(): PendingRequest
    {
        if (!$this->enabled()) {
            throw new PlatformaException('Integracija sa platformom nije konfigurisana (PLATFORMA_URL / PLATFORMA_TOKEN).');
        }

        return Http::withToken((string) config('platforma.token'))
            ->acceptJson()
            ->timeout((int) config('platforma.timeout'))
            ->retry(2, 200, throw: false);
    }

    private function url(string $path): string
    {
        return config('platforma.url') . '/api/integracija' . $path;
    }

    private function send(callable $call): array
    {
        try {
            $response = $call();
        } catch (ConnectionException $e) {
            throw new PlatformaException('Platforma nije dostupna: ' . $e->getMessage());
        }

        return $this->handle($response);
    }

    private function handle(Response $response): array
    {
        if ($response->successful()) {
            return (array) $response->json();
        }

        $body = (array) ($response->json() ?? []);
        $status = $response->status();

        $poruka = match (true) {
            $status === 401 => 'Platforma je odbila token (401). Provjeri PLATFORMA_TOKEN.',
            $status === 403 => 'Token nema pravo pristupa integraciji (403).',
            $status === 404 => $body['message'] ?? 'Zapis nije pronađen na platformi (404).',
            $status === 422 => $body['message'] ?? 'Platforma je odbila podatke (422).',
            $status === 429 => 'Previše zahtjeva prema platformi (429).',
            default => $body['message'] ?? "Greška platforme (HTTP {$status}).",
        };

        throw new PlatformaException($poruka, $status, $body);
    }
}
