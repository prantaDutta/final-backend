<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanPreferenceResource extends JsonResource
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
        $distributed_array = explode(', ', $this->distributed_amounts);
//        foreach ($distributed_array as $key => $value) {
//            if (empty($value)) {
//                unset($distributed_array[$key]);
//            }
//        }
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'distributedArray' => $distributed_array,
            'latestDepositedAmount' => $this->latest_deposited_amount
        ];
    }
}
