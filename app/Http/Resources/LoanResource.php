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
            'uniqueLoan Id' => $this->unique_loan_id,
            'id' => $this->id,
            'amount' => $this->loan_amount,
            'loan Duration (Months)' => $this->loan_duration,
            'interest Rate (%)' => $this->interest_rate,
            'monthly Installment' => $this->monthly_installment_with_company_fees,
            'loan Mode' => $this->loan_mode,
//            'modifiedMonthlyInstallment' => $this->monthly_installment_with_company_fees,
        ];
    }
}
