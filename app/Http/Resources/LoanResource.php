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
            'id' => $this->id,
            'amount' => $this->loan_amount,
            'loanDuration' => $this->loan_duration,
            'uniqueLoanId' => $this->unique_loan_id,
            'interestRate' => $this->interest_rate,
            'monthlyInstallment' => $this->monthly_installment_with_company_fees,
            'loanMode' => $this->loan_mode,
            'modifiedMonthlyInstallment' => $this->monthly_installment_with_company_fees,
//            'users' => $this->users ? UserResource::collection($this->users) : null
        ];
    }
}
