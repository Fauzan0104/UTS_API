<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "street" => $this->street,
            "city" => $this->city,
            "province" => $this->province,
            "country" => $this->country,
            "postal_code" => $this->postal_code,
        ];
    }
}
