<?php

namespace App\Listeners;

use App\Library\LoanDistribution\LoanDistributor;
use Illuminate\Contracts\Queue\ShouldQueue;

class DistributeNewLoanListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(object $event): void
    {
        $loan_distributor = new LoanDistributor(
            $event->user,
            $event->amount,
            $event->unique_loan_id,
        );

        $loan_distributor->distribute();
    }
}
