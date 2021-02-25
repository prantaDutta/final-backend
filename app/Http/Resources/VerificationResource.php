<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VerificationResource extends JsonResource
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
        $date_of_birth = null;
        if (!empty($this->date_of_birth) && $this->date_of_birth) {
            $date = date_create();
            $date_of_birth = date_format($date, "d-F-Y");
        }


        return [
            'dateOfBirth' => $date_of_birth,
            'gender' => $this->gender,
            'address' => $this->address,
            'borrowerType' => $this->borrower_type ?: "N/A",
            'division' => $this->division,
            'zila' => $this->zila,
            'zipCode' => $this->zip_code,
            'verificationPhotos' => $this->verification_photos,
        ];
    }
}
