<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use function PHPUnit\Framework\isEmpty;

class AutoPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:auto-payments';

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
     * @return void
     */
    public function handle(): void
    {
        $users = User::where('role', 'borrower')
            ->where('verified', 'verified')
            ->whereHas('installments', function ($q) {
                $q->where('status', 'due');
            })
            ->whereHas('loan_preference', function ($q) {
                $q->where('auto_payments', 'yes');
            })
            ->get();

        foreach ($users as $user) {
            $balance = (int) $user->balance;
            $due_installments = $user->installments->where('status', 'due');

            if (count($due_installments) > 0) {
                foreach ($due_installments as $installment){
                    $amount = $installment->amount;
                    if ($amount <= $balance) {
                        $user->decrement('balance', $amount);
                        $installment->update([
                            'status' => 'paid'
                        ]);
                    }
                }
            }
        }
    }
}
