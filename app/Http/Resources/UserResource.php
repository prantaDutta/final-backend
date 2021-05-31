<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $mobileNoVerifiedAt = false;
        if ($this->mobile_no_verified_at !== null) {
            $mobileNoVerifiedAt = true;
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'mobileNo' => substr((string)$this->mobile_no, 3),
            'mobileNo Verified' => $mobileNoVerifiedAt,
            'role' => $this->role,
            'balance' => $this->balance,
            'verified' => $this->verified,
        ];
    }
}
