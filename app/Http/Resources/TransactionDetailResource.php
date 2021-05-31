<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionDetailResource extends JsonResource
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
            'card Type' => $this->card_type ?? null,
            'card No' => $this->card_no ?? null,
            'bank TranId' => $this->bank_tran_id ?? null,
//            'error' => $this->error,
            'card Issuer' => $this->card_issuer ?? null,
            'card Brand' => $this->card_brand ?? null,
        ];
    }
}
