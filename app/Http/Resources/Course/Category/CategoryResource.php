<?php

namespace App\Http\Resources\Course\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->resource->id,
            "name" => $this->resource->name,
            "image" => $this->resource->image ? env("APP_URL")."storage/".$this->resource->image : NULL,
            "category_id" => $this->resource->category_id,
            "category" => $this->resource->father ? [
                "name" => $this->resource->father->name,
                "image" => $this->resource->father->image ? env("APP_URL")."storage/".$this->resource->father->image : NULL
            ] : NULL,
            "state" => $this->resource->state ?? 1
        ];
    }
}
