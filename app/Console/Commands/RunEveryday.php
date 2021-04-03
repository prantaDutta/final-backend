<?php

namespace App\Console\Commands;

use App\Models\Installment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RunEveryday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:run-everyday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command will run everyday';

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
//        DB::table('utils')
//            ->where('updated_at', '<', now()->subDays(3))
//            ->update([
//                'loan_limit' => 0,
//            ]);
//        DB::table('utils')
//            ->where('updated_at', '<', now()->subHours(2))
//            ->update([
//                'loan_limit' => 0,
//            ]);
        $admin = User::find(1);
        $penalty_data = $admin->administration->penalty_data;

        Installment::where('status', 'unpaid')
            ->whereDate('due_date', '<=', today())
            ->update([
                'status' => 'due',
            ]);

        $installments = Installment::where('status', 'due')
            ->whereDate('due_date', '<=', today())
            ->get();

        $penalty = 0;

        foreach ($installments as $installment) {
            $due_days = Carbon::parse($installment->due_date)->diffInDays();
            $due_months = Carbon::parse($installment->due_date)->diffInMonths();
            if ($due_months > 0) {
                $penalty += $due_months * (int)($installment->amount / 2);
            }

            if ($due_days >= 31) {
                $due_days = 31;
            }
            foreach ($penalty_data as $penalty_datum) {
                if ($penalty_datum['day'] === $due_days) {
                    $penalty += (int)$penalty_datum['amount'];
                    break;
                }
            }

            $amount = $installment->amount;

            $installment->update([
                'penalty_amount' => $penalty,
                'total_amount' => $penalty + $amount,
            ]);

        }
    }
}
