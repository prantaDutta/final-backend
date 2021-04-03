<?php


namespace App\Library\Util;


class PenaltyData
{

    public function __construct(
        public int $day,
        public int $amount,
    ) {}

}
