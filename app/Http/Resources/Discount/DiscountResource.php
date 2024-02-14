<?php

namespace App\Http\Resources\Discount;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
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
            "code" => $this->resource->code,
            "type_discount" => $this->resource->type_discount,
            "discount" => $this->resource->discount,
            "start_date" => Carbon::parse($this->resource->start_date)->format("Y-m-d"),
            "end_date" => Carbon::parse($this->resource->end_date)->format("Y-m-d"),
            "discount_type" => $this->resource->discount_type,
            "type_campaign" => $this->resource->type_campaign,
            "state" => $this->resource->state ?? 1,
            "courses" => $this->resource->courses->map(function($course_aux){
                return [
                    "id" => $course_aux->course->id,
                    "title" => $course_aux->course->title,
                    "image" => env("APP_URL")."storage/".$course_aux->course->image,
                    "aux_id" => $course_aux->id
                ];
            }),
            "categories" => $this->resource->categories->map(function($category_aux){
                return [
                    "id" => $category_aux->category->id,
                    "name" => $category_aux->category->name,
                    "image" => env("APP_URL")."storage/".$category_aux->category->image,
                    "aux_id" => $category_aux->id
                ];
            })
        ];
    }
}
