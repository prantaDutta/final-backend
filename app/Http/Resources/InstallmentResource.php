<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'unique Installment Id' => $this->unique_installment_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'penalty Amount' => $this->penalty_amount,
            'total Amount' => $this->total_amount,
            'due Date' => Carbon::parse($this->due_date)->format("d M Y"),
            'installment No' => $this->installment_no,
        ];
    }
}
