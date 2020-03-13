<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InstallPassport extends Command
{
    /**
     * Nom et signature de la commande.
     *
     * @var string
     */
    protected $signature = 'docker:passport';

    /**
     * Description de la commande.
     *
     * @var string
     */
    protected $description = 'Install passport and generate oauth clients if there are none.';

    public function handle()
    {
        $create = false;
        if (Schema::hasTable('oauth_clients')) {
            $create = DB::table('oauth_clients')
                    ->count() < 2;
        } else {
            $create = true;
        }

        if ($create) {
            $this->info("Installation of passport.");
            Artisan::call('passport:install');
        } else {
            $this->info("Passport already installed.");
        }
    }
}
