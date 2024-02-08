<?php

namespace App\Http\Resources\Course\Clases;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Course\Clases\CourseClaseResource;

class CourseClaseCollection extends ResourceCollection
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
            "data" => CourseClaseResource::collection($this->collection),
            
        ];
    }
}
