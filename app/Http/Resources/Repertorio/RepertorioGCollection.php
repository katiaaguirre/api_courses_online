<?php

namespace App\Http\Resources\Repertorio;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RepertorioGCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "data" => RepertorioGResource::collection($this->collection)
        ];
    }
}
