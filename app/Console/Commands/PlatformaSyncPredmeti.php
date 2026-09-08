<?php

namespace App\Console\Commands;

use App\Services\Platforma\PlatformaException;
use App\Services\Platforma\PlatformaPredmetSync;
use Illuminate\Console\Command;

class PlatformaSyncPredmeti extends Command
{
    protected $signature = 'platforma:sync-predmeti {--akademska-godina= : ID akademske godine na platformi (podrazumijevano poslednja)}
                            {--fakultet= : Lokalni ID fakulteta (podrazumijevano svi povezani)}';

    protected $description = 'Sinhronizuje matične predmete sa studentske platforme u lokalnu tabelu predmeti';

    public function handle(PlatformaPredmetSync $sync): int
    {
        try {
            $samo = $this->option('fakultet') ? \App\Models\Fakultet::findOrFail((int) $this->option('fakultet')) : null;
            $r = $sync->sinhronizuj($this->option('akademska-godina') ? (int) $this->option('akademska-godina') : null, $samo);
        } catch (PlatformaException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('Fakulteti: ' . implode(', ', $r['fakulteti']) . ". Akademska godina {$r['akademska_godina_id']}: kreirano {$r['kreirano']}, ažurirano {$r['azurirano']}.");

        return self::SUCCESS;
    }
}
