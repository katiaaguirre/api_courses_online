<?php

namespace App\Http\Resources\Ecommerce\Sale;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            "payment_method" => $this->resource->payment_method,
            "currency_payment" => $this->resource->currency_payment,
            "total" => $this->resource->total,
            "n_transaccion" => $this->resource->n_transaccion,
            "sale_details" => $this->resource->sale_details->map(function($sale_detail){
                return [
                    "id" => $sale_detail->id,
                    "course" => [
                        "id" => $sale_detail->course->id,
                        "title" => $sale_detail->course->title,
                        "image" => env("APP_URL")."storage/".$sale_detail->course->image,
                    ],
                    "type_discount" => $sale_detail->type_discount,
                    "discount" => $sale_detail->discount,
                    "type_campaign" => $sale_detail->type_campaign,
                    "coupon_code" => $sale_detail->coupon_code,
                    "discount_code" => $sale_detail->discount_code,
                    "precio_unitario" => $sale_detail->precio_unitario,
                    "total" => $sale_detail->total,
                    "created_at" => $sale_detail->created_at->format("Y-m-d h:i:s"),
                ];
            }),
            "created_at" => $this->resource->created_at->format("Y-m-d h:i:s"),
        ];
    }
}
