<?php

namespace App\Library\DistributedLoans;

class DistributedBorrowing
{
    private $amount;

    public function __construct($amount)
    {
        $this->amount = $amount;
    }

    /**
     * Checks whether we can distribute the amount
     * @return bool
     */
    public function isDistributable()
    {
        return $this->amount >= 2000;
    }

    public function distribute()
    {
        if ($this->amount) {
            
        }
    }
}
