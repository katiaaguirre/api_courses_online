<?php

namespace App\Http\Resources\Repertorio\Opciones;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Repertorio\Opciones\RepertorioOpcionResource;

class RepertorioOpcionCollection extends ResourceCollection
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
            "data" => RepertorioOpcionResource::collection($this->collection)
        ];
    }
}
