<?php

namespace App\Http\Resources\Course\Category;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Course\Category\CategoryResource;

class CategoryCollection extends ResourceCollection 
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
            "data" => CategoryResource::collection($this->collection)
        ];
    }
}
