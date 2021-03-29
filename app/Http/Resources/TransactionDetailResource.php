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
            'cardType' => $this->card_type ?? null,
            'cardNo' => $this->card_no ?? null,
            'bankTranId' => $this->bank_tran_id ?? null,
//            'error' => $this->error,
            'cardIssuer' => $this->card_issuer ?? null,
            'cardBrand' => $this->card_brand ?? null,
        ];
    }
}
