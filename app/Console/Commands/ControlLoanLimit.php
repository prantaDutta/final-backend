<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ControlLoanLimit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:control-loan-limit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle(): void
    {
        DB::table('utils')
            ->where('updated_at', '<', now()->subDays(3))
            ->update([
                'loan_limit' => 0,
            ]);
//        DB::table('utils')
//            ->where('updated_at', '<', now()->subHours(2))
//            ->update([
//                'loan_limit' => 0,
//            ]);
    }
}
