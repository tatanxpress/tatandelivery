<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class VerificarOrdenes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ordenes:verificar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar ordenes de estad5 == 1 y estado6 == 0';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info("Metodo disparado");
    }
}
