<?php

namespace App\Http\Resources\Course\Coupon;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
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
            "type_count" => $this->resource->type_count,
            "num_use" => $this->resource->num_use,
            "type_coupon" => $this->resource->type_coupon,
            "courses" => $this->resource->courses->map(function($course_aux){
                return [
                    "id" => $course_aux->course->id,
                    "title" => $course_aux->course->title,
                    "image" => env("APP_ENV")."storage/".$course_aux->course->image,
                    "aux_id" => $course_aux->id
                ];
            }),
            "categories" => $this->resource->categories->map(function($category_aux){
                return [
                    "id" => $category_aux->category->id,
                    "name" => $category_aux->category->name,
                    "image" => env("APP_ENV")."storage/".$course_aux->category->image,
                    "aux_id" => $category_aux->id
                ];
            })
        ];
    }
}
