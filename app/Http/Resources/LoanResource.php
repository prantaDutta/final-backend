<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'uniqueLoanId' => $this->unique_loan_id,
            'id' => $this->id,
            'amount' => $this->loan_amount,
            'loanDuration' => $this->loan_duration,
            'interestRate' => $this->interest_rate,
            'monthlyInstallment' => $this->monthly_installment_with_company_fees,
            'loanMode' => $this->loan_mode,
            'modifiedMonthlyInstallment' => $this->monthly_installment_with_company_fees,
        ];
    }
}
