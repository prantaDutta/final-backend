<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
            'id' => $this->id,
            'type' => $this->type,
            'notifiableType' => $this->notifiable_type,
            'notifiableId' => $this->notifiable_id,
            'data' => $this->data,
            'readAt' => $this->read_at,
            'updatedAt' => $this->updated_at
        ];
    }
}
