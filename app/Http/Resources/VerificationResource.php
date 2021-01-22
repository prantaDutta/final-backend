<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VerificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        $date=date_create($this->date_of_birth);
        $date_of_birth = date_format($date,"d-F-Y");
        return [
            'dateOfBirth' => $date_of_birth,
            'gender' => $this->gender,
            'address' => $this->address,
            'mobileNo' => $this->mobile_no,
            'borrowerType' => $this->borrower_type ?: "N/A",
            'division' => $this->division,
            'zila' => $this->zila,
            'zipCode' => $this->zip_code,
            'verificationPhotos' => $this->verification_photos,
        ];
    }
}
