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
            'cardType' => $this->card_type,
            'cardNo' => $this->card_no,
            'bankTranId' => $this->bank_tran_id,
//            'error' => $this->error,
            'cardIssuer' => $this->card_issuer,
            'cardBrand' => $this->card_brand,
        ];
    }
}
