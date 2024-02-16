<?php

namespace App\Http\Resources\Ecommerce\Cart;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            "user_id" => $this->resource->user_id,
            "course_id" => $this->resource->course_id,
            "course" => [
                "title" => $this->resource->course->title,
                "image" => env("APP_URL")."storage/".$this->resource->course->image,
                "subtitle" => $this->resource->course->subtitle
            ],
            "type_discount" => $this->resource->type_discount,
            "discount" => $this->resource->discount,
            "type_campaign" => $this->resource->type_campaign,
            "coupon_code" => $this->resource->coupon_code,
            "discount_code" => $this->resource->discount_code,
            "precio_unitario" => $this->resource->precio_unitario,
            "total" => $this->resource->total
        ];
    }
}
