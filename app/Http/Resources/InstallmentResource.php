<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstallmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
//            'loanId' => $this->loan_id,
            'id' => $this->id,
            'uniqueInstallmentId' => $this->unique_installment_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'penaltyAmount' => $this->penalty_amount,
            'totalAmount' => $this->total_amount,
            'dueDate' => $this->due_date,
            'installmentNo' => $this->installment_no,
        ];
    }
}
