<?php


namespace App\Library\LoanDistribution;


class LenderData
{

    public function __construct(
        public int $lender_id,
        public int $amount
    ) {}

}
