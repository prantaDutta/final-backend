<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
        $created_at = Carbon::parse($this->created_at);
        $created_at = $created_at->format('d M,Y h:i A');
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'amount' => $this->amount,
            'status' => $this->status,
            'address' => $this->address,
            'transactionId' => $this->transaction_id,
            'transactionType' => $this->transaction_type,
            'currency' => $this->currency,
            'createdAt' => $created_at,
        ];
    }
}
