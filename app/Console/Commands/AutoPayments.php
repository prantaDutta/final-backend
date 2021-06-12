<?php

namespace App\Console\Commands;

use App\Models\Installment;
use App\Models\User;
use App\Notifications\InstallmentPaidAutomaticallyNotification;
use App\Notifications\InstallmentPaidNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
            $balance = (int)$user->balance;
            $due_installments = $user->installments->where('status', 'due');

            if (count($due_installments) > 0) {
                foreach ($due_installments as $installment) {
                    $amount = $installment->amount;
                    if ($amount <= $balance) {
                        // decrementing borrower balance
                        $user->decrement('balance', $amount);

                        // making the installment paid
                        $installment->update([
                            'status' => 'paid'
                        ]);

                        // Sending Notifications to the borrower
                        $user->notify(new InstallmentPaidAutomaticallyNotification($installment->id));

                        // finding the current loan
                        $current_loan = $installment->loan;

                        // finding every lender
                        foreach ($current_loan->lender_data as $lender_datum) {
                            // finding the lender installment row
                            $lender_installment = Installment::where('loan_id', $current_loan->id)
                                ->where('installment_no', $installment->installment_no)
                                ->whereHas('user', function ($q) use ($lender_datum) {
                                    $q->where('id', $lender_datum['lender_id']);
                                })
                                ->first();

                            if ($lender_installment === null) {
                                return;
                            }

                            // incrementing the lender balance
                            DB::table('users')
                                ->where('id', $lender_installment->user_id)
                                ->increment('balance', $lender_installment->total_amount);

                            // marking the installment as paid
                            $lender_installment->update([
                                'status' => 'paid',
                            ]);

                            // Sending Notifications to the lender
                            $user = $lender_installment->user;
                            $user->notify(new InstallmentPaidNotification($lender_installment->id));
                        }
                    }
                }
            }
        }
    }
}
